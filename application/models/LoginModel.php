<?php

class LoginModel extends CI_Model{
	private $employeeMaster = "employee_master";
	private $menuPermission = "menu_permission";
    private $subMenuPermission = "sub_menu_permission";
    private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager","6"=>"Employee"];

	public function checkAuth($data){
		$result = $this->db->where('emp_contact',$data['user_name'])->where('emp_password',md5($data['password']))->where('is_delete',0)->get($this->employeeMaster);
		
		if($result->num_rows() == 1):
			$resData = $result->row();
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.','field_error'=>0,'field_error_message'=>null];
			else:
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.','field_error'=>0,'field_error_message'=>null];
				else:
					$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
					$RTD_STORE=$this->db->where('is_delete',0)->where('cm_id',$resData->cm_id)->where('store_type',1)->get('location_master')->row();
					$GIF_STORE=$this->db->where('is_delete',0)->where('cm_id',$resData->cm_id)->where('store_type',5)->get('location_master')->row();
					//$PKG_STORE=$this->db->where('is_delete',0)->where('cm_id',$resData->cm_id)->where('store_type',2)->get('location_master')->row();
					//$PROD_STORE=$this->db->where('is_delete',0)->where('cm_id',$resData->cm_id)->where('store_type',4)->get('location_master')->row();
					
					$this->session->set_userdata('LoginOk','login success');
					$this->session->set_userdata('loginId',$resData->id);
					$this->session->set_userdata('role',$resData->emp_role);
					$empRole=$resData->emp_role;
					if($resData->emp_role == -1){$empRole= 1;}
					$this->session->set_userdata('CMID',$resData->cm_id);
					$this->session->set_userdata('roleName',$this->empRole[$empRole]);
					$this->session->set_userdata('emp_name',$resData->emp_name);
					$this->session->set_userdata('RTD_STORE',$RTD_STORE);
					$this->session->set_userdata('GIF_STORE',$GIF_STORE);
					//$this->session->set_userdata('PKG_STORE',$PKG_STORE);
					//$this->session->set_userdata('PROD_STORE',$PROD_STORE);
					
					$startDate = $fyData->start_date;
					$endDate = $fyData->end_date;
					$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
					$this->session->set_userdata('currentYear',$cyear);
					$this->session->set_userdata('financialYear',$fyData->financial_year);
					$this->session->set_userdata('isActiveYear',$fyData->close_status);
					
					$this->session->set_userdata('shortYear',$fyData->year);
					$this->session->set_userdata('startYear',$fyData->start_year);
					$this->session->set_userdata('endYear',$fyData->end_year);
					$this->session->set_userdata('startDate',$startDate);
					$this->session->set_userdata('endDate',$endDate);
					$this->session->set_userdata('currentFormDate',date('d-m-Y'));
					if($data['fyear'] != $cyear):
						$this->session->set_userdata('currentFormDate',date('d-m-Y',strtotime($endDate)));
					endif;
					
					return ['status'=>1,'message'=>'Login Success.','field_error'=>0,'field_error_message'=>null];
				endif;
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password.",'field_error'=>0,'field_error_message'=>null];
		endif;
	}
	
