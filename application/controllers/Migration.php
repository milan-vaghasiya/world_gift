<?php

class Migration extends MY_Controller{
    public function __construct(){
        parent::__construct();
        //echo "Sorry, You have no permission to migrate.";exit;
    }
    
    public function defualtLedger($cm_id){
        $accounts = [
            ['name' => 'Sales Account', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESACC'],
            
            ['name' => 'Sales Account GST', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESGSTACC'],
            
            ['name' => 'Sales Account Tax Free', 'group_name' => 'Sales Account', 'group_code' => 'SA', 'system_code' => 'SALESTFACC'],
            
            ['name' => 'CGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTOPACC'],
            
            ['name' => 'SGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTOPACC'],
            
            ['name' => 'IGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTOPACC'],
            
            ['name' => 'UTGST (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTOPACC'],
            
            ['name' => 'CESS (O/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON SALES', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'Purchase Account', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURACC'],
            
            ['name' => 'Purchase Account GST', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURGSTACC'],
            
            ['name' => 'Purchase Account Tax Free', 'group_name' => 'Purchase Account', 'group_code' => 'PA', 'system_code' => 'PURTFACC'],
            
            ['name' => 'CGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'CGSTIPACC'],
            
            ['name' => 'SGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'SGSTIPACC'],
            
            ['name' => 'IGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'IGSTIPACC'],
            
            ['name' => 'UTGST (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => 'UTGSTIPACC'],
            
            ['name' => 'CESS (I/P)', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TCS ON PURCHASE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'TDS RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST PAYABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'GST RECEIVABLE', 'group_name' => 'Duties & Taxes', 'group_code' => 'DT', 'system_code' => ''],
            
            ['name' => 'ROUNDED OFF', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => 'ROFFACC'],
            
            ['name' => 'CASH ACCOUNT', 'group_name' => 'Cash-In-Hand', 'group_code' => 'CS', 'system_code' => 'CASHACC'],
            
            ['name' => 'ELECTRICITY EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'OFFICE RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'GODOWN RENT EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TELEPHONE AND INTERNET CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PETROL EXP', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALES INCENTIVE', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'INTEREST PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'INTEREST RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'SAVING BANK INTEREST', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT RECEIVED', 'group_name' => 'Income (Indirect)', 'group_code' => 'II', 'system_code' => ''],
            
            ['name' => 'DISCOUNT PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SUSPENSE A/C', 'group_name' => 'Suspense A/C', 'group_code' => 'AS', 'system_code' => ''],
            
            ['name' => 'PROFESSIONAL FEES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'AUDIT FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'ACCOUNTING CHARGES PAID', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'LEGAL FEE', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'SALARY', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'WAGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'FREIGHT CHARGES', 'group_name' => 'Expenses (Direct)', 'group_code' => 'ED', 'system_code' => ''],
            
            ['name' => 'PACKING AND FORWARDING CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'REMUNERATION TO PARTNERS', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'TRANSPORTATION CHARGES', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'DEPRICIATION', 'group_name' => 'Expenses (Indirect)', 'group_code' => 'EI', 'system_code' => ''],
            
            ['name' => 'PLANT AND MACHINERY', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FURNITURE AND FIXTURES', 'group_name' => 'Fixed Assets', 'group_code' => 'FA', 'system_code' => ''],
            
            ['name' => 'FIXED DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => ''],
            
            ['name' => 'RENT DEPOSITS', 'group_name' => 'Deposits (Assets)', 'group_code' => 'DA', 'system_code' => '']	
        ];
        try{
            $this->db->trans_begin();
            $accounts = (object) $accounts;
            foreach($accounts as $row):
                $row = (object) $row;
                $groupData = $this->db->where('group_code',$row->group_code)->where('cm_id',$cm_id)->get('group_master')->row();
                $ledgerData = [
                    'party_category' => 4,
                    'group_name' => $groupData->name,
                    'group_code' => $groupData->group_code,
                    'group_id' => $groupData->id,
                    'party_name' => $row->name,                    
                    'system_code' => $row->system_code
                ];
				$ledgerData['cm_id'] = $cm_id;
				$this->db->insert('party_master',$ledgerData);
				/*
                $this->db->where('party_name',$row->name);
                $this->db->where('is_delete',0);
                $this->db->where('party_category',4);
                $checkLedger = $this->db->get('party_master');
								
                if($checkLedger->num_rows() > 0):
                    $id = $checkLedger->row()->id;
                    $this->db->where('id',$id);
                    $this->db->update('party_master',$ledgerData);
                else:
					$ledgerData['cm_id'] = 1;
                    $this->db->insert('party_master',$ledgerData);
                endif;*/
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Defualt Ledger Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function updateLedgerClosingBalance($cm_id = 0){
        try{
            $this->db->trans_begin();

            if(empty($cm_id)):
                echo "cm id not found.";exit;
            endif;

            $this->db->where('cm_id',$cm_id);
            $this->db->where('is_delete',0);
            $partyData = $this->db->get("party_master")->result();
            foreach($partyData as $row):
                //Set oprning balance as closing balance
                $this->db->where('id',$row->id);
                $this->db->update('party_master',['cl_balance'=>$row->opening_balance ]);

                //get ledger trans amount total
                $this->db->select("SUM(amount * p_or_m) as ledger_amount");
                $this->db->where('vou_acc_id',$row->id);
                $this->db->where('is_delete',0);
                $ledgerTrans = $this->db->get('trans_ledger')->row();
                $ledgerAmount = (!empty($ledgerTrans->ledger_amount))?$ledgerTrans->ledger_amount:0;

                //update colsing balance
                $this->db->set("cl_balance","`cl_balance` + ".$ledgerAmount,FALSE);
                $this->db->where('id',$row->id);
                $this->db->update('party_master');
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Closing Balance Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migratePurchaseInvoice($cmid){
        try{
            $this->db->trans_begin();
            $this->db->select('trans_child.*,trans_main.id as ref_id,trans_main.trans_date,trans_main.trans_number');
            $this->db->where('trans_child.cm_id',$cmid);
            $this->db->where('trans_child.entry_type',12);
            $this->db->where('trans_child.is_delete',0);
            $this->db->join('trans_main','trans_main.id=trans_child.trans_main_id');
            $purchaseData = $this->db->get("trans_child")->result();
            $i=1;
            foreach($purchaseData as $row):
                $updateData = Array();
				if($cmid==1){$updateData['location_id'] = 11;}else{$updateData['location_id'] = 141;}
                $updateData['item_id'] = $row->item_id;
                $updateData['qty'] = $row->qty;
                $updateData['trans_type'] = 1;
                $updateData['ref_type'] = 2;
                $updateData['ref_id'] = $row->ref_id;
                $updateData['trans_ref_id'] = $row->id;
                $updateData['ref_no'] = $row->trans_number;
                $updateData['ref_date'] = date('Y-m-d',strtotime($row->trans_date));
                $updateData['cm_id'] = $cmid;
                $updateData['created_by'] = $row->created_by;
				print_r(json_encode($updateData).'='.$i++.'<br>');
				
                //$this->db->insert('stock_transaction',$updateData);
            endforeach;
            //print_r(count($purchaseData));
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo count($purchaseData)." Purchase Invoices Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
	
	public function migrateSalesInvoice($cmid){
        try{
            $this->db->trans_begin();
            $this->db->select('trans_child.*,trans_main.id as ref_id,trans_main.trans_date,trans_main.trans_number');
            $this->db->where('trans_main.cm_id',$cmid);
            $this->db->where('trans_main.entry_type',6);
            $this->db->where('trans_child.is_delete',0);
            $this->db->join('trans_main','trans_main.id=trans_child.trans_main_id');
            $salesData = $this->db->get("trans_child")->result();
            $i=1;
            foreach($salesData as $row):
                $updateData = Array();
				if($cmid==1){$updateData['location_id'] = 11;}else{$updateData['location_id'] = 141;}
                $updateData['item_id'] = $row->item_id;
                $updateData['qty'] = "-".$row->qty;
                $updateData['trans_type'] = 2;
                $updateData['ref_type'] = 5;
                $updateData['ref_id'] = $row->ref_id;
                $updateData['trans_ref_id'] = $row->id;
                $updateData['ref_no'] = $row->trans_number;
                $updateData['ref_date'] = date('Y-m-d',strtotime($row->trans_date));
                $updateData['cm_id'] = $cmid;
                $updateData['created_by'] = $row->created_by;
				print_r(json_encode($updateData).'= '.$i++.'<br>');
				
                //$this->db->insert('stock_transaction',$updateData);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Sales Invoices Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
	
	/*public function migrateSalesInvoice($cmid){
        try{
            $this->db->trans_begin();
            $this->db->select('trans_child.*,trans_main.id as ref_id,trans_main.trans_date,trans_main.trans_number');
            $this->db->where('trans_main.cm_id',$cmid);
            $this->db->where('trans_main.entry_type',6);
            $this->db->where('trans_child.is_delete',0);
            $this->db->join('trans_main','trans_main.id=trans_child.trans_main_id');
            $salesData = $this->db->get("trans_child")->result();
            
            foreach($salesData as $row):
                $updateData = Array();
				if($cmid==1){$updateData['location_id'] = 11;}else{$updateData['location_id'] = 141;}
                $updateData['item_id'] = $row->item_id;
                $updateData['qty'] = $row->qty;
                $updateData['trans_type'] = 2;
                $updateData['ref_type'] = 5;
                $updateData['ref_id'] = $row->ref_id;
                $updateData['trans_ref_id'] = $row->id;
                $updateData['ref_no'] = $row->trans_number;
                $updateData['ref_date'] = date('Y-m-d',strtotime($row->trans_date));
                $updateData['cm_id'] = $cmid;
                $updateData['created_by'] = $row->created_by;
				//print_r(json_encode($updateData).'<br>');
				
                //$this->db->insert('stock_transaction',$updateData);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Sales Invoices Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    /*public function migratePurchaseInvoice($cmid){
        try{
            $this->db->trans_begin();
            $this->db->select('trans_child.*,trans_main.id as ref_id,trans_main.trans_date,trans_main.trans_number');
            $this->db->where('trans_main.cm_id',$cmid);
            $this->db->where('trans_main.entry_type',12);
            $this->db->where('trans_child.is_delete',0);
            $this->db->join('trans_main','trans_main.id=trans_child.trans_main_id');
            $purchaseData = $this->db->get("trans_child")->result();
            
            foreach($purchaseData as $row):
                $updateData = Array();
				if($cmid==1){$updateData['location_id'] = 11;}else{$updateData['location_id'] = 141;}
                $updateData['item_id'] = $row->item_id;
                $updateData['qty'] = $row->qty;
                $updateData['trans_type'] = 1;
                $updateData['ref_type'] = 2;
                $updateData['ref_id'] = $row->ref_id;
                $updateData['trans_ref_id'] = $row->id;
                $updateData['ref_no'] = $row->trans_number;
                $updateData['ref_date'] = date('Y-m-d',strtotime($row->trans_date));
                $updateData['cm_id'] = $cmid;
                $updateData['created_by'] = $row->created_by;
				//print_r(json_encode($updateData).'<br>');
				
                //$this->db->insert('stock_transaction',$updateData);
            endforeach;
            //print_r(count($purchaseData));
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo count($purchaseData)." Sales Invoices Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    /*public function updateItemOpeningStock(){
        try{
            $this->db->trans_begin();
            $this->db->where('category_id >= ',19);
            $this->db->where('category_id <= ',36);
            $itemData = $this->db->get("item_master")->result();
            foreach($itemData as $row):
                $updateData = Array();
                $updateData['location_id'] = 11;
                $updateData['item_id'] = $row->id;
                $updateData['qty'] = $row->opening_qty1;
                $updateData['ref_type'] = -1;
                $updateData['ref_date'] = '2022-04-01';
                $updateData['ref_batch'] = 'OS/22-23';
                $updateData['cm_id'] = 1;
                //$this->db->insert('stock_transaction',$updateData);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Opening Stock Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }*/
    
    public function updateItemOpeningStock(){
        try{
            $this->db->trans_begin();
            
			/*$this->db->select("os.id, item_master.id as item_id");
			$this->db->join('item_master','os.item_name = item_master.item_name','LEFT');
			$this->db->where('item_master.is_delete',0);
			$this->db->where('os.cm_id',2);
			$this->db->order_by('os.id');*/
			//$this->db->where('os.cm_id',2);
            $itemData = $this->db->get("os")->result();
           
			$i=1;
            foreach($itemData as $row): 
				/*$updateData = Array();
                $updateData['id'] = $row->id;
                $updateData['item_id'] = $row->item_id;
                print_r(json_encode($updateData).'='.$i++.'<br>');*/
				//$this->db->where('id',$row->id);
				//$this->db->update('os',$updateData);
				
                $updateData = Array();
                $updateData['location_id'] = 11;
                $updateData['item_id'] = $row->item_id;
                $updateData['qty'] = $row->qty;
                $updateData['ref_type'] = -1;
                $updateData['ref_date'] = '2022-04-01';
                $updateData['ref_batch'] = 'OS/22-23';
                $updateData['cm_id'] = $row->cm_id;
                print_r(json_encode($updateData).'='.$i++.'<br>');
                //$this->db->insert('stock_transaction',$updateData);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Opening Stock Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function updateTransChildOrgPrice(){
        try{
            $this->db->trans_begin();
            
            $result = $this->db->select('id,item_id,cm_id')->where('is_delete',0)->where('entry_type',6)->get('trans_child')->result();
            
            foreach($result as $row):
                $itemData = $this->db->where('id',$row->item_id)->get('item_master')->row();
                
                $org_price = 0;
                $org_price = ($row->cm_id==1)?$itemData->price1:$itemData->price2;
                $this->db->where('id',$row->id);
                $this->db->update('trans_child',['org_price'=>$org_price]);
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Org Price Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migrateGSTINTransmain(){
        try{
            $this->db->trans_begin();
            
            $this->db->select('trans_main.id,party_master.gstin,trans_main.cm_id');
            $this->db->join('party_master','party_master.id = trans_main.party_id','LEFT');
			//$this->db->where('trans_main.is_delete',0);
			$invoiceData = $this->db->get("trans_main")->result();
            if(!empty($invoiceData))
            {
                foreach($invoiceData as $row)
                {
                    $updateData = Array();
                    if(!empty($row->gstin))
                    {
                        $updateData['gstin'] = $row->gstin;
                        $updateData['party_state_code'] = substr($row->gstin, 0, 2);
                    }
                    else
                    {
                        $updateData['gstin'] = '';
                        $updateData['party_state_code'] = 24;
                    }
                    //if($row->cm_id==1){$updateData['memo_type'] = 'Cash';}
                    print_r($updateData);print_r('<br>');
                    $this->db->where('id',$row->id);
                    //$this->db->update('trans_main',$updateData);
                }
            }
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Org Price Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function updateTransChildIncentive(){
        try{
            $this->db->trans_begin();
            $result = $this->db->select('id,item_id,cm_id')->where('is_delete',0)->where('entry_type',6)->get('trans_child')->result();
            foreach($result as $row):
                $itemData = $this->db->where('id',$row->item_id)->get('item_master')->row();
                $updateData = Array();
                $updateData = ['incentive'=>$itemData->incentive];
                print_r($updateData);print_r('<br>');
                //$this->db->where('id',$row->id);
                //$this->db->update('trans_child',$updateData);
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Incentive Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migratePaytmInvoice(){
        try{
            $this->db->trans_begin();

            $invoiceData = $this->db->query("SELECT * FROM `trans_main` WHERE entry_type = 15 AND party_id = 112 AND is_delete = 0 AND ref_id > 0 AND cm_id = 1 AND vou_acc_id = 172")->result();

            foreach($invoiceData as $row):
                //312 PAYTM 
                /* $this->db->where('id',$row->ref_id);
                $this->db->update('trans_main',['memo_type'=>'DEBIT','party_id'=>312,'opp_acc_id'=>312]); */

               /*  $this->db->where('trans_main_id',$row->ref_id);
                $this->db->where('vou_acc_id',112);
                $this->db->update('trans_ledger',['vou_acc_id'=>312]);

                $this->db->where('trans_main_id',$row->ref_id);
                $this->db->where('opp_acc_id',112);
                $this->db->update('trans_ledger',['opp_acc_id'=>312]); */

                $this->db->where('id',$row->id);
                $this->db->update('trans_main',['party_id'=>312,'opp_acc_id'=>312]);

                $this->db->where('trans_main_id',$row->id);
                $this->db->where('vou_acc_id',112);
                $this->db->update('trans_ledger',['vou_acc_id'=>312]);

                $this->db->where('trans_main_id',$row->id);
                $this->db->where('opp_acc_id',112);
                $this->db->update('trans_ledger',['opp_acc_id'=>312]);
            endforeach;

            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Paytm Invoice Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

    public function migratePaymentVoucher(){
       try{
            $this->db->trans_begin();

            $this->db->reset_query();

            $this->db->where('is_delete',0);
            $this->db->where('entry_type',6);
            $this->db->where('cm_id',1);
            $this->db->where('party_id',112);
            $this->db->update('trans_main',['memo_type'=>"CASH"]);

            $this->db->where('is_delete',0);
            $this->db->where('entry_type',6);
            $this->db->where('cm_id',1);
            $this->db->where('party_id !=',112);
            $this->db->update('trans_main',['memo_type'=>"DEBIT"]);

            $this->db->where('is_delete',0);
            $this->db->where('entry_type',15);
            $this->db->where('cm_id',1);
            $this->db->delete(["trans_main","trans_ledger"]);

            $this->db->reset_query();

            $this->db->where('is_delete',1);
            $this->db->where('entry_type',6);
            $this->db->where('cm_id',1);
            $deletedInvoice = $this->db->get('trans_main')->result();

            foreach($deletedInvoice as $row):
                $this->db->reset_query();

                $this->db->where('trans_main_id',$row->id);
                $this->db->delete(['trans_ledger','trans_expense']);
            endforeach;

            $this->db->reset_query();

            $this->db->where('is_delete',0);
            $this->db->where('entry_type',6);
            $this->db->where('cm_id',1);
            $this->db->where('memo_type',"CASH");
            $this->db->order_by('trans_no',"ASC");
            $invoiceData = $this->db->get('trans_main')->result();

            foreach($invoiceData as $row):
                $this->db->reset_query();

                $trans_prefix = $this->transModel->getTransPrefix(15);
			    $trans_no = $this->transModel->nextTransNo(15);

                $vouData = [
                    'id' => "",
                    'ref_id' => $row->id,
                    'entry_type' => 15,
                    'trans_prefix'=>$trans_prefix,
                    'trans_no'=>$trans_no,
                    'trans_date' => date('Y-m-d',strtotime($row->trans_date)), 
                    'doc_no' => $row->trans_number,
                    'doc_date' => date('Y-m-d',strtotime($row->trans_date)),
                    'party_id' => $row->party_id,
                    'opp_acc_id' => $row->party_id,
                    'vou_acc_id' => 24,
                    'trans_mode' => "CASH",	
                    'net_amount' => $row->net_amount,	
                    'created_by' => $this->session->userdata('loginId'),
                    'cm_id' => 1,
                    'remark'=>''
                ];

                $this->paymentVoucher->save($vouData);
            endforeach;            

            $this->db->reset_query();
            $this->updateLedgerClosingBalance(1);
        
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "<br> Payment Voucher Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    //migration/migrateItemImages
    public function migrateItemImages(){
         try{
            $this->db->trans_begin();
            
            $dir = realpath(APPPATH . '../assets/uploads/product/menual_upload');
            $imageList = scandir($dir);
            //print_r($imageList);exit;
            $i=1;
            foreach($imageList as $img):
                
                if($i > 2):
                    $item_name = "";
                    $item_name = substr($img, 0, strrpos($img, '.'));
                    
                    $this->db->where('item_name',$item_name);
                    $itemData = $this->db->get('item_master')->row();
                    
                    if(!empty($itemData)):
                        $itmNM = strtolower(str_replace(" ","_",$img));
                        $this->db->where('id',$itemData->id);
                        $this->db->update('item_master',['item_image'=>$itmNM]);
                        
                        $new_dir = realpath(APPPATH . '../assets/uploads/product/');
                        
                        rename($dir."/".$img,$new_dir."/".$itmNM);
                    //else:
                        //print_r($img);
                    endif;
                    //print_r("<hr>");
                endif;
                $i++;
            endforeach;
            
            if($this->db->trans_status() !== FALSE):
                $this->db->trans_commit();
                echo "Item Image Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }
    
    public function migratePVDeleted(){
        try{
            $this->db->trans_begin();
            $this->db->where('is_delete',1);
            $this->db->where_in('entry_type',[12,13,14,15,16,17,18]);
            //$this->db->where('cm_id',1);
            $deletedInvoice = $this->db->get('trans_main')->result();

            foreach($deletedInvoice as $row):
                $this->db->reset_query();

                //$this->db->where('trans_main_id',$row->id);
                //$this->db->delete(['trans_ledger','trans_expense']);
            endforeach;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Migration Success Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	// Migrate Trans Prefix of Sales Invoice
	// Link : https://nativebittechnologies.com/world_gift/migration/migrateSINVPrefix/2/DEBIT
	public function migrateSINVPrefix($cmid,$memo_type="CASH"){
        try{
            $this->db->trans_begin();
			$this->db->reset_query();
            $this->db->select('trans_main.*');
            $this->db->where('trans_main.cm_id',$cmid);
            $this->db->where('trans_main.entry_type',6);
            $this->db->where('trans_main.is_delete',0);
            $this->db->where('trans_main.memo_type',$memo_type);
            $this->db->where('trans_main.trans_date > ','2023-03-31');
			$this->db->order_by('trans_main.id');
            $salesInvData = $this->db->get("trans_main")->result();
            $i=1;
            if($cmid==1):
                $prefix = ($memo_type=="DEBIT") ? 'WG/' : 'WGM/';
            else:
                $prefix = ($memo_type=="DEBIT") ? 'RJ/' : 'RJM/';
            endif;
            foreach($salesInvData as $row):
                $updateData = Array();
                $updateData['trans_prefix'] = $prefix;
                $updateData['trans_no'] = $i;
                $updateData['trans_number'] = $prefix.$i;
                $updateData['transport_name'] = $row->trans_no;
				print_r($updateData);print_r('<hr>');
				
				// Update Trans Ledger
				$this->db->reset_query();
                $this->db->where('trans_main_id',$row->id);
				$this->db->where('entry_type',6);
				$this->db->where('cm_id',$cmid);
				//$this->db->update('trans_ledger',['trans_number'=>$updateData['trans_number']]);
				
				// Update Stock Transaction
				$this->db->reset_query();
				$this->db->where('ref_type',5);
                $this->db->where('ref_id',$row->id);
				$this->db->where('cm_id',$cmid);
				//$this->db->update('stock_transaction',['ref_no'=>$updateData['trans_number']]);
				
				// Update Trans Main (Sales Invoice)
				$this->db->reset_query();
                $this->db->where('id',$row->id);
				//$this->db->update('trans_main',$updateData);
				
				$i++;
            endforeach;
			//exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Sales Invoices Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

	// Migrate Trans Prefix of Sales Invoice
	// Link : https://nativebittechnologies.com/world_gift/migration/migrateSINVNoEffect/2
	public function migrateSINVNoEffect($cmid){
        try{
            $this->db->trans_begin();
			$this->db->reset_query();
            $this->db->select('trans_main.*');
            $this->db->where('trans_main.cm_id',$cmid);
            $this->db->where('trans_main.entry_type',6);
            $this->db->where('trans_main.is_delete',0);
            //$this->db->where('trans_main.memo_type',$memo_type);
            $this->db->where('trans_main.trans_date >= ','2023-04-10');
            $this->db->where('trans_main.trans_date <= ','2023-04-28');
			$this->db->order_by('trans_main.id');
            $salesInvData = $this->db->get("trans_main")->result();

            $i=1;
            foreach($salesInvData as $row):
				
				print_r($row->id.' = '.$row->trans_number);print_r('<hr>');
				
				// Update Trans Ledger
				$this->db->reset_query();
                $this->db->where('trans_main_id',$row->id);
				$this->db->where('entry_type',6);
				$this->db->where('cm_id',$cmid);
				//$this->db->update('trans_ledger',['trans_number'=> $row->trans_number]);
				
				// Update Stock Transaction
				$this->db->reset_query();
				$this->db->where('ref_type',5);
                $this->db->where('ref_id',$row->id);
				$this->db->where('cm_id',$cmid);
				//$this->db->update('stock_transaction',['ref_no'=> $row->trans_number]);
				
				$i++;
            endforeach;
			exit;
            if($this->db->trans_status() !== FALSE):
                //$this->db->trans_commit();
                echo "Sales Invoices Migration Success.";
            endif;
        }catch(\Exception $e){
            $this->db->trans_rollback();
            echo $e->getMessage();exit;
        }
    }

}
?>