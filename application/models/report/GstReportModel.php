<?php
class GstReportModel extends MasterModel{

    public function _b2b($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            select party_master.gstin,party_master.party_name,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.net_amount,states.name as state_name,SUBSTRING(party_master.gstin,1,2) as gst_statecode,tc.gst_per as gst_per,trans_main.taxable_amount,trans_main.cess_amount
            from trans_main
            left join party_master on party_master.id = trans_main.party_id
            left join states on party_master.state_id = states.id
            left join (select MAX(gst_per) as gst_per,trans_main_id from trans_child where is_delete = 0 group by trans_main_id) tc on tc.trans_main_id = trans_main.id
            where trans_main.trans_date >= '".$data['from_date']."' 
            and trans_main.trans_date <= '".$data['to_date']."'
            ".$party_id."
            and trans_main.entry_type in (".$data['entry_type'].")
            and party_master.gstin != ''
            and trans_main.is_delete = 0
            and trans_main.cm_id = ".$this->CMID."
            order by trans_main.trans_date ASC
        ")->result();
        
        return $result;
    }

    public function _b2ba($data){
        return [];
    }

    public function _b2cl($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            select party_master.gstin,party_master.party_name,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.net_amount,states.name as state_name,SUBSTRING(party_master.gstin,1,2) as gst_statecode,tc.gst_per as gst_per,trans_main.taxable_amount,trans_main.cess_amount
            from trans_main
            left join party_master on party_master.id = trans_main.party_id
            left join states on party_master.state_id = states.id
            left join (select MAX(gst_per) as gst_per,trans_main_id from trans_child where is_delete = 0 group by trans_main_id) tc on tc.trans_main_id = trans_main.id
            where trans_main.trans_date >= '".$data['from_date']."' 
            and trans_main.trans_date <= '".$data['to_date']."'
            ".$party_id."
            and trans_main.entry_type in (".$data['entry_type'].")
            and party_master.gstin != ''
            and trans_main.is_delete = 0
            and trans_main.cm_id = ".$this->CMID."
            and trans_main.taxable_amount > 250000
            order by trans_main.trans_date ASC
        ")->result();
        
        return $result;
    }

    public function _b2cla($data){
        return [];
    }

