<?php 
class SalesReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    /* Customer's Order Monitoring */
    public function getOrderMonitor($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_prefix,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date,party_master.party_code,employee_master.emp_name';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_child.created_by";
		$queryData['where']['trans_main.entry_type'] = 4;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }

    public function getInvoiceData($data){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id,trans_main.trans_date,trans_main.trans_no,trans_main.trans_prefix,trans_main.delivery_date';
        $data['where']['trans_main.ref_id'] = $data['trans_main_id'];
        $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";

        return $this->rows($data);
    }

    public function getDeliveredQty($item_id,$trans_main_id)
    {
        $data['tableName'] = $this->transChild;
        $data['select'] = 'SUM(trans_child.qty) as dqty';
        $data['where']['trans_child.item_id'] = $item_id;
        $data['where']['trans_child.trans_main_id'] = $trans_main_id;
        return $this->row($data);
    }
    
    public function getDispatchPlan($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.id as so_id,trans_main.trans_no,trans_main.doc_no,trans_main.trans_date, trans_main.remark, trans_main.ref_by,trans_main.order_type,trans_main.sales_type,trans_main.delivery_date, party_master.party_code, party_master.currency, item_master.packing_qty as packingQty';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
		$queryData['where']['trans_main.entry_type'] = 4;
        $queryData['where']['trans_child.trans_status'] = 0;
        //$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['customWhere'][] = "trans_child.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_child.cod_date'] = 'ASC';

		return $this->rows($queryData);
    }
    
	public function getPackingPlan_old($data){
        $queryData = array();
		$queryData['tableName'] = 'packing_master';
        $queryData['select'] = 'packing_master.id,packing_master.item_id,SUM(packing_master.packing_qty) as packing_qty,packing_master.packing_date,party_master.party_code, party_master.currency,item_master.qty as totalStock,item_master.item_code,item_master.price as item_price';
		$queryData['join']['item_master'] = "item_master.id = packing_master.item_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		// $queryData['where']['packing_master.item_id'] = 505;
        $queryData['customWhere'][] = "packing_master.packing_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['packing_master.packing_date'] = 'ASC';
		$queryData['group_by'][] = 'packing_master.packing_date';
		$queryData['group_by'][] = 'packing_master.item_id';

		return $this->rows($queryData);
    }
    
	public function getPackingPlan($data){
        $queryData = array();
		$queryData['tableName'] = 'packing_master';
        $queryData['select'] = 'packing_master.id,packing_master.item_id,SUM(packing_master.packing_qty) as packing_qty,packing_master.packing_date,party_master.party_code, party_master.currency,item_master.qty as totalStock,item_master.item_code,(item_master.price*currency.inrrate) as item_price';
		$queryData['join']['item_master'] = "item_master.id = packing_master.item_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = item_master.party_id";
		$queryData['leftJoin']['currency'] = "currency.currency = party_master.currency";
	//	$queryData['where']['packing_master.item_id'] = 713;
        //$queryData['customWhere'][] = "packing_master.packing_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['packing_master.packing_date'] = 'ASC';
		//$queryData['group_by'][] = 'packing_master.packing_date';
		$queryData['group_by'][] = 'packing_master.item_id';

		return $this->rows($queryData);
    }
	
	public function getDispatchOnPacking($data){
        $queryData = array();
		$queryData['tableName'] = 'trans_child';
        $queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty,AVG(trans_child.price) as dispatch_price,SUM(trans_child.disc_amount) as disc_amt,trans_child.item_id';
		$queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where_in']['trans_main.entry_type'] = '6,7,8';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
        //$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_child.item_id';
		return $this->row($queryData);
    }
	
	/* On Invoice Data */
	public function getDispatchMaterial($data){
        $queryData = array();
		$queryData['tableName'] = 'trans_child';
        $queryData['select'] = 'SUM(trans_child.qty) as dispatch_qty';
		$queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['where_in']['trans_main.entry_type'] = '6,7,8';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
        // $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['where']['trans_main.trans_date >= '] = $data['from_date'];
		$dm = $this->row($queryData);//if($data['item_id'] = 1289){print_r($this->db->last_query());}
		return $dm;
    }


	public function getJobcardBySO($sales_order_id,$product_id)
	{
		$queryData = array();
		$queryData['tableName'] = 'job_card';
		$queryData['where']['sales_order_id'] = $sales_order_id;
		$queryData['where']['product_id'] = $product_id;
		
		return $this->row($queryData);
	}
	
    public function getWIPQtyForDispatchPlan($data)
    {
        $queryData['tableName'] = "job_card";
        $queryData['select'] = "SUM(job_card.qty) as qty";
        $queryData['where']['job_card.sales_order_id'] = $data['trans_main_id'];
        $queryData['where']['job_card.product_id'] = $data['item_id'];
        $queryData['where']['job_card.order_status !=']= 4;
		
		return $this->rows($queryData);
    }

    public function getCurrencyConversion($currency)
    {
        
        $data['tableName'] = 'currency';
        $data['where']['currency'] = $currency;
        $result= $this->rows($data);
        
       return $result;
        

    }
    
    public function getDispatchSummary($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,party_master.party_code,party_master.party_name,party_master.currency';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['where_in']['trans_main.entry_type'] = '5';
        $queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where_in']['trans_child.item_id'] = $data['item_id'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);

        //print_r($result);exit;
		return $result;
    }
    
    public function getItemHistory($item_id){
        $queryData['tableName'] = $this->stockTrans;
        $queryData['select'] = 'stock_transaction.*,item_master.item_code,item_master.item_name';
        $queryData['join']['item_master'] = "item_master.id = stock_transaction.item_id";
        $queryData['where']['stock_transaction.item_id'] = $item_id;
        $queryData['order_by']['stock_transaction.ref_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    public function getSalesEnquiry($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_no,trans_main.trans_prefix,trans_main.trans_date,party_master.party_code,party_master.party_name,party_master.currency,rejection_comment.remark as reason';
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
		$queryData['leftJoin']['rejection_comment'] = "rejection_comment.id = trans_child.item_remark";
		$queryData['where']['trans_child.entry_type'] = 1;
		$queryData['where']['trans_child.feasible'] = 'No';
        if(!empty($data['reson_id']))    
            $queryData['where']['trans_child.item_remark'] = $data['reson_id'];
        if(!empty($data['party_id']))
            $queryData['where']['trans_main.party_id'] = $data['party_id'];

        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }
    
    /* Monthly Sales Report */
    public function getSalesData($data)
    {
        $queryData = array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*';
        $queryData['customWhere'][] = 'trans_main.entry_type IN(6,7,10,11)';
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
        if($data['party']!=0){ $queryData['where']['party_id'] = $data['party']; }
        if($data['product']!=0){
            $queryData['leftJoin']['trans_child'] = "trans_child.trans_main_id = trans_main.id";
            $queryData['where']['trans_child.item_id'] = $data['product'];
        }

		$result = $this->rows($queryData);
		return $result;
    }
    
    /* Dispatch Plan Summary */
    public function getDispatchPlanSummary($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*, trans_main.trans_prefix, trans_main.trans_no,party_master.party_code,party_master.party_name,party_master.currency';
        $queryData['leftJoin']['trans_main'] = "trans_child.trans_main_id = trans_main.id";
        $queryData['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        if(!empty($data['party_id'])){ $queryData['where']['trans_main.party_id'] = $data['party_id']; }        
        if(!empty($data['sales_type'])){ $queryData['where']['trans_main.sales_type'] = $data['sales_type'];}
        $queryData['where']['trans_main.entry_type'] = 4;
        $queryData['customWhere'][] = "trans_child.cod_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_child.cod_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : 
    * Note : 
    */
    public function getEnquiryMonitoring($data){
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'trans_main.*';
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->rows($queryData); 
        return $result;
    }

    public function getEnquiryCount($data){ 
		$result = new StdClass; $result->pending=0; $result->totalEnquiry=0; $result->quoted=0; $result->confirmSo=0; $result->pendingSo=0;

        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['entry_type'] = 1;
        $queryData['customWhere'][] = "trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$totalEnquiry = $this->rows($queryData);
        $result->totalEnquiry = count($totalEnquiry);
		
		$queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['select'] = "trans_main.*";
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.trans_status'] = 1;
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = 'trans_main.id';
        $quoted = $this->rows($queryData);
        $result->quoted = count($quoted);

        /*$queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['select'] = "trans_main.*";
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
        $queryData['where']['trans_child.trans_status != '] = 1;
		$queryData['where']['trans_main.entry_type'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $pending = $this->rows($queryData);
		$result->pending = count($pending);*/
        $result->pending = $result->totalEnquiry - $result->quoted;
        
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
		$queryData['where']['trans_child.from_entry_type'] = 1;
		$queryData['where_in']['trans_child.entry_type'] = '2,3';
		$queryData['where']['trans_child.trans_status'] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $confirmSo = $this->rows($queryData);
        $result->confirmSo = count($confirmSo);

       /* $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['join']['trans_child'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_main.party_id'] = $data['party_id'];
		$queryData['where']['trans_child.from_entry_type'] = 1;
		$queryData['where']['trans_child.trans_status != '] = 1;
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
        $queryData['group_by'][] = 'trans_main.id';
        $pendingSo = $this->rows($queryData);
        $result->pendingSo = count($pendingSo);*/
        $result->pendingSo = $result->totalEnquiry - $result->confirmSo;

		return $result;
	}
	
	 /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesEnquiryByParty($data){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['party_id'] = $data['party_id'];
		$queryData['where']['entry_type'] = 1;
        $queryData['customWhere'][] = "trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$totalEnquiry = $this->rows($queryData);
        return $totalEnquiry;
    }
	
    /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesQuotation($ref_id){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['entry_type'] = 2;
		$return= $this->rows($queryData);
        return $return;
    }
	
    /**
     * Created By Mansee @ 13-12-2021
     */
    public function getSalesOrder($ref_id){
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
		$queryData['where']['ref_id'] = $ref_id;
		$queryData['where']['entry_type'] = 4;
		$return= $this->rows($queryData);
        return $return;
    }

    /* 
        Created By Avruti @ 30-12-2021
    */
    public function getSalesInvoiceTarget($postData){
        $fdate = date("Y-m-d",strtotime($postData['month']));
		$tdate  = date("Y-m-t",strtotime($postData['month']));
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'SUM(trans_main.net_amount) as totalInvoiceAmt';
		$queryData['where']['trans_main.party_id'] = $postData['party_id'];
		$queryData['where_in']['trans_main.entry_type'] = [6,7,8];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$fdate."' AND '".$tdate."'";
        //$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->row($queryData);
		return $result;  
    }
    
    public function getSalesOrderTarget($postData){
        $fdate = date("Y-m-d",strtotime($postData['month']));
		$tdate  = date("Y-m-t",strtotime($postData['month']));
        $queryData = Array();
		$queryData['tableName'] = $this->transMain;
        $queryData['select'] = 'SUM(trans_main.net_amount * inrrate) as totalOrderAmt';
		$queryData['where']['trans_main.party_id'] = $postData['party_id'];
		$queryData['where_in']['trans_main.entry_type'] = [4];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$fdate."' AND '".$tdate."'";
        //$queryData['group_by'][] = 'trans_main.party_id';
        $result = $this->row($queryData);
		return $result;  
    }
    
    // Created By Meghavi 09/07/2022
    public function getUserWiseSale($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
        $queryData['select'] = 'trans_child.*,trans_main.trans_number,trans_main.trans_date,item_master.item_name,employee_master.emp_name';
		$queryData['leftJoin']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
		$queryData['leftJoin']['employee_master'] = "employee_master.id = trans_main.created_by";
		$queryData['leftJoin']['item_master'] = "item_master.id = trans_child.item_id";
        $queryData['where_in']['trans_child.entry_type'] = '6,7,8';
        if(!empty($data['created_by'])){
            $queryData['where']['trans_main.created_by'] = $data['created_by'];
        }
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		$result = $this->rows($queryData);
		return $result;
    }

	/**** For API Created By JP ****/
	
    public function getSalesByEmp($postData){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id,trans_main.trans_date,trans_main.trans_number, trans_main.net_amount';
        $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'";
        $data['where']['trans_main.created_by'] = $this->loginId;

        return $this->rows($data);
    }

    public function getIncentiveByEmp($postData){
        $data['tableName'] = $this->transMain;
        $data['select'] = 'trans_main.id,trans_main.trans_date,trans_main.trans_number, trans_main.net_amount, trans_main.net_amount as incentive_amt';
        $data['customWhere'][] = "trans_main.trans_date BETWEEN '".$postData['from_date']."' AND '".$postData['to_date']."'";
        $data['where']['trans_main.created_by'] = $this->loginId;

        return $this->rows($data);
    }
}
?>