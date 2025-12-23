<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory as io_factory; 
class GstReport extends MY_Controller{
    private $gstr1_report = "report/gst_report/gstr1_report";
    private $gstr2_report = "report/gst_report/gstr2_report";
    private $gstr1Types = [
        'b2b'=>'b2b,sez,de',
        'b2ba' => 'b2ba',
        'b2cl' => 'b2cl',
        'b2cla' => 'b2cla',
        'b2cs' => 'b2cs',
        'b2csa' => 'b2csa',
        'cdnr' => 'cdnr',
        'cdnra' => 'cdnra',
        'cdnur' => 'cdnur',
        'cdnura' => 'cdnura',
        'exp'=>'exp',
        'expa' => 'expa',
        'at' => 'at',
        'ata' => 'ata',
        'atadj' => 'atadj',
        'atadja' => 'atadja',
        'exemp' => 'exemp',
        'hsn' => 'hsn',
        'docs' => 'docs' 
    ];

    public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "GST Report";
		$this->data['headData']->controller = "reports/gstReport";
	}

    public function gstr1(){
        $this->data['headData']->pageTitle = "GSTR 1 REPORT";
        $this->data['pageHeader'] = 'GSTR 1 REPORT';
        $this->data['startDate'] = $this->startYearDate;
        $this->data['endDate'] = $this->endYearDate;
        $this->data['customerData'] = $this->party->getCustomerList();
        $this->data['gstr1Types'] = $this->gstr1Types;
        $this->load->view($this->gstr1_report, $this->data);
    }

    public function getGstr1Report($jsonData=''){        
        if(!empty($jsonData)):
            $data =(array) decodeURL($jsonData);

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
            $bgPrimary = [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DAEAFA'],
                ],
                'font' => ['bold' => true]
            ];

            $i = 0;
            foreach($this->gstr1Types as $key => $value):
                $html = "";
                $html = $this->{$key}($data)['html'];                

                $pdfData = '<table>'.$html.'</table>';
                
                $reader->setSheetIndex($i);

                if($i == 0):
                    $spreadsheet = $reader->loadFromString($pdfData);
                else:
                    $spreadsheet = $reader->loadFromString($pdfData,$spreadsheet);
                endif;

                $spreadsheet->getSheet($i)->setTitle($value);
                $excelSheet = $spreadsheet->getSheet($i);
                $hcol = $excelSheet->getHighestColumn();
                $hrow = $excelSheet->getHighestRow();
                $packFullRange = 'A1:' . $hcol . $hrow;
                foreach (range('A', $hcol) as $col):
                    $excelSheet->getColumnDimension($col)->setAutoSize(true);
                endforeach;
                $excelSheet->getStyle($packFullRange)->applyFromArray($borderStyle);
                $excelSheet->getStyle('A1:'.$hcol.'2')->applyFromArray($bgPrimary);
                $excelSheet->getStyle('A4:'.$hcol.'4')->applyFromArray($bgPrimary);
                $i++;
            endforeach;

            $fileDirectory = realpath(APPPATH . '../assets/uploads/gst_report');
            $fileName = '/gstr1_' . time() . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileDirectory . $fileName);
            header("Content-Type: application/vnd.ms-excel");
            redirect(base_url('assets/uploads/gst_report') . $fileName);
        else:
            $data = $this->input->post();
            $result = $this->{$data['report_type']}($data);
            $this->printJson($result);
        endif;
    }

    public function b2b($data){
        $data['entry_type']='6,7,8';
        $result = $this->gstReport->_b2b($data);

        $no_of_recipients = 0;
        $no_of_invoice = 0;
        $total_invoice_value = 0;
        $total_taxable_value = 0;
        $total_cess = 0;

        $no_of_recipients = count(array_unique(array_column($result,'gstin')));
        $no_of_invoice = count($result);
        $total_invoice_value = array_sum(array_column($result,'net_amount'));
        $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
        $total_cess = array_sum(array_column($result,'cess_amount'));

        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="13">Summary For B2B,SEZ,DE(4A,4B,6B,6C)</th>
            </tr>
            <tr>
                <th>No. of Recipients</th>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td>'.$no_of_recipients.'</td>
                <td></td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_cess.'</td>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Applicable % of Tax Rate</th>
                <th>Invoice Type</th>
                <th>E-Commerce GSTIN</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead><tbody>';

        foreach($result as $row):
            $html .= '<tr>
                <td class="text-left">'.$row->gstin.'</td>
                <td class="text-left">'.$row->party_name.'</td>
                <td class="text-left">'.$row->trans_prefix.$row->trans_no.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.floatVal($row->net_amount).'</td>
                <td class="text-left">'.$row->gst_statecode.'-'.$row->state_name.'</td>
                <td class="text-center">N</td>
                <td class="text-left"></td>
                <td class="text-left">Regular B2B</td>
                <td class="text-left"></td>
                <td class="text-right">'.floatVal($row->gst_per).'</td>
                <td class="text-right">'.floatVal($row->taxable_amount).'</td>
                <td class="text-right">'.floatVal($row->cess_amount).'</td>
            </tr>';
        endforeach;

        $html .= '</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function b2ba($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th>Summary For B2BA</th>
                <th colspan="3">Original Details</th>
                <th colspan="11">Revised details</th>
            </tr>
            <tr>
                <th>No. of Recipients</th>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Applicable % of Tax Rate</th>
                <th>Invoice Type</th>
                <th>E-Commerce GSTIN</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function b2cl($data){
        $data['entry_type']='6,7,8';
        $result=$this->gstReport->_b2cl($data);
        $total_invoice_value = 0;
        $total_taxable_value = 0;
        $total_cess = 0;

        $total_invoice_value = array_sum(array_column($result,'net_amount'));
        $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
        $total_cess = array_sum(array_column($result,'cess_amount'));
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="10">Summary For B2CL(5)</th>
            </tr>
            <tr>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>'.count($result).'</td>
                <td></td>
                <td>'.floatval($total_invoice_value).'</td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.floatval($total_taxable_value).'</td>
                <td>'.floatval($total_cess).'</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
                <th>Sale from Bonded WH</th>
            </tr>
        </thead><tbody>';
        foreach($result as $row):
            $html .= '<tr>
                <td class="text-left">'.$row->trans_prefix.$row->trans_no.'</td>
                <td class="text-center">'.date("d-M-Y",strtotime($row->trans_date)).'</td>
                <td class="text-right">'.floatVal($row->net_amount).'</td>
                <td class="text-left">'.$row->gst_statecode.'-'.$row->state_name.'</td>
                <td class="text-center">N</td>
                <td class="text-right">'.floatVal($row->gst_per).'</td>
                <td class="text-right">'.floatVal($row->taxable_amount).'</td>
                <td class="text-right">'.floatVal($row->cess_amount).'</td>
                <td class="text-left"></td>
                <td class="text-left"></td>
                
            </tr>';
        endforeach;
        $html.='</tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function b2cla($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th>Summary For B2CLA</th>
                <th colspan="2">Original Details</th>
                <th colspan="9">Revised details</th>
            </tr>
            <tr>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td>0</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Original Place Of Supply</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
                <th>Sale from Bonded WH</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function b2cs($data){
        $data['entry_type']='6,7,8';
        $result=$this->gstReport->_b2cs($data);

        $total_taxable_value = 0;
        $total_cess = 0;

        $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
        $total_cess = array_sum(array_column($result,'cess_amount'));        
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>    
                <th colspan="7">Summary For B2CS(7)</th>
            </tr>
            <tr>    
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
            </tr>
            <tr>    
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>'.floatval($total_taxable_value).'</th>
                <th>'.floatval($total_cess).'</th>
                <th></th>
            </tr>
            <tr>
                <th>Type</th>
                <th>Place Of Supply</th>
                <th>Applicable % Of Tax</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commarce GSTIN</th>
            </tr>
        </thead><tbody>';

        foreach($result as $row):
            $html .= '<tr>
                <td class="text-left">OE</td>
                <td class="text-left">'.$row->party_state_code.' - '.$row->state_name.'</td>
                <td class="text-left"></td>
                <td class="text-right">'.$row->gst_per.'</td>
                <td class="text-right">'.floatval($row->taxable_amount).'</td>
                <td class="text-right">'.floatval($row->cess_amount).'</td>
                <td class="text-left"></td>
            </tr>';
        endforeach;

        $html .= '</tbody>';

        return ['status'=>1,'html'=>$html];
    }

    public function b2csa($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th>Summary For B2CSA</th>
                <th>Original Details</th>
                <th colspan="7">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
                <th></th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>0</td>
                <td>0</td>
                <td></td>
            </tr>
            <tr>
                <th>Financial Year</th>
                <th>Original Month</th>
                <th>Place Of Supply</th>
                <th>Type</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
                <th>E-Commerce GSTIN</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function cdnr($data){
        
        $data['entry_type']='13,14';
        $result = $this->gstReport->_cdnr($data);
        $no_of_recipients = 0;
        $no_of_invoice = 0;
        $total_invoice_value = 0;
        $total_taxable_value = 0;
        $total_cess = 0;

        $no_of_recipients = count(array_unique(array_column($result,'gstin')));
        $no_of_invoice = count($result);
        $total_invoice_value = array_sum(array_column($result,'net_amount'));
        $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
        $total_cess = array_sum(array_column($result,'cess_amount'));
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="13">Summary For CDNR(9B)</th>
            </tr>
            <tr>
                <th>No. of Recipient</th>
                <th></th>
                <th>No. of Notes</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <td>'.$no_of_recipients.'</td>
                <td></td>
                <td>'.$no_of_invoice.'</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>'.$total_invoice_value.'</td>
                <td></td>
                <td></td>
                <td>'.$total_taxable_value.'</td>
                <td>'.$total_cess.'</td>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Note Number</th>
                <th>Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Note Supply Type</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead><tbody>';
        foreach($result as $row):
            // print_r($row);
            $html .= '<tr>
                <td class="text-left">'.$row->gstin.'</td>
                <td class="text-left">'.$row->party_name.'</td>
                <td class="text-left">'.$row->trans_number.'</td>
                <td class="text-center">'.$row->trans_date.'</td>
                <td class="text-center">'.(($row->entry_type == 13)?"C":"D").'</td>
                <td class="text-left">'.$row->gst_statecode.'-'.$row->state_name.'</td>
                <td class="text-center">'.(($row->gst_applicable == 1)?"Y":"N").'</td>
                <td class="text-left">Regular B2B</td>
                <td class="text-right">'.floatVal($row->net_amount).'</td>
                <td class="text-center">N</td>  
                <td class="text-right">'.floatVal($row->gst_per).'</td>
                <td class="text-right">'.floatVal($row->taxable_amount).'</td>
                <td class="text-right">'.floatVal($row->cess_amount).'</td>
            </tr>';
        endforeach;
        $html.='</tbody>';
        
        return ['status'=>1,'html'=>$html];
    }

    public function cdnra($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For CDNRA</th>
                <th colspan="5" class="text-center">Original Details</th>
                <th colspan="9" class="text-center">Revised details</th>
            </tr>
            <tr>
                <th>No. of Recipient</th>
                <th></th>
                <th>No. of Notes/Vouchers</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th>0</th>
                <th></th>
                <th>0</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>0</th>
                <th></th>
                <th></th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>GSTIN/UIN of Recipient</th>
                <th>Receiver Name</th>
                <th>Original Note Number</th>
                <th>Original Note Date</th>
                <th>Revised Note Number</th>
                <th>Revised Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Reverse Charge</th>
                <th>Note Supply Type</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function cdnur($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="10">Summary For CDNUR(9B)</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Notes</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th>0</th>
                <th></th>
                <th></th>
                <th></th>
                <th>0</th>
                <th></th>
                <th></th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>UR Type</th>
                <th>Note Number</th>
                <th>Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function cdnura($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For CDNURA</th>
                <th colspan="4" class="text-center">Original Details</th>
                <th colspan="7" class="text-center">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Notes/Vouchers</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Notes/Vouchers</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Note Value</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th>UR Type</th>
                <th>Original Note Number</th>
                <th>Original Note Date</th>
                <th>Revised Note Number</th>
                <th>Revised Note Date</th>
                <th>Note Type</th>
                <th>Place Of Supply</th>
                <th>Note Value</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function exp($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="10">Summary For EXP(6)</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th>No. of Shipping Bill</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th>0</th>
                <th></th>
                <th>0</th>
                <th></th>
                <th>0</th>
                <th></th>
                <th></th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Export Type</th>
                <th>Invoice Number</th>
                <th>Invoice date</th>
                <th>Invoice Value</th>
                <th>Port Code</th>
                <th>Shipping Bill Number</th>
                <th>Shipping Bill Date</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function expa($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For EXP(6)</th>
                <th colspan="2">Original Details</th>
                <th colspan="9">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th>No. of Invoices</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Invoice Value</th>
                <th></th>
                <th>No. of Shipping Bill</th>
                <th></th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th>0</th>
                <th></th>
                <th></th>
                <th></th>
                <th>0</th>
                <th></th>
                <th>0</th>
                <th></th>
                <th></th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Export Type</th>
                <th>Original Invoice Number</th>
                <th>Original Invoice date</th>
                <th>Revised Invoice Number</th>
                <th>Revised Invoice date</th>
                <th>Invoice Value</th>
                <th>Port Code</th>
                <th>Shipping Bill Number</th>
                <th>Shipping Bill Date</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function at($data){
        $data['entry_type']='15';
        $result = $this->gstReport->_at($data);
        
        $total_value = 0;
        $total_cess = 0;

        $total_value = array_sum(array_column($result,'net_amount'));
        $total_cess = array_sum(array_column($result,'cess_amount'));
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="5">Summary For Advance Received (11B)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Received</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>'. $total_value.'</th>
                <th>'.$total_cess.'</th>
            </tr>
            <tr>
                <th>Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Received</th>
                <th>Cess Amount</th>
            </tr>
        </thead><tbody>';
        foreach($result as $row):
            // print_r($row);
            $html .= '<tr>
                <td class="text-left">'.$row->gst_statecode.'-'.$row->state_name.'</td>
                <td class="text-left"></td>
                <td class="text-right"></td>
                <td class="text-right">'.floatVal($row->net_amount).'</td>
                <td class="text-right">'.floatVal($row->cess_amount).'</td>
            </tr>';
        endforeach;
        $html.='</tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function ata($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For Amended Tax Liability(Advance Received)</th>
                <th colspan="2">Original Details</th>
                <th colspan="4">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Received</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Financial Year  </th>
                <th>Original Month</th>
                <th>Original Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Received</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function atadj($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For Advance Adjusted (11B)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Adjusted</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Adjusted</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function atadja($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For Amendement Of Adjustment Advances</th>
                <th colspan="2">Original Details</th>
                <th colspan="4">Revised details</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Advanced Adjusted</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Financial Year  </th>
                <th>Original Month</th>
                <th>Original Place Of Supply</th>
                <th>Applicable % of Tax Rate</th>
                <th>Rate</th>
                <th>Gross Advance Adjusted</th>
                <th>Cess Amount</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function exemp($data){
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="5">Summary For Nil rated, exempted and non GST outward supplies (8)</th>
            </tr>
            <tr>
                <th></th>
                <th>Total Nil Rated Supplies</th>
                <th>Total Exempted Supplies</th>
                <th>Total Non-GST Supplies</th>
            </tr>
            <tr>
                <th></th>
                <th>0</th>
                <th>0</th>
                <th>0</th>
            </tr>
            <tr>
                <th>Description</th>
                <th>Nil Rated Supplies</th>
                <th>Exempted(other than nil rated/non GST supply)</th>
                <th>Non-GST supplies</th>
            </tr>
        </thead>';
        return ['status'=>1,'html'=>$html];
    }

    public function hsn($data){
        $data['entry_type']='6,7,8';
        $result=$this->gstReport->_hsn($data);

        $total_taxable_value = 0;
        $total_value = 0;
        $total_cgst = 0;
        $total_sgst = 0;
        $total_igst = 0;
        $total_cess = 0;

        $total_taxable_value = array_sum(array_column($result,'taxable_amount'));
        $total_value = array_sum(array_column($result,'net_amount'));
        $total_cgst = array_sum(array_column($result,'cgst_amount'));
        $total_sgst = array_sum(array_column($result,'sgst_amount'));
        $total_igst = array_sum(array_column($result,'igst_amount'));       
        $total_cess = array_sum(array_column($result,'cess_amount'));       
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th >Summary For HSN(12)</th>
            </tr>
            <tr>
                <th>No. of HSN</th>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Value</th>
                <th></th>
                <th>Total Taxable Value</th>
                <th>Total Integrated Tax</th>
                <th>Total Central Tax</th>
                <th>Total State/UT Tax</th>
                <th>Total Cess</th>
            </tr>
            <tr>
                <th>'.count($result).'</th>
                <th></th>
                <th></th>
                <th></th>
                <th>'.floatval($total_value).'</th>
                <th></th>
                <th>'.floatval($total_taxable_value).'</th>
                <th>'.floatval($total_igst).'</th>
                <th>'.floatval($total_cgst).'</th>
                <th>'.floatval($total_sgst).'</th>
                <th>'.floatval($total_cess).'</th>
            </tr>
            <tr>
                <th>HSN</th>
                <th>Description</th>
                <th>UQC</th>
                <th>Total Quantity</th>
                <th>Total Value</th>
                <th>Rate</th>
                <th>Taxable Value</th>
                <th>Integrated Tax Amount</th>
                <th>Central Tax Amount</th>
                <th>State/UT Tax Amount</th>
                <th>Cess Amount</th>
            </tr>
        </thead><tbody>';

        foreach($result as $row):
            $html .= '<tr>
            <td>'.$row->hsn_code.'</td>
            <td></td>
            <td>'.$row->unit_name.' - '.$row->unit_description.'</td>
            <td>'.$row->qty.'</td>
            <td>'.floatval($row->net_amount).'</td>
            <td>'.floatval($row->gst_per).'</td>
            <td>'.floatval($row->taxable_amount).'</td>
            <td>'.floatval($row->igst_amount).'</td>
            <td>'.floatval($row->cgst_amount).'</td>
            <td>'.floatval($row->sgst_amount).'</td>
            <td>'.floatval($row->cess_amount).'</td>
            </tr>';
        endforeach;
        $html .='</tbody>';
        return ['status'=>1,'html'=>$html];
    }

    public function docs($data){
        $data['entry_type']='6,7,8';
        $result=$this->gstReport->_docs($data);
        $total_number = 0;
        $total_cancelled = 0;

        $total_number = array_sum(array_column($result,'total_inv'));
        $html = '';
        $html .= '<thead class="thead-info text-center">
            <tr>
                <th colspan="5">Summary of documents issued during the tax period(13)</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>Total Number</th>
                <th>Total Cancelled</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>'.$total_number.'</th>
                <th>'.$total_cancelled.'</th>
            </tr>
            <tr>
                <th>Nature of Document</th>
                <th>Sr. No. From</th>
                <th>Sr. No. To</th>
                <th>Total Number</th>
                <th>Cancelled</th>
            </tr>
        </thead>';
        foreach($result as $row):
			$trans_min_number = "";$trans_max_number = "";
			$tno = (!empty($row->trans_number))?explode("/",$row->trans_number):array();
			if(count($tno) > 0):
				if(count($tno) > 2):
					$trans_min_number = $tno[0]."/".$row->min_trans_no."/".$tno[2];
					$trans_max_number = $tno[0]."/".$row->max_trans_no."/".$tno[2];
				else:
					$trans_min_number = $tno[0]."/".$row->min_trans_no;
					$trans_max_number = $tno[0]."/".$row->max_trans_no;
				endif;
			endif;
            $html .= '<tr>
                <td class="text-left">Invoices for outward supply</td>
                <td class="text-left">'.$trans_min_number.'</td>
                <td class="text-left">'.$trans_max_number.'</td>
                <td class="text-right">'.$row->total_inv.'</td>
                <td class="text-left">0</td>
            </tr>';
        endforeach;
        return ['status'=>1,'html'=>$html];
    }
}
?>