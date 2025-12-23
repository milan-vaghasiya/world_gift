<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getPurchaseDtHeader($page){
    
    /* Item Category Header */ //Updated By Meghavi 15-03-2022
    $data['itemCategory'][] = ["name" => "Action", "style" => "width:5%;"];
    $data['itemCategory'][] = ["name" => "#", "style" => "width:5%;"];
    $data['itemCategory'][] = ["name" => "Category Name"];
    // $data['itemCategory'][] = ["name" => "Category Type"];
    $data['itemCategory'][] = ["name" => "Remark"];

    /* Party Header */
    $data['parties'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['parties'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
	$data['parties'][] = ["name"=>"Party Name"];
    $data['parties'][] = ["name"=>"Category"];
	$data['parties'][] = ["name"=>"Contact Person"];
    $data['parties'][] = ["name"=>"Contact No."];
    $data['parties'][] = ["name"=>"Party Code"];
	
	/* Item Header */
    $data['items'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"Item Code"];
    $data['items'][] = ["name"=>"Item Name"];
    $data['items'][] = ["name"=>"HSN Code"];
    $data['items'][] = ["name"=>"Opening Qty"];
    $data['items'][] = ["name"=>"Stock Qty"];
    $data['items'][] = ["name"=>"Manage Stock"];
	
	/* Purchase Request Header */
    $data['purchaseRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseRequest'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseRequest'][] = ["name"=>"Job No."];
    $data['purchaseRequest'][] = ["name"=>"Material Type"];
    $data['purchaseRequest'][] = ["name"=>"Request Date"];
    $data['purchaseRequest'][] = ["name"=>"Request Item Name"];
    $data['purchaseRequest'][] = ["name"=>"Request Item Qty"];    
    $data['purchaseRequest'][] = ["name"=>"Status"];

    
	/* Purchase Enquiry Header */
    $data['purchaseEnquiry'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseEnquiry'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseEnquiry'][] = ["name"=>"Enquiry No."];
    $data['purchaseEnquiry'][] = ["name"=>"Enquiry Date"];
    $data['purchaseEnquiry'][] = ["name"=>"Supplier Name"];
    //$data['purchaseEnquiry'][] = ["name"=>"Item Description"];
    //$data['purchaseEnquiry'][] = ["name"=>"Qty"];
    $data['purchaseEnquiry'][] = ["name"=>"Approved Price"];
    $data['purchaseEnquiry'][] = ["name"=>"Approved Date"];
    $data['purchaseEnquiry'][] = ["name"=>"Status"];
    $data['purchaseEnquiry'][] = ["name"=>"Remark"];
    
	/* Purchase Order Header */
    $data['purchaseOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseOrder'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseOrder'][] = ["name"=>"Order No."];
    $data['purchaseOrder'][] = ["name"=>"Order Date"];
    $data['purchaseOrder'][] = ["name"=>"Supplier"];
    //$data['purchaseOrder'][] = ["name"=>"Item Name"];
    //$data['purchaseOrder'][] = ["name"=>"Rate"];
    //$data['purchaseOrder'][] = ["name"=>"Order Qty"];
    //$data['purchaseOrder'][] = ["name"=>"Received Qty"];
    //$data['purchaseOrder'][] = ["name"=>"Pending Qty"];
    $data['purchaseOrder'][] = ["name"=>"Delivery Date"];
    //$data['purchaseOrder'][] = ["name"=>"Status","textAlign"=>"center"]; 
    
    /* Purchase Invoice Header */
    $data['purchaseInvoice'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['purchaseInvoice'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['purchaseInvoice'][] = ["name"=>"Inv No."];
	$data['purchaseInvoice'][] = ["name"=>"Ref. No."];
    $data['purchaseInvoice'][] = ["name"=>"Inv Date"];
    $data['purchaseInvoice'][] = ["name"=>"Supplier Name"];
    $data['purchaseInvoice'][] = ["name"=>"Amount"];

     /* Item Family Header */
     $data['familyGroup'][] = ["name" => "Action", "style" => "width:5%;"];
     $data['familyGroup'][] = ["name" => "#", "style" => "width:5%;"];
     $data['familyGroup'][] = ["name" => "Family Name"];
     $data['familyGroup'][] = ["name" => "Remark"];
	
    return tableHeader($data[$page]);
}

/* Item Category Table Data */ //Updated By Meghavi 15-03-2022
function getItemCategoryData($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editItem', 'title' : 'Update Item'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->category_name,$data->remark];//,$data->group_name
}

/* Party Table Data */
/**
 * Updated BY Mansee 25-12-2021 Line No.120-123
 * Updated BY Avruti & meghavi 21-03-2022 
 */
function getPartyData($data){

    // $title = ($data->party_category == 1 ? "Customer": ($data->party_category == 2 ? "Vendor":"Supplier"));
    $deleteParam = $data->id.",'Party'";
    $editParam = "{'id' : ".$data->id.", 'party_category': ".$data->party_category.", 'modal_id' : 'modal-xl', 'form_id' : 'editParty', 'title' : 'Update Party'}";
    $approvalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'partyApproval', 'title' : 'Party Approval', 'fnEdit' : 'partyApproval', 'fnsave' : 'savePartyApproval'}";

    $approvalButton = '<a class="btn btn-info btn-approval permission-approve" href="javascript:void(0)" datatip="Party Approval" flow="down" onclick="edit('.$approvalParam.');"><i class="fa fa-check" ></i></a>';

    $personalParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'personalDetail', 'title' : 'Personal Detail : ".$data->party_name."', 'fnEdit' : 'getPersonalDetail', 'fnsave' : 'savePersonalDetail'}";
    $personalButton = '<a class="btn btn-danger btn-contact permission-modify" href="javascript:void(0)" datatip="Personal Detail" flow="down" onclick="edit('.$personalParam.');"><i class="ti-id-badge"></i></a>';

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $gstJsonBtn="";
    if($data->party_category == 1):
        $contactParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg','button' : 'close', 'form_id' : 'gstDetail', 'title' : 'GST Detail', 'fnEdit' : 'getGstDetail', 'fnsave' : 'saveContact'}";

        $gstJsonBtn = '<a class="btn btn-warning btn-contact permission-modify" href="javascript:void(0)" datatip="GST Detail" flow="down" onclick="edit('.$contactParam.');"><i class="fa fa-address-book"></i></a>';
    endif;
    $action = getActionButton($personalButton.$gstJsonBtn.$approvalButton.$editButton.$deleteButton);

    $category = ($data->party_category == 1?"Customer":($data->party_category == 3?"Supplier":"Both")); 
    $responseData = [$action,$data->sr_no,$data->party_name,$category,$data->contact_person,$data->party_phone,$data->party_code];
    return $responseData;
}


/* Item Table Data */
function getItemData($data){
    $deleteParam = $data->id.",'Item'";
    $editParam = "{'id' : ".$data->id.",'item_type' :".$data->item_type.", 'modal_id' : 'modal-lg', 'form_id' : 'editItem', 'title' : 'Update Item', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    $updateStockBtn = "";
    /* $updateStockBtn = ($data->rm_type == 0)?'<button type="button" class="btn waves-effect waves-light btn-outline-warning itemStockUpdate permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addStockTrans" data-form_title="Update Stock">Update Stock</button>':''; */

	$mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('reports/productReport/itemWiseStock/'.$data->id).'" class="'.$mq.'">'.$data->qty.' ('.$data->unit_name.')</a>';

    $openingStock = '<button type="button" class="btn waves-effect waves-light btn-outline-primary itemOpeningStock permission-modify" data-id="'.$data->id.'" data-item_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="addOpeningStock" data-form_title="Opening Stock">Opening Stock</button>';
	
    return [$action,$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,$data->opening_qty.' ('.$data->unit_name.')',$qty,$openingStock.' '.$updateStockBtn];
}

/* Purchase Request Data  */
function getPurchaseRequestData($data){
    $purchaseOrder = $purchaseEnq =""; $approvereq=""; $closereq="";

    if($data->order_status == 0){
        $approvereq = '<a href="javascript:void(0)" class="btn btn-facebook approvePreq permission-modify" data-id="'.$data->id.'" data-val="2" data-msg="Approve" datatip="Approve Purchase Request" flow="down" ><i class="fa fa-check"></i></a>';
        $closereq = '<a href="javascript:void(0)" class="btn btn-dark closePreq permission-modify" data-id="'.$data->id.'" data-val="3" data-msg="Close" datatip="Close Purchase Request" flow="down" ><i class="ti-close"></i></a>';
    } elseif($data->order_status == 2){
        $purchaseOrder = '<a href="'.base_url('purchaseOrder/addPOFromRequest/'.$data->id).'" class="btn btn-warning btn-inv permission-write" datatip="Purchase Order" flow="down" ><i class="ti-file"></i></a>';
        $purchaseEnq = '<a href="'.base_url('purchaseEnquiry/addEnqFromRequest/'.$data->id).'" class="btn btn-success btn-enq permission-write" datatip="Purchase Enquiry" flow="down" ><i class="fa fa-question-circle"></i></a>';
        $closereq = '<a href="javascript:void(0)" class="btn btn-dark closePreq permission-modify" data-id="'.$data->id.'" data-val="3" data-msg="Close" datatip="Close Purchase Request" flow="down" ><i class="ti-close"></i></a>';
    }

    $action = getActionButton($approvereq.$closereq.$purchaseOrder.$purchaseEnq);

    //$mType = ($data->material_type == 0 ? "Consumable":"Raw Material");
    return [$action,$data->sr_no,(!empty($data->job_no))?$data->job_prefix.$data->job_no:"General Issue",$data->material_type,(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->req_item_name,$data->req_qty,$data->order_status_label];
}

/* Purchase Enquiry Data */
function getPurchaseEnquiryData($data){
    $deleteParam = $data->ref_id.",'Purchase Enquiry'";
    $closeParam = $data->ref_id.",'Purchase Enquiry'";
    $enqComplete = "";$edit = "";$delete = "";$close = "";$reopen = "";
    if(empty($data->confirm_status)):
        $enqComplete = '<a href="javascript:void(0)" class="btn btn-info btn-complete enquiryConfirmed permission-modify" data-id="'.$data->ref_id.'" data-party="'.$data->supplier_name.'" data-enqno="'.$data->enq_prefix.$data->enq_no.'" data-enqdate="'.date("d-m-Y",strtotime($data->enq_date)).'" data-button="both" data-modal_id="modal-lg" data-function="getEnquiryData" data-form_title="Purchase Enquiry Approve" datatip="Approve  Enquiry" flow="down"><i class="fa fa-check"></i></a>';

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->ref_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    else:
        if(empty($data->enq_status)):
            $edit = '<a href="'.base_url('purchaseOrder/createOrder/'.$data->ref_id).'" class="btn btn-info btn-edit permission-write" datatip="Create Order" flow="down"><i class="ti-file"></i></a>';
        endif;
    endif;

    if(empty($data->enq_ref_date)):
        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" data-tooltip="tooltip" data-placement="bottom" data-original-title="Remove" flow="down"><i class="ti-trash"></i></a>';
    /* else:
        if(empty($data->enq_status)):
            $close = '<a href="javascript:void(0)" class="btn btn-info" onclick="closeEnquiry('.$closeParam.');" datatip="Close Enquiry" flow="down"><i class="ti-close"></i></a>';
        else:
            $reopen = '<a href="javascript:void(0)" class="btn btn-info" onclick="reopenEnquiry('.$closeParam.');" datatip="Reopen Enquiry" flow="down"><i class="fa fa-retweet"></i></a>';
        endif; */
    endif;

    $cnDate = (!empty($data->enq_ref_date))?date("d-m-Y",strtotime($data->enq_ref_date)):"";

    $action = getActionButton($enqComplete.$edit.$delete);//.$close.$reopen);
    return [$action,$data->sr_no,$data->enq_prefix.$data->enq_no,date("d-m-Y",strtotime($data->enq_date)),$data->supplier_name,$data->confirm_rate, $cnDate,$data->status,$data->item_remark];
}

/* Purchase Order Table Data */
function getPurchaseOrderData($data){
    $deleteParam = $data->order_id.",'Purchase Order'";
    $grn = "";$edit = "";$delete = "";$approve=""; $shortClose = "";
    if($data->order_status == 0):
        $shortClose = '<a href="javascript:void(0)" class="btn btn-dark closePOrder permission-modify" data-id="'.$data->order_id.'" data-val="2" data-msg="Short Close" datatip="Short Close" flow="down" ><i class="ti-close" ></i></a>';
        
            //$approve = '<a href="javascript:void(0)" onclick="openView('.$data->order_id.')" class="btn btn-facebook permission-approve" data-id="'.$data->order_id.'" data-val="1" data-msg="Approve" datatip="Approve Order" flow="down" ><i class="fa fa-check" ></i></a>';
            $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->order_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
            $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
        
        
            //$approve = '<a href="javascript:void(0)" class="btn btn-facebook approvePOrder permission-approve" data-id="'.$data->order_id.'" data-val="0" data-msg="Reject" datatip="Reject Order" flow="down" ><i class="fa fa-window-close" ></i></a>';
            //$grn = '<a href="javascript:void(0)" class="btn btn-info btn-inv createGrn permission-write" datatip="Create GRN" flow="down" data-party_id="'.$data->party_id.'" data-party_name="'.$data->party_name.'"><i class="ti-file"></i></a>';
        
    endif;
	$itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->order_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';
	$printBtn = '<a class="btn btn-warning btn-edit permission-read" href="'.base_url($data->controller.'/printPO/'.$data->order_id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	$action = getActionButton($itemList.$shortClose.$printBtn.$edit.$delete);
		
    return [$action,$data->sr_no,getPrefixNumber($data->po_prefix,$data->po_no),formatDate($data->po_date),$data->party_name,formatDate($data->delivery_date)];
}

/* Purchase Invoice Data */
function getPurchaseInvoiceData($data){
    $deleteParam = $data->trans_main_id.",'Invoice'";$itemList = "";$printBtn = "";
    if($data->CMID == 1){ $printBtn = '<a class="btn btn-dribbble btn-edit permission-approve" href="'.base_url('products/printTagsByPinv/'.$data->trans_main_id).'" target="_blank" datatip="Print Invoice Data" flow="down"><i class="fa fa-tags" ></i></a>'; }
    $editButton = '';$deleteButton = '';
    //if(empty($data->ref_id) && $data->trans_status != 99):
        $editButton = '<a class="btn btn-success btn-edit permission-modify"  datatip="Edit" flow="down" href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    //endif;

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->trans_main_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $action = getActionButton($printBtn.$itemList.$editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->doc_no,$data->ref_no,formatDate($data->trans_date),$data->party_name,$data->net_amount];
}
/* Item Family Table Data */
function getFamilyGroupData($data){
    $deleteParam = $data->id.",'Family Group'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editfamilyGroup', 'title' : 'Update familyGroup'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->family_name,$data->remark];
}
?>