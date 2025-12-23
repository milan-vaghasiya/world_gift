<?php 
class StoreReportModel extends MasterModel
{
    private $stockTrans = "stock_transaction";
    private $jobDispatch = "job_material_dispatch";
    private $itemMaster = "item_master";
	private $itemGroup = "item_group";
    private $locationMaster = "location_master";

	/* Issue Register Data */
    public function getIssueRegister($data){
        $queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'stock_transaction.*,job_material_dispatch.collected_by,job_material_dispatch.remark,job_material_dispatch.id as dispatch_id,item_master.item_name, item_master.price as itemPrice, department_master.name as dept_name';
		$queryData['join']['job_material_dispatch'] = 'job_material_dispatch.id = stock_transaction.ref_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = stock_transaction.item_id';
		$queryData['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';
		$queryData['where']['stock_transaction.ref_type'] = 3;
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
		if(!empty($data['dept_id'])){$queryData['where']['job_material_dispatch.dept_id'] = $data['dept_id'];}
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['stock_transaction.ref_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }

    public function getIssueItemPrice($dispatch_id){
        $queryData = array();
		$queryData['tableName'] = $this->jobDispatch;
        $queryData['select'] = 'job_material_dispatch.*,grn_transaction.price as ItemPrice';
		$queryData['join']['grn_transaction'] = 'grn_transaction.item_id = job_material_dispatch.req_item_id';
        $queryData['where']['job_material_dispatch.id'] = $dispatch_id;
        $queryData['order_by']['job_material_dispatch.dispatch_date'] = 'ASC';
        $queryData['limit'] = 1;		
        $result = $this->rows($queryData);  
		return $result;
    }

	/* Stock Register */
	public function getStockReceiptQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as rqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 1;
		//$queryData['where']['stock_transaction.ref_type != '] = -1;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	public function getStockIssuedQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as iqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 2;
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}

	/* Consumable */
    public function getConsumable(){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = 2;
		return $this->rows($data);
	}

	/* Raw Material */
    public function getRawMaterialReport(){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = 3;
		return $this->rows($data);
	}

	/* Group wise Item List */
    public function getItemsByGroup($data){
		$data['tableName'] = $this->itemMaster;
		$data['where_in']['item_master.item_type'] = $data['item_type'];
		return $this->rows($data);
	}

	/* Inventory Monitoring */
	public function getItemGroup(){
		$data['tableName'] = $this->itemGroup;
		return $this->rows($data);
	}
	
	public function getFyearOpningStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as fyosqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.ref_type'] = -1;
        $queryData['where']['stock_transaction.ref_date <= '] = date('Y-m-d', strtotime($this->dates[0]));
		return $this->row($queryData);
	}
	public function getOpningStockQty($data){
		//if($data['from_date'] == date('Y-m-d', strtotime($this->dates[0]))){$data['from_date'] = date('Y-m-d', strtotime('+1 day', strtotime($data['from_date'])));} 
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as osqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
        // $queryData['where']['stock_transaction.ref_date < '] = $data['from_date'];
        $queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".date('Y-m-d', strtotime($this->dates[0]))."' AND '".date('Y-m-d', strtotime('-1 day', strtotime($data['from_date'])))."'";
		return $this->row($queryData);
	}

	public function getItemPrice($data){
        $queryData = array();
		$queryData['tableName'] = "grn_transaction";
        $queryData['select'] = 'SUM(grn_transaction.price * grn_transaction.qty) as amount';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
        $queryData['where']['grn_transaction.item_id'] =  $data['item_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);  
    }

    /* Stock Statement finish producct */
	public function getFinishProduct(){
		$queryData['tableName'] = $this->itemMaster;
		$queryData['select'] = 'item_master.*,party_master.party_name';
		$queryData['join']['party_master'] = 'party_master.id = item_master.party_id';
		$queryData['where']['item_master.item_type'] = 1;
		return $this->rows($queryData);
	}

	public function getClosingStockQty($data){
		$queryData = array();
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = 'SUM(stock_transaction.qty) as csqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['customWhere'][] = "stock_transaction.ref_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		return $this->row($queryData);
	}
	
	public function getStockRegister($type){
		$data['tableName'] = $this->itemMaster;
		$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
	/*Tool Issue Register Data */ 
    public function getToolIssueRegister($data){
        $queryData = array();
		$queryData['tableName'] = $this->jobDispatch;
		$queryData['select'] = 'job_material_dispatch.*,department_master.name,job_card.job_no,job_card.job_prefix,job_card.product_id,item_master.price, item_master.item_name';
		$queryData['leftJoin']['job_card'] = 'job_material_dispatch.job_card_id = job_card.id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = job_material_dispatch.dispatch_item_id';
		$queryData['leftJoin']['department_master'] = 'department_master.id = job_material_dispatch.dept_id';
		$queryData['where']['job_material_dispatch.dispatch_item_id != '] = 0;
		$queryData['where']['job_material_dispatch.tools_dispatch_id != '] = 0;
		if(!empty($data['job_card_id'])){$queryData['where']['job_material_dispatch.job_card_id'] = $data['job_card_id'];}
		if(!empty($data['dept_id'])){$queryData['where']['job_material_dispatch.dept_id'] = $data['dept_id'];}
        if(empty($data['job_card_id'])){$queryData['customWhere'][] = "job_material_dispatch.dispatch_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";}
		$queryData['order_by']['job_material_dispatch.dispatch_date'] = 'ASC';

		$result = $this->rows($queryData);
		return $result;
    }
	
	/* Item Location */
	public function getItemLocation($item_id){
		$queryData['tableName'] = "stock_transaction";
		$queryData['select'] = "SUM(qty) as qty, location_id";
		$queryData['where']['item_id'] = $item_id;
		$queryData['having'][] = 'SUM(qty) > 0';
		$queryData['group_by'][] = 'location_id';
		return $this->row($queryData);
	}

	/**
	 * Created By Mansee @ 09-12-2021
	 */

	 /* Stock Statement Row Material Item */
	 public function getRowMaterialScrapQty($data){
	
		$queryData['tableName'] = $this->itemMaster;
		$queryData['select'] = '`item_master`.item_name,item_master.item_code,item_master.price,job_used_material.job_card_id,SUM( (job_approval.pre_finished_weight- job_approval.finished_weight ) * job_approval.in_qty) as scrap_qty';
		$queryData['leftJoin']['job_used_material'] = 'job_used_material.bom_item_id = item_master.id';
		$queryData['leftJoin']['job_approval'] = 'job_approval.job_card_id = job_used_material.job_card_id';
		$queryData['leftJoin']['job_card'] = ' job_approval.job_card_id = job_card.id';
		$queryData['where']['item_master.item_type'] = 3;
		if($data['material_grade'] != 'ALL')
		{
			$queryData['where']['item_master.material_grade'] = $data['material_grade'];
		}
		$queryData['customWhere'][] = "job_card.job_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['group_by'][] = "`item_master`.item_name,item_master.item_code,item_master.price,job_used_material.job_card_id";
		$result= $this->rows($queryData);
		return $result;
	}
	
	public function getJobcardList(){
        $data['tableName'] = $this->jobCard;
        $data['select'] = 'job_card.*,item_master.item_code,item_master.item_name';
        $data['join']['item_master'] = 'item_master.id = job_card.product_id';
        return $this->rows($data); 
    }
}
?>