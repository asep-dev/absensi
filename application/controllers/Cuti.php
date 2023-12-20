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
        $this->form_validation->set_rules('end_date', 'Berakhir cuti', 'trim|required|callback_select_end_date');
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
                
                $employee_id    = $this->input->post('employee_id', TRUE);
                $cuti_type      = $this->input->post('cuti', TRUE);
                $start_date     = strtotime($this->input->post('start_date', TRUE));
                $end_date       = strtotime($this->input->post('end_date', TRUE));
                $reason         = $this->input->post('reason', TRUE);

                $start_temp_date = $start_date;
                $total_day = 0;
                while(date('Y-m-d', $start_temp_date) <= date('Y-m-d', $end_date)){
                    $total_day += date('N', $start_temp_date) < 6 ? 1 : 0;
                    $start_temp_date = strtotime("+1 day", $start_temp_date);
                }

                $check_cuti_now = $this->Cuti_model->get_cuti_datenow($employee_id, date('Y-m-d'));
                $employee = $this->Employee_model->get_employee_by_id($employee_id);
                // var_dump($check_cuti_now);die;
                $settings = $this->Settings_model->get_settings();

                if ($check_cuti_now != null) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Gagal!',
                        'desc' => 'Anda sudah mengajukan cuti hari ini maksimal 1 hari 1x.',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                } else {
                    $result = $employee['remaining_days_off'] - $total_day;
                    if ($cuti_type != 4 && $result < 0) {
                        $message = [
                            'error' => 'true',
                            'title' => 'Mohon Maaf!',
                            'desc' => 'Cuti anda melebihi sisa cuti, jika cuti sangatlah penting hubungi atasan. Sisa Cuti anda ' . $employee['remaining_days_off']. ' Hari',
                            'buttontext' => 'Oke, terimakasih'
                        ];
                    } else {
                        $data = [
                            'employee_id' => htmlspecialchars($employee_id) ,
                            'cuti_type' => htmlspecialchars($cuti_type),
                            'submission_date' => date('Y-m-d'),
                            'start_date' => date('Y-m-d', $start_date),
                            'end_date' => date('Y-m-d', $end_date),
                            'number_of_days' => $total_day,
                            'cuti_status' => 1,
                            'cuti_reason' => htmlspecialchars($reason),
                        ];

                        if ($cuti_type == 4) {
                            if ($_FILES['image']['name']) {
                                $data['cuti_file_letter'] = $this->upload_letter('', TRUE);
                            }
                        }
                        $message = [
                            'success' => 'true',
                            'title' => 'Berhasil!',
                            'desc' => 'Cuti berhasil diajukan harap cek secara berkala apabila cuti <span class="badge badge-soft-success">disetujui </span> / <span class="badge badge-soft-danger">ditolak </span>',
                            'buttontext' => 'Oke, terimakasih'
                        ];

                        $this->Cuti_model->add_cuti($data);
                        $pesan = "Hallo, Admin👋\n\n".$employee['full_name']."\n\nTelah mengajukan cuti silahkan cek menu cuti dan segera lakukan konfirmasi karyawan👍";
                        send_message_whatsapp($settings['contact'], $pesan);
                    }
                    
                }
            }
            echo json_encode($message);
        }
    }

    public function upload_letter(String $cuti_id = '', Bool $return = false){
        if ($this->session->userdata('role_id') != 1) {
            redirect('blocked');
        }
        $config['allowed_types']    = 'jpg|png|jpeg';
        $config['max_size']         = '500';
        $config['upload_path']      = './public/image/letter';
        $config['file_ext_tolower'] = TRUE;
        $config['encrypt_name']     = TRUE;
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('image')) {
            $message = [
                'errors' => 'true',
                'desc' => $this->upload->display_errors('<div class="alert alert-danger alert-dismissible show fade fs-7" role="alert">',
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'),
                'csrfHash' => $this->security->get_csrf_hash(),
            ];
            echo json_encode($message);die;
        } else {
            if ($return) {
                return $this->upload->data('file_name');
            } else {
                $check = $this->Cuti_model->get_cuti_by_id($cuti_id);
                if (!$check) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Gagal!',
                        'desc' => 'Mohon maaf cuti tidak ditemukan.',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                } else {
                    if ($check['cuti_file_letter'] !== NULL) {
                        unlink(FCPATH . 'public/image/letter/' . $check['cuti_file_letter']);
                    }
                    $data_cuti = [
                        'cuti_file_letter' => $this->upload->data('file_name'),
                    ];
                    $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
                    $message = [
                        'success'    => 'true',
                        'title'      => 'Berhasil!',
                        'desc'       => 'Upload cuti surat berhasil diupload',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                }

                echo json_encode($message);
            }
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
            
            $name_cuti = ['CS' => 'Cuti Sakit', 'CI' => 'Cuti Izin', 'CSS' => 'Cuti Sakit Surat', 'CT' => 'Cuti Tahunan'];
            
            if ($cuti['cuti_type'] != 'CT') {
                $code_kehadiran = ($cuti['cuti_type'] == 'CI') ? 2 : (($cuti['cuti_type'] == 'CS') ? 3 : 4); // 2 Izin , 3 Sakit, 4 Sakit dengan Surat
                $data_cuti = ['cuti_status' => $approvecode,];

                if ($approvecode == 3){ //Rejected
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil di ditolak ',
                        'buttontext' => 'Oke, terimakasih'
                    ];

                    $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
                    // Message Whatsapp
                    $pesan = "Hallo, ".$employee['full_name']."👋\nMohon maaf sekali tanggal pengajuan "
                    .$name_cuti[$cuti['cuti_type']]
                    ." anda "
                    .date('l, d F Y', strtotime($cuti['submission_date']))
                    ." ditolak. Silahkan ajukan kembali dihari nanti👍";
                } else {
                    $total_sisa_cuti = $employee['remaining_days_off'] - $cuti['number_of_days'];
                    if ($cuti['cuti_type'] != 'CSS') {
                        $this->Employee_model->update_profile($employee['employee_id'], ['remaining_days_off' => $total_sisa_cuti]);
                    }

                    $start_date = strtotime($cuti['start_date']);
                    $end_date   = strtotime($cuti['end_date']);

                    $data_absensi = [];
                    while(date('Y-m-d', $start_date) <= date('Y-m-d', $end_date)){
                        if(date('N', $start_date) < 6){
                            $absensi = [
                                'employee_id' => $cuti['employee_id'],
                                'department_id' => $employee['department_id'],
                                'date' => date('Y-m-d', $start_date),
                                'presence' => $code_kehadiran,
                            ];
                            $data_absensi[] = $absensi;
                        }
                        $start_date = strtotime("+1 day", $start_date);
                    }
                    $this->db->insert_batch('absensi',$data_absensi);
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil dikonfirmasi.',
                        'buttontext' => 'Oke, tutup'
                    ];

                    $pesan = "Hallo, ".$employee['full_name']."👋\nSelamat tanggal pengajuan "
                    .$name_cuti[$cuti['cuti_type']]
                    ." anda "
                    .date('l, d F Y', strtotime($cuti['submission_date']))
                    ." berhasil disetujui, Semoga cuti anda bermanfaat👍";
                }

                $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
                send_message_whatsapp($employee['contact'], $pesan);
            } else { // Cuti Tahunan
                $data_cuti = ['cuti_status' => $approvecode];

                if ($approvecode == 3){ // Rejected
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil di ditolak ',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                    // Message Whatsapp
                    $pesan = "Hallo, ".$employee['full_name']."👋\nMohon maaf sekali tanggal pengajuan "
                    .$name_cuti[$cuti['cuti_type']]
                    ." anda "
                    .date('l, d F Y', strtotime($cuti['submission_date']))
                    ." ditolak. Silahkan ajukan kembali dihari nanti👍";

                    $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
                    send_message_whatsapp($employee['contact'], $pesan);
                } elseif ($approvecode == 2){ //Approve
                    $total_sisa_cuti = $employee['remaining_days_off'] - $cuti['number_of_days'];
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Cuti berhasil di konfirmasi ',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                    $pesan = "Hallo, ".$employee['full_name']."👋\nSelamat tanggal pengajuan "
                    .$name_cuti[$cuti['cuti_type']]
                    ." anda "
                    .date('l, d F Y', strtotime($cuti['submission_date']))
                    ." berhasil disetujui, Semoga cuti anda bermanfaat👍";

                    $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
                    $this->Employee_model->update_profile($employee['employee_id'], ['remaining_days_off' => $total_sisa_cuti]);
                    send_message_whatsapp($employee['contact'], $pesan);
                } else { // Approve but Modified
                    
                    $this->form_validation->set_rules('start_date', 'Mulai cuti', 'trim|required|callback_select_date');
                    $this->form_validation->set_rules('end_date', 'Berakhir cuti', 'trim|required|callback_select_end_date');

                    if ($this->form_validation->run() == FALSE) {
                        $message = [
                            'errors' => 'true',
                            'desc' => validation_errors('<div class="alert alert-danger alert-dismissible show fade fs-7" role="alert">',
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'),
                            'csrfHash' => $this->security->get_csrf_hash(),
                        ];
                    } else {
                        $start_date = strtotime($this->input->post('start_date', TRUE));
                        $end_date   = strtotime($this->input->post('end_date', TRUE));

                        $start_temp_date = $start_date;
                        $total_day = 0;
                        while(date('Y-m-d', $start_temp_date) <= date('Y-m-d', $end_date)){
                            $total_day += date('N', $start_temp_date) < 6 ? 1 : 0;
                            $start_temp_date = strtotime("+1 day", $start_temp_date);
                        }

                        $total_sisa_cuti = $employee['remaining_days_off'] - $total_day;
                        if ($total_sisa_cuti < 0) {
                            $message = [
                                'warning' => 'true',
                                'title' => 'Mohon Maaf!',
                                'desc' => 'Tanggal cuti yang dimasukan melebihi sisa cuti, Jumlah sisa cuti karyawan ini tinggal '.$employee['remaining_days_off'].' Hari.',
                                'buttontext' => 'Oke, terimakasih'
                            ];
                        } else {
                            $data_cuti = [
                                'start_date' => date('Y-m-d', $start_date),
                                'end_date' => date('Y-m-d', $end_date),
                                'cuti_status' => $approvecode,
                                'number_of_days' => $total_day,
                            ];
                            $message = [
                                'success' => 'true',
                                'title' => 'Berhasil!',
                                'desc' => 'Cuti berhasil di konfirmasi dan dirubah',
                                'buttontext' => 'Oke, terimakasih'
                            ];

                            $pesan = "Hallo, ".$employee['full_name']."👋\nSelamat tanggal pengajuan "
                            .$name_cuti[$cuti['cuti_type']]
                            ." anda "
                            .date('l, d F Y', strtotime($cuti['submission_date']))
                            ." berhasil disetujui tetapi ada perubahan silahkan cek diwebsite, Semoga cuti anda bermanfaat👍";

                            $this->Cuti_model->update_cuti($cuti_id, $data_cuti);
                            $this->Employee_model->update_profile($employee['employee_id'], ['remaining_days_off' => $total_sisa_cuti]);
                            send_message_whatsapp($employee['contact'], $pesan);
                        }
                        
                    }
                    
                }
            }
            echo json_encode($message);
        }
    }

    public function delete_cuti($cuti_id = ''){
        if ($this->session->userdata('role_id') != 1) {
            redirect('blocked');
        }
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $check = $this->Cuti_model->get_cuti_by_id(strip_tags(htmlspecialchars($cuti_id)));
            if (!$check) {
                $message = [
                    'error' => 'true',
                    'title' => 'Gagal!',
                    'desc' => 'Mohon maaf cuti tidak ditemukan atau tidak ada.',
                    'buttontext' => 'Oke, terimakasih'
                ];
            } else {
                if ($check['cuti_status'] != "P") {
                    $message = [
                        'error' => 'true',
                        'title' => 'Mohon Maaf',
                        'desc' => 'Cuti tidak bisa dihapus, karena cuti anda telah di verifikasi.',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                } else {
                    if ($check['cuti_type'] == "CSS" && $check['cuti_file_letter'] != null) {
                        unlink(FCPATH . 'public/image/letter/' . $check['cuti_file_letter']);
                    }
                    $this->Cuti_model->delete_cuti($cuti_id);
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil',
                        'desc' => 'Cuti berhasil dihapus.',
                        'buttontext' => 'Oke, terimakasih'
                    ];
                }
            }

            echo json_encode($message);
        }
    }

    function select_check($str){
        if ($str == '0'){
            $this->form_validation->set_message('select_check', 'The {field} field is required.');
            return FALSE;
        }
        return TRUE;
    }

    function select_date($date){
        if ($this->input->post('cuti') == 4) {
            return TRUE;
        } elseif ($date <= date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))))){
            $this->form_validation->set_message('select_date', 'The {field} cannot be less than the current date.');
            return FALSE;
        }
        return TRUE;
    }
    function select_end_date($date){
        if ($date < date('Y-m-d', strtotime($this->input->post('start_date')))){
            $this->form_validation->set_message('select_end_date', 'The {field} cannot be less than the start date.');
            return FALSE;
        }
        return TRUE;
    }

}