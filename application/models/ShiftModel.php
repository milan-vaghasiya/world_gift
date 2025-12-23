<?php
class ShiftModel extends MasterModel{
    private $shiftMaster = "shift_master";
    private $empMaster = "employee_master";
    
    public function getDTRows($data){
        $data['tableName'] = $this->shiftMaster;
        $data['searchCol'][] = "shift_name";
        $data['searchCol'][] = "start_time";
        $data['searchCol'][] = "end_time";
        $data['searchCol'][] = "production_hour";
        $data['searchCol'][] = "lunch_hour";
        $data['serachCol'][] = "shift_hour";
		$columns =array('','','shift_name','start_time','end_time','production_hour','lunch_hour','shift_hour');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getShift($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->shiftMaster;
        return $this->row($data);
    }

    public function getShiftList(){
        $data['tableName'] = $this->shiftMaster;
        return $this->rows($data);
    }

    public function save($data){
        $data['shift_name'] = trim($data['shift_name']);
        if($this->checkDuplicate($data['shift_name'],$data['id']) > 0):
            $errorMessage['shift_name'] = "Shift Name is duplicate.";
            return ['status' => 0, 'message' => "Some fields are duplicate.",'field_error'=>1,'field_error_message'=>$errorMessage];
        else:
            return $this->store($this->shiftMaster,$data,'Shift');
        endif;
    }

    public function checkDuplicate($shiftname,$id=""){
        $data['tableName'] = $this->shiftMaster;
        $data['where']['shift_name'] = $shiftname;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->shiftMaster,['id'=>$id],'Shift');
    }


    public function getEmpList($shift_id="",$dept_id="",$category_id=""){
        $data['tableName'] = $this->empMaster;
        $data['select'] = "employee_master.*,department_master.name,emp_designation.title";
        $data['join']['department_master'] = "employee_master.emp_dept_id = department_master.id";
        $data['join']['emp_designation'] = "employee_master.emp_designation = emp_designation.id";
        $data['where']['employee_master.emp_name!='] = "Admin";
        if(!empty($shift_id)){$data['where_in']['employee_master.shift_id'] = $shift_id;}
        if(!empty($dept_id)){$data['where']['employee_master.emp_dept_id'] = $dept_id;}
        if(!empty($category_id)){$data['where']['employee_master.emp_category'] = $category_id;}

        return $this->rows($data);
    }

    public function saveAssignShift1($data){
        $empData = $this->getEmpList($data['shiftId'],$data['dept_id'],$data['category_id']);
        $newEmpId = Array();
        if(!empty($data['shift_id'])):
            foreach($data['shift_id'] as $emp_id):
                $newEmpId[] = $emp_id;
                $this->store($this->empMaster,['id'=>$emp_id,'shift_id'=>$data['shiftId']],'Assign Shift');
            endforeach;
        endif;
        foreach($empData as $row):
            if(!in_array($row->id, $newEmpId)):
                $this->store($this->empMaster,['id'=>$row->id,'shift_id'=>0],'Assign Shift');
            endif;
        endforeach;
        return ['status'=>1,'message'=>'Shift Assigned Successfully.','shift_id'=>$data['shiftId'],'field_error'=>0,'field_error_message'=>null];
    }
    
	public function saveAssignShift($data){
        $empData = $this->getEmpList($data['shiftId'],$data['dept_id'],$data['category_id']);
        $newEmpId = Array();
        if(!empty($data['shift_id'])):
            foreach($data['shift_id'] as $emp_id):
                $newEmpId[] = $emp_id;
                $this->store($this->empMaster,['id'=>$emp_id,'shift_id'=>$data['shiftId']],'Assign Shift');
            endforeach;
        endif;
        foreach($empData as $row):
            if(!in_array($row->id, $newEmpId)):
                $removeArr[] = ['id'=>$row->id,'shift_id'=>0];
                $this->store($this->empMaster,['id'=>$row->id,'shift_id'=>0],'Assign Shift');
            endif;
        endforeach;
		$this->updateEmpShift();
        return ['status'=>1,'message'=>'Shift Assigned Successfully.','shift_id'=>$data['shiftId'],'field_error'=>0,'field_error_message'=>null];
    }
	  
