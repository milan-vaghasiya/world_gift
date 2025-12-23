<?php 
class StockVerificationModel extends MasterModel
{
    private $itemMaster = "item_master";
	private $stockVerification = "stock_verification";
	private $stockTrans = "stock_transaction";
	private $months = [
		"01"=>"A",
		"02"=>"B",
		"03"=>"C",
		"04"=>"D",
		"05"=>"E",
		"06"=>"F",
		"07"=>"G",
		"08"=>"H",
		"09"=>"I",
		"10"=>"J",
		"11"=>"K",
		"12"=>"L"
	];

	public function getPrefix($date){
		$currentMonth = date("m",strtotime($date));
		$prefix = $this->months[$currentMonth].date("y");
		return $prefix;
	}

	public function getNextNo($date){
		$startDate = date("Y-m-01",strtotime($date));
		$endDate = date("Y-m-t",strtotime($startDate));

		$queryData = array();
		$queryData['select'] = "MAX(trans_no) as trans_no";
        $queryData['where']['entry_date >= '] = $startDate;
        $queryData['where']['entry_date <= '] = $endDate;
        $queryData['tableName'] = $this->stockVerification;
		$trans_no = $this->specificRow($queryData)->trans_no;
		$nextTransNo = (!empty($trans_no))?($trans_no + 1):1;
		return $nextTransNo;
	}

	public function getDTRows($data){
        $data['tableName'] = $this->stockVerification;
        $data['select'] = "stock_verification.*,item_master.item_name,item_master.item_code";
		$data['leftJoin']['item_master']="item_master.id = stock_verification.item_id";
		$data['leftJoin']['stock_transaction']="stock_transaction.item_id = stock_verification.item_id";
		$data['group_by'][]='stock_verification.item_id';
		
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";
        $data['searchCol'][] = "";

		$columns =array('','','','','','','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
		return $this->pagingRows($data);
    }

	// public function save($data){
	// 	try{
    //         $this->db->trans_begin();
	// 		$trans_prefix = $this->getPrefix(date("Y-m-d"));
	// 		$trans_no = $this->getNextNo(date("Y-m-d"));
	// 		$trans_number = $trans_prefix.sprintf("%02d",$trans_no);
	// 		foreach($data['item_data'] as $row):
	// 			$row['id'] = "";
	// 			$row['trans_no'] = $trans_no;
	// 			$row['trans_prefix'] = $trans_prefix;
	// 			$row['trans_number'] = $trans_number;
	// 			$row['entry_date'] = date("Y-m-d");
	// 			$row['created_by'] = $this->loginId;
	// 			$row['cm_id'] = $this->CMID;
	// 			$this->store($this->stockVerification,$row);
	// 		endforeach;

	// 		$result = ['status'=>1,'message'=>"Record saved successfully.",'field_error'=>0,'field_error_message'=>null];
	// 		if ($this->db->trans_status() !== FALSE):
	// 			$this->db->trans_commit();
	// 			return $result;
	// 		endif;
	// 	}catch(\Exception $e){
	// 		$this->db->trans_rollback();
	// 		return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
	// 	}	
	// }

	public function save($data){
        try{
            $this->db->trans_begin();
            
    		$trans_prefix = $this->getPrefix(date("Y-m-d"));
			foreach($data['item_data'] as $row):
			    
    			$trans_no = $this->getNextNo(date("Y-m-d"));
    			$trans_number = $trans_prefix.sprintf("%02d",$trans_no);
			    
			    $stockData = $this->store->getItemStock($row['item_id']);
			    
			    $trans_type = '';$actual_qty=0;$diff_qty = 0;
			    if(!empty($stockData)){$actual_qty=$stockData->qty;}
			    $diff_qty = $row['qty'] - $actual_qty;
			    
    			if($diff_qty > 0){$trans_type = 1;}
    			if($diff_qty < 0){$trans_type = 2;}
			    
			    if(!empty($diff_qty))
			    {
    			    
    				$vdata['id'] = "";
    				$vdata['trans_no'] = $trans_no;
    				$vdata['trans_prefix'] = $trans_prefix;
    				$vdata['trans_number'] = $trans_number;
    				$vdata['item_id'] = $row['item_id'];
    				$vdata['qty'] = $row['qty'];
    				$vdata['old_qty'] = $actual_qty;
    				$vdata['diff_qty'] = $diff_qty;
    				$vdata['entry_date'] = date("Y-m-d");
    				$vdata['created_by'] = $this->loginId;
    				$vdata['cm_id'] = $this->CMID;
    				$record = $this->store($this->stockVerification,$vdata);
    				
        			$storeData =[
        				'id' =>'',
        				'location_id' => $this->RTD_STORE->id,
        				'trans_type' => $trans_type,
        				'ref_type'=>21,
        				'item_id'=> $row['item_id'],
        				'qty'=> $diff_qty,
        				'ref_id'=> $record['insert_id'],
        				'ref_no'=> $trans_number,
        				'ref_date'=> date("Y-m-d")
        			];
                    $this->store($this->stockTrans,$storeData);
			    }
			endforeach;
			
		$result = ['status'=>1,'message'=>"Record saved successfully.",'field_error'=>0,'field_error_message'=>null];	
        if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
	}catch(\Exception $e){
		$this->db->trans_rollback();
	   return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
	}	
    }

	public function getStoreVerification($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->stockVerification;
        return $this->row($data);
    }
}
?>