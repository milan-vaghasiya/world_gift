<?php
class SalesReport extends MY_Controller
{
    private $indexPage = "report/sales_report/index";
    private $order_monitor = "report/sales_report/order_monitor";
    private $dispatch_plan = "report/sales_report/dispatch_plan";
    private $packing_report = "report/sales_report/packing_report";
    private $dispatch_summary = "report/sales_report/dispatch_summary";
    private $item_history = "report/sales_report/item_history";
    private $sales_enquiry = "report/sales_report/sales_enquiry";
    private $monthlySales = "report/sales_report/monthly_sales";
    private $dispatch_plan_summary = "report/sales_report/dispatch_plan_summary";
    private $enquiry_monitoring = "report/sales_report/enquiry_monitoring";
    private $sales_target = "report/sales_report/sales_target";
    private $userWiseSale = "report/sales_report/user_wise_sale";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Sales Report";
		$this->data['headData']->controller = "reports/salesReport";
		$this->data['floatingMenu'] = $this->load->view('report/sales_report/floating_menu',[],true);       
	    $this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production','In Challan','Out Challan','Tools Issue','Stock Journal','Packing Material','Packing Product','Rejection Scrap','Production Scrap');
        $this->data['monthData'] = ['2021-04-01','2021-05-01','2021-06-01','2021-07-01','2021-08-01','2021-09-01','2021-10-01','2021-11-01','2021-12-01','2022-01-01','2022-02-01','2022-03-01'];
	}
	
	public function index(){
		$this->data['pageHeader'] = 'SALES REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

    /* Customer's Order Monitoring */
	public function orderMonitor(){
        $this->data['pageHeader'] = 'CUSTOMER ORDER MONITORING REPORT';
        $this->load->view($this->order_monitor,$this->data);
    }

    public function getOrderMonitor(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $orderData = $this->salesReportModel->getOrderMonitor($data);
            $tbody="";$i=1;$blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($orderData as $row):
                $data['trans_main_id'] = $row->trans_main_id;
                $invoiceData = $this->salesReportModel->getInvoiceData($data);
                $invoiceCount = count($invoiceData);

                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->trans_date).'</td>
                    <td>'.$row->doc_no.'</td>
                    <td>'.$row->party_code.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.floatVal($row->qty).'</td>
                    <td>'.formatDate($row->cod_date).'</td>
                    <td>'.$row->drg_rev_no.'</td>
                    <td>'.getPrefixNumber($row->trans_prefix,$row->trans_no).'</td>
                    <td>'.formatDate($row->created_at).'</td>
                    <td>'.$row->emp_name.'</td>';

                    if($invoiceCount > 0):
                        $j=1;$dqty=0;
                        foreach($invoiceData as $invRow):
                            $dqty = $this->salesReportModel->getDeliveredQty($row->item_id,$invRow->id)->dqty;
                            $tbody.='<td>'.getPrefixNumber($invRow->trans_prefix,$invRow->trans_no).'</td>
                                    <td>'.formatDate($invRow->trans_date).'</td>
                                    <td>'.floatval($dqty).'</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>';
                            if($j != $invoiceCount){$tbody.='</tr><tr>'.$blankInTd; }
                            $j++;
                        endforeach;
                    else:
                        $tbody.='<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>';
                    endif;
                $tbody .= '</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /*   Dispatch Plan Report    */
    public function dispatchPlan()
    {
        $this->data['pageHeader'] = 'DISPATCH PLAN REPORT';
        $this->load->view($this->dispatch_plan, $this->data);
    }

    public function getDispatchPlan()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $orderData = $this->salesReportModel->getDispatchPlan($data);
            $tbody = "";$i = 1;$toq=0;$tov=0;$wipq=0;$tpq=0;$tpv=0;$tdq=0;$tdv=0;$tpckq=0;$tpackv=0;$pq=0;$pv=0;
            $used_qty=Array();
            foreach ($orderData as $row) :
                $data['trans_main_id'] = $row->trans_main_id;
                $data['item_id'] = $row->item_id;
                //$itmData = $this->item->getItem($row->item_id);
                $price=0;
                if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$price=$inr[0]->inrrate*$row->price;}
                }
                else{$price=$row->price;}
				
				if(!isset($used_qty[$row->item_id])){$used_qty[$row->item_id] = 0;}
                $pendingQty = $row->qty - $row->dispatch_qty;
				$pckQty=0;$packingQty = 0;
				
				$pckQty = $row->packingQty - $used_qty[$row->item_id];
				if($pckQty > 0){if($pckQty > $pendingQty){$pckQty=$pendingQty;}}else{$pckQty=0;}
				
				if($pckQty > 0):
					$packingQty = $pckQty;
					$used_qty[$row->item_id] += $pckQty;
				endif;
                $wipQty = $this->salesReportModel->getWIPQtyForDispatchPlan($data);
                
                $planQty = $row->qty - $row->dispatch_qty - $packingQty;
				if($planQty < 0 ){$planQty = 0;}
                
				$jobData = new StdClass;
				$jobData = $this->salesReportModel->getJobcardBySO($row->so_id,$row->item_id);
				$del_date = formatDate($row->trans_date);
				if(!empty($jobData)){$del_date = formatDate($jobData->delivery_date);}
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->trans_date) . '</td>
                    <td>' . $row->party_code . '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . formatDate($row->cod_date) . '</td>
                    <td>' . $del_date . '</td>
                    <td>' . floatVal($price) . '</td>
                    <td>' . floatVal($row->qty) . '</td>
                    <td>' . floatVal($row->qty * $price) . '</td>
                    <td>' . floatVal($wipQty[0]->qty) . '</td>
                    <td>' . floatVal($planQty) . '</td>
                    <td>' . floatVal($planQty * $price) . '</td>
                    <td>' . floatVal($row->dispatch_qty) . '</td>
                    <td>' . floatVal($row->dispatch_qty * $price) . '</td>
                    <td>' . floatVal($packingQty) . '</td>
                    <td>' . floatVal($packingQty * $price) . '</td>
                    <td>' . floatVal($pendingQty) . '</td>
                    <td>' . floatval($pendingQty * $price) . '</td>';
                $tbody .= '</tr>';
				$toq+=floatVal($row->qty);$tov+=floatVal($row->qty * $price);$wipq+=floatVal($wipQty[0]->qty);
				$tpq+=floatVal($planQty);$tpv+=floatVal($planQty * $price);
				$tdq+=floatVal($row->dispatch_qty);$tdv+=floatVal($row->dispatch_qty * $price);
				$tpckq+=floatVal($packingQty);$tpackv+=floatVal($packingQty * $price);
				$pq+=floatVal($pendingQty);$pv+=floatval($pendingQty * $price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="7">TOTAL</th>
						<th>' . $toq . '</th>
						<th>' . $tov . '</th>
						<th>' . $wipq . '</th>
						<th>' . $tpq . '</th>
						<th>' . $tpv . '</th>
						<th>' . $tdq . '</th>
						<th>' . $tdv . '</th>
						<th>' . $tpckq . '</th>
						<th>' . $tpackv . '</th>
						<th>' . $pq . '</th>
						<th>' . $pv . '</th>
					</tr>';

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }


    /*   Dispatch Summary Report */
    public function dispatchSummary()
    {
        $this->data['pageHeader'] = 'Customer wise Dispatch Report';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemListForSelect(1);
        $this->load->view($this->dispatch_summary, $this->data);
    }
    
    public function getPartyItems(){
        $party_id = $this->input->post('party_id');
        $this->printJson($this->item->getPartyItems($party_id));
    }
    
    public function getDispatchSummary(){
        $data = $this->input->post();
        
        $dispatchData = $this->salesReportModel->getDispatchSummary($data);
        $i=1; $tbody =""; $tfoot=""; $tqty=0;$tamt=0;
        foreach($dispatchData as $row):
            $amt = floatVal(round($row->qty * $row->price,2));
            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>[' . $row->party_code.']' .$row->party_name. '</td>
                <td>' . $row->item_code . '</td>
                <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                <td>' . formatDate($row->trans_date) . '</td>
                <td>'.floatVal($row->qty).'</td>
                <td>'.floatVal($row->price).'</td>
                <td>'.$amt.'</td>
            </tr>';
            $tqty += $row->qty; $tamt += $amt;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="5">Total</th>
                <th>' .floatVal($tqty). '</th>
                <th></th>
                <th>' .floatVal($tamt). '</th>
            </tr>';

            
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);

    }
    
    /* ITEM HISTORY Report */
    public function itemHistory()
    {
        $this->data['pageHeader'] = 'ITEM HISTORY REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->item_history, $this->data);
    }

    public function getItemList(){
        $item_type = $this->input->post('item_type');
        $itemData = $this->item->getItemListForSelect($item_type);

        $item="";
        $item.='<option value="">Select Item</option>';
        foreach($itemData as $row):
            $item.= '<option value="'.$row->id.'">['.$row->item_code.'] '.$row->item_name.'</option>';
        endforeach;
        $this->printJson(['status' => 1, 'itemData' => $item]);
    }

    public function getItemHistory(){
        $item_id = $this->input->post('item_id');

        $itemData = $this->salesReportModel->getItemHistory($item_id);

        $i=1; $tbody =""; $tfoot=""; $credit=0;$debit=0; $tcredit=0;$tdebit=0; $tbalance=0;
        foreach($itemData as $row):
            $credit=0;$debit=0;
            $transType = ($row->ref_type >= 0)?$this->data['refTypes'][$row->ref_type] : "Opening Stock";
            if($row->trans_type == 1){ $credit = abs($row->qty); } else { $debit = abs($row->qty); }

            $tbody .= '<tr>
                <td>' . $i++ . '</td>
                <td>'.$transType.'</td>
                <td>'.$row->ref_no.'</td>
                <td>'.formatDate($row->ref_date).'</td>
                <td>'.$credit.'</td>
                <td>'.$debit.'</td>
                <td>'.abs($credit - $debit).'</td>
            </tr>';
            $tcredit += $credit; $tdebit += $debit; $tbalance += abs($credit) - abs($debit);
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal(round($tcredit,2)). '</th>
                <th>' .floatVal(round($tdebit,2)). '</th>
                <th>' .floatVal(round($tbalance,2)). '</th>
            </tr>';

        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    public function salesEnquiry()
    {
        $this->data['pageHeader'] = 'Regreated Enquiry';
        $this->data['resonData'] = $this->feasibilityReason->getFeasibilityReasonList();
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->sales_enquiry, $this->data);
    }
	
    public function getSalesEnquiry(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $enquiryData = $this->salesReportModel->getSalesEnquiry($data);
            $tbody=''; $i=1;
            if(!empty($enquiryData)):
                foreach($enquiryData as $row):
                    $tbody .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                        <td>' . formatDate($row->trans_date) . '</td>
                        <td>[' . $row->party_code.']' .$row->party_name. '</td>
                        <td>' . $row->item_name . '</td>
                        <td>' . $row->reason . '</td>
                        <td>'.floatVal($row->qty).'</td>
                    </tr>';
                endforeach;
            else:

            endif;

            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Monthly Sales Reports */
    public function monthlySales()
    {
        $this->data['pageHeader'] = 'MONTHLY SALES';
        $this->data['partyList'] = $this->party->getCustomerList();  
        $this->data['productList']=$this->item->getItemLists(1);
        $this->load->view($this->monthlySales, $this->data);
    }

    public function getMonthlySalesData()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $salesData = $this->salesReportModel->getSalesData($data);
            $tbody=""; $i=1; $tfoot=""; $totalTaxAmt=0; $totalGstAmt=0; $totalDiscAmt=0; $TotalNetAmt=0;
            
            foreach ($salesData as $row) :
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->trans_date) . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . $row->party_name . '</td>
                    <td>' . floatVal($row->taxable_amount) . '</td>
                    <td>' . floatVal($row->gst_amount) . '</td>
                    <td>' . floatVal($row->disc_amount) . '</td>
                    <td>' . floatVal($row->net_amount) . '</td>
                </tr>';
                $totalTaxAmt += floatVal($row->taxable_amount);
                $totalGstAmt += floatVal($row->gst_amount);
                $totalDiscAmt += floatVal($row->disc_amount);
                $TotalNetAmt += floatVal($row->net_amount);
            endforeach;
            $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal($totalTaxAmt). '</th>
                <th>' .floatVal($totalGstAmt). '</th>
                <th>' .floatVal($totalDiscAmt). '</th>
                <th>' .floatVal($TotalNetAmt). '</th>
            </tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    public function dispatchPlanSummary(){
        $this->data['pageHeader'] = 'Monthly Order Summary';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->data['itemData'] = $this->item->getItemListForSelect(1);
        $this->load->view($this->dispatch_plan_summary, $this->data);
    }

    public function getDispatchPlanSummary(){
        $data = $this->input->post();
        
        $dispatchData = $this->salesReportModel->getDispatchPlanSummary($data);
        $i=1; $tbody =""; $tfoot=""; $tqty=0;$tamt=0;
        foreach($dispatchData as $row):
            //if($row->qty >= $row->dispatch_qty):
                $qty = $row->qty;$item_price=0;
                if($row->currency != 'INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->price;}else{$item_price=$row->price;}
                }
                else{$item_price=$row->price;}
                $amt = floatVal(round(($qty * $item_price),2));
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>[' . $row->party_code.']' .$row->party_name. '</td>
                    <td>' . $row->item_code . '</td>
                    <td>' . getPrefixNumber($row->trans_prefix, $row->trans_no) . '</td>
                    <td>' . formatDate($row->cod_date) . '</td>
                    <td>'.floatVal($qty).'</td>
                    <td>'.floatVal($item_price).'</td>
                    <td>'.$amt.'</td>
                </tr>';
                $tqty += $qty; $tamt += $amt;
            //endif;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="5">Total</th>
                <th>' .floatVal($tqty). '</th>
                <th></th>
                <th>' .floatVal($tamt). '</th>
            </tr>';

            
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    /*   Packing Report    */
    public function packingReport()
    {
        $this->data['pageHeader'] = 'PACKING REPORT';
        $this->load->view($this->packing_report, $this->data);
    }

    public function getPackingPlan_old()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $packingData = $this->salesReportModel->getPackingPlan($data);
            $tbody = "";$i = 1;$tpckq=0;$tpackv=0;$tdq=0;$tdv=0;$sq=0;$sv=0;
            $used_qty=Array();$q=0;
            foreach ($packingData as $row) :
                
				if(!isset($used_qty[$row->item_id]))
				{
					$used_qty[$row->item_id] = 0;$data['item_id'] = $row->item_id;
					$dispatchData=$this->salesReportModel->getDispatchMaterial($data);
					if(!empty($dispatchData)){$used_qty[$row->item_id]=$q=$dispatchData->dispatch_qty;}
				}
                $item_price=0;$stockQty = 0; $dispatch_qty=$used_qty[$row->item_id];
                /*if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->item_price;}
                }
                else{$item_price=$row->item_price;}*/
				$item_price=$row->item_price;
				if($dispatch_qty > 0):
					if($dispatch_qty > $row->packing_qty){$dispatch_qty = $row->packing_qty;}
					$used_qty[$row->item_id] -= $dispatch_qty;
				endif;
				
                $stockQty = $row->packing_qty - $dispatch_qty;
                $withoutPacking = $row->totalStock - $row->packing_qty;
				
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . formatDate($row->packing_date) . '</td>
                    <td>' . $row->party_code . '</td>
                    <td>' . $row->item_code . '</td>
                    <td class="text-right">' . floatVal($item_price) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty)). '</td>
                    <td class="text-right">' . formatDecimal(floatVal($stockQty)). '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty + $stockQty)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($dispatch_qty * $item_price)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal($stockQty * $item_price)) . '</td>
                    <td class="text-right">' . formatDecimal(floatVal(floatVal($dispatch_qty + $stockQty) * $item_price)) . '</td>';
                $tbody .= '</tr>';
				$tpckq+=floatVal($dispatch_qty + $stockQty);$tpackv+=floatVal(floatVal($dispatch_qty + $stockQty) * $item_price);
				$tdq+=floatVal($dispatch_qty);$tdv+=floatVal($dispatch_qty * $item_price);
				$sq+=floatVal($stockQty);$sv+=floatval($stockQty * $item_price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="5">TOTAL</th>
						<th>' . formatDecimal($tdq) . '</th>
						<th>' . formatDecimal($sq) . '</th>
						<th>' . formatDecimal($tpckq) . '</th>
						<th>' . formatDecimal($tdv) . '</th>
						<th>' . formatDecimal($sv) . '</th>
						<th>' . formatDecimal($tpackv) . '</th>
					</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    public function getPackingPlan()
    {
        $data = $this->input->post();
        $errorMessage = array();
        if ($data['to_date'] < $data['from_date'])
            $errorMessage['toDate'] = "Invalid date.";

        if (!empty($errorMessage)) :
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else :
            $packingData = $this->salesReportModel->getPackingPlan($data);
            $tbody = "";$i = 1;$tpckq=0;$tpackv=0;$tdq=0;$tdv=0;$sq=0;$sv=0;
            $used_qty=Array();$q=0;
            foreach ($packingData as $row) :
                
                $item_price=0;$stockQty = 0; $dispatch_qty=0;$dispatch_price=0;$data['item_id']=$row->item_id;$disc_amt=0;
				$dispatchData=$this->salesReportModel->getDispatchOnPacking($data);
				//print_r($dispatchData);
				if(!empty($dispatchData))
				{
					$dispatch_qty=$dispatchData->dispatch_qty;
					//$dispatch_price=(!empty($dispatchData->dispatch_price)) ? round(($dispatch_qty/$dispatchData->dispatch_price),2) : 0;
					$dispatch_price = $dispatchData->dispatch_price;
					$disc_amt=$dispatchData->disc_amt;
				}
                /*if($row->currency!='INR')
                {         
                    $inr=$this->salesReportModel->getCurrencyConversion($row->currency);
                    if(!empty($inr)){$item_price=$inr[0]->inrrate*$row->item_price;}
                }
                else{$item_price=$row->item_price;}*/
				$item_price=$row->item_price;
				
                $stockQty = $row->packing_qty - $dispatch_qty;
				
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <!--<td>' . formatDate($row->packing_date) . '</td>-->
                    <td>' . $row->item_code . '</td>
                    <td class="text-right">' . floatVal($item_price) . '</td>
                    <td class="text-right">' . floatVal($dispatch_price) . '</td>
                    <td class="text-right">' . formatDecimal($dispatch_qty). '</td>
                    <td class="text-right">' . formatDecimal($stockQty). '</td>
                    <td class="text-right">' . formatDecimal($row->packing_qty) . '</td>
                    <td class="text-right">' . formatDecimal(($dispatch_qty * $dispatch_price)-$disc_amt) . '</td>
                    <td class="text-right">' . formatDecimal($stockQty * $item_price) . '</td>
                    <td class="text-right">' . formatDecimal($row->packing_qty * $item_price) . '</td>';
                $tbody .= '</tr>';
				$tpckq+=floatVal($row->packing_qty);$tpackv+=floatVal(floatVal($row->packing_qty) * $item_price);
				$tdq+=floatVal($dispatch_qty);$tdv+=floatVal($dispatch_qty * $dispatch_price);
				$sq+=floatVal($stockQty);$sv+=floatval($stockQty * $item_price);
            endforeach;
			$tfoot = '<tr class="thead-info">
						<th colspan="4">TOTAL</th>
						<th>' . formatDecimal($tdq) . '</th>
						<th>' . formatDecimal($sq) . '</th>
						<th>' . formatDecimal($tpckq) . '</th>
						<th>' . formatDecimal($tdv) . '</th>
						<th>' . formatDecimal($sv) . '</th>
						<th>' . formatDecimal($tpackv) . '</th>
					</tr>';
            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
        endif;
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : Mansee @ 13-12-2021 [Party wise filter]
    * Note : 
    */
    public function enquiryMonitoring(){
        $this->data['pageHeader'] = 'Enquiry v/s order';
        $this->data['partyData'] = $this->party->getCustomerList();
        $this->load->view($this->enquiry_monitoring, $this->data);
    }
    
    /*
    * Create By : Karmi @06-12-2021
    * Updated By : Mansee @ 13-12-2021 [Party wise filter]
    * Note : 
    */
    public function getEnquiryMonitoring()
    {
        $data = $this->input->post(); //print_r($data);exit;
        $EnqMonitorData = $this->salesReportModel->getEnquiryMonitoring($data);
        $i = 1;
        $tbody = "";
        $tfoot = "";

        if (empty($data['party_id'])) :
            foreach ($EnqMonitorData as $row) :
                $data['party_id'] = $row->party_id;
                $countData = $this->salesReportModel->getEnquiryCount($data);
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $row->party_name . '</td>
                    <td>' . $countData->totalEnquiry . '</td>
                    <td>' . $countData->quoted . '</td>
                    <td>' . $countData->pending . '</td>
                    <td>' . $countData->confirmSo . '</td>
                    <td>' . $countData->pendingSo . '</td>
                </tr>';
            endforeach;
            $tfoot .='<tr>
                <th colspan="7"></th>
            </tr>';
        else :
            $total = 0;
            foreach($data['party_id'] as $key=>$value):
                $qryData['from_date'] = $data['from_date'];
                $qryData['to_date'] = $data['to_date'];
                $qryData['party_id'] = $value;
                $EnqMonitorData = $this->salesReportModel->getSalesEnquiryByParty($qryData);
                if(!empty($EnqMonitorData)):
                    foreach ($EnqMonitorData as $enqData) :
                        $quoteData = $this->salesReportModel->getSalesQuotation($enqData->id);
                        $total_amount = array();
                        $quoteNo = array();
                        $quotedt = array();
                        $quoteDays=array();
                        $transCount = $this->salesEnquiry->getFisiblityCount($enqData->id);
                        $itm = $this->salesEnquiry->getTransChild($enqData->id);
                        
                        $orderNo = array();
                        $orderdt = array();
                        $orderDays=array();
                        foreach ($quoteData as $quote) :
                            $total_amount[] = ($quote->total_amount * $quote->inrrate);
                            $total += ($quote->total_amount * $quote->inrrate);
                            $quoteNo[] = getPrefixNumber($quote->trans_prefix, $quote->trans_no);
                            $quotedt[] = formatDate($quote->trans_date) ;

                            $date2 = strtotime($enqData->trans_date);
                            $date1 = strtotime($quote->trans_date);
                            $datediff = $date1 - $date2;

                            $quoteDays[] =  (floor($datediff / (60 * 60 * 24)));
                        
                        
                            $orderData = $this->salesReportModel->getSalesOrder($quote->id);
                            if (!empty($orderData)) :
                                foreach ($orderData as $order) :

                                    $orderNo[] = getPrefixNumber($order->trans_prefix, $order->trans_no);
                                    $orderdt[] = formatDate($order->trans_date) ;

                                    $orderDate1 = strtotime($order->trans_date);
                                    $orderDate2 = strtotime($quote->trans_date);
                                    $orderDateDiff = $orderDate1 - $orderDate2;

                                    $orderDays[] =  (floor($orderDateDiff / (60 * 60 * 24)));

                                endforeach;
                            endif;
                        endforeach;
                        $quoteprefix = (!empty($quoteNo) ? implode('<hr>', $quoteNo) : '');
                        $quoteDate = (!empty($quotedt) ? implode('<hr>', $quotedt) : '');
                        $quoteTotalDays = (!empty($quoteDays) ? implode('<hr>', $quoteDays) : '');
                        $quotetotal_amount = (!empty($total_amount) ? implode('<hr>', $total_amount) : '');

                        $orderprefix = (!empty($orderNo) ? implode('<hr>', $orderNo) : '');
                        $orderDate = (!empty($orderdt) ? implode('<hr>', $orderdt) : '');
                        $orderTotalDays = (!empty($orderDays) ? implode('<hr>', $orderDays) : '');

                        $tbody .= '<tr>
                                        <td style="min-width:25px;">' . $i++ . '</td>
                                        <td style="min-width:100px;">' . $enqData->party_name . '</td>
                                        <td style="min-width:100px;">' .formatDate($enqData->trans_date)  . '</td>
                                        <td style="min-width:100px;">' . getPrefixNumber($enqData->trans_prefix, $enqData->trans_no)  . '</td>
                                        <td style="min-width:100px;">' . $quoteDate . '</td>
                                        <td style="min-width:100px;">' . $quoteprefix . '</td>
                                        <td style="min-width:50px;">' . $transCount->quoted . '</td>
                                        <td style="min-width:50px;">' . (count($itm) - $transCount->quoted) . '</td>
                                        <td style="min-width:100px;">' .$quotetotal_amount . '</td>
                                        <td style="min-width:100px;">'.$quoteTotalDays.'</td>
                                        <td style="min-width:100px;">' . $orderDate . '</td>
                                        <td style="min-width:100px;">' . $orderprefix . '</td>
                                        <td style="min-width:100px;">'.$orderTotalDays.'</td>
                                </tr>';

                       
                    endforeach;
                endif;
            endforeach;
            $tfoot .='<tr>
                <th colspan="8" style="text-align: left">Total</th>
                <th class="text-right">'.number_format($total).'</th>
                <th colspan="4"></th>
            </tr>';
        endif;
        $this->printJson(['status' => 1, 'tbody' => $tbody, 'tfoot' => $tfoot]);
    }
    
    /* 
        Created By Avruti @ 30-12-2021
    */
    public function salesTarget(){
        $this->data['pageHeader'] = 'TARGET V/S SALES';
        $this->load->view($this->sales_target, $this->data);
    }

    public function getTargetRows(){
		$postData = $this->input->post();
        $errorMessage = array();
		
        if(empty($postData['month']))
            $errorMessage['month'] = "Month is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
			$partyData = $this->employee->getTargetRows($postData);

			$hiddenInputs = '<input type="hidden" id="sexecutive" name="executive" value="'.$postData['sales_executive'].'" />';
			$hiddenInputs .= '<input type="hidden" id="smonth" name="smonth" value="'.$postData['month'].'" />';
			$targetData = '';$i=1; $performance=0;
			if(!empty($partyData)):
				foreach($partyData as $row):
				    $postData['party_id']=$row->id;$salesTargetORD = 0;$salesTargetINV = 0 ;
    			    $salesTargetDataORD = $this->salesReportModel->getSalesOrderTarget($postData);
                    if(!empty($salesTargetDataORD->totalOrderAmt)){$salesTargetORD = $salesTargetDataORD->totalOrderAmt;}
                    
    			    $salesTargetDataINV = $this->salesReportModel->getSalesInvoiceTarget($postData);
                    if(!empty($salesTargetDataINV->totalInvoiceAmt)){$salesTargetINV = $salesTargetDataINV->totalInvoiceAmt;}
                    
                    $performance = 0;
                    if($salesTargetINV >0 && $row->business_target >0){$performance = ($salesTargetINV * 100) / ($row->business_target);}
                    
					$targetData .= '<tr>';
						$targetData .= '<td>'.$i++.'</td>';
						$targetData .= '<td>'.$row->party_name.'</td>';
						$targetData .= '<td>'.$row->contact_person.'</td>';
						$targetData .= '<td>'.$row->business_target.'</td>';
						$targetData .= '<td>'.$salesTargetORD.'</td>';
						$targetData .= '<td>'. $salesTargetINV.'</td>';
						$targetData .= '<td>'.round($performance,2).'%</td>';
					$targetData .= '</tr>';
				endforeach;
		
				$this->printJson(['status'=>1,'targetData'=>$targetData,'hiddenInputs'=>$hiddenInputs]);
			endif;
		endif;
    }
    
    // Created By Meghavi 09/07/2022
    public function userWiseSaleReport(){
        $this->data['pageHeader'] = 'USER WISE SALE REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->userWiseSale,$this->data);
    }

    public function getUserWiseSale(){
        $postData = $this->input->post();
        $errorMessage = array();
		
        if(empty($postData['created_by']))
            $errorMessage['created_by'] = "User is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $inwardData = $this->salesReportModel->getUserWiseSale($postData);
            $i=1; $tbody=''; $tfoot = '';  $totalQty=0;$totalAmt=0;$totalIncentive=0;$usernm='';
            if(!empty($inwardData)){
                foreach($inwardData as $row):
                    $incentive_amt = round((($row->taxable_amount * $row->incentive)/100),2);
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->trans_number.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->item_name.'</td>
                        <td class="text-right">'.floatVal($row->qty).'</td>
                        <td class="text-right">'.round($row->price,2).'</td>
                        <td class="text-right">'.round($row->disc_amount,2).'</td>
                        <td class="text-right">'.round($row->taxable_amount,2).'</td>
                        <td class="text-right">'.round($row->incentive,2).'</td>
                        <td class="text-right">'.round($incentive_amt,2).'</td>
                    </tr>';
                     $totalQty += floatVal($row->qty);$totalAmt += $row->taxable_amount;$totalIncentive += $incentive_amt;$usernm=$row->emp_name;
                endforeach;
                $tfoot = '<tr>
                        <th colspan="4">Total</th>
                        <th class="text-right">'.$totalQty.'</th><th>-</th><th>-</th>
                        <th class="text-right">'.$totalAmt.'</th><th>-</th>
                        <th class="text-right">'.$totalIncentive.'</th>
                    </tr>';
            } 
            $headRow = $usernm . '[ '.date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date'])).' ]';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot, 'headRow' => $headRow]);
        endif;
    }
}
?>