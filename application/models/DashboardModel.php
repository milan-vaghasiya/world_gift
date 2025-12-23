<?php
class DashboardModel extends MasterModel{
    private $transMain = "trans_main";

    public function getTodaySales(){
        $queryData = array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*,employee_master.emp_name,SUM(trans_main.net_amount) as totalAmt';
        $queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		$queryData['where_in']['trans_main.entry_type'] = [6,7,8,10,11];
		$queryData['where']['trans_main.trans_date'] = date('Y-m-d');
        $queryData['order_by']['totalAmt'] = 'DESC';
        $queryData['group_by'][] = 'trans_main.created_by';
        return $this->rows($queryData);
    }
    
    public function getTotalPayRecive($entry_type=""){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'SUM(trans_main.net_amount * inrrate) as totalAmt';
		$queryData['where_in']['entry_type'] = $entry_type;
		$queryData['where']['trans_main.trans_date'] = date('Y-m-d');
		$transMainData = $this->row($queryData); 
        return ['totalAmt'=>$transMainData->totalAmt];
    }
}
?>