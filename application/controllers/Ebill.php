<?php
class Ebill extends MY_Controller{
	public $calcelReason = [
        1 => "Duplicate", 
        2 => "Data entry mistake", 
        3 => "Order Cancelled", 
        4 => "Others"
    ];
	
    public function __construct(){
		parent::__construct();
		$this->data['headData']->pageTitle = "E-Bill";
        $this->data['headData']->controller = "ebill";
	}

	/* Load From For new Eway Bill */
    public function addEwayBill(){
        $party_id = $this->input->post('party_id');
        $partyData = $this->party->getParty($party_id);
        $this->data['ref_id'] = $this->input->post('id');
        $this->data['party_id'] = $party_id;
        $this->data['distance'] = (!empty($partyData->distance))?$partyData->distance:"";
        $this->data['transportData'] = $this->transport->getTransports();
        $this->data['stateList'] = $this->party->getStates(101);
        $this->load->view("e_bill/eway_bill_form",$this->data);
    }

	/* Get Disaptch and Shipping Address on EWB Transaction Type */
    public function getEwbAddress(){
		$from_address = "";$from_pincode = "";$ship_address = "";$ship_pincode = "";
		$from_city="";$from_state="";$ship_city="";$ship_state="";
		$data = $this->input->post();
		
		$partyData = $this->party->getParty($data['party_id']);
		$orgData = $this->masterModel->getCompanyInfo();
		
		$fromCity = $this->party->getCities(4030)['cityList'];
		$shipCity = $this->party->getCities($partyData->state_id)['cityList'];
		
		if ($data['transaction_type'] == 1) {
			$fromCityOptions = '<option value="">Select City</option>';
			foreach($fromCity as $row):
				$fromCitySelected = ($orgData->company_city == $row->name)?"selected":"";
				$fromCityOptions .= '<option value="'.$row->id.'" '.$fromCitySelected.'>'.$row->name.'</option>';
			endforeach;

			$shipCityOptions = '<option value="">Select City</option>';
			foreach($shipCity as $row):
				$shipCitySelected = ($partyData->city_id == $row->id)?"selected":"";
				$shipCityOptions .= '<option value="'.$row->id.'" '.$shipCitySelected.'>'.$row->name.'</option>';
			endforeach;
			
			$from_address = $orgData->company_address;
			$from_pincode = $orgData->company_pincode;
			$ship_address = $partyData->party_address;
			$ship_pincode = $partyData->party_pincode;

			$from_city=$fromCityOptions;
			$from_state=4030;
			
			$ship_city=$shipCityOptions;
			$ship_state=$partyData->state_id;

		} elseif ($data['transaction_type'] == 2) {	
			$fromCityOptions = '<option value="">Select City</option>';
			foreach($fromCity as $row):
				$fromCitySelected = ($orgData->company_city == $row->name)?"selected":"";
				$fromCityOptions .= '<option value="'.$row->id.'" '.$fromCitySelected.'>'.$row->name.'</option>';
			endforeach;
	
			$from_address = $orgData->company_address;
			$from_pincode = $orgData->company_pincode;
			$ship_address = "";
			$ship_pincode = "";

			$from_city=$fromCityOptions;
			$from_state=4030;
			
			$ship_city="";
			$ship_state="";
		} elseif ($data['transaction_type'] == 3) {	

			$from_address = "";
			$from_pincode = "";
			$ship_address = $partyData->party_address;
			$ship_pincode = $partyData->party_pincode;

			$shipCityOptions = '<option value="">Select City</option>';
			foreach($shipCity as $row):
				$shipCitySelected = ($partyData->city_id == $row->id)?"selected":"";
				$shipCityOptions .= '<option value="'.$row->id.'" '.$shipCitySelected.'>'.$row->name.'</option>';
			endforeach;

			$from_city="";
			$from_state="";
			$ship_city=$shipCityOptions;
			$ship_state=$partyData->state_id;
		} elseif ($data['transaction_type'] == 4) {
			$from_address = "";
			$from_pincode = "";
			$ship_address = "";
			$ship_pincode = "";

			$from_city="";
			$from_state="";
			$ship_city="";
			$ship_state="";
		}

		$this->printJson(["status" => 1, "from_address" => $from_address, "from_pincode" => $from_pincode, "ship_address" => $ship_address, "ship_pincode" => $ship_pincode,"from_city"=>$from_city,"from_state"=>$from_state,"ship_city"=>$ship_city,"ship_state"=>$ship_state]);
	}  
	
