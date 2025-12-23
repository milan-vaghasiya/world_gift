<?php
class LeaveSettingModel extends MasterModel{
    private $leaveType = "leave_type";
    private $empDesignation = "emp_designation";
    private $empMaster = "employee_master";
	
	public function getDTRows($data){
        $data['tableName'] = $this->leaveType;
        $data['searchCol'][] = "leave_type";
        $data['searchCol'][] = "remark";

        $columns =array('','','leave_type','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
        return $this->pagingRows($data);
    }

    public function getLeaveType($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->leaveType;
        return $this->row($data);
    }
	
	public function getEmpDesignations()
	{
		$data['tableName'] = $this->empDesignation;
		return $this->rows($data);
	}
	
    public function save($data){
        try{
            $this->db->trans_begin();
        if($this->checkDuplicate($data['leave_type'],$data['id']) > 0):
            $errorMessage['leave_type'] = "Leave Type is duplicate.";
            $result = ['status' => 0, 'message' => "Some fields are duplicate.",'field_error'=>1,'field_error_message'=>$errorMessage];
        else:
			
            $result = $this->store($this->leaveType,$data,'Leave Type');
        endif;
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
        $data['tableName'] = $this->leaveType;
        $data['where']['leave_type'] = $leave_type;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->leaveType,['id'=>$id],'Leave Type');
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
        $data['tableName'] = $this->leaveType;
        return $this->numRows($data);
    }

    public function getLeaveSettingList_api($limit, $start){
        $data['tableName'] = $this->leaveType;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>