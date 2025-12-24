<?php
class ItemModel extends MasterModel{
    private $itemMaster = "item_master";
	private $stockTrans = "stock_transaction";
	private $itemKit = "item_kit";
	private $productProcess = "product_process";
	private $processMaster = "process_master";
    private $unitMaster = "unit_master";
    private $itemCategory = "item_category";
    private $openingStockTrans = "stock_transaction";
    private $productionOperation = "production_operation";
    private $inspectionParam = "inspection_param";
    private $fgRevision = "fg_revisions";
    private $productImage = "product_images";
    //private $familyGroup = "family_group";

    public function getDTRows($data,$type=0){
        $data['tableName'] = $this->itemMaster;
        if($this->CMID == 1):
            $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price1 as price,item_master.wholesale1,item_master.wholesale2,item_master.unit_id,item_master.min_qty,item_master.qty,item_master.opening_qty1 as opening_qty, item_master.item_image,unit_master.unit_name,item_category.category_name";
        else:
            $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price2 as price,item_master.wholesale1,item_master.wholesale2, item_master.unit_id,item_master.min_qty,item_master.qty,item_master.opening_qty2 as opening_qty, item_master.item_image,unit_master.unit_name,item_category.category_name";
        endif;
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['where']['item_master.item_type'] = $type;
        if(!empty($data['category_id'])){ $data['where']['item_master.category_id'] = $data['category_id']; }
        //$data['order_by']['unit_master.unit_name'] = "asc";
		$data['order_by']['item_name'] = "asc";
		
		$data['searchCol'][] = "item_master.item_name";
		$data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_master.hsn_code";
        $data['searchCol'][] = "item_master.price";

		$columns =array('','','','item_master.item_name','item_category.category_name','item_master.hsn_code','item_master.price','','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result= $this->pagingRows($data);
		return $result;
    }
    
    public function getTagsDTRow($data){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.price1 as price,item_master.item_image,item_category.category_name,item_master.qty,unit_master.unit_name";
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $data['join']['stock_transaction'] = "stock_transaction.item_id = item_master.id AND stock_transaction.is_delete = 0 AND stock_transaction.location_id = ".$this->RTD_STORE->id;
        if(!empty($data['category_id'])){ $data['where']['item_master.category_id'] = $data['category_id']; }	
        $data['group_by'][] = 'stock_transaction.item_id';
        $data['having'][] = "SUM(stock_transaction.qty) > 0";

		$data['searchCol'][] = "item_master.item_name";
		$data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_master.price";
        $data['searchCol'][] = "item_master.qty";

		$columns =array('','','','','item_master.item_name','item_category.category_name','item_master.price','item_master.qty');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        $result= $this->pagingRows($data);
		return $result;
    }

	public function getItemList($type=0,$category_id=0){
		$data['tableName'] = $this->itemMaster;
		if($this->CMID == 1):
			$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price1 as price,item_master.unit_id,item_master.qty,unit_master.unit_name,item_category.category_name";
		else:
			$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price2 as price,item_master.unit_id,item_master.qty,unit_master.unit_name,item_category.category_name";
		endif;
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";

		if(!empty($type)){ $data['where']['item_master.item_type'] = $type; }
        if(!empty($category_id)){ $data['where']['item_master.category_id'] = $category_id; }
		return $this->rows($data);
	}
	
    public function getItemLists($type="0"){
		$data['tableName'] = $this->itemMaster;
		if($this->CMID == 1):
		    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price1 as price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		else:
		    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price2 as price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		endif;
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		// $data['where']['NOCMID'] = "";

		if(!empty($type) and $type != "0")
			$data['where_in']['item_master.item_type'] = $type;
		return $this->rows($data);
	}
	
	public function getItemListForSelect($type=0){
		$data['tableName'] = $this->itemMaster;
		 if($this->CMID == 1):
		    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price1 as price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		else:
		    $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price2 as price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		endif;
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		// $data['where']['NOCMID'] = "";

		if(!empty($type))
			$data['where']['item_master.item_type'] = $type;
		return $this->rows($data);
	}

	public function locationWiseBatchStock($item_id,$location_id){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as qty,batch_no";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		$data['order_by']['id'] = "asc";
		$data['group_by'][] = "batch_no";
		return $this->rows($data);
	}

    public function getItem($id){
        $data['tableName'] = $this->itemMaster;
        $data['where']['id'] = $id;
        return $this->row($data);
    }
	
	public function getItemCat($category_id=''){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,item_category.category_name";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        if(!empty($category_id)){$data['where']['category_id'] = $category_id; }
        return $this->rows($data);
    }

	public function getItemForCatalogue($category_id='',$is_excel=''){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,item_category.category_name,SUM(stock_transaction.qty) as stock_qty";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$data['leftJoin']['stock_transaction'] = "stock_transaction.item_id = item_master.id AND stock_transaction.is_delete = 0 AND stock_transaction.location_id = ".$this->RTD_STORE->id;
        if(!empty($category_id)){ $data['where']['item_master.category_id'] = $category_id; }
        if(!empty($is_excel)){ $data['customWhere'][] = '(item_master.item_image IS NULL OR item_master.item_image = "")'; }
        $data['order_by']['item_master.item_name'] = "ASC";
        $data['group_by'][] = "stock_transaction.item_id";
        $data['having'][] = "SUM(stock_transaction.qty) > 0";
        return $this->rows($data);
    }
    
    public function getMultipleItems($item_id=''){
        $data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,item_category.category_name,SUM(stock_transaction.qty) as stock_qty";
		$data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$data['leftJoin']['stock_transaction'] = "stock_transaction.item_id = item_master.id AND stock_transaction.is_delete = 0 AND stock_transaction.location_id = ".$this->RTD_STORE->id;
		if(!empty($item_id)){ $data['where_in']['item_master.id'] = $item_id; }
        $data['order_by']['item_master.item_name'] = "ASC";
        $data['group_by'][] = "stock_transaction.item_id";
        $data['having'][] = "SUM(stock_transaction.qty) > 0";
        return $this->rows($data);
    }
	
    public function getItemBySelect($id,$select){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = $select;
        $data['where']['id'] = $id;
		// $data['where']['NOCMID'] = "";
        return $this->row($data);
    }

    public function itemUnits(){
        $data['tableName'] = $this->unitMaster;
        // $data['where']['NOCMID'] = "";
		return $this->rows($data);
	}
	
	public function itemUnit($id){
        $data['tableName'] = $this->unitMaster;
		$data['where']['id'] = $id;
		// $data['where']['NOCMID'] = "";
		return $this->row($data);
	}

	public function getOpeningRawMaterialList(){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.*,unit_master.unit_name";
        $data['join']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['item_master.item_type'] = 1;
		$data['where']['item_master.opening_remaining_qty != '] = "0.000";
		// $data['where']['NOCMID'] = "";
		return $this->rows($data);
	}

    public function save($data){
		try{
            $this->db->trans_begin();
            
			$process = array(); $itmId = 0;
			$msg = ($data['item_type'] == 0) ? "Item" : "Part";
			
			if(empty($data['is_update_item_code']) && $this->checkDuplicate($data['item_name'],$data['item_type'],$data['id']) > 0):
				$errorMessage['item_name'] =  $msg." Name is duplicate.";
				
				if(!isset($data['source'])):
				    return ['status' => 0, 'message' => "Some fields are duplicate.",'field_error'=>1,'field_error_message'=>$errorMessage];
				else:
				    return ['status' => 1, 'message' => "Some fields are duplicate."];
				endif;
			else:
				if(!empty($data['process_id'])):
					$process = explode(',',$data['process_id']);
				endif;
				unset($data['process_id'],$data['source'],$data['is_update_item_code']);
				
				$mgsName = ($data['item_type'] == 0)?"Item":"Product";
				$result = $this->store($this->itemMaster,$data,$mgsName);
				$itmId = (empty($data['id'])) ? $result['insert_id'] : $data['id'];
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

    public function checkDuplicate($name,$type,$id=""){
        $data['tableName'] = $this->itemMaster;
        $data['where']['item_name'] = $name;
        $data['where']['item_type'] = $type;
        
		// $data['where']['NOCMID'] = "";
		
        if(!empty($id))
            $data['where']['id !='] = $id;

        return $this->numRows($data);
    }

    public function delete($id){
		try{
            $this->db->trans_begin();
			$itemData = $this->getItem($id);
			$mgsName = ($itemData->item_type == 0)?"Item":"Product";
			
			$result = $this->trash($this->itemMaster,['id'=>$id],$mgsName);
			if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}	
    }

    public function getStockTrans($id){
		$data['where']['item_id'] = $id;
		$data['order_by']['trans_date'] = 'desc';
        $data['tableName'] = $this->stockTrans;
		$stockTrans = $this->rows($data);
		
		if(!empty($stockTrans)):
			$html = "";$i=1;
			foreach($stockTrans as $row):
				$typeName = ($row->type == "+")?"Add":"Reduce";
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>'.date('d-m-Y',strtotime($row->trans_date)).'</td>
							<td>('.$row->type.') '.$typeName.'</td>
							<td>'.$row->qty.'</td>
							<td class="text-center"><div class="btn-group"><a href="javascript:void(0)" class="btn btn-outline-danger waves-effect waves-light" onclick="deleteStock('.$row->id.');" ><i class="ti-trash"></i></a></div></td>
						 </tr>';
			endforeach;
			$result = $html;
		else:
			$result = "";
		endif;
		return $result;
	}

    public function saveStockTrans($data){
		try{
            $this->db->trans_begin();
			$data['id'] = "";
			$data['trans_date'] = date('Y-m-d',strtotime($data['trans_date']));
			$data['created_by'] = $this->session->userdata('loginId');
			$this->store($this->stockTrans,$data,"");
			
			$itemData = $this->getItem($data['item_id']);
			if($data['type'] == "+"):
				$qty = $itemData->qty + $data['qty'];
			else:
				$qty = $itemData->qty - $data['qty'];
			endif;

			$this->edit($this->itemMaster,['id'=>$data['item_id']],['qty'=>$qty]);		
			$result = $this->getStockTrans($data['item_id']);
			if($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
				return $result;
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}	
	}
	
	public function getCategoryList1($type=0){
		$data['where']['category_type'] = $type;
        $data['tableName'] = $this->itemCategory;
		// $data['where']['NOCMID'] = "";
        return $this->rows($data);
    }
    
    public function getCategoryList($type='0'){
        $data['tableName'] = $this->itemCategory;
        $data['select'] = 'item_category.*,main_category.category_name as main_name';
        $data['join']['item_category as main_category'] = 'item_category.ref_id = main_category.id';
		$data['where']['item_category.final_category'] = 1;
		if(!empty($type)){
		    $data['where_in']['item_category.category_type'] = $type;
		}
		$data['order_by']['item_category.ref_id'] = 'ASC';
        return $this->rows($data);
    }
	
	public function getItemGroup(){
        $data['tableName'] = 'item_group';
        return $this->rows($data);
    }
    
    public function getItemGroupById($id){
        $data['tableName'] = 'item_group';
		// $data['where']['NOCMID'] = "";
		$data['where']['id'] = $id;
        return $this->row($data);
    }
    
	public function deleteStockTrans($id){
		try{
            $this->db->trans_begin();
            $data['tableName'] = $this->stockTrans;
            $data['where']['id'] = $id;
    		$transData = $this->row($data);		
    		$this->trash($this->stockTrans,['id'=>$id],'Stock');
    		
    		$itemData = $this->getItem($transData->item_id);
    		if($transData->type == "+"):
    			$qty = $itemData->qty - $transData->qty;
    		else:
    			$qty = $itemData->qty + $transData->qty;
    		endif;
    		
            $this->edit($this->itemMaster,['id'=>$transData->item_id],['qty'=>$qty]);		
    		$result = $this->getStockTrans($transData->item_id);
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

	public function getProductProcessForSelect($id){
		$data['select'] = "process_id";
		$data['where']['item_id'] = $id;
		$data['tableName'] = $this->productProcess;
		$result = $this->rows($data);
		$process = array();
		if($result){foreach($result as $row){$process[] = $row->process_id;}}
		return $process;
	}
	
	public function getProductOperationForSelect($id){
		$data['select'] = "operation";
		$data['where']['id'] = $id;
		$data['tableName'] = $this->productProcess;
		$result = $this->row($data);
		return $result->operation;
	}
	
	public function getProductProcess($id){
		$data['select'] = "process_id";
		$data['where']['item_id'] = $id;
		$data['tableName'] = $this->productProcess;
		return $this->rows($data);
	}

	public function saveProductProcess($data){
		try{
            $this->db->trans_begin();
    		$queryData['select'] = "process_id,id,sequence";
    		$queryData['where']['item_id'] = $data['item_id'];
    		$queryData['tableName'] = $this->productProcess;
    		$process_ids =  $this->rows($queryData);
    
    		$process = '';
    		if(!empty($data['process_id'])):
    			$process = explode(',',$data['process_id']);
    		endif;
    		$z=0;
    		foreach($process_ids as $key=>$value):
    			if(!in_array($value->process_id,$process)):
    			
    				$upProcess['tableName'] = $this->productProcess;
    				$upProcess['where']['item_id']=$data['item_id'];
    				$upProcess['where']['sequence > ']=($value->sequence - $z++);
    				$upProcess['where']['is_delete']=0;
    				$upProcess['set']['sequence']='sequence, - 1';
    				$q = $this->setValue($upProcess);
    				$this->remove($this->productProcess,['id'=>$value->id],'');
    			endif;
    		endforeach;
    		foreach($process as $key=>$value):			
    			if(!in_array($value,array_column($process_ids,'process_id'))):
    				$queryData = array();
    				$queryData['select'] = "MAX(sequence) as value";
    				$queryData['where']['item_id'] = $data['item_id'];
    				$queryData['where']['is_delete'] = 0;
    				$queryData['tableName'] = $this->productProcess;
    				$sequence = $this->specificRow($queryData)->value;
    				
    				$productProcessData = [
    					'id'=>"",
    					'item_id'=>$data['item_id'],
    					'process_id'=>$value,
    					'sequence'=>(!empty($sequence))?($sequence + 1):1,
    					'created_by' => $this->session->userdata('loginId')
    				];
    				$this->store($this->productProcess,$productProcessData,'');
    			endif;
    		endforeach;
    
    
    		$result = ['status'=>1,'message'=>'Product process saved successfully.'];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    		
        	}catch(\Exception $e){
        		$this->db->trans_rollback();
        	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
        	}	
    	}
    
    public function saveProductProcessCycleTime($data){
		try{
            $this->db->trans_begin();
    		foreach($data['id'] as $key=>$value):
    			if(!empty($data['cycle_time'][$key])):	
    				$productProcessData = [
    					'id'=>$value,
    					'cycle_time'=>$data['cycle_time'][$key]
    				];
    				$this->store($this->productProcess,$productProcessData,'');
    			endif;
    		endforeach;

    		$result = ['status'=>1,'message'=>'Cycle Time Updated successfully.','field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

	public function getItemProcess($id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "product_process.*,process_master.process_name";
		$data['join']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $id;
		$data['order_by']['product_process.sequence'] = "ASC";
		return $this->rows($data);
	}

	public function getProductProcessBySequence($product_id,$sequence){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "product_process.*,process_master.process_name";
		$data['join']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $product_id;
		$data['where']['product_process.sequence'] = $sequence;
		return $this->row($data);
	}

	public function updateProductProcessSequance($data){
		try{
            $this->db->trans_begin();
    		$ids = explode(',', $data['id']); $i=1;
    		foreach($ids as $pp_id):
    			$seqData=Array("sequence"=>$i++);
    			$this->edit($this->productProcess,['id'=>$pp_id],$seqData);
    		endforeach;
    
    		$queryData['tableName'] = $this->productProcess;
    		$queryData['where']['id'] = $ids[0];
    		$queryData['order_by']['sequence'] = "ASC";		
    		$productProcessRow = $this->row($queryData);
    		$this->edit($this->itemKit,['item_id'=>$productProcessRow->item_id],['process_id'=>$productProcessRow->process_id]);
    		
    		$result = ['status'=>1,'message'=>'Process Sequence updated successfully.','field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

	public function getProductKitData($id){
		$data['select'] = "item_kit.*,item_master.item_name,process_master.process_name";
		$data['join']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['leftJoin']['process_master'] = "process_master.id = item_kit.process_id";
		$data['where']['item_kit.item_id'] = $id;
		$data['tableName'] = $this->itemKit;
		return $this->rows($data);
	}

	public function getProductKitOnProcessData($id,$processId){
		$data['select'] = "item_kit.*,item_master.item_name";
		$data['join']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['where']['item_kit.item_id'] = $id;
		$data['where']['item_kit.process_id'] = $processId;
		$data['tableName'] = $this->itemKit;
		return $this->rows($data);
	}

	public function saveProductKit($data){
		try{
            $this->db->trans_begin();
    		$kitData = $this->getProductKitData($data['item_id']);
    		foreach($data['ref_item_id'] as $key=>$value):
    			if(empty($data['id'][$key])):
    				$itemKitData = ['id'=>"",'item_id'=>$data['item_id'],'ref_item_id'=>$value,'qty'=>$data['qty'][$key],'process_id'=>$data['process_id'][$key]];
    				$this->store($this->itemKit,$itemKitData);
    			else:
    				$where['process_id'] = $data['process_id'][$key];
    				$where['item_id'] = $data['item_id'];
    				$where['id'] = $data['id'][$key];
    				$this->edit($this->itemKit,$where,['qty'=>$data['qty'][$key]]);
    			endif;
    		endforeach;
    		if(!empty($kitData)):
    			foreach($kitData as $key=>$value):
    				if(!in_array($value->id,$data['id'])){
    					$this->trash($this->itemKit,['id'=>$value->id],'');
    				}
    			endforeach;
    		endif;
    		$result = ['status'=>1,'message'=>'Product Kit Item saved successfully.','field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

	public function getProductWiseProcessList($product_id){
		$data['tableName'] = $this->productProcess;
		$data['select'] = "process_master.id,process_master.process_name";
		$data['leftJoin']['process_master'] = "process_master.id = product_process.process_id";
		$data['where']['product_process.item_id'] = $product_id;
		return $this->rows($data);
	}
	
	public function getItemOpeningTrans($id){
		$queryData['tableName'] = $this->openingStockTrans;
		$queryData['select'] = "stock_transaction.*,location_master.store_name,location_master.location";
		$queryData['leftJoin']['location_master'] = "stock_transaction.location_id = location_master.id";
		$queryData['where']['stock_transaction.ref_type'] = "-1";
		$queryData['where']['stock_transaction.ref_id'] = 0;
		$queryData['where']['stock_transaction.trans_type'] = 1;
		$queryData['where']['stock_transaction.item_id']  = $id;
		$openingStockTrans = $this->rows($queryData);

		$html = '';
		if(!empty($openingStockTrans)):
			$i=1;
			foreach($openingStockTrans as $row):
				$html .= '<tr>
							<td>'.$i++.'</td>
							<td>'.$row->qty.'</td>
							<td class="text-center">
								<div class="btn-group">
									<a href="javascript:void(0)" class="btn btn-outline-danger waves-effect waves-light" onclick="deleteOpeningStock('.$row->id.');" ><i class="ti-trash"></i></a>
								</div>
							</td>
						</tr>';
			endforeach;
		endif;
		return ['status'=>1,'htmlData'=>$html,'result'=>$openingStockTrans];
	}

	public function saveOpeningStock($data){
		try{
            $this->db->trans_begin();
    	    if(empty($data['batch_no']))
    			unset($data['batch_no']);
    	    
    		$this->store($this->openingStockTrans,$data);
    
    		$setData = Array();
    		$setData['tableName'] = $this->itemMaster;
    		$setData['where']['id'] = $data['item_id'];
    		// $setData['where']['NOCMID'] = "";
    		$setData['set']['qty'] = 'qty, + '.$data['qty'];
    		$setData['set']['opening_qty'.$this->CMID] = 'opening_qty'.$this->CMID.', + '.$data['qty'];
    		$this->setValue($setData);
    
    		$result = ['status'=>1,'message'=>'Opening Stock saved successfully.','transData'=>$this->getItemOpeningTrans($data['item_id'])['htmlData'],'field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

	public function deleteOpeningStockTrans($id){
		try{
            $this->db->trans_begin();
    		$queryData['tableName'] = $this->openingStockTrans;
    		$queryData['where']['id'] = $id;
    		$transData = $this->row($queryData);
    
    		$setData = Array();
    		$setData['tableName'] = $this->itemMaster;
    		$setData['where']['id'] = $transData->item_id;
    		$setData['set']['qty'] = 'qty, - '.$transData->qty;
    		$setData['set']['opening_qty'.$this->CMID] = 'opening_qty'.$this->CMID.', - '.$transData->qty;
    		$this->setValue($setData);
    
    		$this->remove($this->openingStockTrans,['id'=>$id],"Opening Stock");
    
    		$result = ['status'=>1,'message'=>'Opening Stock deleted successfully.','transData'=>$this->getItemOpeningTrans($transData->item_id)['htmlData'],'field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	   return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}
	
	public function getProcessWiseMachine($processId){
	    $data['where']['item_type'] = 5;
	    $data['customWhere'][] = 'find_in_set("'.$processId.'", process_id)';
        $data['tableName'] = $this->itemMaster;
        return $this->rows($data);
	}
	
	public function getBatchNoCurrentStock($item_id,$location_id,$batch_no){
		$data['tableName'] = "stock_transaction";
		$data['select'] = "SUM(qty) as stock_qty";
		$data['where']['item_id'] = $item_id;
		$data['where']['location_id'] = $location_id;
		$data['where']['batch_no'] = $batch_no;
		return $this->row($data);
	}

	public function saveToolConsumption($data){
		try{
            $this->db->trans_begin();
    		$toolData = $this->getToolConsumption($data['item_id']);
    
    		if(isset($data['id']) AND !empty($data['id'])):
    			foreach($data['id'] as $key=>$value):
    				if(!empty($data['ref_item_id'][$key])):	
    					$tool_cost=($data['price'][$key] / ($data['tool_life'][$key] * $data['number_corner'][$key]));
    					$toolConsumptionData = [
    						'id'=>$value,
    						'item_id'=>$data['item_id'],
    						'dept_id'=>$data['dept_id'][$key],
    						'ref_item_id'=>$data['ref_item_id'][$key],
    						'setup' => $data['setup'][$key],
    						'tool_life'=>$data['tool_life'][$key],
    						'number_corner' => $data['number_corner'][$key],
                        	'price' => $data['price'][$key],
    						'tool_cost'=>$tool_cost,
    						'operation'=>$data['operation'][$key],
    						'created_by'=>$this->session->userdata('loginId')
    					];
    					$this->store('tool_consumption',$toolConsumptionData,'');
    				endif;
    			endforeach;
    		endif;
    
    		if(!empty($toolData)):
    			foreach($toolData as $row):
    				if(isset($data['id']) AND !in_array($row->id,$data['id'])):
    					$this->trash('tool_consumption',['id'=>$row->id]);
    				endif;
    			endforeach;
    		endif;
    
    		$result = ['status'=>1,'message'=>'Tool Consumption Updated successfully.','field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

	public function getToolConsumption($id){
		$data['tableName'] = "tool_consumption";
		$data['select'] = "tool_consumption.*,item_master.item_name,department_master.name as dept_name,process_master.process_name";		
		$data['join']['	item_master'] = "item_master.id = tool_consumption.ref_item_id";
		$data['leftJoin'][' process_master'] = "process_master.id = tool_consumption.setup";
		$data['leftJoin']['	department_master'] = "department_master.id = tool_consumption.dept_id";
		$data['where']['tool_consumption.item_id'] = $id;
		$result = $this->rows($data);
		$response = Array();
		if(!empty($result)):
			foreach($result as $row):
				$ops = $this->getToolConsumptionOperation($row->operation);
				$row->ops_name = '';$i=0;
				foreach($ops as $opValue):
					$row->ops_name .= ($i==0) ? $opValue->operation_name : ', '.$opValue->operation_name;$i++;
				endforeach;
				$response[] = $row;
			endforeach;
		endif;

		return $response;
	}
	
	public function getToolConsumptionOperation($operations){
		$data['tableName'] = "production_operation";
		$data['where_in']['id'] = $operations;
		return $this->rows($data);
	}
	
    public function getProductOperation($id){
        $data['where']['item_id'] = $id;
        $data['tableName'] = $this->productProcess;
        $result = $this->rows($data);

		$operations = Array();
		if(!empty($result)):
			foreach($result as $row)
			{
				if(!empty($row->operation)){
					$ops = explode(',',$row->operation);
					foreach($ops as $op){$operations[] = $op;}
				}
			}
		endif;
		$ops_id = array_unique($operations);$response = Array();
		if(!empty($ops_id)):
			$qData['tableName'] = $this->productionOperation;
			$qData['where_in']['id'] = implode(',',$ops_id);
			$response = $this->rows($qData);
		endif;
		return $response;
    }

	public function saveProductOperation($data){
		try{
            $this->db->trans_begin();
    		$this->store($this->productProcess,['id'=>$data['id'],'operation'=>$data['operation']]);
    		$result = ['status'=>1,'message'=>'Process Operation Updated successfully.','field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

    public function getPartyItems($party_id){
		$queryData['tableName'] = $this->itemMaster;
		if($this->CMID == 1):
			$queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price1 as price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		else:
			$queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price2 as price,item_master.unit_id,item_master.qty,unit_master.unit_name";
		endif;
		
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['where']['item_master.item_type'] = 1;
		// $queryData['where']['NOCMID'] = "";
        //$queryData['length'] = 100;
        //$queryData['start'] = 0;
        $itemData = $this->rows($queryData);
        
        $partyItems='<option value="">Select Product Name</option>';
        if(!empty($itemData)):
			foreach ($itemData as $row):
				$partyItems .= "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
			endforeach;
        endif;
        return ['status'=>1,'partyItems'=>$partyItems];
    }

	public function checkProductOptionStatus($id)
	{
		$result = new StdClass;$result->bom=0;$result->process=0;$result->cycleTime=0;$result->tool=0;
		$queryData = Array();
		$queryData['tableName'] = $this->itemKit;
		$queryData['where']['item_id'] = $id;
		$bomData = $this->rows($queryData);
		$result->bom=count($bomData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$processData = $this->rows($queryData);
		$result->process=count($processData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->productProcess;
		$queryData['where']['item_id'] = $id;
		$queryData['where']['cycle_time'] = '00:00:00';
		$ctData = $this->rows($queryData);
		$result->cycleTime=count($ctData);
		
		$queryData = Array();
		$queryData['tableName'] = 'tool_consumption';
		$queryData['where']['item_id'] = $id;
		$toolData = $this->rows($queryData);
		$result->tool=count($toolData);
		
		$queryData = Array();
		$queryData['tableName'] = $this->inspectionParam;
		$queryData['where']['item_id'] = $id;
		$toolData = $this->rows($queryData);
		$result->inspection=count($toolData);
		
		return $result;
	}
	
	public function getPreInspectionParam($item_id,$param_type="0"){
		$data['where']['item_id']=$item_id;
		$data['where']['param_type']=$param_type;
		$data['tableName'] = $this->inspectionParam;
		return $this->rows($data);
	}

	public function savePreInspectionParam($data){
		try{
            $this->db->trans_begin();
    		$result = $this->store($this->inspectionParam,$data,'Inspection Parameter');
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}

	public function checkDuplicateParam($parameter,$id=""){
        $data['tableName'] = $this->inspectionParam;
        $data['where']['parameter'] = $parameter;
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

	public function deletePreInspection($id){
		try{
            $this->db->trans_begin();
            $result = $this->trash($this->inspectionParam,['id'=>$id],"Record");
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}
	
	public function getFgRevision($item_id){
        $data['where']['item_id'] = $item_id;
        $data['tableName'] = $this->fgRevision;
        return $this->row($data);
    }

	public function saveFgRevision($data){
		try{
            $this->db->trans_begin();
		
             $result=$this->store($this->fgRevision,$data,'product');
    		 $itemData =[
    			 'rev_no'=>$data['new_rev_no'],
    			 'rev_specification'=>$data['new_specs'],
    			 'id'=>$data['item_id']
    		 ];
    
             return $this->store($this->itemMaster,$itemData,'');
    		 if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
    }
    
    public function saveProductFinishedWeight($data){
		try{
            $this->db->trans_begin();
    		$this->store($this->productProcess,['id'=>$data['id'],'finished_weight'=>$data['finished_weight']]);
    		$result = ['status'=>1,'message'=>'Process Finished Weight Updated successfully.','field_error'=>0,'field_error_message'=>null];
    		if ($this->db->trans_status() !== FALSE):
    			$this->db->trans_commit();
    			return $result;
    		endif;
    	}catch(\Exception $e){
    		$this->db->trans_rollback();
    	    return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
    	}	
	}
	
	public function getProductProcessData($item_id,$process_id){
		$data['select'] = "product_process.*";
		$data['where']['item_id'] = $item_id;
		$data['where']['process_id'] = $process_id;
		$data['tableName'] = $this->productProcess;
		$result = $this->row($data);
		return $result;
	}
	
	// public function getfamilyGroupList(){
    //     $data['tableName'] = $this->familyGroup;
    //     $result = $this->rows($data);
    //     return $result;
    // }
	
	//Created By Karmi @31/12/2021
	public function getMaterialBomPrintData($id){
		$data['tableName'] = $this->itemKit;
		$data['select'] = "item_kit.*,item_master.item_name,process_master.process_name,unit_master.unit_name,itm.item_name as product_name";
		$data['leftJoin']['item_master'] = "item_master.id = item_kit.ref_item_id";
		$data['leftJoin']['item_master as itm'] = "itm.id = item_kit.item_id";
		$data['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
		$data['leftJoin']['process_master'] = "process_master.id = item_kit.process_id";
		$data['where']['item_kit.item_id'] = $id;
		$result = $this->rows($data);
		return $result;
	}

	//Created By Karmi @31/03/2022
	public function getItems($id){
        $data['tableName'] = $this->itemMaster;
        $data['where_in']['id'] = $id;
        return $this->rows($data);
    }
    
    //Created By Karmi @21/07/2022
	public function getItemIncentive($data){
		$data['tableName'] = $this->itemMaster;
		$data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.incentive,item_master.category_id,item_category.category_name";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        if(!empty($data['category'])){ $data['where']['item_master.category_id'] = $data['category']; }
		$result = $this->rows($data);
		return $result;
	}

	public function saveMultipleInsentive($data){
		foreach($data['item_id'] as $key=>$value):
			$this->store($this->itemMaster,['id'=>$value,'incentive'=>$data['incentive']]);
		endforeach;
		return ['status'=>1,'message'=> "Incentive Added successfully."];
	}

	/*  Create By : Avruti @27-11-2021 12:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount00($type=0){
        $data['tableName'] = $this->itemMaster;
		if(!empty($type))
		$data['where']['item_type'] = $type;
		// $data['where']['NOCMID'] = "";
        return $this->numRows($data);
    }
    
	public function getItemList_api($data){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        if($this->CMID == 1):
		    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name, item_category.category_name, item_master.price1 as price, unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty";
		else:
		    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name, item_category.category_name, item_master.price2 as price, unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty";
		endif;
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $queryData['where']['item_master.item_type'] = $data['item_type'];
        if($this->CMID == 1):
            $queryData['where']['stock_transaction.location_id'] = 11;
        else:
            $queryData['where']['stock_transaction.location_id'] = 141;
        endif;
        $queryData['where']['item_master.is_delete'] = 0;
        $queryData['having'][] = 'stock_qty > 0';
        $queryData['group_by'][] = 'stock_transaction.item_id';
        
        if(!empty($data['search'])):
            $queryData['like']['item_master.item_code'] = $data['search'];
            $queryData['like']['item_master.item_name'] = $data['search'];
            $queryData['like']['item_category.category_name'] = $data['search'];
        endif;			

        $queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];
        $result = $this->rows($queryData);
        //print_r($this->printQuery());exit;
        return $result;
    }

	public function getItemData($id){
		$queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        if($this->CMID == 1):
		    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name, item_category.category_name, item_master.price1 as price, unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty";
		else:
		    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name, item_category.category_name, item_master.price2 as price,item_master.qty, unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty";
		endif;
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $queryData['where']['item_master.id'] = $id;
        $queryData['where']['item_master.is_delete'] = 0;
        $queryData['group_by'][] = 'stock_transaction.item_id';
		return $this->row($queryData);
	}
	
	public function getItem_api($id){
        $data['tableName'] = $this->itemMaster;
		if($this->CMID == 1):
		    $data['select'] = "item_master.item_name, item_master.price1 as price, item_master.item_code, item_master.gst_per, item_master.hsn_code, item_master.unit_id, unit_master.unit_name";
		else:
		    $data['select'] = "item_master.item_name, item_master.price2 as price, item_master.item_code, item_master.gst_per, item_master.hsn_code, item_master.unit_id, unit_master.unit_name";
		endif;
		$data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$data['where']['item_master.id'] =$id;
        return $this->row($data);
    }

	public function getItemForStockInOut($data){
        $queryData = array();
        $queryData['tableName'] = $this->stockTrans;
        if($data['cm_id'] == 1):
		    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name, item_category.category_name, item_master.price1 as price, unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty";

			if(!empty($data['price_required'])):
				$queryData['where']['item_master.price1 > '] = 0;
			endif;
		else:
		    $queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name, item_category.category_name, item_master.price2 as price, unit_master.unit_name,SUM(stock_transaction.qty) as stock_qty";

			if(!empty($data['price_required'])):
				$queryData['where']['item_master.price2 > '] = 0;
			endif;
		endif;
        $queryData['leftJoin']['item_master'] = "stock_transaction.item_id = item_master.id";
        $queryData['leftJoin']['unit_master'] = "item_master.unit_id = unit_master.id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
        $queryData['where']['item_master.item_type'] = $data['item_type'];

		if(!empty($data['stock_required'])):
			$queryData['having'][] = 'SUM(stock_transaction.qty) > 0';
		endif;

        $queryData['group_by'][] = 'stock_transaction.item_id';
        
        if(!empty($data['search'])):
            $queryData['like']['item_master.item_code'] = $data['search'];
            $queryData['like']['item_master.item_name'] = $data['search'];
            $queryData['like']['item_category.category_name'] = $data['search'];
        endif;			

        $queryData['length'] = $data['limit'];
		$queryData['start'] = $data['off_set'];
        $result = $this->rows($queryData,$data['cm_id']);
        //print_r($this->printQuery());
        return $result;
    }
    
    public function getImageItems($id){
        $data['tableName'] = $this->productImage;
        $data['where']['item_id'] = $id;
        $return= $this->rows($data);
		return $return;
    }
	
	public function uploadImage($data){ 
		try{
            $this->db->trans_begin();
		$result = $this->store($this->productImage,$data,'Image Upload');
		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}	
	}

	public function deleteImage($id){
		try{
            $this->db->trans_begin();
			$data['tableName'] = $this->productImage;
			$data['where']['id'] = $id;
			$imageData= $this->row($data);
        $result = $this->trash($this->productImage,['id'=>$id],"Record");
		unlink(FCPATH.'assets/uploads/item_image/'.$imageData->image_path);

		if ($this->db->trans_status() !== FALSE):
			$this->db->trans_commit();
			return $result;
		endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
		return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}	
	}
	
    //------ API Code End -------//
    
    /* API Function Start */
    public function getItemSuggestions($data){
		$queryData['tableName'] = $this->itemMaster;
		if($this->CMID == 1):
			$queryData['select'] = "item_master.*,item_master.price1 as price,unit_master.unit_name,item_category.category_name";
        else:
			$queryData['select'] = "item_master.*,item_master.price2 as price,unit_master.unit_name,item_category.category_name";
        endif;
        $queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		if(!empty($data['item_type'])){ $queryData['where']['item_master.item_type'] = $data['item_type'];}	

		if(!empty($data['item_name']))
			$queryData['columnSearch']['item_master.item_name'] = $data['item_name'];

		if(!empty($data['category_name']))
			$queryData['columnSearch']['item_category.category_name'] = $data['category_name'];

		if(empty($data['item_code']) AND empty($data['item_name']) AND empty($data['category_name'])){
			$queryData['length'] = (isset($data['length']) && !empty($data['length']))?$data['length']:50;
			$queryData['start'] = (isset($data['start']) && $data['start'] != "")?$data['start']:0;
		}
			
		if(isset($data['length']) && !empty($data['length']))
			$queryData['length'] = $data['length'];

		if(isset($data['start']) && $data['start'] != "")
			$queryData['start'] = $data['start'];
		
		return $this->rows($queryData);
	}

	public function getCount($item_type = 1){
		$queryData['tableName'] = $this->itemMaster;
        $queryData['select'] = "item_master.*,unit_master.unit_name,item_category.category_name";
        $queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		if(!empty($item_type)){$queryData['where']['item_master.item_type'] = $item_type;}
		
		return $this->numRows($queryData);
	}
    
    public function getItemListOnSearch($data){ 
		
		$data['tableName'] = $this->itemMaster;
        if($this->CMID == 1):
            $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price1 as price,item_master.wholesale1,item_master.wholesale2,item_master.unit_id,item_master.min_qty,item_master.qty,item_master.opening_qty1 as opening_qty, item_master.item_image,unit_master.unit_name,item_category.category_name";
        else:
            $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price2 as price,item_master.wholesale1,item_master.wholesale2,item_master.unit_id,item_master.min_qty,item_master.qty,item_master.opening_qty2 as opening_qty, item_master.item_image,unit_master.unit_name,item_category.category_name";
        endif;
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		if(!empty($data['item_type'])){ $data['where']['item_master.item_type'] = $data['item_type'];}
		
		$data['searchCol'][] ="";
		$data['searchCol'][] = "item_master.item_name";
		$data['searchCol'][] = "item_master.item_code";
		$data['searchCol'][] = "item_category.category_name";
		$data['searchCol'][] ="";

		$columns =array('','item_master.item_name','item_category.category_name','');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		
        $result= $this->pagingRows($data);
		// print_r($data);exit;
		return $result;
    }
	/* API Function End */
	
	
	public function getDynamicItemList($postData)
	{

		if (empty($postData['party_id'])) {
			$postData['party_id'] = 0;
		}

		$queryData['tableName'] = $this->itemMaster;
		$queryData['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id, item_master.description, item_master.item_type,item_master.hsn_code, item_master.gst_per, item_master.price, item_master.unit_id, item_master.qty,item_master.item_image, unit_master.unit_name";
		$queryData['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
		$queryData['leftJoin']['item_category'] = "item_master.category_id = item_category.id";

		if (!empty($postData['searchTerm'])) {
			$queryData['like']['item_master.item_code'] = $postData['searchTerm'];
			$queryData['like']['item_master.item_name'] = $postData['searchTerm'];
		}
		if (!empty($postData['item_type'])) {
			$queryData['where_in']['item_master.item_type'] = $postData['item_type'];
		}
		if (!empty($postData['category_id'])) {
			$queryData['where_in']['item_master.category_id'] = $postData['category_id'];
		}
		if (!empty($postData['family_id'])) {
			$queryData['where_in']['item_master.family_id'] = $postData['family_id'];
		}
		if (!empty($postData['party_id'])) {
			$queryData['where']['item_master.party_id'] = $postData['party_id'];
		}

		$itemData = $this->rows($queryData);

		$htmlOptions = array();
		$i = 0;
		$htmlOptions[] = ['id' => "", 'text' => "Select Item", 'row' => json_encode(array())];
		if (!empty($itemData)) :
			foreach ($itemData as $row) :
				$itmName = (!empty($row->item_code)) ? "[" . $row->item_code . "] " . $row->item_name : $row->item_name;
				if (!empty($postData['default_val']) && $postData['default_val'] == $row->id) :
					$htmlOptions[] = ['id' => $row->id, 'text' => $itmName, 'row' => json_encode($row), "selected" => true];
				else :
					$htmlOptions[] = ['id' => $row->id, 'text' => $itmName, 'row' => json_encode($row)];
				endif;
				$i++;
			endforeach;
		endif;
		return $htmlOptions;
	}

	public function getDynamicItemListOnLocation($postData)
	{

		if (empty($postData['party_id'])) {$postData['party_id'] = 0;}
		
		$queryData['tableName'] = $this->stockTrans;
		$queryData['select'] = "SUM(stock_transaction.qty) as qty,stock_transaction.item_id as id,item_master.item_code,item_master.item_name,item_category.category_name,item_master.category_id";
		$queryData['leftJoin']['item_master'] = "item_master.id = stock_transaction.item_id";
		$queryData['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$queryData['where']['stock_transaction.location_id'] = $postData['location_id'];
		$queryData['group_by'][] = 'stock_transaction.item_id';

		if (!empty($postData['searchTerm'])) {
			$queryData['like']['item_master.item_code'] = $postData['searchTerm'];
			$queryData['like']['item_master.item_name'] = $postData['searchTerm'];
		}
		if (!empty($postData['item_type'])) {
			$queryData['where_in']['item_master.item_type'] = $postData['item_type'];
		}
		if (!empty($postData['category_id'])) {
			$queryData['where_in']['item_master.category_id'] = $postData['category_id'];
		}
		
		$itemData = $this->rows($queryData);
		$htmlOptions = array();
		$i = 0;
		$htmlOptions[] = ['id' => "", 'text' => "Select Item", 'row' => json_encode(array())];
		if (!empty($itemData)) :
			foreach ($itemData as $row) :
				if ($row->qty > 0) :
				    $itmName = (!empty($row->item_code)) ? "[" . $row->item_code . "] " . $row->item_name : $row->item_name;
					if (!empty($postData['default_val']) && $postData['default_val'] == $row->id) :
						$htmlOptions[] = ['id' => $row->id, 'text' => $itmName, 'row' => json_encode($row), "selected" => true];
					else :
						$htmlOptions[] = ['id' => $row->id, 'text' => $itmName, 'row' => json_encode($row)];
					endif;
					$i++; 
				endif;
			endforeach;
		endif;
		return $htmlOptions;
	}
	
	/*** Get Scanned Item Data ***/
    public function getScannedItem($id){
        $data['tableName'] = $this->itemMaster;
        if($this->CMID == 1):
            $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price1 as price,item_master.unit_id,item_master.min_qty,item_master.qty,item_master.opening_qty1 as opening_qty, item_master.item_image,unit_master.unit_name,item_category.category_name";
        else:
            $data['select'] = "item_master.id,item_master.item_code,item_master.item_name,item_master.category_id,item_master.description, item_master.item_type,item_master.hsn_code,item_master.gst_per,item_master.price2 as price,item_master.unit_id,item_master.min_qty,item_master.qty,item_master.opening_qty2 as opening_qty, item_master.item_image,unit_master.unit_name,item_category.category_name";
        endif;
        $data['leftJoin']['unit_master'] = "unit_master.id = item_master.unit_id";
        $data['leftJoin']['item_category'] = "item_category.id = item_master.category_id";
		$data['where']['item_master.id'] = $id;
		
        $result= $this->row($data);
		return $result;
    }

    public function getHsnList($postData){
        $queryData['tableName'] = 'hsn_master';
		if (!empty($postData['searchTerm'])) {
	        $queryData['like']['hsn_master.hsn'] = $postData['searchTerm'];
		}
		$hsnData = $this->rows($queryData);

		$htmlOptions = array();
		$i = 0;
		$htmlOptions[] = ['id' => "", 'text' => "Select HSN"];
		if (!empty($hsnData)) :
			foreach ($hsnData as $row) :
				if (!empty($postData['default_val']) && $postData['default_val'] == $row->hsn) :
					$htmlOptions[] = ['id' => $row->hsn, 'text' => $row->hsn, "selected" => true];
				else :
					$htmlOptions[] = ['id' => $row->hsn, 'text' => $row->hsn];
				endif;
				$i++;
			endforeach;
		endif; 
		return $htmlOptions;
    }
    
    public function getItemWiseHsnList(){
        $data['tableName'] = $this->itemMaster;
        $data['select'] = "DISTINCT(hsn_code)";
        return $this->rows($data);
    }
}
?>