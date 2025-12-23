<?php 
class PurchaseReportModel extends MasterModel
{
    private $grnTrans = "grn_transaction";
    private $purchaseTrans = "purchase_order_trans";
    private $transMain = "trans_main";
    private $transChild = "trans_child";

    public function getPurchaseMonitoring($data){
        $queryData = array();
		$queryData['tableName'] = $this->purchaseTrans;
		$queryData['select'] = 'purchase_order_trans.*,purchase_order_master.po_date,item_master.item_name,party_master.party_name,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.remark';
		$queryData['join']['purchase_order_master'] = 'purchase_order_master.id = purchase_order_trans.order_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = purchase_order_trans.item_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = purchase_order_master.party_id';
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
        $queryData['customWhere'][] = "purchase_order_master.po_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['purchase_order_master.po_date'] = 'ASC';
		return $this->rows($queryData);
    }
    
    public function getPurchaseMonitoring11($data){
        $queryData = array();
		$queryData['tableName'] = $this->transChild;
		$queryData['select'] = 'trans_child.*,trans_main.trans_date as po_date,item_master.item_name,party_master.party_name,trans_main.trans_prefix as po_prefix,trans_main.trans_no as po_no,trans_main.remark';
		$queryData['join']['trans_main'] = 'trans_main.id = trans_child.trans_main_id';
		$queryData['leftJoin']['item_master'] = 'item_master.id = trans_child.item_id';
		$queryData['leftJoin']['party_master'] = 'party_master.id = trans_main.party_id';
		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['trans_main.trans_date'] = 'ASC';
		return $this->rows($queryData);
    }

    public function getPurchaseReceipt($data){
        $queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_prefix,grn_master.grn_no,grn_master.grn_date';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['where']['grn_transaction.item_id'] = $data['item_id'];
		$queryData['where']['grn_transaction.po_trans_id'] = $data['grn_trans_id'];
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['grn_master.grn_date'] = 'ASC';
		return $this->rows($queryData);
    }
    
    /* Last Purchase Price */
	public function getLastPrice($data){
		$queryData = array();
		$queryData['tableName'] = 'trans_child';
		$queryData['select'] = 'trans_child.price';
		$queryData['leftJoin']['trans_main'] = 'trans_child.trans_main_id = trans_main.id';
		$queryData['where']['trans_child.item_id'] = $data['item_id'];
		$queryData['where']['trans_main.entry_type'] = 12;
		if(!empty($data['from_date']))
		{
			$queryData['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		}
		$queryData['order_by']['trans_main.trans_date'] = 'DESC';
		$queryData['limit']=1;
		
		$result = $this->row($queryData);
		// print_r($this->db->last_query());
		return $result;
	}
	
	public function getPurchaseInward($data){
		$queryData = array();
		$queryData['tableName'] = $this->grnTrans;
		$queryData['select'] = 'grn_transaction.*,grn_master.grn_prefix,grn_master.grn_no,grn_master.grn_date,party_master.party_name,item_master.item_name,purchase_order_master.po_prefix,purchase_order_master.po_no,purchase_order_master.po_date';
		$queryData['join']['grn_master'] = 'grn_master.id = grn_transaction.grn_id';
		$queryData['join']['item_master'] = 'item_master.id = grn_transaction.item_id';
		$queryData['join']['party_master'] = 'party_master.id = grn_master.party_id';
		$queryData['leftJoin']['purchase_order_master'] = 'purchase_order_master.id = grn_master.order_id';

		if(!empty($data['item_type'])){$queryData['where']['item_master.item_type'] = $data['item_type'];}
        $queryData['customWhere'][] = "grn_master.grn_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'";
		$queryData['order_by']['grn_master.grn_date'] = 'DESC';
		return $this->rows($queryData);
	}
}
?>