    public function _b2cs($data){
        $queryData['tableName'] = 'trans_child';
        $queryData['select']="SUM(CASE WHEN trans_main.taxable_amount <= 250000 THEN trans_child.taxable_amount ELSE 0 END) as taxable_amount,SUM(CASE WHEN trans_main.taxable_amount <= 250000 THEN trans_child.cess_amount ELSE 0 END) as cess_amount,trans_child.gst_per,states.name as state_name,states.gst_statecode as party_state_code";
        $queryData['leftJoin']['trans_main'] = 'trans_child.trans_main_id=trans_main.id';
        $queryData['leftJoin']['party_master'] = 'party_master.id=trans_main.party_id';
        $queryData['leftJoin']['states'] = 'states.id=party_master.state_id';
        $queryData['where_in']['trans_main.entry_type']= $data['entry_type'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '" .$data['from_date'] . "' AND '" . $data['to_date'] . "'";
        if (!empty($data['party_id'])) {
            $queryData['where']['trans_main.party_id']=$data['party_id'];
        }
        $queryData['group_by'][]="trans_child.gst_per,party_master.state_id";
        $result=$this->rows($queryData);
		// print_r($this->printQuery());exit;
        return $result;
    }

    public function _b2csa($data){
        return [];
    }

    public function _cdnr($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";

        $result = $this->db->query("
            select party_master.gstin,party_master.party_name,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_number,trans_main.trans_date,trans_main.net_amount,states.name as state_name,SUBSTRING(party_master.gstin,1,2) as gst_statecode,tc.gst_per as gst_per,trans_main.taxable_amount,trans_main.cess_amount,trans_main.gst_applicable,trans_main.entry_type
            from trans_main
            left join party_master on party_master.id = trans_main.party_id
            left join states on party_master.state_id = states.id
            left join (select MAX(gst_per) as gst_per,trans_main_id from trans_child where is_delete = 0 group by trans_main_id) tc on tc.trans_main_id = trans_main.id
            where trans_main.trans_date >= '".$data['from_date']."' 
            and trans_main.trans_date <= '".$data['to_date']."'
            ".$party_id."
            and trans_main.entry_type in (".$data['entry_type'].")
            and party_master.gstin != ''
            and trans_main.is_delete = 0
            and trans_main.cm_id = ".$this->CMID."
            order by trans_main.trans_date ASC
        ")->result();
        // print_r($this->printQuery());exit;
        return $result;
    }

    public function _cdnra($data){
        return [];
    }

    public function _cdnur($data){
        return [];
    }

    public function _cdnura($data){
        return [];
    }

    public function _exp($data){
        return [];
    }

    public function _expa($data){
        return [];
    }

    public function _at($data){
        $party_id = (!empty($data['party_id']))?"and trans_main.party_id = ".$data['party_id']:"";
        $result = $this->db->query("
        select states.name as state_name,states.gst_statecode,SUM(trans_main.net_amount) as net_amount,SUM(trans_main.cess_amount) as cess_amount,trans_main.gst_applicable,trans_main.entry_type
        from trans_main
        left join party_master on party_master.id = trans_main.party_id
        left join states on party_master.state_id = states.id
        where trans_main.trans_date >= '".$data['from_date']."' 
        and trans_main.trans_date <= '".$data['to_date']."'
        ".$party_id."
        and trans_main.entry_type in (".$data['entry_type'].")
        and trans_main.is_delete = 0
        and trans_main.payment_type = 1
        and trans_main.cm_id = ".$this->CMID."
        GROUP BY party_master.state_id
        order by trans_main.trans_date ASC
    ")->result();
    // print_r($this->printQuery());exit;
    return $result;
    }

    public function _ata($data){
        return [];
    }

    public function _atadj($data){
        return [];
    }

    public function _atadja($data){
        return [];
    }

    public function _exemp($data){
        return [];
    }

    public function _hsn($data){
        $queryData['tableName'] = 'trans_child';
        $queryData['select']="SUM(CASE WHEN trans_main.gst_type = 1 THEN trans_child.cgst_amount ELSE 0 END) as cgst_amount,SUM(CASE WHEN trans_main.gst_type =1 THEN trans_child.sgst_amount ELSE 0 END) as sgst_amount,SUM(CASE WHEN trans_main.gst_type = 2 THEN trans_child.igst_amount ELSE 0 END) as igst_amount,SUM(qty) as qty,SUM(trans_child.taxable_amount) as taxable_amount,SUM(CASE WHEN trans_main.gst_type = 3 THEN trans_child.taxable_amount ELSE  trans_child.net_amount END) as net_amount,SUM(trans_child.cess_amount) as cess_amount,unit_master.unit_name,unit_master.description as unit_description,trans_child.hsn_code,trans_child.gst_per";
        $queryData['leftJoin']['trans_main'] = 'trans_child.trans_main_id=trans_main.id';
        $queryData['leftJoin']['unit_master'] = 'unit_master.id=trans_child.unit_id';
        $queryData['where_in']['trans_main.entry_type']= $data['entry_type'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '" .$data['from_date'] . "' AND '" . $data['to_date'] . "'";
        if (!empty($data['party_id'])) {
            $queryData['where']['trans_main.party_id']=$data['party_id'];
        }
        $queryData['group_by'][]="trans_child.hsn_code,trans_child.unit_id";
        $result=$this->rows($queryData);
		// print_r($this->printQuery());exit;
        return $result;
    }
	
    public function _docs($data){
        $queryData['tableName'] = 'trans_main';
        //$queryData['select']="MAX(trans_main.trans_number) as max_trans_no,MIN(trans_main.trans_number) as min_trans_no,count(trans_main.id) as total_inv,trans_main.trans_prefix";
		$queryData['select'] = "MAX(trans_main.trans_no) as max_trans_no, MIN(trans_main.trans_no) as min_trans_no, count(trans_main.id) as total_inv, trans_main.trans_number";
        $queryData['where_in']['trans_main.entry_type']= $data['entry_type'];
        $queryData['customWhere'][] = "trans_main.trans_date BETWEEN '" .$data['from_date'] . "' AND '" . $data['to_date'] . "'";
        if (!empty($data['party_id'])) {
            $queryData['where']['trans_main.party_id']=$data['party_id'];
        }
        $queryData['group_by'][]="trans_main.trans_prefix";
        $result=$this->rows($queryData);
        // print_r($this->printQuery());exit;
        return $result;
    }
}
?>