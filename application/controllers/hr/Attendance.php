<?php
class Attendance extends MY_Controller
{
    private $indexPage = "hr/attendance/index";
    private $monthlyAttendance = "hr/attendance/month_attendance";
    private $attendanceForm = "hr/attendance/form";
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Attendance";
		$this->data['headData']->controller = "hr/attendance";
		$this->data['headData']->pageUrl = "hr/attendance";
	}
	
	public function index(){
		$this->data['todayStats'] = $this->attendance->getAttendanceStatsByDate(date('Y-m-d'));
		// print_r($this->data['todayStats']);exit;
        $this->load->view($this->indexPage,$this->data);
    }

	public function monthlyAttendance(){
        $this->load->view($this->monthlyAttendance,$this->data);
    }

    public function loadAttendanceSheet(){
        $data = $this->input->post();
		$this->printJson($this->attendance->loadAttendanceSheet($data));
    }

}
?>