<?php
class DepartmentModel extends MasterModel{
    private $departmentMaster = "department_master";
    private $empMaster = "employee_master";
    
	public function getDTRows($data){
        $data['tableName'] = $this->departmentMaster;
        $data['searchCol'][] = "name";
		$columns =array('','','name','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }
    
	public function getDepartmentList(){
        $data['tableName'] = $this->departmentMaster;
        return $this->rows($data);
    }

    public function getDepartment($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->departmentMaster;
        return $this->row($data);
    }

    public function getEmployees($id=0){
		if(!empty($id))
		{
			$data['where']['id'] = $id;
			$data['tableName'] = $this->empMaster;
			return $this->row($data);
		}
		else
		{
			$data['order_by']['emp_name']='ASC';
			$data['tableName'] = $this->empMaster;
			return $this->rows($data);
		}
    }
	
    public function getLeaveAuthorities($emp_ids){
        $data['select'] = 'emp_name';
        $data['where_in']['id'] = $emp_ids;
        $data['tableName'] = $this->empMaster;
		$data['resultType']='resultRows';
        return $this->specificRow($data);
    }
	
    public function getLeaveAuthority($emp_id){
        $data['select'] = 'emp_name';
        $data['where']['id'] = $emp_id;
        $data['tableName'] = $this->empMaster;
        return $this->specificRow($data);
    }
	
    public function save($data){
        try{
            $this->db->trans_begin();
        if($this->checkDuplicate($data['name'],$data['id']) > 0):
            $errorMessage['name'] = "Department name is duplicate.";
            $result = ['status' => 0, 'message' => "Some fields are duplicate.",'field_error'=>1,'field_error_message'=>$errorMessage];
        else:
            $result = $this->store($this->departmentMaster,$data,'Department');
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

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->departmentMaster;
        $data['where']['name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $result = $this->trash($this->departmentMaster,['id'=>$id],'Department');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
        return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function getMachiningDepartment($category){
        $data['where']['category'] = $category;
        $data['tableName'] = $this->departmentMaster;
        return $this->rows($data);
    }
	
	/*  Create By : Avruti @26-11-2021 5:00 PM
		update by : 
		note : 
	*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->departmentMaster;
        return $this->numRows($data);
    }

    public function getDepartmentList_api($limit, $start){
        $data['tableName'] = $this->departmentMaster;
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>