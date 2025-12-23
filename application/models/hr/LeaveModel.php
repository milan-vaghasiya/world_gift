<?php
class LeaveModel extends MasterModel{
    private $leaveMaster = "leave_master";
	private $leaveType = "leave_type";
    private $empDesignation = "emp_designation";
    private $empMaster = "employee_master";
	private $empRole = ["1"=>"Admin","2"=>"Production Manager","3"=>"Accountant","4"=>"Sales Manager","5"=>"Purchase Manager"];
	
	public function getDTRows($data){
		
        $data['tableName'] = $this->leaveMaster;
		$data['select'] = "leave_master.*,employee_master.emp_name,employee_master.emp_designation,employee_master.emp_profile, emp_designation.title";
        $data['join']['employee_master'] = "employee_master.id = leave_master.emp_id";
        $data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        $data['where']['leave_master.emp_id'] = $data['login_emp_id'];
		
		$data['searchCol'][] = "emp_name";
        $data['searchCol'][] = "title";
        $data['searchCol'][] = "leave_type";
        $data['searchCol'][] = "leave_reason";
        $data['searchCol'][] = "start_date";
        $data['searchCol'][] = "end_date";
        $data['searchCol'][] = "total_days";
		
        $columns =array('','','emp_name','leave_type','start_date','end_date','total_days','leave_reason','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
				
        return $this->pagingRows($data);
    }

    public function getLeaveType(){
        $data['tableName'] = $this->leaveType;
        $leaveType = $this->rows($data);
		return $leaveType;
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

    public function checkDuplicate($leave_type,$id=""){
        $data['tableName'] = $this->leaveMaster;
        $data['where']['leave_type'] = $leave_type;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->leaveMaster,['id'=>$id],'Leave');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
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

    public function getLeaveList_api($limit, $start){
        $data['tableName'] = $this->leaveMaster;
        $data['select'] = "leave_master.*,employee_master.emp_name,employee_master.emp_designation,employee_master.emp_profile, emp_designation.title";
        $data['join']['employee_master'] = "employee_master.id = leave_master.emp_id";
        $data['join']['emp_designation'] = "emp_designation.id = employee_master.emp_designation";
        //$data['where']['leave_master.emp_id'] = $data['login_emp_id'];
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>