<?php
class EbillModel extends MasterModel{
    private $ebillLog = "ebill_log";
    private $ewayBillMaster = "eway_bill_master";

    
    /* Generate Eway Bill JSON DATA */
    public function ewbJsonSingle($ewbData){
		$ref_id = $ewbData['ref_id'];
        $postData=array();$billData=array();$itemList=array();

        $invData = $this->salesInvoice->getInvoice($ref_id);
        $invData = $this->calculateInvoiceValue($invData);

        $orgData = $this->getCompanyInfo();
        $cityDataFrom = $this->party->getCity($ewbData['from_city']);
        $cityDataTo = $this->party->getCity($ewbData['ship_city']);

        $postData['Gstin'] = $orgData->company_gst_no;
        $postData['companyInfo'] = [
            'name' => $orgData->company_name,
            'email' => $orgData->company_email,
            'phone_no' => $orgData->company_contact,
            'contact_no' => $orgData->company_contact,
            'country_name' => (!empty($ewbData['from_city']))?$cityDataFrom->country_name:$orgData->company_country,
            'state_name' => (!empty($ewbData['from_city']))?$cityDataFrom->state_name:$orgData->company_state,
            'city_name' => (!empty($ewbData['from_city']))?$cityDataFrom->name:$orgData->company_city,
            'address' => (!empty($ewbData['from_address']))?$ewbData['from_address']:$orgData->company_address,
            'pincode' => (!empty($ewbData['from_pincode']))?$ewbData['from_pincode']:$orgData->company_pincode,
            'gst_no' => $orgData->company_gst_no,
            'pan_no' => $orgData->company_pan_no,
            'state_code' => $orgData->company_state_code
        ];

        
        $partyData = $this->party->getParty($invData->party_id);    
        $postData['partyInfo'] = [
            'name' => $partyData->party_name,
            'gst_no' => (!empty($partyData->gstin))?$partyData->gstin:"URP",
            'pan_no' => $partyData->party_pan,            
            'email' => $partyData->party_email,
            'contact_email' => $partyData->contact_email,
            'phone_no' => $partyData->party_phone,
            'contact_no' => $partyData->party_mobile,
            'billing_address' => str_replace('"',"",((!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address)),
            'billing_pincode' => (!empty($ewbData['ship_pincode']))?$ewbData['ship_pincode']:$partyData->party_pincode,
            'billing_country_name' => (!empty($ewbData['ship_city']))?$cityDataTo->country_name:$partyData->country_name,
            'billing_state_name' => (!empty($ewbData['ship_city']))?$cityDataTo->state_name:$partyData->state_name,
            'billing_city_name' => (!empty($ewbData['ship_city']))?$cityDataTo->name:$partyData->city_name,
            'billing_state_code' => $cityDataTo->state_code,
            'ship_address' => str_replace('"',"",((!empty($ewbData['ship_address']))?$ewbData['ship_address']:$partyData->party_address)),
            'ship_pincode' => (!empty($ewbData['ship_pincode']))?$ewbData['ship_pincode']:$partyData->party_pincode,
            'ship_country_name' => (!empty($ewbData['ship_city']))?$cityDataTo->country_name:$partyData->country_name,
            'ship_state_name' => (!empty($ewbData['ship_city']))?$cityDataTo->state_name:$partyData->state_name,
            'ship_city_name' => (!empty($ewbData['ship_city']))?$cityDataTo->name:$partyData->city_name,
            'ship_state_code' => $cityDataTo->state_code,
        ];

        $mainHsnCode = '';
        foreach($invData->itemData as $trans):
            $sgstRate=0;$cgstRate=0;$igstRate=0;
            if(!empty($trans->gst_per)):
                $igstRate = round($trans->gst_per,2);
                $cgstRate = $sgstRate = round(($igstRate/2),2);
                if(empty($partyData->gstin)):
                    $igstRate=0;
                else:
                    if($cityDataTo->state_code == $orgData->company_state_code):
                        $igstRate=0;
                    else:
                        $sgstRate=0;$cgstRate=0;
                    endif;
                endif;
            endif;
            
            $itemList[]= [
                "productName"=> $trans->item_name,
                "productDesc"=> "", 
                "hsnCode"=> (!empty($trans->hsn_code))?intVal($trans->hsn_code):"", 
                "quantity"=> floatVal($trans->qty),
                "qtyUnit"=> $trans->unit_name, 
                "taxableAmount"=> floatVal($trans->amount), 
                "sgstRate"=> floatVal($sgstRate), 
                "cgstRate"=> floatVal($cgstRate),
                "igstRate"=> floatVal($igstRate), 
                "cessRate"=> 0, 
                "cessNonAdvol"=> 0
            ];

            $mainHsnCode = (!empty($trans->hsn_code))?intVal($trans->hsn_code):"";
        endforeach;

        $ewbData['from_address'] = str_replace(["\r\n", "\r", "\n",'"'], " ", $ewbData['from_address']);
        $orgAdd1 = substr($ewbData['from_address'],0,100);
        $orgAdd2 = (strlen($ewbData['from_address']) > 100)?substr($ewbData['from_address'],100,200):"";

        $ewbData['ship_address'] = str_replace(["\r\n", "\r", "\n",'"'], " ", $ewbData['ship_address']);
        $toAddr1 = substr($ewbData['ship_address'],0,100);
        $toAddr2 = (strlen($ewbData['ship_address']) > 100)?substr($ewbData['ship_address'],100,200):"";
                    
        $billData["supplyType"] = $ewbData['supply_type'];
        $billData["subSupplyType"] = $ewbData['sub_supply_type'];
        $billData["subSupplyDesc"] = "";
        $billData["docType"] = $ewbData['doc_type'];
        $billData["docNo"] = getPrefixNumber($invData->trans_prefix,$invData->trans_no);
        $billData["docDate"] = date("d/m/Y",strtotime($invData->trans_date));
        $billData["fromGstin"] = $orgData->company_gst_no;
        $billData["fromTrdName"] = $orgData->company_name;
        $billData["fromAddr1"] = $orgAdd1;
        $billData["fromAddr2"] = $orgAdd2;
        $billData["fromPlace"] = $cityDataFrom->name;
        $billData["fromPincode"] = (int) $ewbData['from_pincode'];
        $billData["fromStateCode"] = (int) $orgData->company_state_code;
        $billData["actFromStateCode"] = (int) $orgData->company_state_code;
        $billData["toGstin"] = (!empty($partyData->gstin))?$partyData->gstin:"URP";
        $billData["toTrdName"] = $partyData->party_name;
        $billData["toAddr1"] = $toAddr1;
        $billData["toAddr2"] = $toAddr2;
        $billData["toPlace"] = $cityDataTo->name;
        $billData["toPincode"] = (int) $ewbData['ship_pincode']; 
        $billData["toStateCode"] = (int) $cityDataTo->state_code;
        $billData["actToStateCode"] = (int) $cityDataTo->state_code;
        $billData['transactionType'] = (int) $ewbData['transaction_type'];
        $billData['dispatchFromGSTIN'] = "";
        $billData['dispatchFromTradeName'] = "";
        $billData['shipToGSTIN'] = "";
        $billData['shipToTradeName'] = "";
        $billData["otherValue"] = floatVal(($invData->net_amount + ($invData->round_off_amount * -1)) - ($invData->taxable_amount + $invData->igst_amount));
        $billData["totalValue"] = floatVal($invData->taxable_amount);
        $billData["cgstValue"] = ($cityDataTo->state_code == $orgData->company_state_code)?floatVal($invData->cgst_amount):0;
        $billData["sgstValue"] = ($cityDataTo->state_code == $orgData->company_state_code)?floatVal($invData->sgst_amount):0;
        $billData["igstValue"] = ($cityDataTo->state_code != $orgData->company_state_code)?floatVal($invData->igst_amount):0;
        $billData["cessValue"] = 0;
        $billData['cessNonAdvolValue'] = 0;
        $billData["totInvValue"] = floatVal($invData->net_amount);
        $billData["transporterId"] = $ewbData['transport_id'];
        $billData["transporterName"] = $ewbData['transport_name'];
        $billData["transDocNo"] = $ewbData['transport_doc_no'];
        $billData["transMode"] = $ewbData['trans_mode']; 
        $billData["transDistance"] = $ewbData['trans_distance'];
        $billData["transDocDate"] = (!empty($ewbData['transport_doc_date']))?date("d/m/Y",strtotime($ewbData['transport_doc_date'])):"";
        $billData["vehicleNo"] = $ewbData['vehicle_no'];
        $billData["vehicleType"] = $ewbData['vehicle_type'];
        $billData['mainHsnCode'] = $mainHsnCode;
        $billData['itemList']=$itemList;
        
		$postData['ewbData'] = $billData;
        
		return $postData;
    }

    /* Generate New Eway Bill */
    public function generateEwayBill($data){
        $ref_id = $data['ref_id'];
        $postData = $this->ewbJsonSingle($data);
        //print_r($postData);exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/ewayBill",
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
			$resLog = [
                'id' => '',
                'type' => 2,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->ebillLog,$resLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$resLog = [
                    'id' => '',
                    'type' => 2,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $resLog = [
                    'id' => '',
                    'type' => 2,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);

                $this->edit("trans_main",['id'=>$data['ref_id']],['ewb_status'=>1,'eway_bill_no'=>$responseEwaybill->data->eway_bill_no]);

                return ['status'=>1,'message'=>'E-way Bill Generated successfully.'];
            endif;
        endif;
    }

    /* SYNC Eway Bill Data From GOV. Portal */
    public function syncEwayBill($data){
        $ref_id = $data['ref_id'];
        $postData = $this->ewbJsonSingle($data);
        //print_r($postData);exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/syncEwayBill",
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
			$resLog = [
                'id' => '',
                'type' => 6,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->ebillLog,$resLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$resLog = [
                    'id' => '',
                    'type' => 6,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $resLog = [
                    'id' => '',
                    'type' => 6,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEwaybill->data->cancel_reason))?$calcelReason[$responseEwaybill->data->cancel_reason]:"";
                $ewbStatus = (!empty($responseEwaybill->data->cancel_reason))?3:2;

                $this->edit("trans_main",['id'=>$data['ref_id']],['ewb_status'=>$ewbStatus,'eway_bill_no'=>$responseEwaybill->data->eway_bill_no,'close_reason'=>$cancel_reason,'close_date'=>(!empty($responseEwaybill->data->cancel_date))?$responseEwaybill->data->cancel_date:NULL]);

                return ['status'=>1,'message'=>'E-way Bill SYNC successfully.','data'=>$responseEwaybill];
            endif;
        endif;
    }

    /* Cancel Eway Bill Usin Eway Bill No. */
    public function cancelEwayBill($postData){
        $ref_id = $postData['ref_id'];

        $orgData = $this->getCompanyInfo();
        $postData['Gstin'] = $orgData->company_gst_no;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/cancelEwayBill",
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
			$resLog = [
                'id' => '',
                'type' => 7,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->ebillLog,$resLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEwaybill = json_decode($response,false);	
                        
            if(isset($responseEwaybill->status) && $responseEwaybill->status == 0):				
				$resLog = [
                    'id' => '',
                    'type' => 7,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-way Bill Error #: '. $responseEwaybill->error_message,'data'=>$responseEwaybill->data ];
            else:						
                $resLog = [
                    'id' => '',
                    'type' => 7,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEwaybill->data->cancel_reason))?$calcelReason[$responseEwaybill->data->cancel_reason]:"";
                $cancel_date = (!empty($responseEwaybill->data->cancel_date))?date("Y-m-d H:i:s",strtotime($responseEwaybill->data->cancel_date)):NULL;

                $this->edit("trans_main",['id'=>$ref_id],['ewb_status'=>3,'close_reason'=>$cancel_reason,'close_date'=>$cancel_date]);

                return ['status'=>1,'message'=>'E-way Bill Cancel successfully.','data'=>$responseEwaybill];
            endif;
        endif;
    }

    //Recalculate Invoice Values
    public function calculateInvoiceValue($invData){
        foreach($invData->itemList as &$row):
            $row->taxable_amount += $row->exp_taxable_amount;
            $row->gst_amount += $row->exp_gst_amount;
            $row->amount = $row->taxable_amount + $row->disc_amount;
            $row->price = round(($row->amount / $row->qty),2);
            
            $row->cgst_amount = $row->sgst_amount = $row->igst_amount = 0;
            $row->cgst_per = $row->sgst_per = $row->igst_per = 0;
            if($invData->gst_type == 1):
                $row->cgst_per = $row->sgst_per = round(($row->gst_per / 2),2);
                $row->cgst_amount = $row->sgst_amount = round(($row->gst_amount / 2),2);                               
            elseif($invData->gst_type == 2):
                $row->igst_per = $row->gst_per;
                $row->igst_amount = $row->gst_amount;
            endif;

            $row->net_amount = $row->taxable_amount + $row->gst_amount;
        endforeach;

        $invData->taxable_amount = $invData->taxable_amount + array_sum(array_column($invData->itemList,'exp_taxable_amount'));

        return $invData;
    }

    /* Generate Einvoice Json */
    public function einvJson($data){
        $postData = array();
		$ref_id = $data['ref_id'];

        $orgData = $this->getCompanyInfo();

        $invData = $this->salesInvoice->getInvoice($ref_id);
        $invData = $this->calculateInvoiceValue($invData);

        $partyData = $this->party->getParty($invData->party_id);
        $partyData->gstin = (!empty(trim($invData->gstin)))?$invData->gstin:"URP";

        $disCityData = $this->party->getCity($data['dispatch_city']);
        $bilCityData = $this->party->getCity($data['billing_city']);
        $shipCityData = $this->party->getCity($data['ship_city']);

        $postData['Gstin'] = $orgData->company_gst_no;
        $postData['companyInfo'] = [
            'name' => $orgData->company_name,
            'email' => $orgData->company_email,
            'phone_no' => $orgData->company_contact,
            'contact_no' => $orgData->company_contact,
            'country_name' => $orgData->company_country,
            'state_name' => $orgData->company_state,
            'city_name' => $orgData->company_city,
            'address' => $orgData->company_address,
            'pincode' => $orgData->company_pincode,
            'gst_no' => $orgData->company_gst_no,
            'pan_no' => $orgData->company_pan_no,
            'state_code' => $orgData->company_state_code
        ];

        $postData['partyInfo'] = [
            'name' => $partyData->party_name,
            'gst_no' => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$partyData->gstin:"URP",
            'pan_no' => $partyData->party_pan,            
            'email' => $partyData->party_email,
            'contact_email' => $partyData->contact_email,
            'phone_no' => $partyData->party_phone,
            'contact_no' => $partyData->party_mobile,
            'billing_address' => str_replace('"',"",((!empty($data['billing_address']))?$data['billing_address']:$partyData->party_address)),
            'billing_pincode' => (!empty($data['billing_pincode']))?$data['billing_pincode']:$partyData->party_pincode,
            'billing_country_name' => (!empty($data['billing_city']))?$bilCityData->country_name:$partyData->country_name,
            'billing_state_name' => (!empty($data['billing_city']))?$bilCityData->state_name:$partyData->state_name,
            'billing_city_name' => (!empty($data['billing_city']))?$bilCityData->name:$partyData->city_name,
            'billing_state_code' => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$bilCityData->state_code:"96",
            'ship_address' => str_replace('"',"",((!empty($data['ship_address']))?$data['ship_address']:$partyData->party_address)),
            'ship_pincode' => (!empty($data['ship_pincode']))?$data['ship_pincode']:$partyData->party_pincode,
            'ship_country_name' => (!empty($data['ship_city']))?$shipCityData->country_name:$partyData->country_name,
            'ship_state_name' => (!empty($data['ship_city']))?$shipCityData->state_name:$partyData->state_name,
            'ship_city_name' => (!empty($data['ship_city']))?$shipCityData->name:$partyData->city_name,
            'ship_state_code' => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$shipCityData->state_code:"96",
        ];        

        $einvData = array();
        $einvData["Version"] = "1.1";

        $einvData["TranDtls"] = [
            "TaxSch" => "GST", 
            "SupTyp" => $data['type_of_transaction'], 
            "RegRev" => "N", 
            "EcmGstin" => null, 
            "IgstOnIntra" => "N" 
        ];

        $einvData["DocDtls"] = [
            "Typ" => $data['doc_type'], 
            "No" => getPrefixNumber($invData->trans_prefix,$invData->trans_no), 
            "Dt" => date("d/m/Y",strtotime($invData->trans_date))
        ];

        $orgData->company_address = str_replace(["\r\n", "\r", "\n"], " ", $orgData->company_address);
        $orgAdd1 = substr($orgData->company_address,0,100);
        $orgAdd2 = (strlen($orgData->company_address) > 100)?substr($orgData->company_address,100,200):"";
        $orgData->company_contact = str_replace(["+"," ","-"],"",$orgData->company_contact);
        $einvData["SellerDtls"] = [
            "Gstin" => $orgData->company_gst_no, 
            "LglNm" => $orgData->company_name,
            "TrdNm" => $orgData->company_name, 
            "Addr1" => $orgAdd1, 
            "Loc" => $orgData->company_city,  
            "Pin" => (int) $orgData->company_pincode,
            "Stcd" => $orgData->company_state_code, 
            "Ph" => $orgData->company_contact,  
            "Em" => $orgData->company_email
        ];
        if(strlen($orgAdd2)):
            $einvData["SellerDtls"]['Addr2'] = $orgAdd2;
        endif;

        $billingAddress = (!empty($data['billing_address']))?$data['billing_address']:$partyData->party_address;
        $billingAddress = str_replace(["\r\n", "\r", "\n"], " ", $billingAddress);
        $partyAdd1 = substr($billingAddress,0,100);
        $partyAdd2 = (strlen($billingAddress) > 100)?substr($billingAddress,100,200):"";
        $billingPincode = (!empty($data['billing_pincode']))?$data['billing_pincode']:$partyData->party_pincode;
        $einvData["BuyerDtls"] = [
            "Gstin" => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$partyData->gstin:"URP", 
            "LglNm" => $partyData->party_name, 
            "TrdNm" => $partyData->party_name,
            "Pos" => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$bilCityData->state_code:"96", 
            "Addr1" => $partyAdd1, 
            "Loc" => $bilCityData->name, 
            "Pin" => (int) $billingPincode,
            "Stcd" => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$bilCityData->state_code:"96", 
            "Ph" => (!empty($partyData->party_mobile))?trim(str_replace(["+","-"],"",$partyData->party_mobile)):null, 
            "Em" => (!empty($partyData->contact_email))?$partyData->contact_email:null
        ];
        if(strlen($partyAdd2) > 3):
            $einvData["BuyerDtls"]['Addr2'] = $partyAdd2;
        endif;

        $dispatchAddress = (!empty($data['dispatch_address']))?$data['dispatch_address']:$orgData->company_address;
        $dispatchAddress = str_replace(["\r\n", "\r", "\n"], " ", $dispatchAddress);
        $dispatchAdd1 = substr($dispatchAddress,0,100);
        $dispatchAdd2 = (strlen($dispatchAddress) > 100)?substr($dispatchAddress,100,200):"";
        $dispatchPincode = (!empty($data['dispatch_pincode']))?$data['dispatch_pincode']:$orgData->company_pincode;
        $einvData["DispDtls"] = [
            "Nm" => $orgData->company_name,
            "Addr1" => $dispatchAdd1,  
            "Loc" => $disCityData->name,  
            "Pin" => (int) $dispatchPincode,
            "Stcd" => $disCityData->state_code, 
        ];
        if(strlen($dispatchAdd2)):
            $einvData["DispDtls"]['Addr2'] = $dispatchAdd2;
        endif;

        $shippingAddress = (!empty($data['ship_address']))?$data['ship_address']:$partyData->party_address;
        $shippingAddress = str_replace(["\r\n", "\r", "\n"], " ", $shippingAddress);
        $shipAdd1 = substr($shippingAddress,0,100);
        $shipAdd2 = (strlen($shippingAddress) > 100)?substr($shippingAddress,100,200):"";
        $shipCode = (!empty($data['ship_pincode']))?$data['ship_pincode']:$partyData->party_pincode;
        $einvData["ShipDtls"] = [
            "Gstin" => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$partyData->gstin:"URP",
            "LglNm" => $partyData->party_name,
            "TrdNm" => $partyData->party_name, 
            "Addr1" => $shipAdd1, 
            "Loc" => $shipCityData->name,
            "Pin" => (int) $shipCode,  
            "Stcd" => (!in_array($data['type_of_transaction'],["EXPWP","EXPWOP"]))?$shipCityData->state_code:"96"
        ];
        if(strlen($shipAdd2) > 3):
            $einvData["ShipDtls"]['Addr2'] = $shipAdd2;
        endif;

        $i=1;
        foreach($invData->itemData as $row):
            $cgst_amount = 0;
            $sgst_amount = 0;
            $igst_amount = 0;

            if($invData->gst_type == 1):
                $cgst_amount = $row->cgst_amount;
                $sgst_amount = $row->sgst_amount;                
            elseif($invData->gst_type == 2):
                $igst_amount = $row->igst_amount;
            endif;

            $row->item_name = str_replace(['"'], ' ', $row->item_name);
            $einvData["ItemList"][] = [
                "SlNo" => strval($i++), 
                "PrdDesc" => $row->item_name, 
                "IsServc" => (($row->item_type == 10)?"Y":"N"), 
                "HsnCd" => $row->hsn_code, //"9613",
                // "Barcde" => "123456", 
                "Qty" => round($row->qty,2), 
                "FreeQty" => 0, 
                "Unit" => $row->unit_name, 
                "UnitPrice" => round($row->price,2), 
                "TotAmt" => round($row->amount,2), 
                "Discount" => round($row->disc_amount,2), 
                // "PreTaxVal" => 1, 
                "AssAmt" => round($row->taxable_amount,2), 
                "GstRt" => round($row->gst_per,2), 
                "IgstAmt" => round($igst_amount,2), 
                "CgstAmt" => round($cgst_amount,2), 
                "SgstAmt" => round($sgst_amount,2), 
                // "CesRt" => 5, 
                // "CesAmt" => 498.94, 
                // "CesNonAdvlAmt" => 10, 
                // "StateCesRt" => 12, 
                // "StateCesAmt" => 1197.46, 
                // "StateCesNonAdvlAmt" => 5, 
                // "OthChrg" => 10, 
                "TotItemVal" => round($row->net_amount,2), 
                // "OrdLineRef" => "3256", 
                // "OrgCntry" => "AG", 
                // "PrdSlNo" => "12345", 
                // "BchDtls" => [
                //     "Nm" => "123456", 
                //     "Expdt" => "01/08/2020", 
                //     "wrDt" => "01/09/2020" 
                // ], 
                // "AttribDtls" => [
                //     [
                //         "Nm" => "Rice", 
                //         "Val" => "10000" 
                //     ] 
                // ] 
            ];
        endforeach;        

        $einvData["ValDtls"] = [
            "AssVal" => round($invData->taxable_amount,2), 
            "CgstVal" => ($invData->gst_type == 1)?round($invData->cgst_amount,2):0, 
            "SgstVal" => ($invData->gst_type == 1)?round($invData->sgst_amount,2):0, 
            "IgstVal" => ($invData->gst_type == 2)?round($invData->igst_amount,2):0,
            // "CesVal" => 508.94, 
            // "StCesVal" => 1202.46, 
            // "Discount" => floatVal($row->disc_amount), 
            //floatVal($invData->net_amount - ($invData->taxable_amount + $invData->igst_amount));
            "OthChrg" => round((($invData->net_amount + ($invData->round_off_amount * -1)) - ($invData->taxable_amount + $invData->gst_amount)),2), 
            "RndOffAmt" => round($invData->round_off_amount,2), 
            "TotInvVal" => round($invData->net_amount,2), 
            // "TotInvValFc" => 12897.7
        ];

        /* $einvData["PayDtls"] = [
            "Nm" => "ABCDE", 
            "Accdet" => "5697389713210", 
            "Mode" => "Cash", 
            "Fininsbr" => "SBIN11000", 
            "Payterm" => "100", 
            "Payinstr" => "Gift", 
            "Crtrn" => "test", 
            "Dirdr" => "test", 
            "Crday" => 100, 
            "Paidamt" => 10000, 
            "Paymtdue" => 5000
        ]; */

        /* $einvData["RefDtls"] = [
            "InvRm" => "TEST", 
            "DocPerdDtls" => [
                "InvStDt" => "01/08/2020", 
                "InvEndDt" => "01/09/2020" 
            ], 
            "PrecDocDtls" => [
                [
                    "InvNo" => "DOC/002", 
                    "InvDt" => "01/08/2020", 
                    "OthRefNo" => "123456" 
                ] 
            ], 
            "ContrDtls" => [
                [
                    "RecAdvRefr" => "Doc/003", 
                    "RecAdvDt" => "01/08/2020", 
                    "Tendrefr" => "Abc001", 
                    "Contrrefr" => "Co123", 
                    "Extrefr" => "Yo456", 
                    "Projrefr" => "Doc-456", 
                    "Porefr" => "Doc-789", 
                    "PoRefDt" => "01/08/2020" 
                ] 
            ]
        ]; */

        /* $einvData["AddlDocDtls"] = [
            [
                "Url" => "https://einv-apisandbox.nic.in", 
                "Docs" => "Test Doc", 
                "Info" => "Document Test" 
            ]
        ]; */

        /* $einvData["ExpDtls"] = [
            "ShipBNo" => "A-248", 
            "ShipBDt" => "01/08/2020", 
            "Port" => "INABG1", 
            "RefClm" => "N", 
            "ForCur" => "AED", 
            "CntCode" => "AE"
        ]; */

        if($data['ewb_status'] == 1):
            if(!empty($data['transport_id'])):
                $einvData["EwbDtls"]["TransId"] = $data['transport_id'];
            endif;
            
            if(!empty($data['transport_name'])):
                $einvData["EwbDtls"]["TransName"] = $data['transport_name'];
            endif;
            
            $einvData["EwbDtls"]["Distance"] = intval($data['trans_distance']);
            
            if(!empty($data['transport_doc_no'])):
                $einvData["EwbDtls"]["TransDocNo"] = $data['transport_doc_no'];
            endif;
            
            if(!empty($data['transport_doc_date'])):
                $einvData["EwbDtls"]["TransDocDt"] = date("d/m/Y",strtotime($data['transport_doc_date']));
            endif;
            
            if(!empty($data['vehicle_no'])):
                $einvData["EwbDtls"]["VehNo"] = $data['vehicle_no'];
                
                if(!empty($data['vehicle_type'])):
                    $einvData["EwbDtls"]["VehType"] = $data['vehicle_type'];
                endif;
                
                if(!empty($data['trans_mode'])):
                    $einvData["EwbDtls"]["TransMode"] = $data['trans_mode'];
                endif;
            endif;
        endif;

        $postData['einvData'] = $einvData;
        return $postData;
    }

    /* Generate New E-Invoice */
    public function generateEinvoice($data){
        ini_set("precision", 14); 
        ini_set("serialize_precision", -1);
        $ref_id = $data['ref_id'];
        $postData = $this->einvJson($data);
        //print_r(json_encode($postData['einvData']));exit;

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/eInvoice",
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
			$resLog = [
                'id' => '',
                'type' => 3,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->ebillLog,$resLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEinv = json_decode($response,false);	
                        
            if(isset($responseEinv->status) && $responseEinv->status == 0):				
				$resLog = [
                    'id' => '',
                    'type' => 3,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
            else:						
                $resLog = [
                    'id' => '',
                    'type' => 3,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);

                $this->edit("trans_main",['id'=>$ref_id],['e_inv_status'=>1,'e_inv_no'=>$responseEinv->data->ack_no,'e_inv_irn'=>$responseEinv->data->irn]);

                return ['status'=>1,'message'=>$responseEinv->message];
            endif;
        endif;
    }

    /* SYNC E-Invoice From GOV. Portal */
    public function syncEinvoice($data){
        $ref_id = $data['ref_id'];
        $postData = $this->einvJson($data);

        $curlEwaybill = curl_init();
        curl_setopt_array($curlEwaybill, array(
            CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/syncEinv",
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
			$resLog = [
                'id' => '',
                'type' => 4,
                'response_status'=> "Fail",
                'response_data'=> $response,
                'created_by'=> $this->loginId,
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->store($this->ebillLog,$resLog);
			
            return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
        else:
            $responseEinv = json_decode($response,false);	
                        
            if(isset($responseEinv->status) && $responseEinv->status == 0):				
				$resLog = [
                    'id' => '',
                    'type' => 4,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);
				
                return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
            else:						
                $resLog = [
                    'id' => '',
                    'type' => 4,
                    'response_status'=> "Success",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);

                $calcelReason = [
                    1 => "Duplicate", 
                    2 => "Data entry mistake", 
                    3 => "Order Cancelled", 
                    4 => "Others"
                ];

                $cancel_reason = (!empty($responseEinv->data->cancel_reason))?$calcelReason[$responseEinv->data->cancel_reason]:"";
                $einvStatus = (!empty($responseEinv->data->cancel_reason))?3:2;

                $this->edit("trans_main",['id'=>$ref_id],['e_inv_status'=>$einvStatus,'e_inv_no'=>$responseEinv->data->ack_no,'e_inv_irn'=>$responseEinv->data->irn,'close_reason'=>$cancel_reason,'close_date'=>(!empty($responseEinv->data->cancel_date))?$responseEinv->data->cancel_date:NULL]);

                return ['status'=>1,'message'=>'E-Invoice Sync successfully.','data'=>$responseEinv];
            endif;
        endif;
    }

    /* Cancel E-Invoice on irn */
    public function cancelEinv($postData){
        $ref_id = $postData['ref_id'];

        $calcelReason = [
            1 => "Duplicate", 
            2 => "Data entry mistake", 
            3 => "Order Cancelled", 
            4 => "Others"
        ];

        if(!empty($postData['Irn'])):
            $orgData = $this->getCompanyInfo();
            $postData['Gstin'] = $orgData->company_gst_no;

            $curlEwaybill = curl_init();
            curl_setopt_array($curlEwaybill, array(
                CURLOPT_URL => "https://ebill.nativebittechnologies.in/generate/cancelEinv",
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
                $resLog = [
                    'id' => '',
                    'type' => 5,
                    'response_status'=> "Fail",
                    'response_data'=> $response,
                    'created_by'=> $this->loginId,
                    'created_at' => date("Y-m-d H:i:s")
                ];
                $this->store($this->ebillLog,$resLog);
                
                return ['status'=>2,'message'=>'Somthing is wrong1. cURL Error #:'. $error]; 
            else:
                $responseEinv = json_decode($response,false);	
                            
                if(isset($responseEinv->status) && $responseEinv->status == 0):				
                    $resLog = [
                        'id' => '',
                        'type' => 5,
                        'response_status'=> "Fail",
                        'response_data'=> $response,
                        'created_by'=> $this->loginId,
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                    $this->store($this->ebillLog,$resLog);
                    
                    return ['status'=>2,'message'=>'Somthing is wrong2. E-Invoice Error #: '. $responseEinv->error_message ];
                else:						
                    $resLog = [
                        'id' => '',
                        'type' => 5,
                        'response_status'=> "Success",
                        'response_data'=> $response,
                        'created_by'=> $this->loginId,
                        'created_at' => date("Y-m-d H:i:s")
                    ];
                    $this->store($this->ebillLog,$resLog);
                    
                    $cancel_reason = (!empty($responseEinv->data->cancel_reason))?$calcelReason[$responseEinv->data->cancel_reason]:"";
                    $cancel_date = (!empty($responseEinv->data->cancel_date))?date("Y-m-d H:i:s",strtotime($responseEinv->data->cancel_date)):NULL;

                    $this->edit("trans_main",['id'=>$ref_id],['e_inv_status'=>3,'close_reason'=>$cancel_reason,'close_date'=>$cancel_date,'trans_status'=>3]);

                    $this->invoiceReverseEffect($ref_id);

                    return ['status'=>1,'message'=>'E-Invoice Cancel successfully.','data'=>$responseEinv];
                endif;
            endif;
        else:
            $cancel_reason = $calcelReason[$postData['CnlRsn']]." - ".$postData['CnlRem'];
            $cancel_date = date("Y-m-d H:i:s");

            $this->edit("trans_main",['id'=>$ref_id],['close_reason'=>$cancel_reason,'close_date'=>$cancel_date,'trans_status'=>3]);
            $this->invoiceReverseEffect($ref_id);

            return ['status'=>1,'message'=>'Tax Invoice Cancel successfully.'];
        endif;
    }

    public function invoiceReverseEffect($ref_id){
        $transData = $this->salesInvoice->getInvoice($ref_id);
            
        foreach($transData->itemData as $row):
            if(!empty($row->ref_id)):
                $setData = Array();
                $setData['tableName'] = $this->transChild;
                $setData['where']['id'] = $row->ref_id;
                $setData['set']['dispatch_qty'] = 'dispatch_qty, - '.$row->qty;
                $this->setValue($setData);

                $queryData = array();
                $queryData['tableName'] = $this->transChild;
                $queryData['where']['id'] = $row->ref_id;
                $transRow = $this->row($queryData);

                if($transRow->qty != $transRow->dispatch_qty):
                    $this->store($this->transChild,['id'=>$row->ref_id,'trans_status'=>0]);
                    $this->store($this->transMain,['id'=>$transRow->trans_main_id,'trans_status'=>0]);
                endif;
            endif;

            if($row->stock_eff == 1):
                /** Remove Stock Transaction **/
                $this->remove($this->stockTrans,['ref_id'=>$id,'trans_type'=>2,'ref_type'=>5]);
            endif;               
        endforeach;

        if(!empty($transData->ref_id)):
            $refIds = explode(",",$transData->ref_id);
            foreach($refIds as $key=>$value):
                if($transData->from_entry_type == 5):
                    $pendingItems = $this->challan->checkChallanPendingStatus($value);
                elseif($transData->from_entry_type == 4):
                    $pendingItems = $this->salesOrder->checkSalesOrderPendingStatus($value);
                endif;
                if(empty($pendingItems)):
                    $this->store($this->transMain,['id'=>$value,'trans_status'=>0]);
                endif;
            endforeach;
        endif;

        $deleteLedgerTrans = $this->transModel->deleteLedgerTrans($id);
        if($deleteLedgerTrans == false):
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : "];
        endif;
        $deleteExpenseTrans = $this->transModel->deleteExpenseTrans($id);
        if($deleteExpenseTrans == false):
            $this->db->trans_rollback();
            return ['status'=>2,'message'=>"somthing is wrong. Error : "];
        endif;

        /**Payment Voucher Effect */
        $voucherData=$this->paymentVoucher->getReceiveVoucherByRefId($id);
        if(!empty($voucherData->id)){
            $voucherEff=$this->paymentVoucher->delete($voucherData->id);
            if($voucherEff == false):
                $this->db->trans_rollback();
                return ['status'=>2,'message'=>"somthing is wrong. Error : "];
            endif;
        }
        
        return true;
    }
    
    public function generateEinvJson($data){
        $jsonData = $this->einvJson($data);
        $validate = $this->validateEinvoiceJson($jsonData['einvData']);
        if($validate['status'] == 2):
            return $validate;
        endif;

        $invNo = $jsonData['einvData']['DocDtls']['No'];
        return ['status'=>1,'message'=>'Json Generated successfully.','json_data'=>$jsonData['einvData'],'inv_no'=>$invNo];
    }
    
    public function validateEinvoiceJson($data){
		$data = json_decode(json_encode($data));
		
		// Validate
		$validator = new JsonSchema\Validator;
		$validator->validate($data, (object)['$ref' => 'file://' . realpath('e-invoice-json-schema.json')]);

		if ($validator->isValid()):
			return ['status'=>1,'message'=>"The supplied JSON validates against the schema."];
		else:
			$errorMessage = array();
			foreach ($validator->getErrors() as $error):
				$errorMessage[] = $error['property']." -> ".$error['message'];
			endforeach;
			return ['status'=>2,'message'=>"JSON does not validate. Violations: ".implode(", ",$errorMessage)];
		endif;
	}
}
?>