	public function checkApiAuth($data){
		$result = $this->db->where('emp_contact',$data['user_name'])->where('emp_password',md5($data['password']))->where('is_delete',0)->get($this->employeeMaster);
		
		if($result->num_rows() == 1):
			$resData = $result->row();			
			if($resData->is_block == 1):
				return ['status'=>0,'message'=>'Your Account is Blocked. Please Contact Your Software Vendor.','field_error'=>0,'field_error_message'=>null];
			else:	
				if($resData->is_active == 0):
					return ['status'=>0,'message'=>'Your Account is Inactive. Please Contact Your Software admin.','field_error'=>0,'field_error_message'=>null];
				else:
					$otp = rand(100000, 999999);	
					$verificationData['otp'] = $otp;
					$notify = array();$notifyData = array();
					if(!empty($data['device_token'])):
						$verificationData['device_token'] = $data['device_token'];
						
						$notifyData['notificationTitle'] = "OTP";
						$notifyData['notificationMsg'] = "Your one time password is <#>".$otp;						
						$notifyData['payload'] = ['otp'=>$otp];
						$notifyData['pushToken'] = $data['device_token'];
						$notify = $this->notification->sendNotification($notifyData);
					endif;
					$logData = [
						'log_date' => date("Y-m-d H:i:s"),
						'notification_data' => json_encode($notifyData),
						'notification_response' => json_encode($notify),
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s")
					];
					$this->db->insert('notification_log',$logData);
					$this->db->where('id',$resData->id)->update($this->employeeMaster,$verificationData);
					return ['status'=>1,'message'=>'User Found.','field_error'=>0,'field_error_message'=>null,'data'=>['otp'=>$otp,'notificationRes'=>$notify]];
				endif;
				
			endif;
		else:
			return ['status'=>0,'message'=>"Invalid Username or Password.",'field_error'=>0,'field_error_message'=>null];
		endif;
	}

	public function verification($data){
		if(isset($data['is_verify']) && $data['is_verify'] == 1):
			$userData = $this->db->where('emp_contact',$data['user_name'])->where('is_delete',0)->get($this->employeeMaster)->row();
			$updateUser = array();
			$updateUser['otp'] = "";
			if(empty($userData->auth_token)):
				// ***** Generate Token *****
				$char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY!@#%";
				$token = '';
				for ($i = 0; $i < 47; $i++) $token .= $char[(rand() % strlen($char))];
				$updateUser['auth_token'] = $token;
			else:
				$token = $userData->auth_token;
			endif;
			$this->db->where('id',$userData->id)->update($this->employeeMaster,$updateUser);
			
			$userData->auth_token = $token;
			$headData = new stdClass();
			$fyData=$this->db->where('is_active',1)->get('financial_year')->row();
			$RTD_STORE=$this->db->where('is_delete',0)->where('cm_id',$userData->cm_id)->where('store_type',1)->get('location_master')->row();
			$PKG_STORE=$this->db->where('is_delete',0)->where('cm_id',$userData->cm_id)->where('store_type',2)->get('location_master')->row();
			$PROD_STORE=$this->db->where('is_delete',0)->where('cm_id',$userData->cm_id)->where('store_type',4)->get('location_master')->row();
			$headData->LoginOk = "login success";
			$headData->loginId = $userData->id;
			$headData->role = $userData->emp_role;
			$empRole=$userData->emp_role;
			if($userData->emp_role == -1){$empRole= 1;}
			$headData->roleName = $this->empRole[$empRole];
			$headData->CMID = $userData->cm_id;
			$headData->emp_name = $userData->emp_name;
			$headData->RTD_STORE = $RTD_STORE;
			$headData->PKG_STORE = $PKG_STORE;
			$headData->PROD_STORE = $PROD_STORE;
			
			$startDate = $fyData->start_date;
			$endDate = $fyData->end_date;
			$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
			$headData->currentYear = $cyear;
			$headData->financialYear = $fyData->financial_year;
			$headData->isActiveYear = $fyData->close_status;
			$headData->shortYear = $fyData->year;
			$headData->startYear = $fyData->start_year;
			$headData->endYear = $fyData->end_year;
			$headData->startDate = $startDate;
			$headData->endDate = $endDate;
			$headData->currentFormDate = date('d-m-Y');
			if($data['fyear'] != $cyear):
				$headData->currentFormDate = date('d-m-Y',strtotime($endDate));
			endif;	
			
			unset($userData->emp_password,$userData->emp_psc,$userData->device_token,$userData->web_token,$userData->otp,$userData->is_block,$userData->is_active);
			$result['userData'] = $userData;
			$result['headData'] = base64_encode(json_encode($headData));
			
			return ['status'=>1,'message'=>'User verified.','field_error'=>0,'field_error_message'=>null,'data'=>$result];
		else:	
			return ['status'=>0,'message'=>"Somthing is wrong. user not verified.",'field_error'=>0,'field_error_message'=>null,'data'=>null];
		endif;
	}

