<?php
defined( 'BASEPATH' )OR exit( 'No direct script access allowed' );
class ProformaInvoice extends MY_Controller{	
	private $indexPage = "proforma_invoice/index";
    private $invoiceForm = "proforma_invoice/form";

	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Proforma Invoice";
		$this->data['headData']->controller = "proformaInvoice";
		$this->data['headData']->pageUrl = "proformaInvoice";
	}
	
	public function index(){
		$this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);    
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){
        $result = $this->proformaInv->getDTRows($this->input->post());
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $row->controller = $this->data['headData']->controller;
            $sendData[] = getProformaInvoiceData($row);
        endforeach;        
        $result['data'] = $sendData;
        $this->printJson($result);
    }

	/* public function createInvoice(){
		$data = $this->input->post();
		$invMaster = new stdClass();
        $invMaster = $this->party->getParty($data['party_id']);  
		$this->data['gst_type']  = (!empty($invMaster->gstin))?((substr($invMaster->gstin,0,2) == 24)?1:2):1;		
		$this->data['from_entry_type'] = $data['from_entry_type'];
		$this->data['ref_id'] = implode(",",$data['ref_id']);
		$this->data['invMaster'] = $invMaster;
		$this->data['invItems'] = ($data['from_entry_type'] == 5)?$this->challan->getChallanItems($data['ref_id']):$this->salesOrder->getOrderItems($data['ref_id']);
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(6);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(6);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsList();
        $this->load->view($this->invoiceForm,$this->data);
	} */

    public function addInvoice(){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['gst_type'] = 1;
		$this->data['trans_prefix'] = $this->transModel->getTransPrefix(9);
        $this->data['nextTransNo'] = $this->transModel->nextTransNo(9);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales'); 
        $this->load->view($this->invoiceForm,$this->data);
    }	
	
	public function save(){
		$data = $this->input->post();
		$errorMessage = array();
	    $data['currency'] = '';$data['inrrate'] = 0;
		if(empty($data['party_id'])):
			$errorMessage['party_id'] = "Party name is required.";
		else:
			$partyData = $this->party->getParty($data['party_id']); 
			if(floatval($partyData->inrrate) <= 0):
				$errorMessage['party_id'] = "Currency not set.";
			else:
				$data['currency'] = $partyData->currency;
				$data['inrrate'] = $partyData->inrrate;
			endif;
		endif;
		if(empty($data['item_id'][0]))
			$errorMessage['item_name_error'] = "Product is required.";
		
		if(!empty($data['item_id'])):
			$i=1;
			foreach($data['item_id'] as $key=>$value):
				if(empty($data['price'][$key])):
					$errorMessage['price'.$i] = "Price is required.";
				endif;
				$i++;
			endforeach;
		endif;
		
		if(empty($data['term_id'][0]))
			$errorMessage['term_id'] = "Terms Conditions is required.";

		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
		else:
			$data['terms_conditions'] = "";$termsArray = array();
			if(isset($data['term_id']) && !empty($data['term_id'])):
				foreach($data['term_id'] as $key=>$value):
					$termsArray[] = [
						'term_id' => $value,
						'term_title' => $data['term_title'][$key],
						'condition' => $data['condition'][$key]
					];
				endforeach;
				$data['terms_conditions'] = json_encode($termsArray);
			endif;
			
			if($data['apply_round'] == 1):
				$data['net_amount_total'] = $data['net_amount_total'] - $data['round_off'];
				$data['round_off'] = 0; 
			endif;
			
			$masterData = [ 
				'id' => $data['proforma_id'],
				'entry_type' => $data['entry_type'],
				'from_entry_type' => $data['reference_entry_type'],
				'ref_id' => $data['reference_id'],
				'trans_no' => $data['inv_no'], 
				'trans_prefix' => $data['inv_prefix'],
				'trans_date' => date('Y-m-d',strtotime($data['inv_date'])), 
				'party_id' => $data['party_id'],
				'party_name' => $data['party_name'],
				'party_state_code' => $data['party_state_code'],
				'gst_type' => $data['gst_type'], 
				'gst_applicable' => $data['gst_applicable'],
				'sales_type' => $data['sales_type'], 
				// 'challan_no' => $data['challan_no'], 
				'doc_no'=>$data['so_no'],
				'ref_by' => $data['ref_by'],
				// 'total_packet' => $data['total_packet'],
				// 'transport_name' => $data['transport'],
				// 'shipping_address' => $data['supply_place'],
				'total_amount' => $data['amount_total'],
				'taxable_amount' => $data['amount_total'],
				'gst_amount' => $data['igst_amt_total'],
				'freight_amount' => $data['freight_amt'],
				'igst_amount' => $data['igst_amt_total'], 
				'cgst_amount' => $data['cgst_amt_total'], 
				'sgst_amount' => $data['sgst_amt_total'], 
				'disc_amount' => $data['disc_amt_total'],
				'apply_round' => $data['apply_round'], 
				'round_off_amount' => $data['round_off'], 
				'net_amount' => $data['net_amount_total'],
				'terms_conditions' => $data['terms_conditions'],
                'remark' => $data['remark'],
				'currency' => $data['currency'],
                'inrrate' => $data['inrrate'],
				'created_by' => $this->session->userdata('loginId')
			];
				
			$itemData = [
				'id' => $data['trans_id'],
				'from_entry_type' => $data['from_entry_type'],
				'ref_id' => $data['ref_id'],
				'item_id' => $data['item_id'],
				'item_name' => $data['item_name'],
				'item_type' => $data['item_type'],
				'item_code' => $data['item_code'],
				'item_desc' => $data['item_desc'],
				'unit_id' => $data['unit_id'],
				'unit_name' => $data['unit_name'],
				'stock_eff' => $data['stock_eff'],
				'hsn_code' => $data['hsn_code'],
				'qty' => $data['qty'],
				'price' => $data['price'],
				'amount' => $data['amount'] + $data['disc_amount'],
				'taxable_amount' => $data['amount'],				
				'gst_per' => $data['gst_per'],
				'gst_amount' => $data['igst_amt'],
				'igst_per' => $data['igst'],
				'igst_amount' => $data['igst_amt'],
				'sgst_per' => $data['sgst'],
				'sgst_amount' => $data['sgst_amt'],
				'cgst_per' => $data['cgst'],
				'cgst_amount' => $data['cgst_amt'],
				'disc_per' => $data['disc_per'],
				'disc_amount' => $data['disc_amount'],
				'item_remark' => $data['item_remark'],
				'net_amount' => $data['net_amount']
			];

			$this->printJson($this->proformaInv->save($masterData,$itemData));
		endif;
	}

	public function edit($id){
		$this->data['from_entry_type'] = 0;
		$this->data['ref_id'] = "";
		$this->data['invoiceData'] = $this->proformaInv->getInvoice($id);
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemList(1);
        $this->data['unitData'] = $this->item->itemUnits();
		$this->data['locationData'] = $this->store->getStoreLocationList();
		$this->data['terms'] = $this->terms->getTermsListByType('Sales'); 
        $this->load->view($this->invoiceForm,$this->data);
	}

	public function delete(){
		$id = $this->input->post('id');
		if(empty($id)):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
		else:
			$this->printJson($this->proformaInv->deleteInv($id));
		endif;
	}

	public function getPartyItems(){
		$this->printJson($this->item->getPartyItems($this->input->post('party_id')));
	}
	
	public function invoice_pdf()
	{ 
		$postData = $this->input->post();
		$original=0;$duplicate=0;$triplicate=0;$header_footer=0;$extra_copy=0;
		if(isset($postData['original'])){$original=1;}
		if(isset($postData['duplicate'])){$duplicate=1;}
		if(isset($postData['triplicate'])){$triplicate=1;}
		if(isset($postData['header_footer'])){$header_footer=1;}
		if(!empty($postData['extra_copy'])){$extra_copy=$postData['extra_copy'];}
		
		$sales_id=$postData['printsid'];
		$salesData = $this->salesInvoice->getInvoice($sales_id);
		$companyData = $this->salesInvoice->getCompanyInfo();
		
		$partyData = $this->party->getParty($salesData->party_id);
		
		$response="";
		$letter_head=base_url('assets/images/letterhead_top.png');
		
		$currencyCode = "INR";
		$symbol = "";
		
		$response="";$inrSymbol=base_url('assets/images/inr.png');
		$headerImg = base_url('assets/images/rtth_lh_header.png');
		$footerImg = base_url('assets/images/rtth_lh_footer.png');
		$logoFile=(!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
		$logo=base_url('assets/images/'.$logoFile);
		$auth_sign=base_url('assets/images/rtth_sign.png');
		
		$gstHCol='';$gstCol='';$blankTD='';$bottomCols=2;$GSTAMT=$salesData->igst_amount;
		$subTotal=$salesData->taxable_amount;
		$itemList='<table class="table table-bordered poItemList">
					<thead><tr class="text-center">
						<th style="width:6%;">Sr.No.</th>
						<th class="text-left">Description of Goods</th>
						<th style="width:10%;">HSN/SAC</th>
						<th style="width:10%;">Qty</th>
						<th style="width:10%;">Rate</th>
						<th style="width:8%;">GST %</th>
						<th style="width:11%;">Amount</th>
					</tr></thead><tbody>';
		
		// Terms & Conditions
		$blankLines=25;if(!empty($header_footer)){$blankLines=25;}
		$terms = '<table class="table">';$t=0;$tc=new StdClass;	
		$terms1 = '<tr><td style="width:60%;vertical-align:top;">
						<table class="table"><tr><th style="width:60%;text-align:left;">Terms & Conditions : </th></tr>';
					
		$terms2 = '<td style="width:40%; vertical-align:top;">
						<table class="table">
							<tr>
							<th style="width:40%;vertical-align:top;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'</th>
							</tr>
							<tr><td height="40"></td></tr>
							<tr><td class="text-center fs-14">(Authorised Signatury)</td></tr>
						</table>';
		$termsLine=0;
		if(!empty($salesData->terms_conditions)){
			$tc=json_decode($salesData->terms_conditions);$termsLine=count($tc);
			$blankLines=21 - count($tc);if(!empty($header_footer)){$blankLines=21 - count($tc);}
			foreach($tc as $trms):
				$terms1 .= '<tr><td style="width:60%;font-size:12px;text-align:left;">'.($t+1).'. <i>'.$trms->condition.'</i></td></tr>';
				$t++;
			endforeach;
		}else{
			$tc = array();
			$terms1 .= '<tr>
							<td style="width:65%;font-size:12px;">Subject to RAJKOT Jurisdiction</td>
							<th rowspan="'.count($tc).'" style="width:35%;vertical-align:bottom;text-align:center;font-size:1rem;padding:5px 2px;">
								For, '.$companyData->company_name.'<br>
								<!--<img src="'.$auth_sign.'" style="width:120px;">-->
							</th>
					</tr>';
		}
		$terms1 .= '</table>';
		$terms .= $terms1.$terms2.'</td></tr></table>';
		
		$subTotal=0;$lastPageItems = '';$pageCount = 0; $sgstAmt = 0; $cgstAmt=0; $igstAmt=0; $taxableAmt=0;
		$i=1;$tamt=0;$cgst=9;$sgst=9;$cgst_amt=0;$sgst_amt=0;$netamt=0;$igst=0;$hsnCode='';$total_qty=0;$page_qty = 0;$page_amount = 0;
		$pageData = array();
		
		$itmLine=26;if(!empty($header_footer)){$itmLine=26;}
		$orderData = $this->salesInvoice->salesTransactions($sales_id);
		
		$totalItems = count($orderData);
		$firstArr = $orderData;$secondArr = Array();$lastPageRow = $totalItems;$pagedArray = Array();$rowPerPage = $itmLine;
		if($totalItems > $itmLine){
			$rowPerPage = ($totalItems > $itmLine) ? $itmLine : $itmLine ;
			$lastPageRow = $totalItems % $rowPerPage;
			$firstArr = array_slice($orderData,($totalItems - $lastPageRow),$lastPageRow);
			$secondArr = array_slice($orderData,0,($totalItems - $lastPageRow));
		}
		
		$pagedArray = array_chunk($secondArr,$rowPerPage);
		
		$pagedArray[] = $firstArr;
		$blankLines = $itmLine - $lastPageRow;
		
		$x=1;$totalPage = count($pagedArray);$i=1;$highestGst = 0;$itmGst = Array();
		foreach($pagedArray as $tempData){
			$page_qty = 0;$page_amount = 0;$pageItems = '';$page_nos = 0;$prevLines = 0;

			if(!empty($tempData)){
				foreach ($tempData as $row){
					$pageItems.='<tr>';
						$pageItems.='<td class="text-center" height="26">'.$i.'</td>';
						$pageItems.='<td class="text-left">'.$row->item_name.'</td>';
						$pageItems.='<td class="text-center">'.$row->hsn_code.'</td>';
						$pageItems.='<td class="text-center">'.sprintf('%0.2f', $row->qty).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->price).'</td>';
						$pageItems.='<td class="text-center">'.floatval($row->igst_per).'</td>';
						$pageItems.='<td class="text-right">'.sprintf('%0.2f', $row->amount).'</td>';
					$pageItems.='</tr>';
					
					$total_qty += $row->qty;$page_qty += $row->qty;$page_amount += $row->amount; 
					if($this->CMID == 1):
						$subTotal += $row->amount; 
						$cgstAmt += $row->cgst_amount;
						$sgstAmt += $row->sgst_amount;
						$igstAmt += $row->igst_amount;
						$taxableAmt+= $row->taxable_amount;
					else:
						$subTotal += $row->amount ;
						$cgstAmt += $salesData->sgst_amount + round(($salesData->freight_gst / 2),2);
						$sgstAmt += $salesData->cgst_amount + round(($salesData->freight_gst / 2),2);
						$taxableAmt+= $subTotal + $salesData->freight_amount;
					endif;
					$itmGst[] = $row->igst_per;
					$i++;
				}
			}
			if($x==$totalPage){
				$pageData[$x-1]= '';
				$lastPageItems = $pageItems;
			}else{
				$pageData[$x-1]=$itemList.$pageItems.'</tbody></table><div class="text-right"><i>Continue to Next Page</i></div>';
			} $x++;
		}
		$fgst = round(($salesData->freight_gst / 2),2);
		$rwspan= 4;$cgstPer = $sgstPer = round((MAX($itmGst)/2),2);$igstPer = round(MAX($itmGst),2);
		$gstAmount = $salesData->cgst_amount + $salesData->sgst_amount;
		
		$gstRow='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">CGST '.$cgstPer.'%</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->cgst_amount).'</td>';
		$gstRow.='</tr>';
		
		$gstRow.='<tr>';
			$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">SGST '.$sgstPer.'%</td>';
			$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', $salesData->sgst_amount).'</td>';
		$gstRow.='</tr>';
		
		$party_gstin = $salesData->gstin;$party_stateCode = $salesData->party_state_code;
		
		if(!empty($party_gstin)){
			if($party_stateCode!="24"){
				$gstRow='<tr>';
					$gstRow.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;">IGST '.$igstPer.'%</td>';
					$gstRow.='<td class="text-right" style="border-top:0px !important;">'.sprintf('%0.2f', ($salesData->igst_amount)).'</td>';
				$gstRow.='</tr>';$rwspan= 3;$gstAmount = $salesData->igst_amount;
			}
		}
		$totalCols = 8;
		$itemList .= $lastPageItems;
		if($i<$blankLines){
			for($z=$i;$z<=$blankLines;$z++)
			{$itemList.='<tr><td height="26">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';}
		}
		
		$itemList.='<tr>';
			$itemList.='<td colspan="3" class="text-right" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Total Qty</b></td>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $total_qty).'</th>';
			$itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Sub Total</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $salesData->total_amount).'</th>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="'.$rwspan.'" class="text-left" style="vartical-align:top;border-top:1px solid #000;border-right:1px solid #000;"><b>Bank Name : </b>'.$companyData->company_bank_name.'<br>
			<b>A/c. No. : </b>'.$companyData->company_acc_no.'<br>
			<b>IFSC Code : </b>'.$companyData->company_ifsc_code.'
			</td>';
			$itemList.='<td colspan="2" class="text-right" style="border-top:0px !important;border-right:1px solid #000;"></td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;"></td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="2" class="text-right" style="border:1px solid #000;border-left:0px solid #000;">Taxable Amount</th>';
			$itemList.='<th class="text-right" style="border:1px solid #000;border-left:0px solid #000;">'.sprintf('%0.2f', $salesData->taxable_amount).'</th>';
		$itemList.='</tr>';
		
		$itemList.=$gstRow;
		
		$itemList.='<tr>';
			$itemList.='<td colspan="4" rowspan="2" class="text-left" style="vartical-align:top;border:1px solid #000;border-left:0px;">
				<i><b>Total GST : </b>'.numToWordEnglish($gstAmount).'</i><br>
				<i><b>Bill Amount : </b>'.numToWordEnglish($salesData->net_amount).'</i>
			</td>';
			$itemList.='<td colspan="2" class="text-right" style="border-right:1px solid #000;">Round Off</td>';
			$itemList.='<td class="text-right" style="border-top:0px !important;border-left:0px;">'.sprintf('%0.2f', $salesData->round_off_amount).'</td>';
		$itemList.='</tr>';
		
		$itemList.='<tr>';
			$itemList.='<th colspan="2" class="text-right" style="border-top:1px solid #000;border-right:1px solid #000;font-size:14px;">Payable Amount</th>';
			$itemList.='<th class="text-right" height="40" style="border-top:1px solid #000;border-left:0px;font-size:14px;">'.sprintf('%0.2f', $salesData->net_amount).'</th>';
		$itemList.='</tr>';
		$itemList.='<tbody></table>';
		
		$pageData[$totalPage-1] .= $itemList;
		$pageData[$totalPage-1] .= $terms;
		
		$invoiceType=array();
		$invType = array("ORIGINAL","DUPLICATE","TRIPLICATE","EXTRA COPY");$i=0;
		$baseTable='<table class="poTopTable" style="margin-bottom:5px;">';
		foreach($invType as $it){
			$invoiceType[$i++]='<tr>
				<th style="width:35%;letter-spacing:2px;border:0px;font-size:14px;" class="text-left" >GSTIN: '.$companyData->company_gst_no.'</th>
				<th style="width:30%;letter-spacing:2px;border:0px;font-size:15px;" class="text-center">PROFORMA INVOICE</th>
				<th style="width:35%;letter-spacing:2px;border:0px;font-size:14px;" class="text-right">'.$it.'</th>
			</tr>';
		}

		$gstJson=json_decode($partyData->json_data);
		$partyAddress=(!empty($gstJson->{$salesData->gstin})?$gstJson->{$salesData->gstin}:'');
		$place_of_supply = '';
		if(!empty($party_stateCode)){
			$stateData = $this->party->getStateByIdOrCode('',$party_stateCode);
			if(!empty($stateData)){$place_of_supply = $party_stateCode.'-'.$stateData->name;}
		}

		$baseDetail='<table class="poTopTable" style="margin-bottom:5px;">
						<tr>
							<td style="width:65%;" rowspan="3">
								<table>
									<tr><td style="vertical-align:top;"><b>M/s. : '.$salesData->party_name.'</b></td></tr>
									<tr><td class="text-left" style="">'.(!empty($partyData->party_address)?$partyData->party_address:'').'<br>'.(!empty($partyData->party_phone)?'<b>Phone No:</b> '.$partyData->party_phone:'').'</td></tr>
									<tr><td class="text-left" style=""><b>GSTIN : '.$salesData->gstin.'</b></td></tr>
								</table>
							</td>
							<td style="width:14%;border-right:0px;"><b>Invoice No.</b></td>
							<td style="width:21%;border-left:0px;">: '.$salesData->trans_prefix.$salesData->trans_no.'</td>
						</tr>
						<tr>
							<td style="border-right:0px;"><b>Date : </b></td>
							<td style="border-left:0px;">: '.date('d/m/Y', strtotime($salesData->trans_date)).'</td>
						</tr>
						<tr>
							<td style="border-right:0px;"><b>Supply of Place</b></td>
							<td style="border-left:0px;">: '.(!empty($partyData->city_name)?$partyData->city_name:'').'</td>
						</tr>
					</table>';
				
		$orsp='';$drsp='';$trsp='';
		$htmlHeader = '<table class="topTable">
						<tr>
							<th colspan="3" class="org_title text-uppercase text-center bg-light" style="font-size:1.2rem;">'.$companyData->company_name.'</th>
						</tr>
						<tr><td colspan="3" class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.' | Mobile: '.$companyData->company_contact.'</td></tr>
					';
		$htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
						<tr>
							<td style="width:25%;font-size:12px;">INV No. & Date : '.$salesData->trans_prefix.$salesData->trans_no.'-'.formatDate($salesData->trans_date).'</td>
							<td style="width:25%;font-size:12px;"></td>
							<td style="width:25%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
						</tr>
					</table>';
	
		$mpdf = $this->m_pdf->load();
		$i=1;$p='P';
		$pdfFileName=base_url('assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf');
		$fpath='/assets/uploads/sales/sales_invoice_'.$sales_id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/css/bill_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		
		if(!empty($header_footer)){
			$mpdf->SetWatermarkImage($logo,0.05,array(120,60));
			$mpdf->showWatermarkImage = true;
		}
		
		if(!empty($original)){
			$pdfData = '';
			if(!empty($header_footer)){
				$htmlHeader .= $invoiceType[0].'</table>';
				$mpdf->SetHTMLHeader($htmlHeader);
				$mpdf->SetHTMLFooter($htmlFooter);
				$pdfData = $baseDetail;
			}else{
				$pdfData =$baseDetail;
			}
			foreach($pageData as $pg){
				$mpdf->AddPage('P','','','','',7,7,28,7,5,6);
				$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv1">'.$pdfData.$pg.'</div></div>');
			}
		}
		
		if(!empty($duplicate)){
			foreach($pageData as $pg){
				if(!empty($header_footer)){
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}else{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[1].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		if(!empty($triplicate)){
			foreach($pageData as $pg){
				if(!empty($header_footer)){
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}else{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[2].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		
		for($x=0;$x<$extra_copy;$x++){
			foreach($pageData as $pg){
				if(!empty($header_footer)){
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}else{
					$mpdf->AddPage('P','','','','',0,0,33,3,3,0);
					$mpdf->WriteHTML('<div style="position:relative;"><div class="poDiv">'.$invoiceType[3].$baseDetail.$pg.'</div></div>');
				}
			}
		}
		$mpdf->Output($pdfFileName,'I');
	}

	//Created BY Karmi @22/02/2022
	public function getPartyOrders(){
		$this->printJson($this->proformaInv->getPartyOrders($this->input->post('party_id')));
	}
	
	/* Stock Effect */
	public function updateStock(){
		$data = $this->input->post();
		
		if(empty($data['id'])):
			$this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
		else:
			$this->printJson($this->proformaInv->updateStock($data));
		endif;
	}
}
?>