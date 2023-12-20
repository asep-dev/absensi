<?php


defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_model extends CI_Model {

    public function get_absensi()
    {
        return $this->db->select('*')
                        ->from('absensi')
                        ->where('date', date('Y-m-d'))
                        ->join('employee','employee.employee_id = absensi.employee_id')
                        ->join('department','department.department_id = absensi.department_id')
                        ->get()
                        ->result_array();
    }

    public function check_absensi_employee($employee_id, $date){
        $check = [
            'date' => $date,
            'employee_id' => $employee_id,
        ];
        return $this->db->get_where('absensi', $check)->row_array();
    }

    public function count_absensi_by_day($presence){
        return $this->db->select('date', 'presence')
                        ->from('absensi')
                        ->where('date', date('Y-m-d'))
                        ->where('presence', $presence)
                        ->get()
                        ->num_rows();
    }

    public function count_absensi_by_employee($employee_id, $presence){
        return $this->db->select('date','employee_id','presence')
                        ->from('absensi')
                        ->where('DATE_FORMAT(date,"%Y")',date('Y'))
                        ->where('employee_id', $employee_id)
                        ->where('presence', $presence)
                        ->get()
                        ->num_rows();
    }

    public function get_absensi_per_month(){
        return $this->db->select("date, SUM(if(presence = 1,1,0)) AS hadir, SUM(if(presence = 2,1,0)) AS izin , SUM(if(presence = 3 OR presence = 4,1,0)) AS sakit,")
                        ->from('absensi')
                        ->where('MONTH(date) =', date('m'))
                        ->group_by('date')
                        ->get()
                        ->result_array();
    }

    public function check_in($data){
        $this->db->insert('absensi', $data);
    }

    public function check_out($employee_id, $data){
        $this->db->set($data)
                ->where(['employee_id' => $employee_id, 'date' => date('Y-m-d')])
                ->update('absensi');
    }

    public function get_laporan_by_month($bulan, $employee_id){
        return $this->db->select("employee_id, DATE_FORMAT(date, '%d-%m-%Y') as date, sum(if(presence = 1,1,0)) AS hadir, sum(if(presence = 2,1,0)) AS izin , sum(if(presence = 3 OR presence = 4,1,0)) AS sakit, sum(if(DAYOFWEEK(date) = 1 OR DAYOFWEEK(date) = 7,1,0)) as work_on_holiday")
                        ->from('absensi')
                        ->where('DATE_FORMAT(date, "%m-%Y")=', $bulan)
                        ->where('employee_id', $employee_id)
                        ->group_by('employee_id')
                        ->get()
                        ->row_array();
    }

    public function get_laporan($bulan, $tahun, $employee_id){
        return $this->db->select('*')
                        ->from('absensi')
                        ->where('DATE_FORMAT(date, "%m")=', $bulan)
                        ->where('DATE_FORMAT(date, "%Y")=', $tahun)
                        ->where('employee_id', $employee_id)
                        ->get()
                        ->result();
    }

    public function get_laporan_by_year($employee_id){
        return $this->db->select("employee_id, DATE_FORMAT(date, '%Y') as date, sum(if(presence = 1,1,0)) AS hadir, sum(if(presence = 2,1,0)) AS izin , sum(if(presence = 3 OR presence = 4,1,0)) AS sakit,")
                        ->from('absensi')
                        ->where('employee_id', $employee_id)
                        ->where('DATE_FORMAT(date,"%Y")', date('Y'))
                        ->group_by('employee_id')
                        ->get()
                        ->row_array();
    }

}

/* End of file Absensi_model.php */