	public function updateEmpShift(){
		$empQ['tableName'] = $this->empMaster;
        $empQ['select'] = "employee_master.id,employee_master.emp_code,employee_master.shift_id,shift_master.shift_start, shift_master.shift_end,shift_master.shift_name,shift_master.total_shift_time";
        $empQ['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";
		$empData = $this->rows($empQ);
		
		$attendaceLog = Array();$empList = Array();
        if(!empty($empData)):
			$currentDate =  date("Y-m-d");
			$dd1Query['tableName'] = 'attendance_shiftlog';
			$dd1Query['where']['attendance_date'] = $currentDate;
			$oldData = $this->row($dd1Query);
			
			foreach($empData as $row):
				$shiftLog = Array();
				$shiftLog['emp_id']=$row->id;$shiftLog['emp_code']=$row->emp_code;$shiftLog['shift_id']=$row->shift_id;
				$shiftLog['shift_start']=$row->shift_start;$shiftLog['shift_end']=$row->shift_end;
				$shiftLog['shift_name']=$row->shift_name;$shiftLog['total_shift_time']=$row->total_shift_time;
				$empList[] = $shiftLog;
			endforeach;
			
			// Add Previous Day Data if not found
			$prevDate = date('Y-m-d', strtotime($currentDate.' -1 day'));
			$prevDay = date("D",strtotime($prevDate));
			if($prevDay == 'Wed')
			{
    			$dd1Query1['tableName'] = 'attendance_shiftlog';
    			$dd1Query1['where']['attendance_date'] = $prevDate;
    			$prevData = $this->row($dd1Query1);
    			
    			if(empty($prevData)):
    				$attendaceLog = ['id'=>"",'attendance_date'=>$prevDate, 'punchdata'=>json_encode($empList),'created_by'=>$this->loginID];
    			else:
				    $attendaceLog = ['id'=>$prevData->id,'attendance_date'=>$prevDate, 'punchdata'=>json_encode($empList)];
    			endif;
    			$this->store('attendance_shiftlog',$attendaceLog,'Attendance Log');
			}
			$attendaceLog = Array();
			// Add Current Day Data
			if(empty($oldData)):
				$attendaceLog = ['id'=>"",'attendance_date'=>$currentDate, 'punchdata'=>json_encode($empList),'created_by'=>$this->loginID];
			else:
				$attendaceLog = ['id'=>$oldData->id,'attendance_date'=>$currentDate, 'punchdata'=>json_encode($empList)];
			endif;
			$this->store('attendance_shiftlog',$attendaceLog,'Attendance Log');
        endif;
        
        return true;
    }
	
	public function getAttendanceLog1($attendance_date,$emp_id){
		$alQuery['tableName'] = 'attendance_shiftlog';
		$alQuery['where']['attendance_date'] = $attendance_date;
		$alData = $this->row($alQuery);
		
		$punchData = Array();$empList = Array();
        if(!empty($alData)):
			$punchData = json_decode($alData->punchdata);
			$empPucnhes = array_keys(array_combine(array_keys($punchData), array_column($punchData, 'emp_id')),$emp_id);
			return $punchData[$empPucnhes[0]];
        endif;
        
        return Array();
    }
    public function getAttendanceLog($attendance_date,$emp_id){
		$alQuery['tableName'] = 'attendance_shiftlog';
		$alQuery['where']['attendance_date'] = $attendance_date;
		$alData = $this->row($alQuery);
		
		$punchData = Array();$shiftData = Array();
        if(!empty($alData)):
			$punchData = json_decode($alData->punchdata);
			$empPucnhes = array_keys(array_combine(array_keys($punchData), array_column($punchData, 'emp_id')),$emp_id);
			
			if(empty($empPucnhes))
			{
				$empQ['tableName'] = $this->empMaster;
			    $empQ['select'] = "employee_master.id,employee_master.emp_code,employee_master.shift_id,shift_master.shift_start, shift_master.shift_end,shift_master.shift_name,shift_master.total_shift_time";
				$empQ['leftJoin']['shift_master'] = "employee_master.shift_id = shift_master.id";
	        	$empQ['where']['employee_master.id'] = $emp_id;
				$empData = $this->row($empQ);
				$shiftLog = Array();
				$shiftLog['emp_id']=$empData->id;$shiftLog['emp_code']=$empData->emp_code;$shiftLog['shift_id']=$empData->shift_id;
				$shiftLog['shift_start'] = (empty($empData->shift_start)) ? date('H:i:s',strtotime('00:00:00')) : $empData->shift_start;
				$shiftLog['shift_end'] = (empty($empData->shift_end)) ? date('H:i:s',strtotime('00:00:00')) : $empData->shift_end;
				$shiftLog['shift_name'] = (empty($empData->shift_name)) ? '-' : $empData->shift_name;
				$shiftLog['total_shift_time'] = (empty($empData->total_shift_time)) ? '00:00' : $empData->total_shift_time;
				$shiftData = (object)$shiftLog;
			}
			else{$shiftData = $punchData[$empPucnhes[0]];}
			
        endif;
        return $shiftData;
    }
	
}
?>