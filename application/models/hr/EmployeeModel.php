<?php
class EmployeeModel extends MasterModel{
    private $empMaster = "employee_master";
    private $designation = "emp_designation";
    private $empSalary = "emp_salary_detail";
    private $empDocs = "emp_docs_detail";
    private $empNom = "emp_nomination_detail";
    private $empEdu = "emp_education_detail";
    private $salesTarget = "sales_targets";

    public function getDTRows($data){

        $data['tableName'] = $this->empMaster;
        // $data['searchCol'][] = "";
        // $data['searchCol'][] = "";
        $data['searchCol'][] = "employee_master.emp_name";
        $data['searchCol'][] = "employee_master.emp_contact";
        // $data['searchCol'][] = "";

        // $data['searchCol'][] = "department_master.name";
        // $data['searchCol'][] = "employee_master.emp_code";

        // $data['select'] = "employee_master.*,department_master.name";
        // $data['join']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        // $data['where']['employee_master.emp_role !='] = "-1";
		
		$columns =array('','','employee_master.emp_name','employee_master.emp_contact');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        
        return $this->pagingRows($data);
    }

    public function getEmpList(){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.id,employee_master.emp_code,employee_master.emp_name,department_master.name";
        $data['leftJoin']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['where']['employee_master.emp_role !='] = "-1";
		return $this->rows($data);
    }

    public function getEmp($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->empMaster;
        return $this->row($data);
    }

    public function getsalesExecutives(){
        $data['where']['emp_sys_desc_id'] = 5;
        $data['tableName'] = $this->empMaster;
        return $this->rows($data);
    }

    public function getEmployeeList(){
        $data['tableName'] = $this->empMaster;
        return $this->rows($data);
    }

    public function getSetterList(){
        $data['tableName'] = $this->empMaster;
        $data['where']['emp_sys_desc_id'] = 4;
        return $this->rows($data);
    }

    public function getSetterInspectorList(){
        $data['tableName'] = $this->empMaster;
        $data['where']['emp_sys_desc_id'] = 3;
        return $this->rows($data);
    }

    public function getLineInspectorList(){
        $data['tableName'] = $this->empMaster;
        $data['where']['emp_sys_desc_id'] = 2;
        return $this->rows($data);
    }

    public function getMachineOperatorList(){
        $data['tableName'] = $this->empMaster;
        $data['where']['emp_sys_desc_id'] = 1;
        return $this->rows($data);
    }
    
    public function getSupervisorList(){
        $data['tableName'] = $this->empMaster;
        $data['where']['emp_designation'] = 12;
        return $this->rows($data);
    }

    public function getEmpSalary($emp_id){
        $data['where']['emp_id'] = $emp_id;
        $data['tableName'] = $this->empSalary;
        return $this->row($data);
    }

    public function getEmpDocs($emp_id){
        $data['where']['emp_id'] = $emp_id;
        $data['tableName'] = $this->empDocs;
        return $this->row($data);
    }

