<?php
class Dashboard extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }
	
	public function index($off_set=0){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search];
        
        $totalSales = (!empty($totalSales->net_amount))?$totalSales->net_amount:0;
        $totalIncentive = (!empty($totalSales->incentive))?$totalSales->incentive:0;
        $todaySales = (!empty($todaySales->net_amount))?$todaySales->net_amount:0;
        $todayIncentive = (!empty($todaySales->incentive))?$todaySales->incentive:0;

        $salesStatusData = array();
        $salesStatusData = [
            'totalSales' => (!empty($totalSales->net_amount))?$totalSales->net_amount:0,
            'totalIncentive' => (!empty($totalSales->incentive))?$totalSales->incentive:0,
            'todaySales' => (!empty($todaySales->net_amount))?$todaySales->net_amount:0,
            'todayIncentive' => (!empty($todaySales->incentive))?$todaySales->incentive:0
        ];
        $this->data['sales_status'] = $salesStatusData;
        $this->data['trans_list'] = $this->salesInvoice->getSalesInvoiceList_api($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }
    
}
?>