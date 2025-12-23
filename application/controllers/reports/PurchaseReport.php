<?php
class PurchaseReport extends MY_Controller
{
    private $indexPage = "report/purchase_report/index";
    private $raw_material = "report/purchase_report/raw_material";
    private $purchase_monitoring = "report/purchase_report/purchase_monitoring";
    private $purchase_inward = "report/purchase_report/purchase_inward";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Purchase Report";
		$this->data['headData']->controller = "reports/purchaseReport";
		$this->data['floatingMenu'] = $this->load->view('report/purchase_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'PURCHASE REPORT';
        $this->load->view($this->indexPage,$this->data);
    }

    /* RawMaterial Report */
	public function rawMaterialReport(){
        $this->data['pageHeader'] = 'RAW MATERIAL REPORT';
        $this->data['rawMaterialData'] = $this->storeReportModel->getrawMaterialReport();
        $this->load->view($this->raw_material,$this->data);
    }

    /* Purchase Monitoring Report */
    public function purchaseMonitoring(){
        $this->data['pageHeader'] = 'PURCHASE MONITORING REGISTER REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->purchase_monitoring,$this->data);
    }

    public function getPurchaseMonitoring(){
        $data = $this->input->post();
        $errorMessage = array();
		if($data['to_date'] < $data['from_date'])
			$errorMessage['toDate'] = "Invalid date.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $purchaseData = $this->purchaseReport->getPurchaseMonitoring($data);
            $tbody="";$i=1;
            $blankInTd='<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>';
            foreach($purchaseData as $row):
                    $data['item_id'] = $row->item_id;$data['grn_trans_id'] = $row->id;
                    $receiptData = $this->purchaseReport->getPurchaseReceipt($data);
                    $receiptCount = count($receiptData);
                    $tbody .= '<tr>
                        <td class="text-center">'.$i++.'</td>
                        <td>'.formatDate($row->po_date).'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.getPrefixNumber($row->po_prefix,$row->po_no).'</td>
                        <td>'.floatval($row->qty).'</td>
                        <td>'.formatDate($row->delivery_date).'</td>';
                        if($receiptCount > 0):
                            $j=1;
                            foreach($receiptData as $recRow):
                                $totalAmt = $recRow->qty * $recRow->price;
                                $tbody.='<td>'.getPrefixNumber($recRow->grn_prefix,$recRow->grn_no).'</td>
                                            <td>'.formatDate($recRow->grn_date).'</td>
                                            <td>'.floatval($recRow->qty).'</td>
                                            
                                            <td>'.floatval($recRow->price).'</td>
                                            <td>'.floatval($totalAmt).'</td>';
                                if($j != $receiptCount){$tbody.='</tr><tr>'.$blankInTd; }
                                $j++;
                            endforeach;
                        else:
                            $tbody.='<td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>';
                        endif;
                        $tbody.='</tr>';
            endforeach;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }
    
    /* Purchase Inward Report */
    public function purchaseInward(){
        $this->data['pageHeader'] = 'PURCHASE INWARD REPORT';
        $this->data['itemTypeData'] = $this->item->getItemGroup();
        $this->load->view($this->purchase_inward,$this->data);
    }

    public function getPurchaseInward(){
        $data = $this->input->post();
        $inwardData = $this->purchaseReport->getPurchaseInward($data);
        $i=1; $tbody=''; $totalAmt=0; $poNo=''; $tfoot = ''; $totalQty=0; $totalItemPrice=0; $total=0;
        if(!empty($inwardData)){
            foreach($inwardData as $row):
                $totalAmt = ($row->qty * $row->price);
                if(!empty($row->po_prefix) && !empty($row->po_no)){
                    $poNo = getPrefixNumber($row->po_prefix,$row->po_no);
                }
                $tbody .= '<tr>
                    <td>'.$i++.'</td>
                    <td>'.formatDate($row->grn_date).'</td>
                    <td>'.getPrefixNumber($row->grn_prefix,$row->grn_no).'</td>
                    <td>'.$poNo.'</td>
                    <td>'.formatDate($row->po_date).'</td>
                    <td>'.$row->item_name.'</td>
                    <td>'.$row->party_name.'</td>
                    <td>'.$row->qty.'</td>
                    <td>'.$row->price.'</td>
                    <td>'.$totalAmt.'</td>
                </tr>';
                $totalQty += $row->qty; $totalItemPrice += $row->price; $total += $totalAmt;
            endforeach;
            $tfoot = '<tr>
                    <th colspan="7">Total</th>
                    <th>'.round($totalQty).'</th>
                    <th>'.round($totalItemPrice, 2).'</th>
                    <th>'.round($total, 2).'</th>
                </tr>';
        } else {
            $tbody .= '<tr><td colspan="10">No Data Found</td></tr>';
        }
        $this->printJson(['status'=>1, 'tbody'=>$tbody, 'tfoot'=>$tfoot]);
    }
}
?>