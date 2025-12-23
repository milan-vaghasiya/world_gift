<?php
class Dashboard extends MY_Controller{
	
	public function __construct()	{
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Dashboard";
		$this->data['headData']->controller = "dashboard";
	}
	
	public function index(){
		$this->data['todaySalesData'] = $this->dashboard->getTodaySales();
		$this->data['payData'] = $this->dashboard->getTotalPayRecive('12'); 
		$this->data['reciveData'] = $this->dashboard->getTotalPayRecive('6,7,8');
		$this->load->view('dashboard',$this->data);
	}
}
?>