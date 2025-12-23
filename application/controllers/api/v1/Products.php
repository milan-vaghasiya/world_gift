<?php
class Products extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }
    public function getItemList($off_set=-1){
        $limit = (isset($_REQUEST['per_page']) && !empty($_REQUEST['per_page']))?$_REQUEST['per_page']:10;
        $search = (isset($_REQUEST['search']) && !empty($_REQUEST['search']))?$_REQUEST['search']:"";
        $item_type = (isset($_REQUEST['item_type']) && !empty($_REQUEST['item_type']))?$_REQUEST['item_type']:1;
        $postData = ['off_set'=>$off_set,'limit'=>$limit,'search'=>$search,'item_type'=>$item_type];
        $this->data['itemList'] = $this->item->getItemList_api($postData);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }

    public function getItemData(){
        $id = $this->input->post('item_id');
        $this->data['itemData'][] = $this->item->getItemData($id);
        $this->printJson(['status'=>1,'message'=>'Recored found.','field_error'=>0,'field_error_message'=>null,'data'=>$this->data]);
    }

    public function transferItem1(){
        $data = $this->input->post();

        if(empty($data['scanned_cm_id']))
            $this->printJson(['status'=>0,'message'=>'Please scan qr code.','field_error'=>0,'field_error_message'=>null,'data'=>null]); 

        if(empty($data['item_id']))
            $this->printJson(['status'=>0,'message'=>'Please select items.','field_error'=>0,'field_error_message'=>null,'data'=>null]); 

        if(!empty($data['scanned_cm_id'])):
            if($data['scanned_cm_id'] == $this->cm_id):
                $this->printJson(['status'=>0,'message'=>'Invalid','field_error'=>0,'field_error_message'=>null,'data'=>null]); 
            endif;
        endif;

        try{        
            $this->db->trans_begin();

            $itemData = array();$total_amount = 0;$total_gst_amount=0;$total_net_amount = 0;
            foreach($data['item_id'] as $key=>$value):
                $productData = $this->db->select("item_master.*,unit_master.unit_name")->join('unit_master',"item_master.unit_id = unit_master.id","left")->where('item_master.id',$value)->get('item_master')->row();

                $gst_per = $productData->gst_per;
                $amount = round(($data['qty'][$key] * $productData->price),2);
                $gst_amount = round((($amount * $gst_per) / 100),2);
                $net_amount = round(($amount + $gst_amount),2);

                $total_amount += $amount;
                $total_gst_amount += $gst_amount;
                $total_net_amount += $net_amount;
                $itemData[] = [
                    'item_id' => $value,
                    'item_name' => $productData->item_name,
                    'item_type' => $productData->item_type,
                    'item_code' => $productData->item_code,
                    'item_desc' => $productData->description,
                    'unit_id' => $productData->unit_id,
                    'unit_name' => $productData->unit_name,
                    'location_id' => $this->RTD_STORE->id,
                    'batch_no' => "General Batch",
                    'hsn_code' => $productData->hsn_code,
                    'qty' => $data['qty'][$key],
                    'stock_eff' => 1,
                    'price' => $productData->price,
                    'amount' => $amount,
                    'taxable_amount' => $amount,				
                    'gst_per' => $gst_per,
                    'gst_amount' => $gst_amount,
                    'igst_per' => $gst_per,
                    'igst_amount' => $gst_amount,
                    'sgst_per' => round(($gst_per/2),2),
                    'sgst_amount' => round(($gst_amount/2),2),
                    'cgst_per' => round(($gst_per/2),2),
                    'cgst_amount' => round(($gst_amount/2),2),
                    'disc_per' => 0,
                    'disc_amount' => 0,
                    'item_remark' => "",
                    'net_amount' => $net_amount,
                ];
            endforeach;

            $this->db->where('party_cm_id',$data['scanned_cm_id']);
            $this->db->where('party_category',3);
            $this->db->where('is_delete',0);
            $partyData = $this->db->get('party_master')->row();

            $party_state_code = (!empty($partyData->gstin))?substr($partyData->gstin,0,2):"";
            $transNo = getPrefixNumber($this->transModel->getTransPrefix(12),$this->transModel->nextTransNo(12));
            $total_nt_amt = round($total_net_amount,0,PHP_ROUND_HALF_UP);
            $round_off_amt = $total_nt_amt - $total_net_amount;
            $masterData = [
                'entry_type' => 12,
                'trans_no' => $this->transModel->nextTransNo(12),
                'trans_prefix' => $this->transModel->getTransPrefix(12),
                'trans_number' => $transNo,			
                'trans_date' => date('Y-m-d'),
                'party_id' => $partyData->id,
                'party_name' => $partyData->party_name,
                'party_state_code' => $party_state_code,
                'gstin' => $partyData->gstin,
                'gst_applicable' => 1,
                'gst_type' => 1,
                'doc_no' => $transNo,
                "doc_date" => date('Y-m-d'),
                'total_amount' => round($total_amount,2),
                'taxable_amount' => round($total_amount,2),
                'gst_amount' => round($total_gst_amount,2),
                'igst_amount' => round($total_gst_amount,2),
                'sgst_amount' => round(($total_gst_amount/2),2),
                'cgst_amount' => round(($total_gst_amount/2),2),
                'apply_round'=>1,
                'round_off_amount' => $round_off_amt,
                'net_amount' => $total_nt_amt,
                'vou_name_s' => getVoucherNameShort(12),
                'vou_name_l' => getVoucherNameLong(12),
                'ledger_eff' => 1,
                'created_by' => $this->loginId,
                'cm_id' => $this->cm_id
            ];

            $this->db->insert('trans_main',$masterData);
            $purchaseId = $this->db->insert_id();

            foreach($itemData as $row):
                $row['entry_type'] = $masterData['entry_type'];
                $row['trans_main_id'] = $purchaseId;
                $row['cm_id'] = $masterData['cm_id'];
                $row['created_by'] = $masterData['created_by'];
                $this->db->insert('trans_child',$row);
                $transId = $this->db->insert_id();

                $stockTransData = [
                    'location_id' => $row['location_id'],
                    'trans_type' => 1,
                    'item_id' => $row['item_id'],
                    'qty' => $row['qty'],
                    'ref_type' => 2,
                    'ref_id' => $purchaseId,
                    'ref_no' => $masterData['trans_number'],
                    'trans_ref_id' => $transId,
                    'ref_date' => $masterData['trans_date'],
                    'cm_id' => $masterData['cm_id'],
                    'created_by' => $masterData['created_by']
                ];
                $this->db->insert('stock_transaction',$stockTransData);
            endforeach;

            $this->db->where('cm_id',$data['scanned_cm_id']);
            $this->db->where('party_cm_id',$this->cm_id);
            $this->db->where('party_category',1);
            $this->db->where('is_delete',0);
            $partyData = $this->db->get('party_master')->row();

            $party_state_code = (!empty($partyData->gstin))?substr($partyData->gstin,0,2):"";
            $transNo = getPrefixNumber($this->transModel->getTransPrefix(6),$this->transModel->nextTransNo(6));
            $masterData['entry_type'] = 6;
            $masterData['trans_no'] = $this->transModel->nextTransNo(6);
            $masterData['trans_prefix'] = $this->transModel->getTransPrefix(6);
            $masterData['trans_number'] = $transNo;
            $masterData['party_id'] = $partyData->id;
            $masterData['party_name'] = $partyData->party_name;
            $masterData['party_state_code'] = $party_state_code;
            $masterData['gstin'] = $partyData->gstin;
            $masterData['vou_name_s'] = getVoucherNameShort(6);
            $masterData['vou_name_l'] = getVoucherNameLong(6);
            $masterData['created_by'] = 0;
            $masterData['cm_id'] = $data['scanned_cm_id'];

            $this->db->insert('trans_main',$masterData);
            $salesId = $this->db->insert_id();

            $transId = 0;
            foreach($itemData as $row):
                $row['entry_type'] = $masterData['entry_type'];
                $row['trans_main_id'] = $salesId;
                $row['cm_id'] = $masterData['cm_id'];
                $row['created_by'] = $masterData['created_by'];
                $this->db->insert('trans_child',$row);
                $transId = $this->db->insert_id();

                $stockTransData = [
                    'location_id' => $row['location_id'],
                    'trans_type' => -1,
                    'item_id' => $row['item_id'],
                    'qty' => $row['qty'],
                    'ref_type' => 5,
                    'ref_id' => $salesId,
                    'ref_no' => $masterData['trans_number'],
                    'trans_ref_id' => $transId,
                    'ref_date' => $masterData['trans_date'],
                    'cm_id' => $masterData['cm_id'],
                    'created_by' => $masterData['created_by']
                ];
                $this->db->insert('stock_transaction',$stockTransData);
            endforeach;

           
            if ($this->db->trans_status() !== FALSE):
				$this->db->trans_commit();
                $this->printJson(['status'=>1,'message'=>'Stock Transferd successfully.','field_error'=>0,'field_error_message'=>null]);
			endif;
		}catch(\Exception $e){
			$this->db->trans_rollback();
			$this->printJson(['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null]);
		}
    }
}
?>