	/* public function vehicleSearch(){
		$this->printJson($this->ebill->vehicleSearch());
	} */ 

	/* Generate New Eway Bill */
	public function generateEwb(){
		$data = $this->input->post();  
		$errorMessage = array();

        if(empty($data['doc_type']))
            $errorMessage['doc_type'] = "Document Type is required.";
        if(empty($data['supply_type']))
            $errorMessage['supply_type'] = "Supply Type is required.";
        if(empty($data['sub_supply_type']))
            $errorMessage['sub_supply_type'] = "Sub Supply Type is required.";
        if(empty($data['trans_mode']))
            $errorMessage['trans_mode'] = "Transport Mode is required.";
        if(empty($data['trans_distance']))
            $errorMessage['trans_distance'] = "Trans. Distance is required.";
        if(empty($data['vehicle_no']))
            $errorMessage['vehicle_no'] = "Vehicle no. is required.";
		if(empty($data['from_address']))
            $errorMessage['from_address'] = "Dispatch Address is required.";
		if(empty($data['ship_address']))
            $errorMessage['ship_address'] = "Shipping Address is required.";
        if(!isset($data['ref_id']))
            $errorMessage['ref_id'] = "Please select recoreds.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else: 
			$this->printJson($this->ebill->generateEwayBill($data));
		endif;
	}