	public function checkToken($token){
		$result = $this->db->where('auth_token',$token)->where('is_delete',0)->get($this->employeeMaster)->num_rows();
		return ($result > 0)?1:0;
	}

	public function getEmployeePermission_api($emp_id){
		$this->db->select("menu_permission.*,menu_master.menu_name");
		$this->db->join("menu_master","menu_master.id = menu_permission.menu_id","left");
		$this->db->where("menu_master.is_delete",0);
		$this->db->where('menu_permission.emp_id',$emp_id);
		$this->db->where('menu_permission.is_delete',0);
		$this->db->order_by("menu_master.menu_seq","ASC");
		$menuData = $this->db->get($this->menuPermission)->result();
		
		$result = array();
		foreach($menuData as $row):			
			if(!empty($row->is_master)):
                if(!empty($row->is_read)):
                    if(!empty($row->is_read) || !empty($row->is_write) || !empty($row->is_modify) || !empty($row->is_remove)):
						$result[] = $row;
					endif;
                endif;
            else:
				$this->db->select("sub_menu_permission.*,sub_menu_master.sub_menu_name");
				$this->db->join("sub_menu_master","sub_menu_master.id = sub_menu_permission.sub_menu_id","left");
				$this->db->where("sub_menu_master.is_delete",0);
				$this->db->where('sub_menu_permission.emp_id',$emp_id);
				$this->db->where('sub_menu_permission.is_delete',0);
				$this->db->where('sub_menu_permission.menu_id',$row->menu_id);
				$this->db->order_by("sub_menu_master.sub_menu_seq","ASC");
				$subMenuData = $this->db->get($this->subMenuPermission)->result();
				
				$show_menu = false; $subMenu = array();
                foreach($subMenuData as $subRow):
                    if(!empty($subRow->is_read)):
                        if(!empty($subRow->is_read) || !empty($subRow->is_write) || !empty($subRow->is_modify) || !empty($subRow->is_remove)):
                            $show_menu = true; 
							$subMenu[] = $subRow;
						endif;
                    endif;
                endforeach;
				if($show_menu == true):
					$row->sub_menu = $subMenu;
					$result[] = $row;
				endif;
            endif;
        endforeach;
        return $result;
    }

	public function webLogout($id){
		$updateUser['web_token'] = "";
		$this->db->where('id',$id)->update($this->employeeMaster,$updateUser);
		return true;
	}

	public function appLogout($id){
		$updateUser['device_token'] = "";
		$updateUser['auth_token'] = "";
		$updateUser['otp'] = "";
		$this->db->where('id',$id)->update($this->employeeMaster,$updateUser);
		return ['status'=>1,'message'=>'Logout successfull.','field_error'=>0,'field_error_message'=>null];
	}
	
	public function setFinancialYear($year){
		$fyData=$this->db->where('financial_year',$year)->get('financial_year')->row();
		$startDate = $fyData->start_date;
		$endDate = $fyData->end_date;
		$cyear  = date("Y-m-d H:i:s",strtotime("01-04-".date("Y")." 00:00:00")).' AND '.date("Y-m-d H:i:s",strtotime("31-03-".((int)date("Y") + 1)." 23:59:59"));
		$this->session->set_userdata('currentYear',$cyear);
		$this->session->set_userdata('financialYear',$fyData->financial_year);
		$this->session->set_userdata('isActiveYear',$fyData->close_status);
		
		$this->session->set_userdata('shortYear',$fyData->year);
		$this->session->set_userdata('startYear',$fyData->start_year);
		$this->session->set_userdata('endYear',$fyData->end_year);
		$this->session->set_userdata('startDate',$startDate);
		$this->session->set_userdata('endDate',$endDate);
		$this->session->set_userdata('currentFormDate',date('d-m-Y'));
		return true;
	}
}
?>