<?php

use LDAP\Result;

defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        check_login();
        $this->load->model('Employee_model');
        $this->load->model('Settings_model');
        $this->load->model('Department_model');
        $this->load->model('Absensi_model');
        
    }

    public function index()
    {   
        if ($this->session->userdata('role_id') == 2) {
            $this->load->model('Cuti_model');
            $data['count_pending'] = $this->Cuti_model->get_count_cuti_pending();
            $data['list_employee'] = $this->Employee_model->get_employee_aktif(1);
        }
        $data['settings'] = $this->Settings_model->get_settings();
        $data['employee'] = $this->Employee_model->get_employee_by_id($this->session->userdata('employee_id'));
        $data['absensi'] = $this->Absensi_model->get_absensi();

        $data['title'] = 'Absensi';
        $data['slug'] = 'Kehadiran';
        $data['judul'] = 'Kehadiran / Absensi';
        render_template('absensi/absensi', $data);
    }

    public function check_in($employee_id, $code_kehadiran){ // 1 Hadir, 2 izin, 3 sakit
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $check_absen = $this->Absensi_model->check_absensi_employee($employee_id, date('Y-m-d'));
            $get_employee = $this->Employee_model->get_employee_by_id($employee_id);

            if ($check_absen) {
                $message = [
                    'warning' => 'true',
                    'title' => 'Peringatan!',
                    'desc' => 'Anda sudah melakukan absensi.',
                    'buttontext' => 'Oke, tutup'
                ];
            } else {
                $data = [
                    'employee_id' => $employee_id,
                    'department_id' => $get_employee['department_id'],
                    'date' => date('Y-m-d'),
                    'clock_in' => date('Y-m-d H:i:s'),
                    'presence' => $code_kehadiran,
                ];
                $message = [
                    'success' => 'true',
                    'title' => 'Absen berhasil!',
                    'desc' => 'Terimakasih telah melakukan absensi.',
                    'buttontext' => 'Oke, tutup'
                ];
                    $this->Absensi_model->check_in($data);
            }
            echo json_encode($message);
        }
    }

    public function check_out($employee_id){
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $check_absen = $this->Absensi_model->check_absensi_employee($employee_id, date('Y-m-d'));
            if ($check_absen == null) {
                $message = [
                    'warning' => 'true',
                    'title' => 'Peringatan!',
                    'desc' => 'Anda belum melakukan absensi.',
                    'buttontext' => 'Oke, tutup'
                ];
            } else {
                if ($check_absen['clock_out'] != null) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Peringatan!',
                        'desc' => 'Anda sudah melakukan absensi keluar hari ini!',
                        'buttontext' => 'Oke, tutup'
                    ];
                } else {
                    $time_to_out = strtotime('+9 hours', strtotime($check_absen['clock_in']));
                    if(date('d H:i') <= date('d H:i', $time_to_out) ){
                        $diff = strtotime(date('Y-m-d H:i:s', $time_to_out)) - strtotime(date('Y-m-d H:i:s'));
                        $jam  = floor($diff / (60 * 60));
                        $menit= $diff - $jam * (60 * 60);

                        $message = [
                            'error' => 'true',
                            'title' => 'Peringatan!',
                            'desc' => 'Maaf anda belum bisa keluar hari ini, aturan 9 jam. Sisa waktu '. $jam . ' Jam ' .floor($menit/60). ' Menit.' ,
                            'buttontext' => 'Oke, tutup'
                        ];
                    } else {
                        $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($check_absen['clock_in']);
                        $jam  = floor($diff / (60 * 60));
                        $menit= $diff - $jam * (60 * 60);
                        $total_kerja = $jam.' Jam '.floor($menit/60).' Menit';

                        $message = [
                            'success' => 'true',
                            'title' => 'Absen berhasil!',
                            'desc' => 'Terimakasih telah bekerja dengan maksimal, hati-hati diwaktu pulang dan semoga diberi kesehatan😊',
                            'buttontext' => 'Oke, tutup'
                        ];

                        $data = [
                            'clock_out' => date('Y-m-d H:i:s'),
                            'total_hour' => $total_kerja
                        ];

                        $this->Absensi_model->check_out($employee_id, $data);
                    }
                }
            }
            echo json_encode($message);
        }
    }

    public function check_in_manual(){
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $this->form_validation->set_rules('select_name', 'Karyawan', 'trim|callback_select_check');
            $this->form_validation->set_rules('clock_in', 'Tanggal', 'trim|required');

            if ($this->form_validation->run() == false) {
                $message = [
                    'errors' => 'true',
                    'desc' => validation_errors('<div class="alert alert-danger alert-dismissible show fade fs-7" role="alert">',
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'),
                    'csrfhash' => $this->security->get_csrf_hash(),
                ];
                
            } else {
                
                $employee_id = $this->input->post('select_name', TRUE);
                $datetime = $this->input->post('clock_in', TRUE);
                $get_employee = $this->Employee_model->get_employee_by_id($employee_id);
                $check_absen = $this->Absensi_model->check_absensi_employee($employee_id, date('Y-m-d', strtotime($datetime)));
                
                if ($check_absen) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Gagal!',
                        'desc' => 'Karyawan ini sudah melakukan absensi.',
                        'buttontext' => 'Oke, tutup'
                    ];
                } else {
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Terimakasih, Anda telah menambahkan absensi masuk untuk <span class="badge badge-soft-success">'.$get_employee['full_name'].'</span>.',
                        'buttontext' => 'Oke, terimakasih'
                    ];

                    $data = [
                        'employee_id' => $employee_id,
                        'department_id' => $get_employee['department_id'],
                        'date' => date('Y-m-d', strtotime($datetime)),
                        'clock_in' => date('Y-m-d H:i:s', strtotime($datetime)),
                        'presence' => 1,
                    ];
                    $this->Absensi_model->check_in($data);
                }
            }
            echo json_encode($message);
        }
    }

    public function check_out_manual(){
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $this->form_validation->set_rules('select_name', 'Karyawan', 'trim|callback_select_check');
            $this->form_validation->set_rules('clock_out', 'Tanggal', 'trim|required');

            if ($this->form_validation->run() == false) {
                $message = [
                    'errors' => 'true',
                    'desc' => validation_errors('<div class="alert alert-danger alert-dismissible show fade fs-7" role="alert">',
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'),
                    'csrfhash' => $this->security->get_csrf_hash(),
                ];
                
            } else {
                $employee_id = $this->input->post('select_name', TRUE);
                $datetime = $this->input->post('clock_out', TRUE);
                $check_absen = $this->Absensi_model->check_absensi_employee($employee_id, date('Y-m-d', strtotime($datetime)));
                
                if ($check_absen == NULL) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Gagal!',
                        'desc' => 'Karyawan belum melakukan absensi masuk, silahkan tambahkan terlebih dahulu.',
                        'buttontext' => 'Oke, tutup'
                    ];
                } else {
                    if ($check_absen['clock_out']) {
                        $diff = strtotime(date('Y-m-d H:i:s', strtotime($datetime))) - strtotime($check_absen['clock_in']);
                        $jam  = floor($diff / (60 * 60));
                        $menit= $diff - $jam * (60 * 60);
                        $total_kerja = $jam.' Jam '.floor($menit/60).' Menit';

                        $message = [
                            'error' => 'true',
                            'title' => 'Gagal!',
                            'desc' => 'Terimakasih, Jam keluar karyawan berhasil dirubah.',
                            'buttontext' => 'Oke, tutup'
                        ];

                        $data = [
                            'clock_out' => date('Y-m-d H:i:s', strtotime($datetime)),
                            'total_hour' => $total_kerja
                        ];
                        $this->Absensi_model->check_out($employee_id, $data);
                    } else if($check_absen['clock_out'] == null && $check_absen['presence'] == 1) {
                        $diff = strtotime(date('Y-m-d H:i:s', strtotime($datetime))) - strtotime($check_absen['clock_in']);
                        $jam  = floor($diff / (60 * 60));
                        $menit= $diff - $jam * (60 * 60);
                        $total_kerja = $jam.' Jam '.floor($menit/60).' Menit';

                        $message = [
                            'success' => 'true',
                            'title' => 'Berhasil!',
                            'desc' => 'Terimakasih, Anda telah menambahkan absensi keluar secara manual.',
                            'buttontext' => 'Oke, terimakasih'
                        ];

                        $data = [
                            'clock_out' => date('Y-m-d H:i:s', strtotime($datetime)),
                            'total_hour' => $total_kerja
                        ];
                        $this->Absensi_model->check_out($employee_id, $data);
                    } else {
                        $message = [
                            'error' => 'true',
                            'title' => 'Gagal!',
                            'desc' => 'Karyawan tidak bisa absensi keluar karena sakit atau izin.',
                            'buttontext' => 'Oke, tutup'
                        ];
                    }
                }
            }
            echo json_encode($message);
        }
    }

    public function laporan(){
        check_user_acces();
        if ($this->session->userdata('role_id') == 2) {
            $this->load->model('Cuti_model');
            $data['count_pending'] = $this->Cuti_model->get_count_cuti_pending();
        }
        $data['settings'] = $this->Settings_model->get_settings();
        $data['employee'] = $this->Employee_model->get_employee_by_id($this->session->userdata('employee_id'));
        $data['all_employee'] = $this->Employee_model->get_employee_aktif(1);

        $data['title'] = 'Laporan';
        $data['slug'] = 'Laporan';
        $data['judul'] = 'Laporan Absensi Karyawan';
        render_template('absensi/laporan', $data);
    }

    public function get_laporan(){
        check_user_acces();
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $this->form_validation->set_rules('filter_month', 'Bulan', 'trim|callback_select_check');
            $this->form_validation->set_rules('filter_name', 'Nama Karyawan', 'trim|callback_select_check');

            $bulan = $this->input->post('filter_month', TRUE);
            $employee_id = $this->input->post('filter_name', TRUE);
            if ($this->form_validation->run() == FALSE) {
                $message = [
                    'errors' => 'true',
                    'title' => 'Gagal!',
                    'desc' => validation_errors('<p class="fs-7">','</p>'),
                    'buttontext' => 'Oke, tutup'
                ];
            } else {
                $result = $this->Absensi_model->get_laporan_by_month($bulan, $employee_id);

                if ($result == null) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Absensi Karyawan belum ada',
                        'buttontext' => 'Oke, tutup'
                    ];
                } else {

                    $date_filter       = explode('-', $bulan);
                    $total_dayin_month = cal_days_in_month(CAL_GREGORIAN,$date_filter[0],$date_filter[1]);

                    $create_date = mktime(0, 0, 0, $date_filter[0], 1, $date_filter['1']);
                    $month_name  = date('M', $create_date);

                    $laporan = $this->Absensi_model->get_laporan($date_filter[0], $date_filter[1], $employee_id);

                    $loop_date = $create_date;
                    $calendar  = [];
                    for ($i=1; $i <= $total_dayin_month; $i++) { 
                        $holiday      = date('w', $loop_date) == 0 || date('w', $loop_date) == 6 ? 'holiday' : 'masuk';
                        $background   = $holiday == 'holiday' ? 'bg-danger' : 'bg-primary';
                        $selected_day = date('d-m-y', $loop_date) == date('d-m-y') ? 'border-primary' : '';
                        $target       = array_filter($laporan, fn($v) => date('j', strtotime($v->date)) == $i);
                        $status       = $loop_date < strtotime(date('y-m-d')) && $holiday != 'holiday' ? 'A' : '-';
                        if (count($target) > 0) {
                            $target = [...$target][0]->presence;
                            switch ($target) {
                                case 1:
                                    $status = 'H';
                                    $text_color = 'text-primary';
                                    break;
                                case 2:
                                    $status = 'I';
                                    $text_color = 'text-blue';
                                    break;
                                default:
                                    $status = 'S';
                                    $text_color = 'text-pink';
                                break;
                            }

                        } else {
                            $text_color = $status == 'A' ? 'text-danger' : '';
                        }
                        $calendar[] = '
                            <div class="col-4 col-md-2 col-lg-2">
                                <div class="position-relative card card-border mb-0 overflow-hidden '.$selected_day.'">
                                    <div class="card-body p-2 d-flex align-items-center gap-2">
                                        <div class="border px-2 rounded-3 text-center">
                                            <span class="px-2 rounded fs-8" style="background: #ededed">'.$month_name.'</span>
                                            <h4 class="fw-bold" style="margin-bottom:-10px">'.$i.'</h4>
                                            <span class="fs-9">'.date('D', $loop_date).'</span>
                                        </div>
                                        <div class="d-flex align-items-end mt-2 mx-auto">
                                            <h2 class="fw-bold mb-0 '.$text_color.'">'.$status.'</h2>
                                            <span class="feather-icon text-primary fw-bold">
                                                <i data-feather="check-circle"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="position-absolute top-0 end-0 fs-9 '.$background.' text-white px-1 fw-bold">'.ucwords($holiday).'</span>
                                </div>
                            </div>
                            ';

                        $loop_date  = strtotime('+1 day', $loop_date);
                    }
                    
                    $total_days = $this->countDays(strtotime($result['date']), 'day');
                    $total_alpa = $total_days - $result['hadir'];

                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => $result,
                        'total_day' => $total_days,
                        'calendar' => $calendar,
                        'total_alpa' => $total_alpa,
                        'csrfhash' => $this->security->get_csrf_hash(),
                        'buttontext' => 'Oke, tutup'
                    ];
                }
            }
            
            echo json_encode($message);
        }
    }
    public function get_laporan_year(){
        check_user_acces();
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        } else {
            $this->form_validation->set_rules('filter_name', 'Nama Karyawan', 'trim|callback_select_check');

            $employee_id = $this->input->post('filter_name', TRUE);
            if ($this->form_validation->run() == FALSE) {
                $message = [
                    'errors' => 'true',
                    'title' => 'Gagal!',
                    'desc' => validation_errors('<p class="fs-7">','</p>'),
                    'buttontext' => 'Oke, tutup'
                ];
            } else {
                $result = $this->Absensi_model->get_laporan_by_year($employee_id);

                if ($result == null) {
                    $message = [
                        'error' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => 'Absensi Karyawan belum ada',
                        'buttontext' => 'Oke, tutup'
                    ];
                } else {
                    $message = [
                        'success' => 'true',
                        'title' => 'Berhasil!',
                        'desc' => $result,
                        'total_day' => '-',
                        'total_sat_sun' => '-',
                        'total_alpa' => '-',
                        'csrfhash' => $this->security->get_csrf_hash(),
                        'buttontext' => 'Oke, tutup'
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
        }else{
                return TRUE;
        }
    }

    private function countDays($date, $type) {

        $year  = date('Y', $date);
        $month = date('n', $date);
        $total_date_per_month = date('t', $date);

        $start = mktime(0, 0, 0, $month, 1, $year);
        $end   = mktime(0, 0, 0, $month, $total_date_per_month, $year);
        $day = 0;
        $satsun = 0;

        while(date('Y-m-d', $start) <= date('Y-m-d', $end)){
            $day += date('N', $start) < 6  ? 1 : 0;
            $satsun += date('N', $start) >= 6 ? 1 : 0;
            $start = strtotime("+1 day", $start);
        }

        if($type == 'satsun'){
            return $satsun;
        }

        return $day;
    }

}

/* End of file Absensi.php */
