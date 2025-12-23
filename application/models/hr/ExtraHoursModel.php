<?php
class ExtraHoursModel extends MasterModel{
    private $empAttendance = "emp_attendance";

    public function getDTRows($data){
        $data['tableName'] = $this->empAttendance;
        $data['where']['source'] =3;
        $data['select'] = "emp_attendance.*,employee_master.emp_name,employee_master.emp_code";
        $data['join']['employee_master'] = "employee_master.id = emp_attendance.emp_id";
        $data['searchCol'][] = "emp_name";
        $data['searchCol'][] = "emp_code";
        $data['searchCol'][] = "punch_in";
        $data['searchCol'][] = "remark";
		$columns =array('','','emp_code','emp_name','attendance_date','punch_in','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		return $this->pagingRows($data);
    }

    public function getExtraHours($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->empAttendance;
        return $this->row($data);
    }

    public function save($data){
        return $this->store($this->empAttendance,$data,'Extra Hours');
    }

    public function delete($id){
        return $this->trash($this->empAttendance,['id'=>$id],'Extra Hours');
    }
}
?>