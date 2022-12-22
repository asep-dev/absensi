<?php

use LDAP\Result;

defined('BASEPATH') OR exit('No direct script access allowed');

class Cuti extends CI_Controller {

    
    public function __construct()
    {
        parent::__construct();
        
        check_login();
        $this->load->model('Settings_model');
        $this->load->model('Department_model');
        $this->load->model('Employee_model');
        $this->load->model('Cuti_model');
    }

    public function index()
    {
        $data['settings'] = $this->Settings_model->get_settings();
        $data['employee'] = $this->Employee_model->get_employee_by_id($this->session->userdata('employee_id'));
        $data['department'] = $this->Department_model->get_department();
        
        if ($this->session->userdata('role_id') == 2) {
            $data['list_cuti'] = $this->Cuti_model->get_cuti();
            $data['count_pending'] = $this->Cuti_model->get_count_cuti_pending();
        } else {
            $data['list_cuti'] = $this->Cuti_model->get_cuti($data['employee']['employee_id']);
        }

        // var_dump( $data['list_cuti']);die;

        $data['title'] = 'Cuti';
        $data['slug'] = 'Cuti';
        $data['judul'] = 'Cuti Karyawan';
        render_template('cuti/cuti', $data);
    }

    public function add_cuti(){
        if ($this->session->userdata('role_id') == 2) {
            redirect('blocked');
        }
        $this->form_validation->set_rules('cuti', 'Kategori Cuti', 'trim|max_length[1]|callback_select_check');
        $this->form_validation->set_rules('start_date', 'Mulai cuti', 'trim|required|callback_select_date');
        $this->form_validation->set_rules('end_date', 'Berakhir cuti', 'trim|required|callback_not_matches');
        $this->form_validation->set_rules('reason', 'Alasan Cuti', 'trim|required');

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            if ($this->form_validation->run() == false) {
                $message = [
                    'errors' => 'true',
                    'desc' => validation_errors('<div class="alert alert-danger alert-dismissible show fade fs-7" role="alert">',
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'),
                    'csrfHash' => $this->security->get_csrf_hash(),
                ];
                
            } else {
                
                $employee_id = $this->input->post('employee_id', TRUE);
                $cuti_type = $this->input->post('cuti', TRUE);
                $start_date = $this->input->post('start_date', TRUE);
                $end_date = $this->input->post('end_date', TRUE);
                $reason = $this->input->post('reason', TRUE);
                
                $diff = strtotime($end_date) - strtotime($start_date);
                $total_day = floor($diff /  (60 * 60 * 24) );
                
                $check_cuti_now = $this->Cuti_model->get_cuti_datenow($employee_id, date('Y-m-d'));
                // var_dump($check_cuti_now);die;

                if ($check_cuti_now != null) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Gagal!',
                        'desc' => 'Anda sudah mengajukan cuti hari ini maksimal 1 hari 1x.',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                } else {
                    $data = [
                        'employee_id' => htmlspecialchars($employee_id) ,
                        'cuti_type' => htmlspecialchars($cuti_type),
                        'submission_date' => date('Y-m-d'),
                        'start_date' => htmlspecialchars($start_date),
                        'end_date' => htmlspecialchars($end_date),
                        'number_of_days' => $total_day,
                        'cuti_status' => 1,
                        'cuti_reason' => htmlspecialchars($reason),
                    ];
    
                    $message = [
                        'warning' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil diajukan harap cek secara berkala apabila cuti <span class="badge badge-soft-success">disetujui </span> / <span class="badge badge-soft-danger">ditolak </span>',
                        'buttontext' => 'Oke, terimakasih'
                    ];
    
                    $this->Cuti_model->add_cuti($data);
                }
            }
            echo json_encode($message);
        }
    }

    public function approve_reject($cuti_id = '', $approvecode = ''){ // enum(P, A, R, AC)
        check_user_acces();
        $this->load->model('Absensi_model');

        $cuti = $this->Cuti_model->get_cuti_by_id($cuti_id);
        $employee = $this->Employee_model->get_employee_by_id($cuti['employee_id']);

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            if ($cuti['cuti_type'] == 'CS' || $cuti['cuti_type'] == 'CI') {
                $code_kehadiran = $cuti['cuti_type'] == 'CI' ? 2 : 3; // 2 Izin , 3 Sakit
                $data_cuti = ['cuti_status' => $approvecode,];

                if ($approvecode == 3){ //Rejected
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil di ditolak ',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                    $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
                } else {
                    for ($i= 0; $i < $cuti['number_of_days']; $i++) { 
                        $data_absensi = [
                            'employee_id' => $cuti['employee_id'],
                            'department_id' => $employee['department_id'],
                            'date' => date('Y-m-d', strtotime('+'.$i.' day', strtotime(date('Y-m-d')))),
                            'presence' => $code_kehadiran,
                        ];
                        $this->Absensi_model->check_in($data_absensi);
                    }
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil dikonfirmasi.',
                        'buttontext' => 'Oke, tutup'
                    ];
                }

                $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
            } else { // Cuti Tahunan
                $data_cuti = ['cuti_status' => $approvecode];

                if ($approvecode == 3){ // Rejected
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil di ditolak ',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                } elseif ($approvecode == 2){ //Approve
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil di konfirmasi ',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                } else { // Approve but Modified
                    
                    $this->form_validation->set_rules('start_date', 'Mulai cuti', 'trim|required|callback_select_date');
                    $this->form_validation->set_rules('end_date', 'Berakhir cuti', 'trim|required|callback_not_matches');

                    if ($this->form_validation->run() == FALSE) {
                        $message = [
                            'errors' => 'true',
                            'desc' => validation_errors('<div class="alert alert-danger alert-dismissible show fade fs-7" role="alert">',
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'),
                            'csrfHash' => $this->security->get_csrf_hash(),
                        ];
                    } else {
                        $start_date = $this->input->post('start_date', TRUE);
                        $end_date = $this->input->post('end_date', TRUE);

                        $diff = strtotime($end_date) - strtotime($start_date);
                        $total_day = floor($diff /  (60 * 60 * 24) );

                        $data_cuti = [
                            'start_date' => htmlspecialchars($start_date),
                            'end_date' => htmlspecialchars($end_date),
                            'cuti_status' => $approvecode,
                            'number_of_days' => $total_day,
                        ];
                        $message = [
                            'success' => 'true',
                            'title' => 'Berhasil!',
                            'desc' => 'Cuti berhasil di konfirmasi dan dirubah',
                            'buttontext' => 'Oke, terimakasih'
                        ];
                    }
                    
                }
                
                $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
            }
            echo json_encode($message);
        }
    }

    function select_check($str){
        if ($str == '0'){
                $this->form_validation->set_message('select_check', 'The {field} field is required.');
                return FALSE;
        }else{
                return TRUE;
        }
    }

    function select_date($date){
        if ($date <= date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))))){
                $this->form_validation->set_message('select_date', 'The {field} cannot be less than the current date.');
                return FALSE;
        } else {
                return TRUE;
        }
    }

    function not_matches($date){
        if ($date == $this->input->post('start_date', TRUE)){
            $this->form_validation->set_message('not_matches', 'The {field} cannot be the same as the start date.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}