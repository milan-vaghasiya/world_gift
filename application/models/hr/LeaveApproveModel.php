<?php
class LeaveApproveModel extends MasterModel{
    private $leaveMaster = "leave_master";
	private $leaveType = "leave_type";
    private $empDesignation = "emp_designation";
    private $empMaster = "employee_master";
	private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager"];
	
	public function getDTRows($data){
		
		$emp1 = $this->leaveApprove->getEmpData($data['login_emp_id']);
		
		$data['tableName'] = $this->leaveMaster;
		
		$data['select'] = "leave_master.*,employee_master.emp_name,employee_master.emp_designation,employee_master.emp_profile, emp_designation.title, department_master.leave_authorities";
        $data['join']['employee_master'] = "employee_master.id = leave_master.emp_id";
        $data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['join']['department_master'] = "department_master.id = employee_master.emp_dept_id";
        $data['where']['employee_master.id!='] = $emp1->id;
        $data['customWhere'][] = 'FIND_IN_SET('.$emp1->id.',department_master.leave_authorities)<> 0';
		
		$data['searchCol'][] = "emp_name";
        $data['searchCol'][] = "leave_type";
        $data['searchCol'][] = "start_date";
        $data['searchCol'][] = "end_date";
        $data['searchCol'][] = "total_days";
        $data['searchCol'][] = "leave_reason";
		
		$columns =array('','','emp_name','leave_type','start_date','end_date','total_days','leave_reason','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
				
        return $this->pagingRows($data);
    }

	public function save($data){
		try{
            $this->db->trans_begin();
        $result = $this->store($this->leaveMaster,$data,'Leave');
		if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function getLeaveType(){
        $data['tableName'] = $this->leaveType;
        $leaveType = $this->rows($data);
		return $leaveType;
    }
	
    public function checkAuthority($id){
		$data['select'] = "employee_master.*, department_master.leave_authorities";
        $data['join']['department_master'] = "department_master.id = employee_master.emp_dept_id";
        $data['customWhere'][] = 'FIND_IN_SET('.$id.',department_master.leave_authorities) <> 0';
        $data['where']['employee_master.id'] = $id;
		$data['resultType']='numRows';
        $data['tableName'] = $this->empMaster;
        return $this->specificRow($data);
    }
	
    public function getEmpData($id){
		$data['where']['id'] = $id;
        $data['tableName'] = $this->empMaster;
        return $this->row($data);
    }
	
    public function getLeave($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->leaveMaster;
        return $this->row($data);
    }
	
    public function getEmpLeaves($emp_id,$leave_type_id,$start_date,$end_date){
		
		$emp_leaves = Array();
		
        $empData = $this->getEmpData($emp_id);
		if(!empty($leave_type_id)){$data['where']['id'] = $leave_type_id;}
		$data['tableName'] = $this->leaveType;
		$leaveType = $this->rows($data);
		if(!empty($leaveType))
		{
			foreach($leaveType as $row)
			{
				$lq=array();$max_leave=0;$leave_period=1;
				$data1['select'] = "SUM(total_days) as total_days";
				$data1['where']['emp_id'] = $emp_id;
				$data1['where']['approve_status'] = 1;
				$data1['where']['start_date>='] = $start_date;
				$data1['where']['end_date<='] = $end_date;
				$data1['where']['leave_type_id'] = $row->id;
				$data1['tableName'] = $this->leaveMaster;
				$used_leaves = $this->specificRow($data1)->total_days;
				if(empty($used_leaves)){$used_leaves=0;}
				if(!empty($row->leave_quota))
				{
					$leave_quota = (array)json_decode($row->leave_quota);									
					foreach($leave_quota as $key=>$value){if($key == $row->id){$max_leave = $value->leave_days;$leave_period = $value->m_or_y;}}
				}
				$lq['emp_id'] = $emp_id;
				$lq['leave_type_id'] = $row->id;
				$lq['leave_type'] = $row->leave_type;
				$lq['emp_designation_id'] = $empData->emp_designation;
				$lq['designation'] = $this->db->where('id',$empData->emp_designation)->get($this->empDesignation)->row()->title;
				$lq['max_leave'] = $max_leave;
				$lq['leave_period'] = $leave_period;
				$lq['used_leaves'] = $used_leaves;
				$lq['remain_leaves'] = $max_leave - $used_leaves;
				$emp_leaves[] = $lq;
			}
		}
		return $emp_leaves;
    }

/*  Create By : Avruti @26-11-2021 5:00 PM
    update by : 
    note : 
*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->leaveMaster;
        return $this->numRows($data);
    }

    public function getLeaveApproveList_api($limit, $start){

		//$emp1 = $this->leaveApprove->getEmpData($data['login_emp_id']);
		
		$data['tableName'] = $this->leaveMaster;
		
		$data['select'] = "leave_master.*,employee_master.emp_name,employee_master.emp_designation,employee_master.emp_profile, emp_designation.title, department_master.leave_authorities";
        $data['join']['employee_master'] = "employee_master.id = leave_master.emp_id";
        $data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['join']['department_master'] = "department_master.id = employee_master.emp_dept_id";
        //$data['where']['employee_master.id!='] = $emp1->id;
       // $data['customWhere'][] = 'FIND_IN_SET('.$emp1->id.',department_master.leave_authorities)<> 0';
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>