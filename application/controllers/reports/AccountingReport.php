<?php
class AccountingReport extends MY_Controller
{
    private $indexPage = "report/account_report/index";
    private $sales_register = "report/account_report/sales_register";
    private $purchase_register = "report/account_report/purchase_register";
    private $stock_register = "report/account_report/stock_register";
    private $receivable = "report/account_report/receivable";
    private $payable = "report/account_report/payable";
    private $bank_book = "report/account_report/bank_book";
    private $cash_book = "report/account_report/cash_book";
    private $account_ledger = "report/account_report/account_ledger";
    private $debit_note = "report/account_report/debitNote_register";
    private $credit_note = "report/account_report/creditNote_register";
    private $sales_report = "report/account_report/sales_report";
    private $purchase_report = "report/account_report/purchase_report";
    private $account_ledger_detail = "report/account_report/account_ledger_detail";
    private $gstr1_report = "report/account_report/gstr1_report";
    private $gstr2_report = "report/account_report/gstr2_report";
    private $hsn_wise_sales = "report/account_report/hsn_wise_sales";
    private $hsn_wise_purchase = "report/account_report/hsn_wise_purchase";

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Accounting Report";
		$this->data['headData']->controller = "reports/accountingReport";
		$this->data['floatingMenu'] = $this->load->view('report/account_report/floating_menu',[],true);
	}
	
	public function index(){
		$this->data['pageHeader'] = 'Reports';
        $this->load->view($this->indexPage,$this->data);
    }

	public function salesRegisterReport(){
        $this->data['pageHeader'] = 'SALES REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->sales_register,$this->data);
    }

    public function purchaseRegisterReport(){
        $this->data['pageHeader'] = 'PURCHASE REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getSupplierList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->purchase_register,$this->data);
    }
	
    public function receivableReport(){
        $this->data['pageHeader'] = 'RECEIVABLE';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->receivable,$this->data);
    }

    public function payableReport(){
        $this->data['pageHeader'] = 'PAYABLE';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->payable,$this->data);
    }

    public function bankBookReport(){
        $this->data['pageHeader'] = 'BANK BOOK';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->bank_book,$this->data);
    }

    public function cashBookReport(){
        $this->data['pageHeader'] = 'CASH BOOK';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->cash_book,$this->data);
    }

    public function accountLedgerReport(){
        $this->data['pageHeader'] = 'ACCOUNT LEDGER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
       // $this->data['ledgerSummary'] = $this->accountingReport->getLedgerSummary();
        $this->load->view($this->account_ledger,$this->data);
    }

    public function debitNoteRegisterReport(){
        $this->data['pageHeader'] = 'DEBIT NOTE REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->debit_note,$this->data);
    }

    public function creditNoteRegisterReport(){
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['pageHeader'] = 'CREDIT NOTE REGISTER';
        $this->load->view($this->credit_note,$this->data);
    }

    public function salesReport(){
        $this->data['pageHeader'] = 'SALES REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->sales_report,$this->data);
    }

    public function purchaseReport(){
        $this->data['pageHeader'] = 'PURCHASE REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->load->view($this->purchase_report,$this->data);
    }

    //Updated By Karmi @06/05/2022
    public function getAccountLedger(){
        $data = $this->input->post();
        $ledgerSummary = $this->accountingReport->getLedgerSummary($data['from_date'],$data['to_date']);
        $i=1; $tbody="";
        foreach($ledgerSummary as $row):
            $accountName = '<a href="' . base_url('reports/accountingReport/ledgerDetail/' . $row->id.'/'.$data['from_date'].'/'.$data['to_date']) . '" class="getAccountData" data-id="'.$row->id.'" target="_blank" datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$accountName.'</td>
                <td>'.$row->group_name.'</td>
                <td>'.$row->op_balance.'</td>
                <td>'.$row->cr_balance.'</td>
                <td>'.$row->dr_balance.'</td>
                <td>'.$row->cl_balance.'</td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function ledgerDetail($acc_id,$start_date,$end_date){
        $this->data['pageHeader'] = 'ACCOUNT LEDGER DETAIL';
        $this->data['acc_id'] = $acc_id;
        $this->data['startDate'] = $start_date;
        $this->data['endDate'] = $end_date;
        //$this->data['ledgerTransactions'] = $this->accountingReport->getLedgerDetail();
        //$this->data['ledgerBalance'] = $this->accountingReport->getLedgerBalance();
        $this->load->view($this->account_ledger_detail,$this->data);
    }

    //Updated By Karmi @06/05/2022
    public function getLedgerTransaction(){
        $data = $this->input->post();
        $ledgerTransactions = $this->accountingReport->getLedgerDetail($data['from_date'],$data['to_date'],$data['acc_id']); //print_r($ledgerTransactions);exit;
        $ledgerBalance = $this->accountingReport->getLedgerBalance($data['from_date'],$data['to_date'],$data['acc_id']);
        $i=1; $tbody="";
        foreach($ledgerTransactions as $row):
            $paymentVoucher = '<button type="button" class="btn waves-effect waves-light btn-outline-primary float-center addVoucher " data-button="both" data-modal_id="modal-lg" data-id="'.$row->id.'" data-partyid="'.$data['acc_id'].'" data-function="addPaymentVoucher" data-form_title="Add Payment ">Payment</button>';
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.formatDate($row->trans_date).'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.$row->account_name.'</td>
                <td>'.$row->vou_name_s.'</td>
                <td>'.$row->cr_amount.'</td>
                <td>'.$row->dr_amount.'</td>
                <td style="text-align: center;">'.$paymentVoucher.'</td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'ledgerBalance'=>$ledgerBalance]);
    }

    public function getReceivable(){
        $data = $this->input->post();
        $receivable = $this->accountingReport->getReceivable($data['from_date'],$data['to_date']);
        $i=1; $tbody="";$totalClBalance = 0;
        foreach($receivable as $row):
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->account_name.'</td>
                <td>'.$row->group_name.'</td>
                <td>'.$row->cl_balance.'</td>
            </tr>';
            $totalClBalance += $row->cl_balance;
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'totalClBalance'=>$totalClBalance]);
    }

    public function getPayable(){
        $data = $this->input->post();
        $payable = $this->accountingReport->getPayable($data['from_date'],$data['to_date']);
        $i=1; $tbody="";$totalClBalance = 0;
        foreach($payable as $row):
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->account_name.'</td>
                <td>'.$row->group_name.'</td>
                <td>'.$row->cl_balance.'</td>
            </tr>';
            $totalClBalance += $row->cl_balance;
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'totalClBalance'=>$totalClBalance]);
    }

    public function getBankBook(){
        $data = $this->input->post();
        $bankBook = $this->accountingReport->getBankCashBook($data['from_date'],$data['to_date'],'BA','BO');
        $i=1; $tbody="";//$totalClBalance = 0; 
        foreach($bankBook as $row):
        $accountName = '<a href="javascript:void(0);" class="getAccountData" data-toggle="modal" data-target="#accountDetails" data-id="'.$row->id.'"  datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$accountName.'</td>
                <td>'.$row->group_name.'</td>
                <td>'.$row->op_balance.'</td>
                <td>'.$row->cl_balance.'</td>
            </tr>'; 
            //$totalClBalance += $row->cl_balance; 
        endforeach;        
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function getCashBook($jsonData=''){
        if(!empty($jsonData)){$postData = (Array) json_decode(urldecode(base64_decode($jsonData)));}
        else{$postData = $this->input->post();}
        $cashBook = $this->accountingReport->getBankCashBook($postData['from_date'],$postData['to_date'],'CS');
        $i=1; $tbody="";
        foreach($cashBook as $row):
			$accountName = $row->account_name;
            if(empty($jsonData)){
                $accountName = '<a href="javascript:void(0);" class="getAccountData" data-toggle="modal" data-target="#accountDetails" data-id="'.$row->id.'"  datatip="Account" flow="down"><b>'.$row->account_name.'</b></a>';
            }
            $totalAmt = floatVal($row->cl_balance) - floatVal($row->op_balance);
            $tbody .= '<tr>
                <td height="40">'.$i++.'</td>
                <td class="text-center">'.$accountName.'</td>
                <td class="text-center">'.$row->group_name.'</td>
                <td class="text-center">'.(round($totalAmt,2)).'</td>
                <td class="text-center">'.$row->cl_balance.'</td>
            </tr>';
        endforeach;      
     
        $reportTitle = 'CASH BOOK';
        $report_date = date('d-m-Y',strtotime($postData['from_date'])).' to '.date('d-m-Y',strtotime($postData['to_date']));
        $thead = (empty($jsonData)) ? '<tr class="text-center"><th colspan="11">'.$reportTitle.' ('.$report_date.')</th></tr>' : '';
        $thead .= '<tr>
                        <th style="min-width:25px;" height="30">#</th>
                        <th style="min-width:80px;">Account Name</th>
                        <th style="min-width:80px;">Group Name</th>
                        <th style="min-width:25px;">Amount</th>
                        <th style="min-width:25px;">Closing Amount</th>							
                </tr>';

        $companyData = $this->salesInvoice->getCompanyInfo();
        $logoFile = (!empty($companyData->company_logo)) ? $companyData->company_logo : 'logo.png';
        $logo = base_url('assets/images/' . $logoFile);
        
        $pdfData = '<table id="commanTable" class="table table-bordered poItemList" repeat_header="1">
                            <thead class="thead-info" id="theadData">'.$thead.'</thead>
                            <tbody id="receivableData">'.$tbody.'</tbody>
                        </table>';
        $htmlHeader = '<table class="table" style="border-bottom:1px solid #036aae;">
                        <tr>
                            <td class="text-uppercase text-left" style="font-size:1rem;width:30%">Date : '.$report_date.'</td>
                            <td class="org_title text-uppercase text-center" style="font-size:1.3rem;width:40%">'.$companyData->company_name.'</td>
                            <td class="text-uppercase text-right" style="font-size:1rem;width:30%">'.$reportTitle.'</td>
                        </tr>
                    </table>
                    <table class="table" style="border-bottom:1px solid #036aae;margin-bottom:2px;">
                        <tr><td class="org-address text-center" style="font-size:13px;">'.$companyData->company_address.'</td></tr>
                    </table>';
        $htmlFooter = '<table class="table top-table" style="margin-top:10px;border-top:1px solid #545454;">
                    <tr>
                        <td style="width:50%;font-size:12px;">Printed On ' . date('d-m-Y') . '</td>
                        <td style="width:50%;text-align:right;font-size:12px;">Page No. {PAGENO}/{nbpg}</td>
                    </tr>
                </table>';
			
        if(!empty($postData['file_type'] == 'PDF'))
        {
            $mpdf = new \Mpdf\Mpdf();
            $filePath = realpath(APPPATH . '../assets/uploads/');
            $pdfFileName = $filePath.'/CashBook.pdf';
            $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->SetWatermarkImage($logo, 0.08, array(120, 120));
            $mpdf->showWatermarkImage = true;
            $mpdf->SetTitle($reportTitle);
            $mpdf->SetHTMLHeader($htmlHeader);
            $mpdf->SetHTMLFooter($htmlFooter);
            $mpdf->AddPage('L','','','','',5,5,30,5,3,3,'','','','','','','','','','A4-L');
            $mpdf->WriteHTML($pdfData); 
            ob_clean();
            $mpdf->Output($pdfFileName, 'I');
        }
        else { $this->printJson(['status'=>1, 'tbody'=>$tbody]); }
    }

    public function getDebitNote(){
        $data = $this->input->post();
        $debitNote = $this->accountingReport->getAccountReportData($data['from_date'],$data['to_date'],14);
        $i=1; $tbody="";$otherAmt = 0;
        foreach($debitNote as $row):
            $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_date.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->taxable_amount.'</td>
                <td>'.$row->cgst_amount.'</td>
                <td>'.$row->sgst_amount.'</td>
                <td>'.$row->igst_amount.'</td>
                <td>'.round($otherAmt,2).'</td>
                <td>'.$row->net_amount.'</td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function getCreditNote(){
        $data = $this->input->post();
        $creditNote = $this->accountingReport->getAccountReportData($data['from_date'],$data['to_date'],13);
        $i=1; $tbody="";$otherAmt = 0;
        foreach($creditNote as $row):
            $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_date.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->taxable_amount.'</td>
                <td>'.$row->cgst_amount.'</td>
                <td>'.$row->sgst_amount.'</td>
                <td>'.$row->igst_amount.'</td>
                <td>'.round($otherAmt,2).'</td>
                <td>'.$row->net_amount.'</td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function getSalesReport(){
        $data = $this->input->post();
        $salesReport = $this->accountingReport->getAccountReportData($data['from_date'],$data['to_date'],'6,7,8,10,11,13');
        $i=1; $tbody="";$otherAmt = 0;
        foreach($salesReport as $row):
            $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_date.'</td>
                <td>'.$row->trans_number.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->vou_name_s.'</td>
                <td>'.$row->taxable_amount.'</td>
                <td>'.$row->cgst_amount.'</td>
                <td>'.$row->sgst_amount.'</td>
                <td>'.$row->igst_amount.'</td>
                <td>'.round($otherAmt,2).'</td>
                <td>'.$row->net_amount.'</td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function getPurchaseReport(){
        $data = $this->input->post();
        $purchaseReport = $this->accountingReport->getAccountReportData($data['from_date'],$data['to_date'],'12,14');
        $i=1; $tbody="";$otherAmt = 0;
        foreach($purchaseReport as $row):
            $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
            $tbody .= '<tr>
                <td>'.$i++.'</td>
                <td>'.$row->trans_date.'</td>
                <td>'.$row->doc_no.'</td>
                <td>'.$row->party_name.'</td>
                <td>'.$row->vou_name_s.'</td>
                <td>'.$row->taxable_amount.'</td>
                <td>'.$row->cgst_amount.'</td>
                <td>'.$row->sgst_amount.'</td>
                <td>'.$row->igst_amount.'</td>
                <td>'.round($otherAmt,2).'</td>
                <td>'.$row->net_amount.'</td>
            </tr>';
        endforeach;           
        $this->printJson(['status'=>1, 'tbody'=>$tbody]);
    }

    public function hsnWiseSales(){
        $this->data['pageHeader'] = 'HSN WISE SALES REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->hsn_wise_sales,$this->data);
    }

    public function getHsnWiseSalesReport(){
        $data = $this->input->post();
        $salesRegisterReport = $this->accountingReport->getAccountReportDataHsnWise($data['from_date'],$data['to_date'],'6,7,8,10,11',$data['memo_type']);
        $debitColH = '';$rowspan=5;
        if($data['memo_type'] == 'DEBIT')
        {
            $debitColH = '
					<th style="min-width:50px;">Vou. No</th>
					<th style="min-width:100px;">Account Name</th>
					<th style="min-width:100px;">Gst Number</th>
					<th style="min-width:100px;">State</th>';
			$rowspan=9;
            //$debitCol = '<td>'.$row->state_code.'-'.$row->state_name.'</td>';
        }
        $i=1; $tbody=""; $otherAmt = 0;$subTotal=0;$cgst=0;$sgst=0;$igst=0;$oAmt=0;$netAmount=0;
        foreach($salesRegisterReport as $row):
                $partyFilture = (!empty($data['party_id'])?$row->party_id == $data['party_id']:$row->party_id == $row->party_id);
                $empFilture = (!empty($data['emp_id']) ? $row->created_by == $data['emp_id'] : $row->created_by == $row->created_by);
                
                if($partyFilture){
                    if($empFilture){
                        if(!empty($row->party_state_code))
                        {
                            if($row->party_state_code != 24){$row->cgst_amount = $row->sgst_amount = 0;}
                            else{$row->igst_amount = 0;}
                        }else{$row->igst_amount = 0;}
                        $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
                        //$pay_type = (!empty($row->gstin))?'Debit':'Cash';
                        $pay_type = ((!empty($row->pay_mode)) AND $row->pay_mode=='CASH')?'Cash':'Debit';
                        $tbody .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->memo_type.'</td>
                            <td>'.formatDate($row->trans_date).'</td>
                            <td>'.$row->hsn_code.'</td>';
                        if($data['memo_type'] == 'DEBIT')
                        {    
                            $tbody .= '<td>'.$row->trans_number.'</td>
                                <td>'.$row->party_name.'</td>
                                <td class="text-right">'.$row->gstin.'</td>
                                <td>'.$row->state_code.'-'.$row->state_name.'</td>';
                        }    
                        $tbody .= '<td>'.formatDecimal($row->gst_per).'%</td>
                            <td class="text-right">'. formatDecimal($row->taxable_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->cgst_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->sgst_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->igst_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->net_amount).'</td>
                        </tr>';
                        $subTotal+=floatVal($row->taxable_amount);$cgst+=floatVal($row->cgst_amount);
                        $sgst+=floatVal($row->sgst_amount);$igst+=floatVal($row->igst_amount);
                        $oAmt+=floatVal($otherAmt);$netAmount+=floatval($row->net_amount);
                    }
                }   
        endforeach;
        $thead = '<tr class="thead-info">
					<th class="text-center" colspan="'.$rowspan.'">HSN WISE SALES REGISTER</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
                </tr>
                <tr>
                    <th style="min-width:25px;">#</th>
					<th style="min-width:80px;">Cash/Debit</th>
					<th style="min-width:80px;">Vou. Date</th>
				    <th style="min-width:50px;">HSN</th>
				    '.$debitColH.'
					<th style="min-width:50px;">GST(%)</th>
					<th style="min-width:100px;">Taxable Amount</th>
					<th style="min-width:100px;">Cgst Amount</th>
					<th style="min-width:100px;">Sgst Amount</th>
					<th style="min-width:100px;">Igst Amount</th>
					<th style="min-width:50px;">Vou. Amount</th>
                </tr>';
        $tfoot = '<tr class="thead-info">
					<th colspan="'.$rowspan.'">TOTAL</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
				</tr>';
        
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'thead'=>$thead, 'tfoot'=>$tfoot]);
    }

    public function hsnWisePurchase(){
        $this->data['pageHeader'] = 'HSN WISE PURCHASE REGISTER';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getSupplierList();
        $this->data['empData'] = $this->employee->getEmployeeList();
        $this->load->view($this->hsn_wise_purchase,$this->data);
    }

    public function getHsnWisePurchaseReport(){
        $data = $this->input->post();
        $salesRegisterReport = $this->accountingReport->getAccountReportDataHsnWise($data['from_date'],$data['to_date'],'12',$data['memo_type']);
        $debitColH = '';$rowspan=5;
        if($data['memo_type'] == 'DEBIT')
        {
            $debitColH = '
					<th style="min-width:50px;">Vou. No</th>
					<th style="min-width:100px;">Account Name</th>
					<th style="min-width:100px;">Gst Number</th>
					<th style="min-width:100px;">State</th>';
			$rowspan=9;
        }
        $i=1; $tbody=""; $otherAmt = 0;$subTotal=0;$cgst=0;$sgst=0;$igst=0;$oAmt=0;$netAmount=0;
        foreach($salesRegisterReport as $row):
                $partyFilture = (!empty($data['party_id'])?$row->party_id == $data['party_id']:$row->party_id == $row->party_id);
                $empFilture = (!empty($data['emp_id']) ? $row->created_by == $data['emp_id'] : $row->created_by == $row->created_by);
                
                if($partyFilture){
                    if($empFilture){
                        if(!empty($row->party_state_code))
                        {
                            if($row->party_state_code != 24){$row->cgst_amount = $row->sgst_amount = 0;}
                            else{$row->igst_amount = 0;}
                        }else{$row->igst_amount = 0;}
                        $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
                        //$pay_type = (!empty($row->gstin))?'DEBIT':'CASE';
                        $tbody .= '<tr>
                            <td>'.$i++.'</td>
                            <td>'.$row->memo_type.'</td>
                            <td>'.formatDate($row->trans_date).'</td>
                            <td>'.$row->hsn_code.'</td>';
                        if($data['memo_type'] == 'DEBIT')
                        {    
                            $tbody .= '<td>'.$row->doc_no.'</td>
                                <td>'.$row->party_name.'</td>
                                <td class="text-right">'.$row->gstin.'</td>
                                <td>'.$row->state_code.'-'.$row->state_name.'</td>';
                        }    
                        $tbody .= '<td>'.formatDecimal($row->gst_per).'%</td>
                            <td class="text-right">'. formatDecimal($row->taxable_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->cgst_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->sgst_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->igst_amount).'</td>
                            <td class="text-right">'.formatDecimal($row->net_amount).'</td>
                        </tr>';
                        $subTotal+=floatVal($row->taxable_amount);$cgst+=floatVal($row->cgst_amount);
                        $sgst+=floatVal($row->sgst_amount);$igst+=floatVal($row->igst_amount);
                        $oAmt+=floatVal($otherAmt);$netAmount+=floatval($row->net_amount);
                    }
                }   
        endforeach;
        $thead = '<tr class="thead-info">
					<th class="text-center" colspan="'.$rowspan.'">HSN WISE PURCHASE REGISTER</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
                </tr>
                <tr>
                    <th style="min-width:25px;">#</th>
					<th style="min-width:80px;">Cash/Debit</th>
					<th style="min-width:80px;">Vou. Date</th>
				    <th style="min-width:50px;">HSN</th>
				    '.$debitColH.'
					<th style="min-width:50px;">GST(%)</th>
					<th style="min-width:100px;">Taxable Amount</th>
					<th style="min-width:100px;">Cgst Amount</th>
					<th style="min-width:100px;">Sgst Amount</th>
					<th style="min-width:100px;">Igst Amount</th>
					<th style="min-width:50px;">Vou. Amount</th>
                </tr>';
        $tfoot = '<tr class="thead-info">
					<th colspan="'.$rowspan.'">TOTAL</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
				</tr>';
        
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'thead'=>$thead, 'tfoot'=>$tfoot]);
    }

    public function getSalesRegisterReport(){
        
        $data = $this->input->post();
        $salesRegisterReport = $this->accountingReport->getAccountReportDataItemWise($data['from_date'],$data['to_date'],'6,7,8,10,11',$data['memo_type']);
        $i=1; $tbody=""; $otherAmt = 0;$subTotal=0;$cgst=0;$sgst=0;$igst=0;$oAmt=0;$netAmount=0;
        foreach($salesRegisterReport as $row):
            $partyFilture = (!empty($data['party_id'])?$row->party_id == $data['party_id']:$row->party_id == $row->party_id);
            $empFilture = (!empty($data['emp_id']) ? $row->created_by == $data['emp_id'] : $row->created_by == $row->created_by);
                
            if($partyFilture){
                if($empFilture){
                    if(!empty($row->party_state_code))
                    {
                        if($row->party_state_code != 24){$row->cgst_amount = $row->sgst_amount = 0;}
                        else{$row->igst_amount = 0;}
                    }else{$row->igst_amount = 0;}
                    $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->memo_type.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->trans_number.'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->state_code.'-'.$row->state_name.'</td>
                        <td class="text-right">'.$row->gstin.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.formatDecimal($row->qty).'</td>
                        <td>'.formatDecimal($row->price).'</td>
                        <td>'.formatDecimal($row->gst_per).'%</td>
                        <td class="text-right">'. formatDecimal($row->taxable_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->cgst_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->sgst_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->igst_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->net_amount).'</td>
                        <td>'.$row->unit_name.'</td>
                    </tr>';
                    $subTotal+=floatVal($row->taxable_amount);$cgst+=floatVal($row->cgst_amount);
                    $sgst+=floatVal($row->sgst_amount);$igst+=floatVal($row->igst_amount);
                    $oAmt+=floatVal($otherAmt);$netAmount+=floatval($row->net_amount);
                }
            }
        endforeach;
        $thead = '<tr class="thead-info">
					<th class="text-center" colspan="12">SALES REGISTER</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
					<th></th>
                </tr>
                <tr>
                    <th style="min-width:25px;">#</th>
					<th style="min-width:80px;">Cash/Debit</th>
					<th style="min-width:80px;">Vou. Date</th>
					<th style="min-width:50px;">Vou. No</th>
					<th style="min-width:100px;">Account Name</th>
					<th style="min-width:100px;">State</th>
					<th style="min-width:100px;">Gst Number</th>
					<th style="min-width:100px;">Item Name</th>
				    <th style="min-width:50px;">HSN</th>
					<th style="min-width:50px;">Quantity</th>
					<th style="min-width:50px;">Price</th>
					<th style="min-width:50px;">GST(%)</th>
					<th style="min-width:100px;">Taxable Amount</th>
					<th style="min-width:100px;">Cgst Amount</th>
					<th style="min-width:100px;">Sgst Amount</th>
					<th style="min-width:100px;">Igst Amount</th>
					<th style="min-width:50px;">Vou. Amount</th>
					<th style="min-width:50px;">Prod. Unit</th>
                </tr>';
        $tfoot = '<tr class="thead-info">
					<th colspan="12">TOTAL</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
					<th></th>
				</tr>';
        
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'thead'=>$thead, 'tfoot'=>$tfoot]);
    }

    public function getPurchaseRegisterReport(){
        $data = $this->input->post();
        $salesRegisterReport = $this->accountingReport->getAccountReportDataItemWise($data['from_date'],$data['to_date'],'12',$data['memo_type']);
        $i=1; $tbody=""; $otherAmt = 0;$subTotal=0;$cgst=0;$sgst=0;$igst=0;$oAmt=0;$netAmount=0;
        foreach($salesRegisterReport as $row):
            $partyFilture = (!empty($data['party_id'])?$row->party_id == $data['party_id']:$row->party_id == $row->party_id);
            $empFilture = (!empty($data['emp_id']) ? $row->created_by == $data['emp_id'] : $row->created_by == $row->created_by);
                
            if($partyFilture){
                if($empFilture){
                    if(!empty($row->party_state_code))
                    {
                        if($row->party_state_code != 24){$row->cgst_amount = $row->sgst_amount = 0;}
                        else{$row->igst_amount = 0;}
                    }else{$row->igst_amount = 0;}
                    $otherAmt = $row->net_amount - ($row->taxable_amount + $row->cgst_amount + $row->sgst_amount + $row->igst_amount);
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->memo_type.'</td>
                        <td>'.formatDate($row->trans_date).'</td>
                        <td>'.$row->doc_no.'</td>
                        <td>'.$row->party_name.'</td>
                        <td>'.$row->state_code.'-'.$row->state_name.'</td>
                        <td class="text-right">'.$row->gstin.'</td>
                        <td>'.$row->item_name.'</td>
                        <td>'.$row->hsn_code.'</td>
                        <td>'.formatDecimal($row->qty).'</td>
                        <td>'.formatDecimal($row->price).'</td>
                        <td>'.formatDecimal($row->gst_per).'%</td>
                        <td class="text-right">'. formatDecimal($row->taxable_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->cgst_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->sgst_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->igst_amount).'</td>
                        <td class="text-right">'.formatDecimal($row->net_amount).'</td>
                        <td>'.$row->unit_name.'</td>
                    </tr>';
                    $subTotal+=floatVal($row->taxable_amount);$cgst+=floatVal($row->cgst_amount);
                    $sgst+=floatVal($row->sgst_amount);$igst+=floatVal($row->igst_amount);
                    $oAmt+=floatVal($otherAmt);$netAmount+=floatval($row->net_amount);
                }
            }
        endforeach;
        $thead = '<tr class="thead-info">
					<th class="text-center" colspan="12">PURCHASE REGISTER</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
					<th></th>
                </tr>
                <tr>
                    <th style="min-width:25px;">#</th>
					<th style="min-width:80px;">Cash/Debit</th>
					<th style="min-width:80px;">Vou. Date</th>
					<th style="min-width:50px;">Vou. No</th>
					<th style="min-width:100px;">Account Name</th>
					<th style="min-width:100px;">State</th>
					<th style="min-width:100px;">Gst Number</th>
					<th style="min-width:100px;">Item Name</th>
				    <th style="min-width:50px;">HSN</th>
					<th style="min-width:50px;">Quantity</th>
					<th style="min-width:50px;">Price</th>
					<th style="min-width:50px;">GST(%)</th>
					<th style="min-width:100px;">Taxable Amount</th>
					<th style="min-width:100px;">Cgst Amount</th>
					<th style="min-width:100px;">Sgst Amount</th>
					<th style="min-width:100px;">Igst Amount</th>
					<th style="min-width:50px;">Vou. Amount</th>
					<th style="min-width:50px;">Prod. Unit</th>
                </tr>';
        $tfoot = '<tr class="thead-info">
					<th colspan="12">TOTAL</th>
					<th class="text-right">' . formatDecimal($subTotal) . '</th>
					<th class="text-right">' . formatDecimal($cgst) . '</th>
					<th class="text-right">' . formatDecimal($sgst) . '</th>
					<th class="text-right">' . formatDecimal($igst) . '</th>
					<th class="text-right">' . formatDecimal($netAmount) . '</th>
					<th></th>
				</tr>';
        
        $this->printJson(['status'=>1, 'tbody'=>$tbody,'thead'=>$thead, 'tfoot'=>$tfoot]);
    }
    
    //UPDATED BY MEGHAVI 15-03-2022
    public function stockRegisterReport(){
        $this->data['pageHeader'] = 'STOCK REGISTER';
        $this->data['itemGroup'] = $this->storeReportModel->getItemGroup();
        $this->load->view($this->stock_register,$this->data);
    }

    //CREATED BY MEGHAVI 16-03-2022
    public function getStockRegister(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
            $itemData = $this->accountingReport->getStockRegister($data['item_type']);
            $thead="";$tbody="";$i=1;$receiptQty=0;$issuedQty=0;
            
            if(!empty($itemData)):
               foreach($itemData as $row):  
                    $data['item_id'] = $row->id;
                    $receiptQty = $this->accountingReport->getStockReceiptQty($data)->rqty;
                    $issuedQty = $this->accountingReport->getStockIssuedQty($data)->iqty;
                    $balanceQty = round($receiptQty - abs($issuedQty),3);
                    $tamt = ($balanceQty > 0)? round($balanceQty * $row->price, 2) : 0;
                    $tbody .= '<tr>
                        <td>'.$i++.'</td>
                        <td>'.$row->item_name.'</td>
                        <td class="text-right">'.floatVal($receiptQty).'</td>
                        <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                        <td class="text-right">'.floatVal($balanceQty).'</td>
                        <td class="text-right">'.number_format($tamt,2).'</td>
                    </tr>';
                endforeach;
            // else:
            //     $tbody .= '<tr style="text-align:center;"><td colspan="5">Data not found</td></tr>';
            endif;
            $this->printJson(['status'=>1, 'tbody'=>$tbody]);
        endif;
    }    
        
    public function gstr1Report(){
        $this->data['headData']->pageTitle = "GSTR 1 REPORT";
        $this->data['pageHeader'] = 'GSTR 1 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->load->view($this->gstr1_report, $this->data);
    }

    public function getGstr1ReportData($jsonData = ""){
        if (!empty($jsonData)) {
            $data = (array) decodeURL($jsonData);
        } else {
            $data = $this->input->post();
        }
        $data['entry_type']='6,7,8';
        $companyData = $this->accountingReport->getCompanyInfo();
        $salesReport = $this->accountingReport->getGstData($data);
        $i = 1;
        $tbody = '';
        $tfoot = '';
        $total_amount = 0;
        $taxable_amount = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $cess = 0;
        $gst_amount = 0;
        foreach ($salesReport as $row) :
            $tbody .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . formatDate($row->trans_date) . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfoot = "
        <tr>
            <th colspan='6' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";
        //$salesTable .= $tbody.'</tbody><tfoot id="footerData">'.$tfoot.'</tfoot></table>';
        $data['entry_type']='13,14';
        $data['vou_acc_id'] = 1;
        $salesReturnReport = $this->accountingReport->getGstData($data);
        $i = 1;
        $tbodyReturn = "";
        $tfootReturn = "";
        $total_amount = 0;
        $taxable_amount = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $cess = 0;
        $gst_amount = 0;
        foreach ($salesReturnReport as $row) :
            $row->total_amount = ($row->entry_type == 13)?($row->total_amount * -1):$row->total_amount;
            $row->taxable_amount = ($row->entry_type == 13)?($row->taxable_amount * -1):$row->taxable_amount;
            $row->cgst_amount = ($row->entry_type == 13)?($row->cgst_amount * -1):$row->cgst_amount;
            $row->sgst_amount = ($row->entry_type == 13)?($row->sgst_amount * -1):$row->sgst_amount;
            $row->igst_amount = ($row->entry_type == 13)?($row->igst_amount * -1):$row->igst_amount;
            $row->cess_amount = ($row->entry_type == 13)?($row->cess_amount * -1):$row->cess_amount;
            $row->gst_amount = ($row->entry_type == 13)?($row->gst_amount * -1):$row->gst_amount;
            $tbodyReturn .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . formatDate($row->trans_date) . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfootReturn = "<tr>
            <th colspan='6' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";

        if (!empty($data['file_type']) && $data['file_type'] == 'EXCEL') {

            $tableHeaderS = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR1 - SALES</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="5">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableHeaderSR = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR1 - SALES RETURN</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="5">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableSubHeader = '
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Customer Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="3">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Taxable Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<th>State Code</th><th>State Name</th><th>Invoice No.</th><th>Invoice Date</th>
										<th>Invoice Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>CESS</th><th>Total Tax</th>
									</tr>';
            $salesTable = $tableHeaderS . $tableSubHeader . $tbody . $tfoot . '</table>';
            $salesReturnTable = $tableHeaderSR . $tableSubHeader . $tbodyReturn . $tfootReturn . '</table>';

            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];

            // Sales Sheet
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->loadFromString($salesTable);
            $spreadsheet->getSheet(0)->setTitle('Sales');
            $salesSheet = $spreadsheet->getSheet(0);
            $sales_hcol = $salesSheet->getHighestColumn();
            $sales_hrow = $salesSheet->getHighestRow();
            $salesFullRange = 'A1:' . $sales_hcol . $sales_hrow;

            foreach (range('A', $sales_hcol) as $col) {
                $salesSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesSheet->getStyle('A1:' . $sales_hcol . '3')->applyFromArray($styleArray);
            $salesSheet->getStyle('A' . $sales_hrow . ':' . $sales_hcol . $sales_hrow)->applyFromArray($fontBold);
            $salesSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesSheet->getStyle('A' . $sales_hrow)->applyFromArray($alignRight);
            $salesSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesSheet->getStyle($salesFullRange)->applyFromArray($borderStyle);


            // Sales Return Sheet
            $reader->setSheetIndex(1);
            $salesReturnSheet = $spreadsheet->createSheet();
            $salesReturnSheet->setTitle('Sales Return');
            $spreadsheet = $reader->loadFromString($salesReturnTable, $spreadsheet);
            $salesreturn_hcol = $salesReturnSheet->getHighestColumn();
            $salesreturn_hrow = $salesReturnSheet->getHighestRow();
            $salesReturnFullRange = 'A1:' . $salesreturn_hcol . $salesreturn_hrow;

            foreach (range('A', $salesreturn_hcol) as $col) {
                $salesReturnSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesReturnSheet->getStyle('A1:' . $salesreturn_hcol . '3')->applyFromArray($styleArray);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow . ':' . $salesreturn_hcol . $salesreturn_hrow)->applyFromArray($fontBold);
            $salesReturnSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow)->applyFromArray($alignRight);
            $salesReturnSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesReturnSheet->getStyle($salesReturnFullRange)->applyFromArray($borderStyle);

            $fileDirectory = realpath(APPPATH . '../assets/uploads/');
            $fileName = '/GSTR1' . time() . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/') . $fileName);
        } else {

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tbodyReturn' => $tbodyReturn, 'tfoot' => $tfoot, 'tfootReturn' => $tfootReturn]);
        }
    }

    public function gstr2Report(){
        $this->data['headData']->pageTitle = "GSTR 2 REPORT";
        $this->data['pageHeader'] = 'GSTR 2 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getSupplierList();
        $this->load->view($this->gstr2_report, $this->data);
    }

    public function getGstr2ReportData($jsonData = ""){
        if (!empty($jsonData)) {
            $data = (array) decodeURL($jsonData);
        } else {
            $data = $this->input->post();
        }

        $data['entry_type'] = '12';
        $companyData = $this->accountingReport->getCompanyInfo();        
        $purchaseReport = $this->accountingReport->getGstData($data);

        $i = 1;$tbody = '';$tfoot = '';$total_amount = 0;$taxable_amount = 0; $cgst = 0;$sgst = 0;$igst = 0;$cess = 0;$gst_amount = 0;

        foreach ($purchaseReport as $row) :
            $tbody .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . $row->doc_no . '</td>
                <td>' . $row->trans_date . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfoot = "
        <tr>
            <th colspan='7' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";

        $data['entry_type'] = "13,14";
        $data['vou_acc_id'] = 10;
        $purchaseReturnReport = $this->accountingReport->getGstData($data);
        $i = 1;
        $tbodyReturn = "";$tfootReturn = "";
        $total_amount = 0;
        $taxable_amount = 0;
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $cess = 0;
        $gst_amount = 0;
        foreach ($purchaseReturnReport as $row) :
            $row->total_amount = ($row->entry_type == 14)?($row->total_amount * -1):$row->total_amount;
            $row->taxable_amount = ($row->entry_type == 14)?($row->taxable_amount * -1):$row->taxable_amount;
            $row->cgst_amount = ($row->entry_type == 14)?($row->cgst_amount * -1):$row->cgst_amount;
            $row->sgst_amount = ($row->entry_type == 14)?($row->sgst_amount * -1):$row->sgst_amount;
            $row->igst_amount = ($row->entry_type == 14)?($row->igst_amount * -1):$row->igst_amount;
            $row->cess_amount = ($row->entry_type == 14)?($row->cess_amount * -1):$row->cess_amount;
            $row->gst_amount = ($row->entry_type == 14)?($row->gst_amount * -1):$row->gst_amount;
            $tbodyReturn .= '<tr>
                <td>' . $row->gstin . '</td>
                <td>' . $row->party_name . '</td>
                <td>' . $row->party_state_code . '</td>
                <td>' . $row->state_name . '</td>
                <td>' . $row->trans_number . '</td>
                <td>' . $row->trans_date . '</td>
                <td>' . $row->total_amount . '</td>
                <td>0</td>
                <td>' . $row->taxable_amount . '</td>
                <td>' . $row->cgst_amount . '</td>
                <td>' . $row->sgst_amount . '</td>
                <td>' . $row->igst_amount . '</td>
                <td>' . $row->cess_amount . '</td>
                <td>' . $row->gst_amount . '</td>
            </tr>';
            $total_amount += $row->total_amount;
            $taxable_amount += $row->taxable_amount;
            $sgst += $row->sgst_amount;
            $cgst += $row->cgst_amount;
            $igst += $row->igst_amount;
            $cess += $row->cess_amount;
            $gst_amount += $row->gst_amount;
        endforeach;
        $tfootReturn = "<tr>
            <th colspan='6' class='text-right'>Total</th>
            <th>" . $total_amount . "</th>
            <th></th>
            <th>" . $taxable_amount . "</th>
            <th>" . $cgst . "</th>
            <th>" . $sgst . "</th>
            <th>" . $igst . "</th>
            <th>" . $cess . "</th>
            <th>" . $gst_amount . "</th>
        </tr>";
        if (!empty($data['file_type']) && $data['file_type'] == 'EXCEL') {

            $tableHeaderP = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR2 - PURCHASE</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="6">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableHeaderPR = '<table id="commanTable" class="table table-bordered">
							<tr><th colspan="4">GSTR2 - PURCHASE RETURN</th><th colspan="5">' . $companyData->company_name . '</th><th colspan="5">' . date('d/m/Y', strtotime($data['from_date'])) . ' - ' . date('d/m/Y', strtotime($data['to_date'])) . '</th></tr>';
            $tableSubHeaderP = '
									<tr>
										<th rowspan="2">GSTIN</th>
										<th rowspan="2">Customer Name</th>
										<th colspan="2">Place Of supply</th>
										<th colspan="4">Invoice Detail</th>
										<th rowspan="2">Total Tax(%)</th>
										<th rowspan="2">Taxable Value</th>
										<th colspan="5">Amount Of Tax</th>
									</tr>
									<tr>
										<th>State Code</th><th>State Name</th><th>Invoice No.</th><th>Original Invoice No.</th><th>Invoice Date</th>
										<th>Invoice Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>CESS</th><th>Total Tax</th>
									</tr>';
            $tableSubHeaderPR = '
            <tr>
                <th rowspan="2">GSTIN</th>
                <th rowspan="2">Customer Name</th>
                <th colspan="2">Place Of supply</th>
                <th colspan="3">Invoice Detail</th>
                <th rowspan="2">Total Tax(%)</th>
                <th rowspan="2">Taxable Value</th>
                <th colspan="5">Amount Of Tax</th>
            </tr>
            <tr>
                <th>State Code</th><th>State Name</th><th>Invoice No.</th><th>Invoice Date</th>
                <th>Invoice Value</th><th>CGST</th><th>SGST</th><th>IGST</th><th>CESS</th><th>Total Tax</th>
            </tr>';
            $purchaseTable = $tableHeaderP . $tableSubHeaderP . $tbody . $tfoot . '</table>';
            $purchaseReturnTable = $tableHeaderPR . $tableSubHeaderPR . $tbodyReturn . $tfootReturn . '</table>';

            $spreadsheet = new Spreadsheet();
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Htm();
            $styleArray = [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
            ];
            $fontBold = ['font' => ['bold' => true]];
            $alignLeft = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]];
            $alignCenter = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]];
            $alignRight = ['alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]];
            $borderStyle = [
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']],
                ]
            ];

            // Sales Sheet
            $reader->setSheetIndex(0);
            $spreadsheet = $reader->loadFromString($purchaseTable);
            $spreadsheet->getSheet(0)->setTitle('Purchase');
            $salesSheet = $spreadsheet->getSheet(0);
            $sales_hcol = $salesSheet->getHighestColumn();
            $sales_hrow = $salesSheet->getHighestRow();
            $salesFullRange = 'A1:' . $sales_hcol . $sales_hrow;

            foreach (range('A', $sales_hcol) as $col) {
                $salesSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesSheet->getStyle('A1:' . $sales_hcol . '3')->applyFromArray($styleArray);
            $salesSheet->getStyle('A' . $sales_hrow . ':' . $sales_hcol . $sales_hrow)->applyFromArray($fontBold);
            $salesSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesSheet->getStyle('A' . $sales_hrow)->applyFromArray($alignRight);
            $salesSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesSheet->getStyle($salesFullRange)->applyFromArray($borderStyle);


            // Sales Return Sheet
            $reader->setSheetIndex(1);
            $salesReturnSheet = $spreadsheet->createSheet();
            $salesReturnSheet->setTitle('Purchase Return');
            $spreadsheet = $reader->loadFromString($purchaseReturnTable, $spreadsheet);
            $salesreturn_hcol = $salesReturnSheet->getHighestColumn();
            $salesreturn_hrow = $salesReturnSheet->getHighestRow();
            $salesReturnFullRange = 'A1:' . $salesreturn_hcol . $salesreturn_hrow;

            foreach (range('A', $salesreturn_hcol) as $col) {
                $salesReturnSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $salesReturnSheet->getStyle('A1:' . $salesreturn_hcol . '3')->applyFromArray($styleArray);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow . ':' . $salesreturn_hcol . $salesreturn_hrow)->applyFromArray($fontBold);
            $salesReturnSheet->getStyle('A1')->applyFromArray($alignLeft);
            $salesReturnSheet->getStyle('A' . $salesreturn_hrow)->applyFromArray($alignRight);
            $salesReturnSheet->getStyle('J1')->applyFromArray($alignRight);
            $salesReturnSheet->getStyle($salesReturnFullRange)->applyFromArray($borderStyle);

            $fileDirectory = realpath(APPPATH . '../assets/uploads/');
            $fileName = '/GSTR2' . time() . '.xlsx';
            $writer = new Xlsx($spreadsheet);

            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/') . $fileName);
        } else {

            $this->printJson(['status' => 1, 'tbody' => $tbody, 'tbodyReturn' => $tbodyReturn, 'tfoot' => $tfoot, 'tfootReturn' => $tfootReturn]);
        }
    }
    
}
?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  