	/* Sync E-Way Bill on Document No. */
	public function syncEwayBill(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->syncEwayBill($data));
		endif;
	}

	/* Cancel Eway Bill From */
	public function loadCancelEwayBillForm(){
		$id = $this->input->post('id');
		$invoiceData = $this->salesInvoice->getInvoice($id);
		$this->data['ref_id'] = $id;
		$this->data['ewbNo'] = $invoiceData->eway_bill_no;
		$this->data['reasonList'] = $this->calcelReason;
		$this->load->view('e_bill/eway_bill_cancel_form',$this->data);
	}

	/* Cancel Eway Bill */
	public function cancelEwayBill(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ewbNo']))
			$errorMessage['ewbNo'] = "Eway Bill No. is required.";
		if(empty($data['cancelRsnCode']))
			$errorMessage['cancelRsnCode'] = "Cancel Reason is required.";
		if(empty($data['cancelRmrk']))
			$errorMessage['cancelRmrk'] = "Cancel Remark is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->cancelEwayBill($data));
		endif;
	}

	/* Print of Eway Bill */
	public function ewb_pdf($ewb_no=""){
		if(!empty($ewb_no)):
			$comapnyInfo = $this->masterModel->getCompanyInfo();
			$postData['Gstin'] = $comapnyInfo->company_gst_no;
			$postData['ewayBillNo'] = $ewb_no;

			$curlEwaybill = curl_init();
			curl_setopt_array($curlEwaybill, array(
				CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/ewayBillPdf",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				CURLOPT_POSTFIELDS => json_encode($postData)
			));

			$response = curl_exec($curlEwaybill);
			$error = curl_error($curlEwaybill);
			curl_close($curlEwaybill);
			
			if($error):
				$this->data['heading'] = "Eway Bill PDF Error.";
				$this->data['message'] = 'Somthing is wrong1. cURL Error #:'. $error;
				$this->load->view('page-404',$this->data);
			else:
				$response = json_decode($response);
				if(isset($response->status) && $response->status == 0):	
					$this->data['heading'] = "Eway Bill PDF Error.";
					$this->data['message'] = 'Somthing is wrong2. E-way Bill Error #: '. $response->error_message;
					$this->load->view('page-404',$this->data);
				else:
					return redirect($response->data->pdf_path);
				endif;
			endif;
		else:
			echo "<script>window.close();</script>";
		endif;
	}

	/* Detail Print of Eway Bill */
	public function ewb_detail_pdf($ewb_no=""){
		if(!empty($ewb_no)):
			$comapnyInfo = $this->masterModel->getCompanyInfo();
			$postData['Gstin'] = $comapnyInfo->company_gst_no;
			$postData['ewayBillNo'] = $ewb_no;

			$curlEwaybill = curl_init();
			curl_setopt_array($curlEwaybill, array(
				CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/ewayBillDetailPdf",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				CURLOPT_POSTFIELDS => json_encode($postData)
			));

			$response = curl_exec($curlEwaybill);
			$error = curl_error($curlEwaybill);
			curl_close($curlEwaybill);
			
			if($error):
				$this->data['heading'] = "Eway Bill PDF Error.";
				$this->data['message'] = 'Somthing is wrong1. cURL Error #:'. $error;
				$this->load->view('page-404',$this->data);
			else:
				$response = json_decode($response);
				if(isset($response->status) && $response->status == 0):	
					$this->data['heading'] = "Eway Bill PDF Error.";
					$this->data['message'] = 'Somthing is wrong2. E-way Bill Error #: '. $response->error_message;
					$this->load->view('page-404',$this->data);
				else:
					return redirect($response->data->pdf_path);
				endif;
			endif;
		else:
			echo "<script>window.close();</script>";
		endif;
	}

	/* Load From For new E-Invoice */
	public function addEinvoice(){
		$party_id = $this->input->post('party_id');
        $this->data['ref_id'] = $this->input->post('id');

        $partyData = $this->party->getParty($party_id);
        $this->data['partyData'] = $partyData;
		$this->data['companyInfo'] = $this->masterModel->getCompanyInfo();
        //$this->data['transportData'] = $this->transport->getTransports();
        $this->data['countryList'] = $this->party->getCountries();
        $this->data['stateList'] = $this->party->getStates(101);
        $this->data['cityList'] = $this->party->getCities(4030);
		$this->data['billingState'] = $this->party->getStates($partyData->country_id,$partyData->state_id);
		$this->data['billingCity'] = $this->party->getCities($partyData->state_id,$partyData->city_id);
		$this->load->view('e_bill/einvoice_form',$this->data);
	}

	/* Generate New E-Invoice */
	public function generateEinvoice(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";
		if(!empty($data['ewb_status']) && empty($data['trans_distance']))
			$errorMessage['trans_distance'] = "Trans. Distance is required.";
		if(!empty($data['ewb_status']) && !empty($data['vehicle_no']) && empty($data['trans_mode']))
			$errorMessage['trans_mode'] = "Trans. Mode is required.";

        if(empty($data['billing_country']))
            $errorMessage['billing_country'] = "Billing Country is required.";
        if(empty($data['billing_state']))
            $errorMessage['billing_state'] = "Billing State is required.";
        if(empty($data['billing_city']))
            $errorMessage['billing_city'] = "Billing City is required.";
        if(empty($data['billing_pincode']))
            $errorMessage['billing_pincode'] = "Billing Pincode is required.";
        if(empty($data['billing_address']))
            $errorMessage['billing_address'] = "Billing Address is required.";

        if(empty($data['ship_country']))
            $errorMessage['ship_country'] = "Shipping Country is required.";
        if(empty($data['ship_state']))
            $errorMessage['ship_state'] = "Shipping State is required.";
        if(empty($data['ship_city']))
            $errorMessage['ship_city'] = "Shipping City is required.";
        if(empty($data['ship_pincode']))
            $errorMessage['ship_pincode'] = "Shipping Pincode is required.";
        if(empty($data['ship_address']))
            $errorMessage['ship_address'] = "Shipping Address is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->generateEinvoice($data));
		endif;
	}

	/* SYNC E-Invoice From GOV. Portal */
	public function syncEinvoice(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->syncEinvoice($data));
		endif;
	}

	/* Load Cencel E-Invoice From */
	public function loadCancelInvForm(){
		$id = $this->input->post('id');
		$invoiceData = $this->salesInvoice->getInvoice($id);
		$this->data['ref_id'] = $id;
		$this->data['akc_no'] = $invoiceData->e_inv_no;
		$this->data['irn'] = $invoiceData->e_inv_irn;
		$this->data['reasonList'] = $this->calcelReason;
		$this->load->view('e_bill/einvoice_cancel_form',$this->data);
	}

	/* Cancel E-Invoice on irn */
	public function cancelEinvoice(){
		$data = $this->input->post();//print_r($data);exit;
		$errorMessage = array();

		if(empty($data['CnlRsn']))
			$errorMessage['CnlRsn'] = "Cancel Reason is required.";
		if(empty($data['CnlRem']))
			$errorMessage['CnlRem'] = "Cancel Remark is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$this->printJson($this->ebill->cancelEinv($data));
		endif;
	}

	/* Print of E-Invoice */
	public function einv_pdf($ackNo=""){
		if(!empty($ackNo)):
			$comapnyInfo = $this->masterModel->getCompanyInfo();
			$postData['Gstin'] = $comapnyInfo->company_gst_no;
			$postData['ackNo'] = $ackNo;

			$curlEwaybill = curl_init();
			curl_setopt_array($curlEwaybill, array(
				CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/einvPdf",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_SSL_VERIFYHOST => FALSE,
				CURLOPT_SSL_VERIFYPEER => FALSE,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
				CURLOPT_POSTFIELDS => json_encode($postData)
			));

			$response = curl_exec($curlEwaybill);
			$error = curl_error($curlEwaybill);
			curl_close($curlEwaybill);
			
			if($error):
				$this->data['heading'] = "E-Invoice PDF Error.";
				$this->data['message'] = 'Somthing is wrong1. cURL Error #:'. $error;
				$this->load->view('page-404',$this->data);
			else:
				$response = json_decode($response);
				if(isset($response->status) && $response->status == 0):	
					$this->data['heading'] = "E-Invoice PDF Error.";
					$this->data['message'] = 'Somthing is wrong2. E-way Bill Error #: '. $response->error_message;
					$this->load->view('page-404',$this->data);
				else:
					return redirect($response->data->pdf_path);
				endif;
			endif;
		else:
			echo "<script>window.close();</script>";
		endif;
	}
	
	public function downloadEinvJson(){
		$data = $this->input->post();
		$errorMessage = array();

		if(empty($data['ref_id']))
			$errorMessage['general_error'] = "Somthing is wrong.";
		if(!empty($data['ewb_status']) && empty($data['trans_distance']))
			$errorMessage['trans_distance'] = "Trans. Distance is required.";

        if(empty($data['billing_country']))
            $errorMessage['billing_country'] = "Billing Country is required.";
        if(empty($data['billing_state']))
            $errorMessage['billing_state'] = "Billing State is required.";
        if(empty($data['billing_city']))
            $errorMessage['billing_city'] = "Billing City is required.";
        if(empty($data['billing_pincode']))
            $errorMessage['billing_pincode'] = "Billing Pincode is required.";
        if(empty($data['billing_address']))
            $errorMessage['billing_address'] = "Billing Address is required.";

        if(empty($data['ship_country']))
            $errorMessage['ship_country'] = "Shipping Country is required.";
        if(empty($data['ship_state']))
            $errorMessage['ship_state'] = "Shipping State is required.";
        if(empty($data['ship_city']))
            $errorMessage['ship_city'] = "Shipping City is required.";
        if(empty($data['ship_pincode']))
            $errorMessage['ship_pincode'] = "Shipping Pincode is required.";
        if(empty($data['ship_address']))
            $errorMessage['ship_address'] = "Shipping Address is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>$errorMessage]);
		else:
			$result = $this->ebill->generateEinvJson($data);
			$this->printJson($result);
		endif;
	}
}
?>