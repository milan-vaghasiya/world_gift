<?php
class PartyModel extends MasterModel
{
	private $partyMaster = "party_master";
	private $countries = "countries";
	private $states = "states";
	private $cities = "cities";
	private $customer_details = "customer_details";

	public function getDTRows($data)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['cm_id'] = $this->CMID;
		$data['where']['coust_type != '] = 1;
		$data['where']['party_category != '] = 4;
		$data['searchCol'][] = "party_name";
		$data['searchCol'][] = "contact_person";
		$data['searchCol'][] = "party_mobile";
		$data['searchCol'][] = "party_code";
		$data['searchCol'][] = "currency";
		$columns = array('', '', 'party_name', 'contact_person', 'party_mobile', 'party_code','currency');
		if (isset($data['order'])) {
			$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
		}
		return $this->pagingRows($data);
	}

	public function getCustomerList($select="",$party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		if(!empty($select)){$data['select'] = $select;}
		$data['where_in']['party_category'] = "1,5";
		$data['where']['cm_id'] = $this->CMID;
		//$data['where']['party_type'] = $party_type;
		return $this->rows($data);
	}

	public function getVendorList($party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['party_category'] = 2;
		$data['where']['cm_id'] = $this->CMID;
		// $data['where']['party_type'] = $party_type;
		return $this->rows($data);
	}

	public function getSupplierList($party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where_in']['party_category'] = "3,5";
		$data['where']['cm_id'] = $this->CMID;
		// $data['where']['party_type'] = $party_type;
		return $this->rows($data);
	}

	public function getPartyList($party_type = 1)
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['cm_id'] = $this->CMID;
		// $data['where']['party_type'] = $party_type;
		return $this->rows($data);
	}

	public function getParty($id,$select="")
	{
		$data['tableName'] = $this->partyMaster;
		if(!empty($select)){$data['select'] = $select;}
		else{$data['select'] = 'party_master.*,currency.inrrate,countries.name as country_name,states.name as state_name,states.gst_statecode as state_code,cities.name as city_name';}
		$data['leftJoin']['currency'] = 'currency.currency = party_master.currency';
		$data['leftJoin']['countries'] = "countries.id = party_master.country_id";
		$data['leftJoin']['states'] = 'party_master.state_id = states.id';
        $data['leftJoin']['cities'] = 'party_master.city_id = cities.id';
		$data['where']['party_master.id'] = $id;
		return $this->row($data);
	}

	public function salesTransactions($id, $limit = "")
	{
		$queryData['tableName'] = 'trans_child';
		$queryData['where']['trans_main_id'] = $id;
		return $this->rows($queryData);
	}

	/**Updated BY Mansee @ 27-12-2021 Line No : 104-105 */
	public function save($data)
	{
		try {
			$this->db->trans_begin();
			if ($this->checkDuplicate($data['party_name'], $data['party_category'], $data['id']) > 0) :
				$errorMessage['party_name'] = "Company name is duplicate.";
				$result = ['status' => 0, 'message' => "Some fields are duplicate.",'field_error'=>1,'field_error_message'=>$errorMessage];
			else :
				$data['opening_balance'] = (!empty($data['opening_balance'])) ? $data['opening_balance'] : 0;
				if (empty($data['id'])) :
					$groupCode = ($data['party_category'] == 1) ? "SD" : "SC";
					$groupData = $this->group->getGroupOnGroupCode($groupCode, true);

					$data['group_id'] = $groupData->id;
					$data['group_name'] = $groupData->name;
					$data['group_code'] = $groupData->group_code;
					if(isset($data['balance_type']) AND !empty($data['balance_type'])):
					    $data['cl_balance'] = $data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
					endif;
				else :
				    if(isset($data['balance_type']) AND !empty($data['balance_type'])):
    					$partyData = $this->getParty($data['id']);
    					$data['opening_balance'] = $data['opening_balance'] * $data['balance_type'];
    					if ($partyData->opening_balance > $data['opening_balance']) :
    						$varBalance = $partyData->opening_balance - $data['opening_balance'];
    						$data['cl_balance'] = $partyData->cl_balance - $varBalance;
    					elseif ($partyData->opening_balance < $data['opening_balance']) :
    						$varBalance = $data['opening_balance'] - $partyData->opening_balance;
    						$data['cl_balance'] = $partyData->cl_balance + $varBalance;
    					else :
    						$data['cl_balance'] = $partyData->cl_balance;
    					endif;
				    endif;
				endif;
	        	$data['cm_id'] = $this->CMID;
				$result = $this->store($this->partyMaster, $data, 'Party');
				$data['party_id'] = (!empty($data['id'])) ? $data['id'] : $result['insert_id'];
				$this->saveGst($data);
			endif;

			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}
	}

	public function savePartyApproval($data)
	{
		try {
			$this->db->trans_begin();
			$result = $this->store($this->partyMaster, $data, 'Party');
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}
	}

	public function checkDuplicate($name, $party_category, $id = "")
	{
		$data['tableName'] = $this->partyMaster;
		$data['where']['party_name'] = $name;
		$data['where']['party_category'] = $party_category;

		if (!empty($id))
			$data['where']['id !='] = $id;

		return $this->numRows($data);
	}

	public function saveCity($ctname, $state_id, $country_id)
	{
		try {
			$this->db->trans_begin();
			$queryData = ['id' => '', 'name' => $ctname, 'state_id' => $state_id, 'country_id' => $country_id];
			$cityData = $this->store($this->cities, $queryData, 'Party');
			$result = $cityData['insert_id'];
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}
	}

	public function saveState($statename, $country_id)
	{
		try {
			$this->db->trans_begin();
			$queryData = ['id' => '', 'name' => $statename, 'country_id' => $country_id];
			$stateData = $this->store($this->states, $queryData, 'Party');
			$result = $stateData['insert_id'];
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}
	}

	public function delete($id)
	{
		try {
			$this->db->trans_begin();
			$result = $this->trash($this->partyMaster, ['id' => $id], 'Party');
			if ($this->db->trans_status() !== FALSE) :
				$this->db->trans_commit();
				return $result;
			endif;
		} catch (\Exception $e) {
			$this->db->trans_rollback();
			return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(),'field_error'=>0,'field_error_message'=>null];
		}
	}

	public function getCountries()
	{
		$data['tableName'] = $this->countries;
		$data['order_by']['name'] = "ASC";
		// $data['where']['NOCMID'] = "";
		return $this->rows($data);
	}

	public function getCurrency()
	{
		$data['tableName'] = 'currency';
		return $this->rows($data);
	}

	public function getStates($id, $stateId = "")
	{
		$data['tableName'] = $this->states;
		$data['where']['country_id'] = $id;
		// $data['where']['NOCMID'] = "";
		$data['order_by']['name'] = "ASC";
		$state = $this->rows($data);

		$html = '<option value="">Select State</option>';
		foreach ($state as $row) :
			$selected = (!empty($stateId) && $row->id == $stateId) ? "selected" : "101";
			$html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
		endforeach;

		return ['status' => 1, 'result' => $html];
	}

	public function getStateByIdOrCode($id="",$state_code="")
	{
		$data['tableName'] = $this->states;
		if(!empty($id)){$data['where']['id'] = $id;}
		if(!empty($state_code)){$data['where']['gst_statecode'] = $state_code;}
		$state = $this->row($data);
		return $state;
	}

	public function getCities($id, $cityId = "")
	{
		$data['tableName'] = $this->cities;
		$data['where']['state_id'] = $id;
		// $data['where']['NOCMID'] = "";
		$data['order_by']['name'] = "ASC";
		$city = $this->rows($data);

		$html = '<option value="">Select City</option>';
		foreach ($city as $row) :
			$selected = (!empty($cityId) && $row->id == $cityId) ? "selected" : "4030";
			$html .= '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
		endforeach;

		return ['status' => 1, 'result' => $html];
	}

	public function getPartyListOnGroupCode($groupCode = ['"BA"', '"CS"'])
	{
		$data['tableName'] = $this->partyMaster;
		$data['where_in']['group_code'] = $groupCode;
		$result = $this->rows($data);
		return $result;
	}

	//-----------  API Function Start -----------//

	public function getPartyList_api($limit, $start, $party_type = 0)
	{
		$data['tableName'] = $this->partyMaster;
	    $data['where']['cm_id'] = $this->CMID;
		if (!empty($party_type))
			$data['where']['party_category'] = $party_type;

		$data['length'] = $limit;
		$data['start'] = $start;
		return $this->rows($data);
	}

	public function getCount($party_type = 0)
	{
		$data['tableName'] = $this->partyMaster;
	    $data['where']['cm_id'] = $this->CMID;
		if (!empty($party_type))
			$data['where']['party_category'] = $party_type;
		return $this->numRows($data);
	}

	//----------- API Function End -----------//
	
	public function getCustomerList_api($postData){
        $queryData = array();
        $queryData['tableName'] = $this->partyMaster;
        $queryData['select'] = "party_master.id, party_master.party_code, party_master.party_name, party_master.party_phone, party_master.gstin	, party_master.party_email";
		$queryData['where_in']['party_category'] = $postData['party_category'];
		$queryData['where']['cm_id'] = $this->CMID;
        
        if(!empty($postData['search'])):
            $queryData['like_or']['party_master.party_code'] = $postData['search'];
            $queryData['like_or']['party_master.party_name'] = $postData['search'];
            $queryData['like_or']['party_master.party_phone'] = $postData['search'];
        endif;

        $queryData['length'] = $postData['limit'];
		$queryData['start'] = $postData['off_set'];
        $result = $this->rows($queryData);
        return $result;
    }

	/**
	 * Created By  Mansee @ 25-12-2021
	 */
	public function saveGst($data)
	{
		$queryData['where']['id'] = $data['party_id'];
		$queryData['select'] = 'party_master.*';
		$queryData['tableName'] = $this->partyMaster;
		$contactData = $this->row($queryData);
		$contactArr = new stdClass();
        
		if (!empty($contactData)) {
			$contactArr = json_decode($contactData->json_data);
		}
		// $data['party_address'] = $contactData->party_address;
		// $data['party_pincode'] = $contactData->party_pincode;
        
        if(empty($contactArr)):
            $contactArr = new stdClass();
            $contactArr->{$data['gstin']}=[];
        endif;
		if(!empty($data['gstin']))
		{
			$contactArr->{$data['gstin']} =  [
				'party_address' => $data['party_address'],
				'party_pincode' => $data['party_pincode'],
				'delivery_address' => $data['delivery_address'],
				'delivery_pincode' => $data['delivery_pincode']
			];
		}
        
		return $this->store($this->partyMaster, ['id' => $data['party_id'], 'json_data' => json_encode($contactArr)], 'Party');
	}
	/**
	 * Created By  Mansee @ 25-12-2021
	 */
	public function deleteGst($party_id, $gstin)
	{
		$data['where']['id'] = $party_id;
		$data['select'] = 'json_data';
		$data['tableName'] = $this->partyMaster;
		$contactData = $this->row($data)->json_data;


		$contactArr = json_decode($contactData);
		unset($contactArr->{$gstin});
		$result = $this->store($this->partyMaster, ['id' => $party_id, 'json_data' => json_encode($contactArr)], 'Party');

		return $result;
	}

	/*Created By : Avruti @21-3-2022 */
	public function getPersonalDataList($party_id){
		$data['tableName'] = $this->customer_details;
        $data['where']['party_id'] = $party_id;
		return $this->rows($data);
	}

	public function savePersonalDetail($data){
		return $this->store($this->customer_details,$data,'Personal');
   	}

   	public function deletePersonalDetail($party_id){
		return $this->trash($this->customer_details,['id'=>$party_id],"Record");
	}

	public function getCity($id){
        $data['tableName'] = $this->cities;
        $data['select'] = 'cities.*,states.name as state_name,states.gst_statecode as state_code,countries.name as country_name';
        $data['leftJoin']['states'] = 'cities.state_id = states.id';
        $data['leftJoin']['countries'] = "countries.id = cities.country_id";
        $data['where']['cities.id'] = $id;
        return $this->row($data);
    }
}
?>