<?php

defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );

header('Content-Type:application/json');

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}
// Access-Control headers are received during OPTIONS requests

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit(0);
}

class Auth extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('masterModel');
        $this->load->model('NotificationModel','notification');
        $this->load->model('LoginModel','loginModel');
    }

    public function check(){
        $data = $this->input->post();
        echo json_encode($this->loginModel->checkApiAuth($data));
    }

    public function isVerified(){
        $data = $this->input->post();
        $data['fyear'] = 3;
        echo json_encode($this->loginModel->verification($data));
    }

    public function logout(){
        $headData = json_decode(base64_decode($this->input->get_request_header('headData')));
        echo json_encode($this->loginModel->appLogout($headData->loginId));
    }

    public function getAuth(){
        $PRD_STORE=$this->db->where('is_delete',0)->where('store_type',1)->get('location_master')->row();
        $headData = new stdClass();
        $headData->loginId = 1;
        $headData->cm_id = 1;
        $headData->RTD_STORE = $PRD_STORE;
        $result['headData'] = base64_encode(json_encode($headData));
		echo json_encode(['status'=>1,'message'=>'User verified.','field_error'=>0,'field_error_message'=>null,'data'=>$result]);
    }
	
	public function changePassword(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['old_password']))
            $errorMessage['old_password'] = "Old Password is required.";
        if(empty($data['new_password']))
            $errorMessage['new_password'] = "New Password is required.";
        
        if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some field is required.','field_error'=>1,'field_error_message'=>$errorMessage]);
		else:
            $data['id'] = $this->loginId;
			$result =  $this->employee->changePassword($data);
			$this->printJson($result);
		endif;
    }
}
?>