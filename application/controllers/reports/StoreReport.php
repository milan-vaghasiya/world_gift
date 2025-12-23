<?php
class StoreReport extends MY_Controller
{
    private $indexPage = "report/store_report/index";
    private $issue_register = "report/store_report/issue_register";
    private $stock_register = "report/store_report/stock_register";
    private $inventory_monitor = "report/store_report/inventory_monitor";
    private $consumable_report = "report/store_report/consumable_report";
    private $fgstock_report = "report/store_report/fgstock_report";
    private $tool_issue_register = "report/store_report/tool_issue_register";
    private $scrap_book = "report/store_report/scrap_book";
    private $item_history = "report/sales_report/item_history";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store Report";
		$this->data['headData']->controller = "reports/storeReport";
		$this->data['floatingMenu'] = $this->load->view('report/store_report/floating_menu',[],true);
	    $this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production','In Challan','Out Challan','Tools Issue','Stock Journal','Packing Material','Packing Product','Rejection Scrap','Production Scrap');
    }
	
	public function index(){
		$this->data['pageHeader'] = 'STORE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }
 
    /* ISSUE REGISTER (CONSUMABLE) REPORT */
    public function issueRegister(){
        $this->data['pageHeader'] = 'ISSUE REGISTER (Consumable/Raw Material) REPORT';
        $this->data['deptData'] = $this->department->getDepartmentList();
        $this->load->view($this->issue_register,$this->data);
    }

    public function getIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $issueData = $this->storeReportModel->getIssueRegister($data);
            $tbody="";$i=1; $thead=""; $tfoot=""; $totalQty=0; $totalItemPrice=0; $total=0;
            if($data['item_type'] == 3):
                $thead.="<th colspan='7'>Issue Register (Raw Material)</th>
                <th colspan='2'>F ST 04 (00/01.06.20)</th>";
            elseif($data['item_type'] == 2):
                $thead.="<th colspan='7'>Issue Register (Consumable)</th>
                <th colspan='2'>F ST 04 (00/01.06.20)</th>";
            else:
                
                $thead.="<th colspan='7'>Issue Register (Consumable/Raw Material)</th>
                <th colspan='2'>F ST 04 (00/01.06.20)</th>";
            endif;
            foreach($issueData as $row):
                
                $empdata = $this->employee->getEmp($row->collected_by);
                $emp_name = (!empty( $empdata))?$empdata->emp_name:"";
                $data['item_id']=$row->item_id;
                $prs=$this->purchaseReport->getLastPrice($data);
                // print_r($prs);
                $price=$row->itemPrice;
                if(!empty($prs))
                {
                    $price=$prs->price;
                }
                $totalPrice = (abs($row->qty) * $price);
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->dept_name.'</td>
                    <td>'.abs($row->qty).'</td>
                    <td>'.$emp_name.'</td>
                    <td>'.$row->remark.'</td>
                    <td>'.$price.(!empty($prs)?' (P) ':' (D) ').'</td>
                    <td>'.round($totalPrice, 2).'</td>
                </tr>';
                $totalQty+=abs($row->qty);$totalItemPrice += $price; $total += $totalPrice;
            endforeach;
            $tfoot = '<tr>
                    <th colspan="4">Total</th>
                    <th>'.round($totalQty).'</th>
                    <th colspan="2"></th>
                    <th>'.round($totalItemPrice, 2).'</th>
                    <th>'.round($total, 2).'</th>
                </tr>';
            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    /* STOCK REGISTER (CONSUMABLE) REPORT */
    public function stockRegister(){
        $this->data['pageHeader'] = 'STOCK REGISTER (CONSUMABLE) REPORT';
        $this->data['item_type'] = 2;
        $this->load->view($this->stock_register,$this->data);
    }

    /* STOCK REGISTER (RAW MATERIAL) REPORT */
    public function stockRegisterRawMaterial(){
        $this->data['pageHeader'] = 'STOCK REGISTER (RAW MATERIAL) REPORT';
        $this->data['item_type'] = 3;
        $this->load->view($this->stock_register,$this->data);
    }

    public function getStockRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getStockRegister($data['item_type']);
            $thead="";$tbody="";$i=1;$receiptQty=0;$issuedQty=0;
            
            if(!empty($itemData)):
                $type = ($data['item_type'] == 2)? "Consumable":"Raw Material";
                $formate = ($data['item_type'] == 2)? "F ST 05":"F ST 02";
                $thead = '<tr class="text-center"><th colspan="4">Stock Register ('.$type.')</th><th colspan="2">'.$formate.'(00/01.06.20)</th></tr><tr><th>#</th><th>Item Description</th><th>Receipt Qty.</th><th>Issued Qty.</th><th>Balance Qty.</th><th>Location</th></tr>';
                foreach($itemData as $row):
                    $data['item_id'] = $row->id;
                    $receiptQty = $this->storeReportModel->getStockReceiptQty($data)->rqty;
                    $issuedQty = $this->storeReportModel->getStockIssuedQty($data)->iqty;
                    $balanceQty = round($receiptQty - abs($issuedQty),3);
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.floatVal($receiptQty).'</td>
                        <td>'.abs(floatVal($issuedQty)).'</td>
                        <td>'.floatVal($balanceQty).'</td>
                        <td></td>
                    </tr>';
                endforeach;
            else:
                $tbody .= '<tr style="text-align:center;"><td colspan="5">Data not found</td></tr>';
            endif;
            $this->printJson(['status'=>1, 'thead'=>$thead, 'tbody'=>$tbody]);
        endif;
    }

    /* INVENTORY MONITORING REPORT */
    public function inventoryMonitor(){
        $this->data['pageHeader'] = 'INVENTORY MONITORING REPORT';
        $this->data['itemGroup'] = $this->storeReportModel->getItemGroup();
        $this->load->view($this->inventory_monitor,$this->data);
    }

    public function getInventoryMonitor(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            // $rawMaterialData = $this->storeReportModel->getRawMaterialReport();
            $itemData = $this->storeReportModel->getItemsByGroup($data);
            $tbody="";$i=1;$opningStock=0;$closingStock=0;$fyOpeningStock=0;$totalOpeningStock=0;$monthlyInward=0;$monthlyCons=0;$inventory=0;$amount=0;$total=0;$totalInventory=0;$totalValue=0;$totalUP=0;
            foreach($itemData as $row)://if($row->id==124):
                $data['item_id'] = $row->id;$untPrice=0;
                $fyOSData = Array();
                if($data['from_date'] == '2021-04-01'){$fyOSData = $this->storeReportModel->getFyearOpningStockQty($data);}
                $osData = $this->storeReportModel->getOpningStockQty($data);
                $fyOpeningStock = (!empty($fyOSData->fyosqty)) ? $fyOSData->fyosqty : 0;
                $opningStock = (!empty($osData->osqty)) ? $osData->osqty : 0;
                $monthlyInward = $this->storeReportModel->getStockReceiptQty($data)->rqty;
                $monthlyCons = abs($this->storeReportModel->getStockIssuedQty($data)->iqty);
                
                $grnData = $this->storeReportModel->getItemPrice($data);
                $amount = $grnData->amount;
                $untPrice = ($monthlyInward > 0)?round(($amount / $monthlyInward), 2):0;
                
                // $totalOpeningStock = (($opningStock > 0) ? floatVal($opningStock) : 0) + floatVal($fyOpeningStock);
                $totalOpeningStock = floatval($opningStock) + floatVal($fyOpeningStock);
                
                if($untPrice == 0):
                    $untPrice = $this->item->getItemBySelect($data['item_id'],'price')->price;
                endif;
                
                $closingStock = ($totalOpeningStock + $monthlyInward - $monthlyCons);
                // $inventory = ($closingStock - $row->min_qty);
                $inventory = $closingStock;
                
                $total = round(($inventory * $untPrice), 2);
                if(floatVal($inventory) != 0):
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->size.'</td>
                        <td>'.floatVal($totalOpeningStock).'</td>
                        <td>'.floatVal(round($monthlyInward,2)).'</td>
                        <td>'.floatVal(round($closingStock,2)).'</td>
                        <td>'.floatVal(round($monthlyCons,2)).'</td>
                        <td>'.floatVal($row->min_qty).'</td>
                        <td>'.floatVal($row->max_qty).'</td>
                        <td>'.floatVal(round($inventory,2)).'</td>
                        <td>'.sprintf('%0.2f',$untPrice).'</td>
                        <td>'.sprintf('%0.2f',$total).'</td>
                    </tr>';
                    $totalInventory += round($inventory,2);
                    $totalValue += $total;//endif;
                endif;
            endforeach;
            
            $totalUP = (!empty($totalInventory)) ? round(($totalValue / $totalInventory),2) : 0;
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'totalInventory'=>sprintf('%0.2f',$totalInventory), 'totalUP'=>sprintf('%0.2f',$totalUP), 'totalValue'=>sprintf('%0.2f',$totalValue)]);
        endif;
    }

    /* Consumable Report */
    /* Consumable Report */
    public function consumableReport(){
        $this->data['pageHeader'] = 'CONSUMABLES REPORT';
        $consumableData = $this->storeReportModel->getConsumable();

        $i=1;  $this->data['tbody']='';
        if(!empty($consumableData)){
            foreach($consumableData as $row):                
                $locData = $this->storeReportModel->getItemLocation($row->id);
                if(!empty($locData)){
                        $location_id = explode(',',$locData->location_id);
                        $x=1;$location='';
                        foreach($location_id as $lid)
                        {
                            if(!empty($lid)){
                                $store_name = $this->store->getStoreLocation($lid)->store_name;
                                if($x == 1){ $location .= $store_name; }
                                else { $location .= ', '.$store_name; } $x++;

                            }
                        }
                }
                $size = (!empty($row->size))? ' / '.$row->size : "";
                $this->data['tbody'] .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->material_grade.$size.'</td>
                    <td>'.$row->min_qty.'</td>
                    <td>'.$location.'</td>
                    <td>'.$row->description.'</td>
                </tr>';
                
                
            endforeach;
        }

        $this->load->view($this->consumable_report,$this->data);
    }

    /* Stock Statement finish producct */
    public function fgStockReport(){
        $this->data['pageHeader'] = 'STOCK STATEMENT REPORT';
        $this->load->view($this->fgstock_report,$this->data);
    }

    public function getFgStockReport(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $fgData = $this->storeReportModel->getFinishProduct();
            $tbody="";$i=1;
            foreach($fgData as $row):
                $data['item_id'] = $row->id;
                $cqty = $this->storeReportModel->getClosingStockQty($data)->csqty;
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.$row->item_code.'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->drawing_no.'</td>
                    <td>'.$row->rev_no.'</td>
                    <td>'.abs($cqty).'</td>
                </tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /*TOOL ISSUE REGISTER (CONSUMABLE) REPORT */  
    public function toolissueRegister(){
        $this->data['pageHeader'] = 'TOOL ISSUE REGISTER REPORT';
        $this->data['deptData'] = $this->department->getMachiningDepartment(8);
        $this->data['jobCardData'] = $this->storeReportModel->getJobcardList();
        $this->load->view($this->tool_issue_register,$this->data);
        
    } 
    
	public function getToolIssueRegister(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $issueData = $this->storeReportModel->getToolIssueRegister($data); 
			// print_r($issueData);exit;
			
            $tbody="";$i=1; $total_amount = 0;$total_qty = 0;
            foreach($issueData as $row):
                // print_r($row);
                $data['item_id']=$row->dispatch_item_id;
                $prs=$this->purchaseReport->getLastPrice($data);
                // print_r($prs);
                $price=$row->price;
                if(!empty($prs))
                {
                    $price=$prs->price;
                }
                $amount = round((floatVal($row->dispatch_qty) * floatval($price)),2);
				$partCode=''; $jobNo = '';
				if(!empty($row->job_no)){
                    $jobNo = getPrefixNumber($row->job_prefix,$row->job_no);
                    $pcode = $this->item->getItem($row->product_id)->item_code;
                    $partCode = '['.$pcode.'] ';
                }
                
                $tbody .= '<tr>
                    <td class="text-center">'.$i++.'</td>
                    <td>'.formatDate($row->dispatch_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->name.'</td>
                    <td>'.$partCode.$jobNo.'</td>
                    <td>'.floatVal($row->dispatch_qty).'</td>
                    <td>'.floatval($price).(!empty($prs)?' (P) ':' (D) ').'</td>
                    <td>'.$amount.'</td>
                </tr>';
				$total_amount += $amount;$total_qty += $row->dispatch_qty;
            endforeach;
			$avgPrice = ($total_qty > 0) ? round((floatVal($total_amount / $total_qty)),2) : 0;
			$tfoot = '<tr>
                    <th colspan="5">Total</th>
                    <th>'.floatVal($total_qty).'</th>
                    <th>'.$avgPrice.'</th>
                    <th>'.$total_amount.'</th>
                </tr>';
            $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
        endif;
    }

    /**
     * Created By Mansee @ 09-12-2021
     */
    public function scrapBook(){
        $this->data['pageHeader'] = 'SCRAP BOOK REPORT';
        $this->data['scrapGroup']=$this->materialGrade->getScrapList();
        $this->data['materialGrade']=$this->materialGrade->getMaterialGrades();        
        $this->load->view($this->scrap_book,$this->data);
    }

    public function getScrapReport(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $itemData = $this->storeReportModel->getRowMaterialScrapQty($data);
            $tbody="";$i=1;
            $totalQty = 0;
            $totalPrice = 0;
            $netValuation = 0;
            $avgPrice = 0;
            if(!empty($itemData)):
                foreach($itemData as $row):
                    $totalQty += $row->scrap_qty;
                    $totalPrice += $row->price;
                                    
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_code.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->scrap_qty.'</td>
                        <td>'.$row->price.'</td>
                        <td>'.($row->price*$row->scrap_qty).'</td>                    
                    </tr>';
                    $netValuation += round(($row->price*$row->scrap_qty),2);
                endforeach;
                $avgPrice = (!empty($netValuation))?round(($netValuation / $totalQty),2):0;
            endif;           
            
            $this->printJson(['status'=>1, 'tbody'=>$tbody,'avg_price'=>$avgPrice,'net_valuation'=>$netValuation,'total_qty'=>$totalQty]);
        endif;
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
}
?>