    public function save($data){  
        try{
            $this->db->trans_begin();
            if($this->checkDuplicate($data['emp_contact'],$data['id']) > 0):
                $errorMessage['emp_contact'] = "This Number is already exist.";
                return ['status' => 0, 'message' => "Some fields are duplicate.",'field_error'=>1,'field_error_message'=>$errorMessage];
            else:
                if(empty($data['id'])):
                    $data['emp_psc'] = $data['emp_password'];
                    $data['emp_password'] = md5($data['emp_password']); 
                endif;
                $empData =  $this->store($this->empMaster,$data,'Employee');
                if($empData['insert_id'] > 0):
                    $this->store('emp_salary_detail',['id'=>'','emp_id'=>$empData['insert_id']],'Employee Salary');
                endif;
                $result = $empData;
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

    public function checkDuplicate($emp_contact,$id=""){
        if(!empty($emp_contact)):
            $data['tableName'] = $this->empMaster;
            $data['where']['emp_contact'] = $emp_contact;
            
            if(!empty($id))
                $data['where']['id !='] = $id;
            return $this->numRows($data);
        else:
            return 0;
        endif;
    }

    public function saveEmpSalary($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->empSalary,$data,'Employee');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function saveEmpDocs($data){
        try{
            $this->db->trans_begin();
        $result = $this->store($this->empDocs,$data,'Employee');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function getNominationData($id){
		$data['where']['emp_id'] = $id;
		$data['tableName'] = $this->empNom;
		return $this->rows($data);
	}

    public function saveEmpNom($data){
        try{
            $this->db->trans_begin();
        $nomData = $this->getNominationData($data['emp_id']);

        foreach($data['nom_name'] as $key=>$value):
			$empNomData = [
                            'id'=>$data['trans_id'][$key],
                            'emp_id'=>$data['emp_id'],
                            'nom_name'=>$value,
                            'nom_gender'=>$data['nom_gender'][$key],
                            'nom_relation'=>$data['nom_relation'][$key],
                            'nom_dob'=>$data['nom_dob'][$key],
                            'nom_proportion'=>$data['nom_proportion'][$key],
                            'created_by'=>$data['created_by']
                        ];
            $this->store($this->empNom,$empNomData);
		endforeach;
        if(!empty($nomData)):
			foreach($nomData as $key=>$value):
				if(!in_array($value->id,$data['trans_id'])){
					$this->trash($this->empNom,['id'=>$value->id],'');
				}
			endforeach;
		endif;
		$result = ['status'=>1,'message'=>'Employee Nomination Details saved successfully.','field_error'=>0,'field_error_message'=>null];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function getEducationData($id){
		$data['where']['emp_id'] = $id;
		$data['tableName'] = $this->empEdu;
		return $this->rows($data);
	}

    public function saveEmpEdu($data){
        try{
            $this->db->trans_begin();
        $eduData = $this->getEducationData($data['emp_id']);

        foreach($data['course'] as $key=>$value):
			$empEduData = [
                            'id'=>$data['trans_id'][$key],
                            'emp_id'=>$data['emp_id'],
                            'course'=>$value,
                            'university'=>$data['university'][$key],
                            'passing_year'=>$data['passing_year'][$key],
                            'grade'=>$data['grade'][$key],
                            'created_by'=>$data['created_by']
                        ];
            $this->store($this->empEdu,$empEduData);
		endforeach;
        if(!empty($eduData)):
			foreach($eduData as $key=>$value):
				if(!in_array($value->id,$data['trans_id'])){
					$this->trash($this->empEdu,['id'=>$value->id],'');
				}
			endforeach;
		endif;
		$result = ['status'=>1,'message'=>'Employee Education Details saved successfully.','field_error'=>0,'field_error_message'=>null];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function delete($id){
        try{
            $this->db->trans_begin();
        $this->trash($this->empSalary,['emp_id'=>$id],'Employee');
        $this->trash($this->empDocs,['emp_id'=>$id],'Employee');
        $this->trash($this->empNom,['emp_id'=>$id],'Employee');
        $this->trash($this->empEdu,['emp_id'=>$id],'Employee');
        $result = $this->trash($this->empMaster,['id'=>$id],'User');
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function activeInactive($id,$value){
        $this->edit($this->empMaster,['id'=>$id],['is_active'=>$value],'');
        $msg = ($value == 1)?"actived":"in-active";
        return ['status'=>1,'message'=> "Employee ".$msg." successfully.",'field_error'=>0,'field_error_message'=>null];
    }

    public function changePassword($data){
        try{
            $this->db->trans_begin();
            if(empty($data['id'])):
                return ['status'=>0,'message'=>'User ID not found.','field_error'=>0,'field_error_message'=>null];
            endif;
            $empData = $this->getEmployee($data['id']);
            if(md5($data['old_password']) != $empData->emp_password):
                return ['status'=>0,'message'=>"Old password not match.",'field_error'=>0,'field_error_message'=>null];
            endif;

            $postData = ['id'=>$data['id'],'emp_password'=>md5($data['new_password']),'emp_psc'=>$data['new_password']];
            $result = $this->store($this->empMaster,$postData,'Password');

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                return $result;
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            return ['status'=>0,'message'=>"somthing is wrong. Error : ".$e->getMessage(),'field_error'=>0,'field_error_message'=>null];
        }
    }

    public function getDesignation()
    {
        $data['tableName'] = $this->designation;
        return  $this->rows($data);
    }

    public function saveDesignation($designation,$emp_dept_id){
        try{
            $this->db->trans_begin();
        $created_by = $this->session->userdata('loginId');
        $queryData = ['id'=>'','title'=>$designation,'dept_id'=>$emp_dept_id,'created_by'=>$created_by];
        $designationData = $this->store("emp_designation",$queryData,'Employee');
        $result = $designationData['insert_id'];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    }	
    }

    public function getEmployee($emp_id){
        $data['where']['employee_master.id'] = $emp_id;
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,department_master.name,emp_designation.title";
        $data['join']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['join']['emp_designation'] = "department_master.id = emp_designation.dept_id";
        return $this->row($data);
    }

    public function getEmpReport(){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,emp_designation.title";
        $data['leftJoin']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.emp_role !='] = "-1";
        return $this->rows($data);
    }

    public function getEmpEdu($id){
        $data['tableName'] = $this->empEdu;
        $data['where']['emp_id'] =  $id;
        return $this->rows($data);
    }
    
    //Created By meghavi 02-12-21
    public function getTargetRows($postData){
        $data['tableName'] = 'party_master';
        $data['select'] = "party_master.id,party_master.party_name,party_master.contact_person,party_master.party_mobile";
        //$data['where']['party_master.sales_executive'] = $postData['sales_executive'];
        $data['where']['party_master.party_category'] = 1;
        $data['where']['party_master.party_type'] = 1;
		$partyData = $this->rows($data);
		
		$targetData = array();
		
		if(!empty($partyData)):
			foreach($partyData as $row):
				$row->business_target = $row->recovery_target = 0;$row->st_id="";
				
				$qData['tableName'] = 'sales_targets';
				$qData['select'] = "sales_targets.*";
				$qData['where']['sales_targets.sales_executive'] = $postData['sales_executive'];
				$qData['where']['sales_targets.party_id'] = $row->id;
				$qData['where']['sales_targets.month'] = $postData['month'];
				$stData = $this->row($qData);
				if(!empty($stData))
				{
					$row->business_target=$stData->business_target;
					$row->recovery_target=$stData->recovery_target;
					$row->st_id = $stData->id;
				}
				$targetData[] = $row;
			endforeach;
            
		endif;
		//print_r($targetData);exit;
		return $targetData;
	}

	public function saveTargets($postData){
		
		foreach($postData['st_id'] as $key=>$value):
			$salesTargetData = [
								'id'=>$value,
								'sales_executive' => $postData['sales_executive'],
								'party_id' => $postData['party_id'][$key],
								'month' => $postData['month'],
								'business_target' => $postData['business_target'][$key],
								'recovery_target' => $postData['recovery_target'][$key],
								'created_by' => $this->loginID,
								];
			$saveData = $this->store($this->salesTarget,$salesTargetData);
		endforeach;
		return ['status'=>0,'message'=>'Sales Target updated successfully.','field_error'=>0,'field_error_message'=>null];
	}
	
	//Created By Karmi @13/01/2022
    public function changeEmpPsw($id){
        $data['id'] = $id;
        $data['emp_psc'] = '123456';
        $data['emp_password'] = md5($data['emp_psc']); 
        $this->store($this->empMaster,['id'=>$data['id'], 'emp_password'=>  $data['emp_password'], 'emp_psc'=> $data['emp_psc']]);
        return ['status'=>1,'message'=>'Password Reset successfully.','field_error'=>0,'field_error_message'=>null];
    
	}
	
	/*  Create By : Avruti @26-11-2021 5:00 PM
		update by : 
		note : 
	*/

     //---------------- API Code Start ------//

     public function getCount(){
        $data['tableName'] = $this->empMaster;
        return $this->numRows($data);
    }

    public function getEmployeesList_api($limit, $start){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,department_master.name";
        $data['join']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['where']['employee_master.emp_role !='] = "-1";
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
?>