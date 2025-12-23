<?php
class AccountingReportModel extends MasterModel{

    public function getLedgerSummary($fromDate="1970-01-01",$toDate=""){
        $startDate = (!empty($fromDate))?$fromDate:$this->startYearDate;
        $endDate = (!empty($toDate))?$toDate:$this->endYearDate;
        $startDate = date("Y-m-d",strtotime($startDate));
        $endDate = date("Y-m-d",strtotime($endDate));

        $ledgerSummary = $this->db->query("SELECT lb.id as id, am.party_name as account_name,  CASE WHEN lb.op_balance > 0 THEN CONCAT(abs(lb.op_balance),' CR.') WHEN lb.op_balance < 0 THEN CONCAT(abs(lb.op_balance),' DR.') ELSE lb.op_balance END op_balance,am.group_name, lb.cr_balance, lb.dr_balance, CASE WHEN lb.cl_balance > 0 THEN CONCAT(abs(lb.cl_balance),' CR.') WHEN lb.cl_balance < 0 THEN CONCAT(abs(lb.cl_balance),' DR.') ELSE lb.cl_balance END as cl_balance 
        FROM (
            SELECT am.id, ((am.opening_balance) + SUM( CASE WHEN tl.trans_date < '".$startDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance) + SUM( CASE WHEN tl.trans_date <= '".$endDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
            WHERE am.is_delete = 0  AND am.cm_id='".$this->CMID."' GROUP BY am.id, am.opening_balance) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id WHERE am.is_delete = 0 AND am.cm_id='".$this->CMID."'
        ORDER BY am.party_name")->result();
        return $ledgerSummary;

        /* $queryData = array();
        $queryData['tableName'] = "(SELECT am.id, ((am.opening_balance) + SUM( CASE WHEN tl.trans_date < '".$startDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as op_balance, SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN tl.amount ELSE 0 END ELSE 0 END) as dr_balance, SUM( CASE WHEN tl.trans_date >= '".$startDate."' AND tl.trans_date <= '".$endDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN tl.amount ELSE 0 END ELSE 0 END) as cr_balance, ((am.op_balance) + SUM( CASE WHEN tl.trans_date <= '".$endDate."' THEN (tl.amount * tl.p_or_m) ELSE 0 END )) as cl_balance FROM party_master as am LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id WHERE am.is_delete = 0 GROUP BY am.id, am.opening_balance ) as lb";

        $queryData['select'] = "lb.id as id, am.name as account_name, lb.op_balance , lb.cr_balance , lb.dr_balance , abs(lb.cl_balance) as cl_balance";

        $queryData['leftJoin']['party_master as am'] = "lb.id = am.id";
        $queryData['order_by']['am.name'] = "ACS"; 
        $ledgerSummary = $this->rows($queryData);*/
    }

    public function getLedgerDetail($fromDate,$toDate,$acc_id){
        $ledgerTransactions = $this->db->query ("SELECT 
        tl.trans_main_id AS id, 
        tl.entry_type AS ent_type, 
        tl.trans_date AS trans_date, 
        tl.trans_number AS trans_number, 
        tl.vou_name_s AS vou_name_s, 
        am.party_name AS account_name, 
        tm.trans_number as bill_no,
        
        CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END AS dr_amount, 
        CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END AS cr_amount, 

        tl.remark AS remark 

        FROM ( trans_ledger AS tl LEFT JOIN party_master AS am ON am.id = tl.opp_acc_id ) 
        LEFT JOIN trans_main on trans_main.id = tl.trans_main_id
        LEFT JOIN trans_main as tm on tm.id = trans_main.ref_id
        WHERE tl.vou_acc_id = ".$acc_id." 
        AND tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."'  AND tl.cm_id='".$this->CMID."'
        ORDER BY tl.trans_date, tl.trans_number")->result();
        return $ledgerTransactions;
    }

    public function getLedgerBalance($fromDate,$toDate,$acc_id){
        $ledgerBalance = $this->db->query ("SELECT am.id,am.party_name AS account_name,((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance, 
        SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
        SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
        ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
        FROM party_master as am 
        LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
        LEFT JOIN currency as ccy ON am.currency = ccy.currency
        WHERE am.is_delete = 0  AND am.cm_id='".$this->CMID."'
        AND am.id = ".$acc_id."
        GROUP BY am.id, am.opening_balance")->row();
        $ledgerBalance->op_balance_type=(!empty($ledgerBalance->op_balance) && $ledgerBalance->op_balance > 0)?(($ledgerBalance->op_balance > 0)?'CR':''):(($ledgerBalance->op_balance < 0)?'DR':'');
        $ledgerBalance->cl_balance_type=(!empty($ledgerBalance->cl_balance) && $ledgerBalance->cl_balance > 0)?(($ledgerBalance->cl_balance > 0)?'CR':''):(($ledgerBalance->cl_balance < 0)?'DR':'');
        return $ledgerBalance;
    }

    public function getReceivable($fromDate,$toDate){
        $receivable = $this->db->query ("SELECT lb.id as id, am.party_name as account_name,am.group_name, abs(lb.cl_balance) as cl_balance
        FROM (
            SELECT am.id, ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
            LEFT JOIN currency as ccy ON am.currency = ccy.currency
            WHERE am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 AND am.cm_id='".$this->CMID."' GROUP BY am.id, am.opening_balance 
        ) as lb
        LEFT JOIN party_master as am ON lb.id = am.id 
        WHERE lb.cl_balance < 0 AND am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 AND am.cm_id='".$this->CMID."' ORDER BY am.party_name")->result();
        return $receivable;
    }

    public function getPayable($fromDate,$toDate){
        $payable = $this->db->query ("SELECT lb.id as id, am.party_name as account_name,am.group_name, abs(lb.cl_balance) as cl_balance

        FROM (
            SELECT am.id, ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
            LEFT JOIN currency as ccy ON am.currency = ccy.currency
            WHERE am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 AND am.cm_id='".$this->CMID."' GROUP BY am.id, am.opening_balance 
        ) as lb
        
        LEFT JOIN party_master as am ON lb.id = am.id 
        WHERE lb.cl_balance > 0 AND am.group_code IN ( 'SD','SC' ) AND am.is_delete = 0 AND am.cm_id='".$this->CMID."' ORDER BY am.party_name")->result();
        return $payable;
    }

    public function getBankCashBook($fromDate,$toDate,$groupCode){
        $bankCashBook = $this->db->query ("SELECT lb.id as id, am.party_name as account_name, am.group_name, lb.cr_balance, lb.dr_balance, 
        CASE WHEN lb.op_balance > 0 THEN CONCAT(abs(lb.op_balance),' CR.') WHEN lb.op_balance < 0 THEN CONCAT(abs(lb.op_balance),' DR.') ELSE lb.op_balance END op_balance,  
        CASE WHEN lb.cl_balance > 0 THEN CONCAT(abs(lb.cl_balance),' CR.') WHEN lb.cl_balance < 0 THEN CONCAT(abs(lb.cl_balance),' DR.') ELSE lb.cl_balance END as cl_balance 
        FROM (
            SELECT am.id, ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date < '".$fromDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as op_balance, 
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'DR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as dr_balance,
            SUM( CASE WHEN tl.trans_date >= '".$fromDate."' AND tl.trans_date <= '".$toDate."' THEN CASE WHEN tl.c_or_d = 'CR' THEN (tl.amount * tl.inrrate) ELSE 0 END ELSE 0 END) as cr_balance,
            ((am.opening_balance * ccy.inrrate) + SUM( CASE WHEN tl.trans_date <= '".$toDate."' THEN ((tl.amount * tl.p_or_m) * tl.inrrate) ELSE 0 END )) as cl_balance 
            FROM party_master as am 
            LEFT JOIN trans_ledger as tl ON am.id = tl.vou_acc_id 
            LEFT JOIN currency as ccy ON am.currency = ccy.currency
            WHERE am.is_delete = 0 AND am.cm_id='".$this->CMID."' AND am.group_code IN ('".$groupCode."') GROUP BY am.id, am.opening_balance
            ) as lb 
        LEFT JOIN party_master as am ON lb.id = am.id WHERE am.is_delete = 0 AND am.cm_id='".$this->CMID."'
        ORDER BY am.party_name")->result();
        //print_r($this->db->last_query()); exit;
        return $bankCashBook;
    }

    public function getAccountReportData($fromDate,$toDate,$entry_type){ 
        
        $accountReport = $this->db->query ("SELECT id,trans_number,doc_no,trans_date,party_id,party_name,gstin,currency,net_amount,(net_amount * inrrate) as net_amount_inr,vou_name_s,taxable_amount,cgst_amount,sgst_amount,igst_amount,(taxable_amount * inrrate) as taxable_amount_inr, created_by
        FROM trans_main 
        WHERE is_delete = 0
        AND entry_type IN (".$entry_type.")
        AND trans_date BETWEEN '".$fromDate."' AND '".$toDate."' AND cm_id='".$this->CMID."'
        ORDER BY trans_date")->result();
        return $accountReport;
    }

    public function getAccountReportDataHsnWise($fromDate,$toDate,$entry_type,$memo_type=""){
        $memoCondition = "";$groupBY = 'trans_main.trans_date,item_master.hsn_code';
        if(!empty($memo_type)){$memoCondition = " AND trans_main.memo_type = '".$memo_type."'";}

        //if(!empty($memo_type) && $entry_type == 12){$memoCondition = ($memo_type == "DEBIT")?" AND trans_main.gstin != ''":" AND trans_main.gstin = ''";}

        if(!empty($memo_type) AND $memo_type=='DEBIT'){$groupBY = 'trans_main.id,trans_main.trans_date,item_master.hsn_code';}

        $accountReport = $this->db->query ("SELECT trans_main.id, trans_main.trans_number, trans_main.doc_no, trans_main.trans_date, trans_main.party_id, trans_main.party_name,trans_main.party_state_code,trans_main.gstin, trans_main.currency, trans_main.vou_name_s, 
        SUM((CASE WHEN trans_main.gst_type = 3 THEN (trans_child.net_amount - trans_child.igst_amount) ELSE trans_child.net_amount END)) as net_amount, 
        SUM((trans_child.net_amount * trans_main.inrrate)) AS net_amount_inr,
        SUM(trans_child.taxable_amount) as taxable_amount,
        SUM((CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END)) as cgst_amount,
        SUM((CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END)) as sgst_amount,
        SUM((CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END)) as igst_amount,
        SUM((trans_child.taxable_amount * trans_main.inrrate)) AS taxable_amount_inr,trans_main.created_by,trans_child.qty,trans_child.price,item_master.item_name,item_master.item_code,item_master.hsn_code,unit_master.unit_name,trans_child.gst_per,states.name as state_name, states.gst_statecode as  state_code, tm.trans_mode as pay_mode,trans_main.memo_type
            FROM trans_child
            JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            JOIN item_master ON item_master.id = trans_child.item_id
            JOIN unit_master ON unit_master.id = item_master.unit_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN states ON states.id = party_master.state_id
            LEFT JOIN trans_main tm ON (tm.ref_id = trans_main.id AND tm.entry_type=15)
            WHERE trans_child.is_delete = 0
            AND trans_main.entry_type IN (".$entry_type.")
            AND trans_main.trans_date BETWEEN '".$fromDate."' AND '".$toDate."' AND trans_main.cm_id='".$this->CMID."'".$memoCondition. "
			GROUP BY ".$groupBY. "
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC")->result();
        return $accountReport;
    }
    
    public function getAccountReportDataItemWise($fromDate,$toDate,$entry_type,$memo_type=""){ 
        
        $memoCondition = "";$groupBY = 'trans_main.id,trans_main.trans_date,trans_child.item_id';
        if(!empty($memo_type)){$memoCondition = " AND trans_main.memo_type = '".$memo_type."'";}

        //if(!empty($memo_type) && $entry_type == 12){$memoCondition = ($memo_type == "DEBIT")?" AND trans_main.gstin != ''":" AND trans_main.gstin = ''";}

        if(!empty($memo_type) AND $memo_type=='DEBIT'){$groupBY = 'trans_main.id,trans_main.trans_date,trans_child.item_id';}

        $accountReport = $this->db->query ("SELECT trans_main.id, trans_main.trans_number, trans_main.doc_no, trans_main.trans_date, trans_main.party_id, trans_main.party_name,trans_main.party_state_code,trans_main.gstin, trans_main.currency, trans_main.vou_name_s, 
        SUM((CASE WHEN trans_main.gst_type = 3 THEN (trans_child.net_amount - trans_child.igst_amount) ELSE trans_child.net_amount END)) as net_amount, 
        SUM((trans_child.net_amount * trans_main.inrrate)) AS net_amount_inr,
        SUM(trans_child.taxable_amount) as taxable_amount,
        SUM((CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END)) as cgst_amount,
        SUM((CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END)) as sgst_amount,
        SUM((CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END)) as igst_amount,
        SUM((trans_child.taxable_amount * trans_main.inrrate)) AS taxable_amount_inr,trans_main.created_by,trans_child.qty,trans_child.price,item_master.item_name,item_master.item_code,item_master.hsn_code,unit_master.unit_name,trans_child.gst_per,states.name as state_name, states.gst_statecode as  state_code, trans_main.memo_type
            FROM trans_child
            JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            JOIN item_master ON item_master.id = trans_child.item_id
            JOIN unit_master ON unit_master.id = item_master.unit_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN states ON states.id = party_master.state_id
            WHERE trans_child.is_delete = 0
            AND trans_main.entry_type IN (".$entry_type.")
            AND trans_main.trans_date BETWEEN '".$fromDate."' AND '".$toDate."' AND trans_main.cm_id='".$this->CMID."'".$memoCondition. "
			GROUP BY ".$groupBY. "
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC")->result();
        $result = $accountReport;
        //$this->printQuery();exit;
        return $result;
    }
    
    public function getAccountReportDataItemWise00($fromDate,$toDate,$entry_type,$memo_type=""){ 
        
        $memoCondition = "";$groupBY = 'trans_main.trans_date,trans_child.item_id';
        if(!empty($memo_type)){$memoCondition = " AND trans_main.memo_type = '".$memo_type."'";}

        if(!empty($memo_type) && $entry_type == 12){$memoCondition = ($memo_type == "DEBIT")?" AND trans_main.gstin != ''":" AND trans_main.gstin = ''";}

        if(!empty($memo_type) AND $memo_type=='DEBIT'){$groupBY = 'trans_main.id,trans_main.trans_date,trans_child.item_id';}

        $accountReport = $this->db->query ("SELECT trans_main.id, trans_main.trans_number, trans_main.doc_no, trans_main.trans_date, trans_main.party_id, trans_main.party_name,trans_main.party_state_code,trans_main.gstin, trans_main.currency, trans_main.vou_name_s, 
        SUM((CASE WHEN trans_main.gst_type = 3 THEN (trans_child.net_amount - trans_child.igst_amount) ELSE trans_child.net_amount END)) as net_amount, 
        SUM((trans_child.net_amount * trans_main.inrrate)) AS net_amount_inr,
        SUM(trans_child.taxable_amount) as taxable_amount,
        SUM((CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END)) as cgst_amount,
        SUM((CASE WHEN trans_main.gst_type = 1 THEN trans_child.sgst_amount ELSE 0 END)) as sgst_amount,
        SUM((CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END)) as igst_amount,
        SUM((trans_child.taxable_amount * trans_main.inrrate)) AS taxable_amount_inr,trans_main.created_by,trans_child.qty,trans_child.price,item_master.item_name,item_master.item_code,item_master.hsn_code,unit_master.unit_name,trans_child.gst_per,states.name as state_name, states.gst_statecode as  state_code, tm.trans_mode as pay_mode,trans_main.memo_type
            FROM trans_child
            JOIN trans_main ON trans_main.id = trans_child.trans_main_id
            JOIN item_master ON item_master.id = trans_child.item_id
            JOIN unit_master ON unit_master.id = item_master.unit_id
            LEFT JOIN party_master ON party_master.id = trans_main.party_id
            LEFT JOIN states ON states.id = party_master.state_id
            LEFT JOIN trans_main tm ON (tm.ref_id = trans_main.id AND tm.entry_type=15)
            WHERE trans_child.is_delete = 0
            AND trans_main.entry_type IN (".$entry_type.")
            AND trans_main.trans_date BETWEEN '".$fromDate."' AND '".$toDate."' AND trans_main.cm_id='".$this->CMID."'".$memoCondition. "
			GROUP BY ".$groupBY. "
            ORDER BY trans_main.trans_date,trans_main.trans_no ASC")->result();
        $resut = $accountReport;
        $this->printQuery();exit;
        return $result;
    }
    
    public function getGstData($postData)
    {
        $data['tableName'] = 'trans_main';
        $data['select'] = 'trans_main.id,trans_main.entry_type,trans_main.trans_number,trans_main.doc_no,trans_main.trans_date,trans_main.party_name,trans_main.currency,trans_main.net_amount,trans_main.vou_name_s,trans_main.taxable_amount,trans_main.party_state_code,trans_main.total_amount,cgst_amount,sgst_amount,igst_amount,cess_amount,gst_amount,states.name as state_name,trans_main.doc_no,party_master.gstin';
        $data['leftJoin']['party_master'] = 'party_master.id=trans_main.party_id';
        $data['leftJoin']['states'] = 'states.id=party_master.state_id';
        $data['where_in']['entry_type']= $postData['entry_type'];
        if(!empty($postData['vou_acc_id'])){$data['where']['vou_acc_id'] = $postData['vou_acc_id'];}
        if(!empty($postData['sales_type'])){$data['where']['sales_type']= $postData['sales_type'];}
        $data['customWhere'][] = "trans_date BETWEEN '" .$postData['from_date'] . "' AND '" . $postData['to_date'] . "'";

        if (!empty($postData['party_id'])) {
            $data['where']['trans_main.party_id']=$postData['party_id'];
        }
        if (!empty($postData['state_code'])) {
            if ($postData['state_code'] == 1) {$data['where']['trans_main.party_state_code']=24;}
            if ($postData['state_code'] == 2) {$data['where']['trans_main.party_state_code !=']=24;}
        }
        $data['order_by']['trans_main.trans_no']='ASC';
        return $this->rows($data);
    }
    
    //CREATED BY MEGHAVI 15-03-2022
    public function getStockRegister($type){
		$data['tableName'] = 'item_master';
		$data['where_in']['item_master.item_type'] = $type;
        // $data['where']['NOCMID']='';
		return $this->rows($data);
	}

    //CREATED BY MEGHAVI 15-03-2022
    public function getStockReceiptQty($data){
		$queryData = array();
		$queryData['tableName'] = 'stock_transaction';
		$queryData['select'] = 'SUM(stock_transaction.qty) as rqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 1;
		$queryData['where']['stock_transaction.ref_date < '] = $data['to_date'];
		return $this->row($queryData);
	}

    //CREATED BY MEGHAVI 15-03-2022
	public function getStockIssuedQty($data){
		$queryData = array();
		$queryData['tableName'] = 'stock_transaction';
		$queryData['select'] = 'SUM(stock_transaction.qty) as iqty';
		$queryData['where']['stock_transaction.item_id'] = $data['item_id'];
		$queryData['where']['stock_transaction.trans_type'] = 2;
		$queryData['where']['stock_transaction.cm_id'] = $this->CMID;
		$queryData['where']['stock_transaction.ref_date < '] = $data['to_date'];
        return $this->row($queryData);
	}
}
?>