<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getSalesDtHeader($page)
{	
    /* Party Header */
    $data['customer'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['customer'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['customer'][] = ["name"=>"Company Name"];
	$data['customer'][] = ["name"=>"Contact Person"];
    $data['customer'][] = ["name"=>"Contact No."];
    $data['customer'][] = ["name"=>"Party Code"];
    $data['customer'][] = ["name"=>"Currency"];
    
	/* Sales Enquiry Header */
	$data['salesEnquiry'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesEnquiry'][] = ["name"=>"Enq. No."];
    $data['salesEnquiry'][] = ["name"=>"Enq. Date"];
    $data['salesEnquiry'][] = ["name"=>"Customer Name"];
    //$data['salesEnquiry'][] = ["name"=>"Item Name"];
    //$data['salesEnquiry'][] = ["name"=>"Qty"];
    $data['salesEnquiry'][] = ["name"=>"Status"];
    // $data['salesEnquiry'][] = ["name"=>"Quoted","style"=>"width:5%;","textAlign"=>"center"];
    // $data['salesEnquiry'][] = ["name"=>"Feasible","style"=>"width:5%;","textAlign"=>"center"];
    // $data['salesEnquiry'][] = ["name"=>"Not Feasible","style"=>"width:5%;","textAlign"=>"center"];
    $data['salesEnquiry'][] = ["name"=>"Remark"];

	/* Sales Quotation Header */
    $data['salesQuotation'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['salesQuotation'][] = ["name"=>"Quote No."];
    $data['salesQuotation'][] = ["name"=>"Quote Date"];
    $data['salesQuotation'][] = ["name"=>"Customer Name"];
    //$data['salesQuotation'][] = ["name"=>"Product Name"];
    //$data['salesQuotation'][] = ["name"=>"Qty"];
    //$data['salesQuotation'][] = ["name"=>"Quote Price"];
    //$data['salesQuotation'][] = ["name"=>"Confirmed Price"];
    $data['salesQuotation'][] = ["name"=>"Confirmed Date"];
    $data['salesQuotation'][] = ["name"=>"Enq. No."];

    /* Sales Order Header */
    $data['salesOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['salesOrder'][] = ["name"=>"#","textAlign"=>"center"];
	$data['salesOrder'][] = ["name"=>"SO. No.","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"SO. Date","style"=>"width:10%;","textAlign"=>"center"];
    //$data['salesOrder'][] = ["name"=>"Slaes Type","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Customer Name"];
    $data['salesOrder'][] = ["name"=>"Cust. PO.NO."];
	$data['salesOrder'][] = ["name"=>"Quot. No."];
    //$data['salesOrder'][] = ["name"=>"Product"];
    //$data['salesOrder'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    //$data['salesOrder'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"];
    //$data['salesOrder'][] = ["name"=>"Pending Qty.","textAlign"=>"center","textAlign"=>"center"];
    $data['salesOrder'][] = ["name"=>"Delivery Date","textAlign"=>"center"]; 
    //$data['salesOrder'][] = ["name"=>"Status","textAlign"=>"center"]; 

    /* Sales Invoice Header */
    $data['salesInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['salesInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['salesInvoice'][] = ["name"=>"Customer Name"]; 
    $data['salesInvoice'][] = ["name"=>"Memo Type"];
    $data['salesInvoice'][] = ["name"=>"Bill Amount","textAlign"=>"right"];  

    /* Delivery Challan Header */
    $data['deliveryChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['deliveryChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['deliveryChallan'][] = ["name"=>"Challan. No.","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"DC. Date","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"Customer Name"]; 
    //$data['deliveryChallan'][] = ["name"=>"Product Name"]; 
    //$data['deliveryChallan'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"]; 

	/* packing instruction Header */
	$data['packingInstruction'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['packingInstruction'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['packingInstruction'][] = ["name"=>"Dispatch Date"];
	$data['packingInstruction'][] = ["name"=>"Item Code"];
	$data['packingInstruction'][] = ["name"=>"Item Name"];
	$data['packingInstruction'][] = ["name"=>"Qty."];
	$data['packingInstruction'][] = ["name"=>"Remark"];
	$data['packingInstruction'][] = ["name"=>"Status","textAlign"=>"center"];
	
	/* Commercial Invoice Header */
    $data['commercialInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['commercialInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['commercialInvoice'][] = ["name"=>"Customer Name"]; 
    $data['commercialInvoice'][] = ["name"=>"Bill Amount","textAlign"=>"center"]; 

    /* Custom Invoice Header */
    $data['customInvoice'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['customInvoice'][] = ["name"=>"Invoice No.","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Invoice Date","textAlign"=>"center"];
    $data['customInvoice'][] = ["name"=>"Customer Name"]; 
    $data['customInvoice'][] = ["name"=>"Bill Amount","textAlign"=>"center"];

	/* Product Header */ //Updated By Meghavi 15-03-2022
	$data['products'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['products'][] = ["name"=>"Item Image","textAlign"=>"center"];
	$data['products'][] = ["name"=>"ID","textAlign"=>"center"];
	$data['products'][] = ["name"=>"Item Name"];
	$data['products'][] = ["name"=>"Category"];
	$data['products'][] = ["name"=>"HSN Code"];
	// $data['products'][] = ["name"=>"Part No"];
	// $data['products'][] = ["name"=>"Drawing No."];
	// $data['products'][] = ["name"=>"Rev. No."];
	$data['products'][] = ["name"=>"Price"];
	$data['products'][] = ["name"=>"Semi Price"];
	$data['products'][] = ["name"=>"Wholesale"];
	//$data['products'][] = ["name"=>"Opening Qty"];
	$data['products'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
	$data['products'][] = ["name"=>"Manage Stock"];

	/*	Cycle Time Header */
    $data['cycleTime'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['cycleTime'][] = ["name"=>"Part Code"];
    $data['cycleTime'][] = ["name"=>"Manage Time","style"=>"width:20%;"];

    /* Tool Consumption Header */
    $data['toolConsumption'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['toolConsumption'][] = ["name"=>"Tool Description"];
    $data['toolConsumption'][] = ["name"=>"Action","style"=>"width:20%;"];

    /* Proforma Invoice Header */
    $data['proformaInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['proformaInvoice'][] = ["name"=>"Invoice No."];
    $data['proformaInvoice'][] = ["name"=>"Invoice Date"];
    $data['proformaInvoice'][] = ["name"=>"Customer Name"]; 
    // $data['proformaInvoice'][] = ["name"=>"Product Name"]; 
    // $data['proformaInvoice'][] = ["name"=>"Product Amount"]; 
    $data['proformaInvoice'][] = ["name"=>"Bill Amount"]; 
    
    /* feasibility Reason Header */
	$data['feasibilityReason'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['feasibilityReason'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['feasibilityReason'][] = ["name"=>"Type"];
	$data['feasibilityReason'][] = ["name"=>"Feasibility Reason"];

     /* Offers Header */
	$data['offers'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['offers'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['offers'][] = ["name"=>"Offer Date"];
	$data['offers'][] = ["name"=>"Title"];
	$data['offers'][] = ["name"=>"Valid From"];
	$data['offers'][] = ["name"=>"Valid To"];
	$data['offers'][] = ["name"=>"Product"];
	$data['offers'][] = ["name"=>"percentage"];
	$data['offers'][] = ["name"=>"amount"];
	$data['offers'][] = ["name"=>"remark"];

    $masterCheckBox = '<input type="checkbox" name ="masterSelect" id="masterSelect" class="filled-in chk-col-success bulkTags" value=""><label for="masterSelect">ALL</label>';
	$data['printTags'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['printTags'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
	$data['printTags'][] = ["name"=>"No Of Tags."];
	$data['printTags'][] = ["name"=>"Item Image"]; 
	$data['printTags'][] = ["name"=>"Item Name"];
	$data['printTags'][] = ["name"=>"Category"];
	$data['printTags'][] = ["name"=>"Price"];
	$data['printTags'][] = ["name"=>"Stock Qty"];
	
    $data['hsn'][] = ["name"=>"Action","style"=>"width:4%;"];
	$data['hsn'][] = ["name"=>"#","style"=>"width:3%;","textAlign"=>"center"];
    $data['hsn'][] = ["name"=>"HSN Code"];
    $data['hsn'][] = ["name"=>"HSN Type"];
	$data['hsn'][] = ["name"=>"GST %"];

	return tableHeader($data[$page]);
}

/*Change By : avruti @15-3-2022 */
/* Sales Enquiry Table Data */
function getSalesEnquiryData($data){
    $deleteParam = $data->trans_main_id.",'Sales Enquiry'";
    $closeParam = $data->trans_main_id.",'Sales Enquiry'";
    $edit = "";$delete = "";$close = "";$reopen = "";$quotation="";   

    if(empty($data->trans_status)):

        $quotation = '<a href="'.base_url('salesQuotation/createQuotation/'.$data->trans_main_id).'" class="btn btn-info permission-write" datatip="Create Quotation" flow="down"><i class="fa fa-file-alt"></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    else:
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        /*if($data->trans_status == 1):
            $close = '<a href="javascript:void(0)" class="btn btn-dark" onclick="closeEnquiry('.$closeParam.');" datatip="Close Enquiry" flow="down"><i class="ti-close"></i></a>';
        else:
            $reopen = '<a href="javascript:void(0)" class="btn btn-warning" onclick="reopenEnquiry('.$closeParam.');" datatip="Reopen Enquiry" flow="down"><i class="fa fa-retweet"></i></a>';
        endif;*/

    endif;

    $action = getActionButton($quotation.$edit.$delete.$close.$reopen);

    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->status,$data->remark];
}

/*Change By : avruti @15-3-2022 */
/* Sales Quotation Table Data */
function getSalesQuotationData($data){
    $deleteParam = $data->trans_main_id.",'Sales Quotation'";
    $closeParam = $data->trans_main_id.",'Sales Quotation'";
    $confirm = "";$edit = "";$delete = "";$saleOrder ="";$printBtn = '';$revision = ''; $followup="";
    if(empty($data->confirm_by)):

        $confirm = '<a href="javascript:void(0)" class="btn btn-info confirmQuotation permission-write" data-id="'.$data->trans_main_id.'" data-quote_id="'.$data->trans_main_id.'"  data-party="'.$data->party_name.'" data-customer_id="'.$data->party_id.'" data-quote_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" data-quotation_date="'.date("d-m-Y",strtotime($data->trans_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getQuotationItems" data-form_title="Confirm Quotation" datatip="Confirm Quotation" flow="down"><i class="fa fa-check"></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
		
		$followup='<a href="javascript:void(0)" class="btn btn-warning addFolloUp permission-write" data-id="'.$data->trans_main_id.'" data-button="both" data-modal_id="modal-lg" data-function="getFollowUp" data-form_title="Follow Up" datatip="Follow Up" flow="down"><i class="fa fa-list-ul"></i></a>';
		
        $revision = '<a href="'.base_url($data->controller.'/reviseQuotation/'.$data->trans_main_id).'" class="btn btn-primary btn-edit permission-modify" datatip="Revision" flow="down"><i class="fa fa-retweet"></i></a>';

		$delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    else:
        if(empty($data->trans_status)):
            $saleOrder = '<a href="'.base_url('salesOrder/createOrder/'.$data->trans_main_id).'" class="btn btn-info permission-write" datatip="Create Order" flow="down"><i class="fa fa-file-alt"></i></a>';
        endif;
    endif;
	
	$printBtn = '<a class="btn btn-success btn-edit permission-approve" href="'.base_url($data->controller.'/printQuotation/'.$data->trans_main_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    $printRevisionBtn = '<a class="btn btn-facebook btn-edit permission-approve createSalesQuotation"  datatip="View Revised Quatation" data-id="'.$data->trans_main_id.'" data-sq_no="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" flow="down"><i class="fas fa-eye" ></i></a>';

    $action = getActionButton($printBtn.$printRevisionBtn.$confirm.$followup.$revision.$edit.$delete.$saleOrder);
	
    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,(!empty($data->cod_date))?date("d-m-Y",strtotime($data->cod_date)):"",$data->ref_no];
}

/* Sales Order Table Data */
function getSalesOrderData($data){
    $deleteParam = $data->trans_main_id.",'Sales Order'";
    $view = ""; $edit = ""; $delete = ""; $complete = ""; $invoiceCreate = "";$dispatch = ""; $approve='';$invoice = "";$itemList='';
    $closeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'closeSalesOrder', 'title' : 'Close Sales Order', 'fnEdit' : 'closeSalesOrder', 'fnsave' : 'saveCloseSO'}";

    $printBtn = '<a class="btn btn-dribbble btn-edit permission-modify" href="'.base_url($data->controller.'/salesOrder_pdf/'.$data->trans_main_id).'" target="_blank" datatip="Print Sales Order" flow="down"><i class="fas fa-print" ></i></a>';
	
    if(empty($data->trans_status)):
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        $dispatch = '<a href="javascript:void(0)" class="btn btn-primary createDeliveryChallan permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Challan" flow="down"><i class="fa fa-truck" ></i></a>';
        $invoice = '<a href="javascript:void(0)" class="btn btn-primary createSalesInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Create Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';
        $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
        $complete = '<a class="btn btn-info btn-solution permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" onclick="edit('.$closeParam.');"><i class="fa fa-window-close"></i></a>';
    endif;    

    // $action = getActionButton($approve.$printBtn.$complete.$dispatch.$invoice.$edit.$delete);
    $action = getActionButton($approve.$printBtn.$itemList.$complete.$edit.$delete);
    $orderType = "";
    $salesType = "";
	
	$responseData[] = $action;
	$responseData[] = $data->sr_no;
	$responseData[] = getPrefixNumber($data->trans_prefix,$data->trans_no);
    $responseData[] = formatDate($data->trans_date);
    //$responseData[] = $salesType;
    $responseData[] = $data->party_name;    
    $responseData[] = $data->doc_no;
	$responseData[] = $data->ref_no;
    //$responseData[] = $data->item_name;
    //$responseData[] = floatVal($data->qty);
    //$responseData[] = floatVal($data->dispatch_qty);
    //$responseData[] = floatVal($data->pending_qty);
    $responseData[] = formatDate($data->cod_date); 	
    //$responseData[] = $data->order_status_label;
	return $responseData;
}


/* Sales Invoice Table Data */
function getSalesInvoiceData($data){ // Created By Meghavi
    $deleteParam = $data->id.",'Sales Invoice'";
    $itemlist="";
  
	$printExport=""; $printCustom=""; $print=""; $thermalPrint=""; $audit="";$ExcelDownload='';$itemList='';$edit=''; $delete='';
    if($data->audit_status == 0){
        if($data->sales_type == 4){
            $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
        } else if($data->sales_type == 3){
            $printCustom = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Custom Invoice" flow="down" data-id="'.$data->id.'" data-function="custom_invoice_pdf"><i class="fa fa-print"></i></a>';
        } else {
            $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';
            $thermalPrint = '<a href="javascript:void(0)" class="btn btn-warning btn-edit permission-approve invoiceThermalPrint" datatip="Thermal Print" flow="down" data-id="'.$data->id.'"><i class="fa fa-print"></i></a>';
        }
        $audit = '<a href="javascript:void(0)" class="btn btn-facebook auditStatus permission-modify" data-id="'.$data->id.'" data-val="1" data-msg="Audit" datatip="Audit" flow="down" ><i class="fa fa-at"></i></a>';
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
        $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
        $ExcelDownload = '<a href="'.base_url($data->controller . '/createExcel/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Download Excel" flow="down"><i class="fa fa-file-excel"></i></a>';
    
        $ewbPDF = '';$ewbDetailPDF = '';$generateEWB = '';  $cancelEwb = '';
        /* $ewbParam = "{'id' : ".$data->id.",'party_id' : ".$data->party_id.", 'modal_id' : 'modal-xl', 'form_id' : 'generateEwayBill', 'title' : 'E-way Bill For Invoice No. : ".(getPrefixNumber($data->trans_prefix,$data->trans_no))."', 'fnEdit' : 'addEwayBill', 'fnsave' : 'generateEwb', 'fnonclick' : 'generateEwb','syncBtn':1}";

        if(!empty($data->ewb_status)):
            $ewbPDF = '<a href="'.base_url('ebill/ewb_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';

            $ewbDetailPDF = '<a href="'.base_url('ebill/ewb_detail_pdf/'.$data->eway_bill_no).'" target="_blank" datatip="EWB DETAIL PDF" flow="down" class="btn btn-warning"><i class="fa fa-print"></i></a>';

            if($data->ewb_status == 3):
                $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
            else:
                $cancelEwbParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'cancelEwb', 'title' : 'Cancel Eway Bill [ Invoice No. : ".(getPrefixNumber($data->trans_prefix,$data->trans_no))." ]', 'fnEdit' : 'loadCancelEwayBillForm', 'fnsave' : 'cancelEwayBill', 'fnonclick' : 'cancelEwayBill','syncBtn':0,'save_btn_text':'Cancel EWB'}";
                $cancelEwb = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Eway Bill" flow="down" onclick="ebillFrom('.$cancelEwbParam.');"><i class="fa fa-times"></i></a>';
            endif;
        else:
            if(empty($data->eway_bill_no)):
                $generateEWB = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-way Bill" flow="down" onclick="ebillFrom('.$ewbParam.');"><i class="fa fa-truck"></i></a>';
            endif;
        endif; */

        $generateEinv = ""; $einvPdf = "";$cancelInv = "";
        //if($data->cm_id == 2):            
            if(!empty($data->e_inv_status)):
                $einvPdf = '<a href="'.base_url('ebill/einv_pdf/'.$data->e_inv_no).'" target="_blank" datatip="E-Invoice PDF" flow="down" class="btn btn-dark"><i class="fa fa-print"></i></a>';
            else:
                if(empty($data->e_inv_no)):
                    $einvParam = "{'id' : ".$data->id.",'party_id' : ".$data->party_id.", 'modal_id' : 'modal-xl', 'form_id' : 'generateEinv', 'title' : 'E-Invoice For Invoice No. : ".(getPrefixNumber($data->trans_prefix,$data->trans_no))."', 'fnEdit' : 'addEinvoice', 'fnsave' : 'generateEinvoice', 'fnonclick' : 'generateEinvoice','syncBtn':1}";

                    $generateEinv = '<a href="javascript:void(0)" class="btn btn-dark" datatip="E-Invoice" flow="down" onclick="ebillFrom('.$einvParam.');"><i class="icon-Receipt-3"></i></a>';
                endif;
            endif;
        //endif;
            
        if($data->trans_status != 3):
            $cancelInvParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'cancelInv', 'title' : 'Cancel Invoice No. : ".(getPrefixNumber($data->trans_prefix,$data->trans_no))."', 'fnEdit' : 'loadCancelInvForm', 'fnsave' : 'cancelEinvoice', 'fnonclick' : 'cancelEinv','syncBtn':0,'save_btn_text':'Cancel Invoice'}";
            $cancelInv = '<a href="javascript:void(0)" class="btn btn-danger" datatip="Cancel Invoice" flow="down" onclick="ebillFrom('.$cancelInvParam.');"><i class="fa fa-times"></i></a>';
        else:
            $edit="";$delete="";$generateEinv = "";$generateEWB = '';  $cancelEwb = ''; $audit = "";
        endif;        
    }
    
    if(!empty($data->e_inv_no)){$edit = $delete = '';}

    $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';
    $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
   
    $action = getActionButton($audit.$ExcelDownload.$thermalPrint.$print.$itemList.$generateEinv.$einvPdf.$cancelInv.$edit.$delete);

    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->memo_type,$data->net_amount];
}

function getSalesInvoiceData1($data){
    $deleteParam = $data->id.",'Sales Invoice'";
    $itemlist="";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
    $ExcelDownload = '<a href="'.base_url($data->controller . '/createExcel/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Download Excel" flow="down"><i class="fa fa-file-excel"></i></a>';

	$printExport=""; $printCustom=""; $print=""; $thermalPrint="";
    if($data->sales_type == 4){
        $printExport = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Export Invoice" flow="down" data-id="'.$data->id.'" data-function="export_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else if($data->sales_type == 3){
        $printCustom = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Custom Invoice" flow="down" data-id="'.$data->id.'" data-function="custom_invoice_pdf"><i class="fa fa-print"></i></a>';
    } else {
        $print = '<a href="javascript:void(0)" class="btn btn-info btn-edit printInvoice permission-approve" datatip="Print Invoice" flow="down" data-id="'.$data->id.'" data-function="invoice_pdf"><i class="fa fa-print"></i></a>';
        $thermalPrint = '<a href="javascript:void(0)" class="btn btn-warning btn-edit permission-approve invoiceThermalPrint" datatip="Thermal Print" flow="down" data-id="'.$data->id.'"><i class="fa fa-print"></i></a>';
    }
    
    
    $action = getActionButton($ExcelDownload.$thermalPrint.$printCustom.$printExport.$print.$itemList.$edit.$delete);
    if($data->CMID == 1){
        return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,'',$data->net_amount];
    } else {
        return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,'',$data->net_amount];
    }
}

/* Delivery Challan */
function getDeliveryChallanData($data){
    $deleteParam = $data->trans_main_id.",'Delivery Challan'";
    $invoice = "";$edit = "";$delete = "";$itemList="";
    if(empty($data->trans_status)):
        $invoice = '<a href="javascript:void(0)" class="btn btn-info createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    $printBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-edit printInvoice" datatip="Print Invoice" flow="down" data-id="'.$data->trans_main_id.'" data-function="challan_pdf"><i class="fa fa-print"></i></a>';

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($printBtn.$invoice.$itemList.$edit.$delete);

    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->party_name];
}

/* Packing Instruction Table Data*/
function getPackingInstructionData($data){

    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editPacking', 'title' : 'Update Packing Quantity'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    
    $action = getActionButton($editButton);

    return [$action,$data->sr_no,formatDate($data->dispatch_date),$data->item_code,$data->item_name,$data->qty,$data->remark,$data->packing_status_label];
}

/* Proforma Invoice Table Data */
function getProformaInvoiceData($data){
    $edit = ''; $stockButton = '';
    if (abs($data->stock_qty) <= 0) {
        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
        
        $stockButton = '<a href="javascript:void(0)" class="btn btn-info updateStock permission-modify" data-id="'.$data->trans_main_id.'" data-trans_number="'.getPrefixNumber($data->trans_prefix,$data->trans_no).'" datatip="Stock Effect" flow="down"><i class="fa fa-check"></i></a>';
    }    

    $deleteParam = $data->trans_main_id.",'Proforma Invoice'";
    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$print = '<a href="javascript:void(0)" class="btn btn-primary btn-edit printInvoice" datatip="Print Invoice" flow="down" data-id="'.$data->trans_main_id.'"><i class="fa fa-print"></i></a>';
	    
    $action = getActionButton($print.$stockButton.$edit.$delete);

    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),date("d-m-Y",strtotime($data->trans_date)),$data->party_name,$data->inv_amount];
}

/* Product Table Data  */ //Updated By Meghavi 15-03-2022
function getProductData($data){
    $deleteParam = $data->id.",'Product'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'editProduct', 'title' : 'Update Product'}";
    $fgRevisionParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'fgRevision', 'title' : 'Fg Revision', 'fnEdit' : 'getFgRevision', 'fnsave' : 'updateFgRevision'}";

    $fgRevisionButton = '<a class="btn btn-info btn-salary permission-modify" href="javascript:void(0)" datatip="Fg Revision" flow="down" onclick="edit('.$fgRevisionParam.');"><i class="fa fa-list"></i></a>';

    $setProductProcess = '<a href="javascript:void(0)" class="btn btn-info setProductProcess permission-modify" datatip="Set Product Process" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addProductProcess" data-form_title="Set Product Process" flow="down"><i class="fas fa-cogs"></i></a>';

    $viewProductProcess = '<a href="javascript:void(0)" class="btn btn-purple viewItemProcess permission-modify" datatip="View Process" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="viewProductProcess" data-form_title="View Product Process" flow="down"><i class="fa fa-list"></i></a>';

    $printTags = '<a href="javascript:void(0)" class="btn btn-primary printTagsModal permission-modify" datatip="Print Tags" flow="down" data-id="'.$data->id.'" data-function="printTags"><i class="fa fa-tags"></i></a>';

    $productKit = '<a href="javascript:void(0)" class="btn btn-warning productKit permission-modify" datatip="Product BOM" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Product BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$imageUploadParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'imageUpload', 'title' : 'Image Upload', 'fnEdit' : 'getImageUpload', 'fnsave' : 'uploadImage'}";
    $imageUploadBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Image Upload" flow="down" onclick="edit('.$imageUploadParam.');"><i class="fa fa-upload"></i></a>';

	
	$mq = ''; 
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.floatVal($data->qty).' <small>'.$data->unit_name.'</small></a>';
    
    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">OS Stock</button>';
    if(!empty($data->item_image)):
	    $productImg = '<img src="'.base_url('assets/uploads/product/'.$data->item_image).'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
	else:
        $productImg = '<img src="'.base_url('assets/uploads/product/default.png').'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
    endif;
	$action = getActionButton($imageUploadBtn.$printTags.$editButton.$deleteButton);
	$itmName = (!empty($data->item_code)) ? '['.$data->item_code.'] '.$data->item_name : $data->item_name ;
    return [$action,$data->sr_no,$productImg,$data->id,$itmName,$data->category_name,$data->hsn_code,$data->price,$data->wholesale1,$data->wholesale2,$qty,$openingStock]; //($data->part_no,$data->drawing_no,$data->rev_no)
}

/* Tool Cunsumption Table Data*/
function ToolConsumption($data){

    $toolConsumption = '<button type="button" class="btn waves-effect waves-light btn-outline-primary addToolConsumption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addToolConsumption" data-form_title="Add Tool Consumption">Add Tool Consumption</button>';

    return [$data->sr_no,$data->item_code,$toolConsumption];
}

/* Feasibility Reason Data  */
function getFeasibilityReasonData($data){
    
    $deleteParam = $data->id.",'Rejected Reason'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editFeasibilityReason', 'title' : 'Update Rejected Reason'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $type = ($data->type == 3)?"Item Feasibility":"Customer Feedback";
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$type,$data->remark];

}

/* Offers Data  */
function getOffersData($data){
    
    $deleteParam = $data->id.",'Offer'";
    $editButton = '<a href="'.base_url('offers/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->offer_date)),$data->offer_title,date("d-m-Y",strtotime($data->valid_from)),date("d-m-Y",strtotime($data->valid_to)),$data->item_name,$data->percentage,$data->amount,$data->remark];

}

function getHsnData($data){
    $deleteParam = $data->id.",'hsn'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editHsn', 'title' : 'Update HSN'}";
    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->hsn,$data->type,$data->igst];
}

?>