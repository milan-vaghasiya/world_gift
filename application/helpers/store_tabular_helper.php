<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getStoreDtHeader($page){
    /* store header */
    $data['store'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['store'][] = ["name"=>"#","style"=>"width:5%;"]; 
    $data['store'][] = ["name"=>"Store Name"];
    $data['store'][] = ["name"=>"Location"];
    $data['store'][] = ["name"=>"Remark"];

    /* Dispatch Material */
    $data['jobMaterialDispatch'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['jobMaterialDispatch'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Job No.","style"=>"width:9%;"];
    $data['jobMaterialDispatch'][] = ["name"=>"Request Date","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Item Name"];
    $data['jobMaterialDispatch'][] = ["name"=>"Stock Qty","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Requested Qty","textAlign"=>"center"];    
    $data['jobMaterialDispatch'][] = ["name"=>"Issue Qty","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Issue Date","textAlign"=>"center"];
    $data['jobMaterialDispatch'][] = ["name"=>"Pending Qty","textAlign"=>"center"]; 
    $data['jobMaterialDispatch'][] = ["name"=>"Status","textAlign"=>"center"]; 

    $data['toolsIssue'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"Issue Date","textAlign"=>"center"];
    $data['toolsIssue'][] = ["name"=>"Issue Qty","textAlign"=>"center"];

    /* Item Header */
    $data['items'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['items'][] = ["name"=>"Item Code"];
    $data['items'][] = ["name"=>"Item Name"];
    $data['items'][] = ["name"=>"HSN Code"];
    $data['items'][] = ["name"=>"Opening Qty"];
    $data['items'][] = ["name"=>"Stock Qty"];
    $data['items'][] = ["name"=>"Manage Stock"];

    /* Capital Goods Header */
    $data['capitalGoods'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['capitalGoods'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['capitalGoods'][] = ["name"=>"Item Name"];
    $data['capitalGoods'][] = ["name"=>"Category"];
    $data['capitalGoods'][] = ["name"=>"Opening Qty"];
    $data['capitalGoods'][] = ["name"=>"Stock Qty"];
    $data['capitalGoods'][] = ["name"=>"Manage Stock"];

    /* Item Header */
	$data['storeItem'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
    $data['storeItem'][] = ["name"=>"Item Name"];
    $data['storeItem'][] = ["name"=>"Category"];
    $data['storeItem'][] = ["name"=>"Price"];
    $data['storeItem'][] = ["name"=>"Stock Qty."];

    /* LIST OF STOCK VERIFICATION  */
    $data['stockVerification'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['stockVerification'][] = ["name"=>"Item Name"];
    $data['stockVerification'][] = ["name"=>"Physical Stock"];
    $data['stockVerification'][] = ["name"=>"System Stock"];
    $data['stockVerification'][] = ["name"=>"Variation"];
    $data['stockVerification'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];

	/* Stock Journal Header */
    $data['stockJournal'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['stockJournal'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['stockJournal'][] = ["name"=>"Date"];
    $data['stockJournal'][] = ["name"=>"RM Item Name"];
    $data['stockJournal'][] = ["name"=>"RM Qty."];
    $data['stockJournal'][] = ["name"=>"FG Item Name"];
    $data['stockJournal'][] = ["name"=>"FG Qty."];
    $data['stockJournal'][] = ["name"=>"Remark"];

    /* GRN Header */
    $data['grn'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['grn'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['grn'][] = ["name"=>"GRN No."];
	$data['grn'][] = ["name"=>"Challan No."];
    $data['grn'][] = ["name"=>"GRN Date"];
    $data['grn'][] = ["name"=>"Order No."];
    $data['grn'][] = ["name"=>"Supplier/Customer"];
    $data['grn'][] = ["name"=>"Item"];
    $data['grn'][] = ["name"=>"Qty"];
    $data['grn'][] = ["name"=>"UOM"];
    $data['grn'][] = ["name"=>"Heat/Batch No."];
    $data['grn'][] = ["name"=>"Colour Code"];
    
    /* Production Log Header */
    $data['productionLog'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['productionLog'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['productionLog'][] = ["name"=>"Voucher Date"];
    $data['productionLog'][] = ["name"=>"Voucher No"];
    $data['productionLog'][] = ["name"=>"Remark"];
    $data['productionLog'][] = ["name"=>"Row Material Items"];
    $data['productionLog'][] = ["name"=>"Finish Good Items"];
	
	/* Stock Transfer Header */
    $data['stockTransfer'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['stockTransfer'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['stockTransfer'][] = ["name"=>"Voucher No."];
    $data['stockTransfer'][] = ["name"=>"Voucher Date"];
    $data['stockTransfer'][] = ["name"=>"Doc No."];
    $data['stockTransfer'][] = ["name"=>"Doc Date"];
    $data['stockTransfer'][] = ["name"=>"Note"];
    
    return tableHeader($data[$page]);

}

/* Store Table Data */
function getStoreData($data){
    $deleteParam = $data->id.",'Store'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location'}";

    $editButton=''; $deleteButton='';
    if($data->store_type == 0){
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    }
	$action = getActionButton($editButton.$deleteButton);
	
    return [$action,$data->sr_no,$data->store_name,$data->location,$data->remark];
}

// Created By Meghavi
/* Job Material Dispatch Table Data */
function getJobMaterialIssueData($data){
    $deleteParam = $data->id.",'Dispatch'";
    $shortClose = '';
    $consumptionBtn ="";
    $dispatchBtn="";
    $requestParamBtn="";
    $deleteButton="";
    if($data->md_status != 1):
        $shortClose = '<a class="btn btn-instagram btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Close" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-close"></i></a>';

        $dispatchParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'Material Issue'}";

        $consumptionBtn = "{'id' : ".$data->product_id.",'job_card_id' : ".$data->job_card_id.", 'modal_id' : 'modal-md', 'form_id' : 'toolConsumption', 'title' : 'Tool Consumption'}";

        $consumptionBtn = '<a class="btn btn-warning btn-consumption permission-modify" href="javascript:void(0)" datatip="Tool Consumption" flow="down" onclick="consumption('.$consumptionBtn.');"><i class="fas fa-wrench"></i></a>';

        $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Material Issue" flow="down" onclick="dispatch('.$dispatchParam.');"><i class="fas fa-paper-plane"></i></a>';
        
        $requestParamBtn = '<a class="btn btn-info btn-request permission-modify" href="javascript:void(0)" datatip="Purchase Request" flow="down" onclick="request('.$data->id.');"><i class="icon-Check"></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;
    $action = getActionButton($shortClose.$dispatchBtn.$deleteButton);
    //if($data->req_type == 0 && $data->req_status == 0) {$action = getActionButton($requestParamBtn.$dispatchBtn.$deleteButton);}

    $itemName = (!empty($data->dispatch_item_name))?$data->dispatch_item_name:$data->req_item_name;

    $stockQty = (!empty($data->dispatch_item_name))?$data->dispatch_item_stock:$data->req_item_stock;
    
    $unitName = (!empty($data->dis_unit_name))?$data->dis_unit_name:$data->req_unit_name;

    $pendingQty = $data->req_qty - $data->dispatch_qty;
    $pendingQty = ($pendingQty < 0)?0:floatVal(round($pendingQty,3));

    return [$action,$data->sr_no,(!empty($data->job_no))?getPrefixNumber($data->job_prefix,$data->job_no):"General Issue",(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$itemName,floatVal($stockQty)." (".$unitName.") ",$data->req_qty,$data->dispatch_qty,(!empty($data->dispatch_date))?date("d-m-Y",strtotime($data->dispatch_date)):"",$pendingQty,$data->order_status_label];
}

/* Job Tools Dispatch Table Data */
function getToolsIssueData($data){
    $deleteParam = $data->id.",'Dispatch'";
    $dispatchParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-xl', 'form_id' : 'dispatchMaterial', 'title' : 'Tools Issue'}";

    $dispatchBtn = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="dispatch('.$dispatchParam.');"><i class="ti-pencil-alt"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($dispatchBtn.$deleteButton);

    return [$action,$data->sr_no,(!empty($data->issue_date))?date("d-m-Y",strtotime($data->issue_date)):"",$data->total_qty];
}

/* Item Table Data */
function getItemsData($data){
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

/* Capital Goods Table Data */
function getCapitalGoods($data){
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
	
    return [$action,$data->sr_no,$data->item_name,$data->category_name,$data->opening_qty.' ('.$data->unit_name.')',$qty,$openingStock.' '.$updateStockBtn];
}

/* Store Item Table Data */  
function getStoreItemData($data){
    $mq = '';
    if($data->qty < $data->min_qty){ $mq = 'text-danger'; }
	$qty = '<a href="'.base_url('store/itemStockTransfer/'.$data->id).'" class="'.$mq.'">'.$data->qty.'</a>';
	
    return [$data->sr_no,$data->item_name,$data->category_name,$data->price,$qty];
}

/* Process Table Data */
function getStoresData($page,$data){
	
	switch($page)
	{
		case 'purchaseReport':
						return [$data->sr_no,$data->item_code,$data->item_name,$data->hsn_code,printDecimal($data->gst),printDecimal($data->qty)];
						break;
		case 'products':
						break;
	}
	return [];
}

/* Stock Journal Data */
function getStockjournalData($data){
    $deleteParam = $data->id.",'Stock journal'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $action = getActionButton($deleteButton);   
    return [$action,$data->sr_no,formatDate($data->date),$data->rm_name,$data->rm_qty,$data->fg_name,$data->fg_qty,$data->remark];
}

/* GRN Table Data */
function getGRNData($data){
    $deleteParam = $data->grn_id.",'GRN'";$itemList = "";

    $itemList = '<a href="javascript:void(0)" class="btn btn-primary createItemList permission-read" data-id="'.$data->grn_id.'" data-party_name="'.$data->party_name.'" datatip="Item List" flow="down"><i class="fa fa-list" ></i></a>';

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->grn_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	$action = '';$order_no = "";

	if($data->type == 1 && $data->inspected_qty < $data->qty):
		$action = getActionButton($itemList.$edit.$delete);
    endif;

    if($data->type == 2):
        $action = getActionButton($itemList.$edit.$delete);
	endif;

	if(!empty($data->po_no) and !empty($data->po_prefix)):
		$order_no = getPrefixNumber($data->po_prefix,$data->po_no);
	endif;
    return [$action,$data->sr_no,getPrefixNumber($data->grn_prefix,$data->grn_no),$data->challan_no,formatDate($data->grn_date),$order_no,$data->party_name,$data->item_name,$data->qty,$data->unit_name,$data->batch_no,$data->color_code];
}

/* Store Table Data */
function getProductionLogData($data){
    $deleteParam = $data->id.",'Store'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location'}";

    $editButton=''; $deleteButton='';
    $editButton = '<a href="'.base_url('productionLog/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    
    $printBtn = '<a class="btn btn-dribbble btn-edit permission-modify" href="'.base_url('productionLog/printTagsByProduction/'.$data->id).'" target="_blank" datatip="Print Production Data" flow="down"><i class="fa fa-tags" ></i></a>'; 
    $printLogBtn = '<a class="btn btn-success" href="'.base_url('productionLog/productionLog_pdf/'.$data->id).'" target="_blank" datatip="Print Production log" flow="down"><i class="fa fa-print" ></i></a>'; 
    $catalogueBtn = '<a class="btn btn-info" href="'.base_url('productionLog/productionCatalogue/'.$data->id).'" target="_blank" datatip="Print Catalogue" flow="down"><i class="fa fa-print" ></i></a>'; 
	$action = getActionButton($printBtn.$printLogBtn.$catalogueBtn.$editButton.$deleteButton);
	
    return [$action,$data->sr_no,formatDate($data->prd_date),$data->trans_no,$data->remark,$data->total_rm_qty,$data->total_fg_qty];
}

/* Stock Transfer Data */
function getStockTransferData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editStoreLocation', 'title' : 'Update Store Location'}";

    $deleteParam = $data->id.",'Stock'";
    $editButton=''; $deleteButton='';
    $editButton = '<a href="'.base_url('stockTransfer/edit/'.$data->id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);   
    return [$action,$data->sr_no,$data->trans_no,formatDate($data->trans_date),$data->doc_no,formatDate($data->doc_date),$data->remark];
}

function getStockVerificationData($data){
	$variation ='';
	$variation = $data->qty - $data->system_stock;

    $editParam = "{'id' : ".$data->id.",'button':'close','system_stock' : ".$data->system_stock.",'variation' : ".$variation.",'modal_id' : 'modal-md', 'form_id' : 'updateStock', 'title' : 'Update Stock','fnsave' : 'save'}";
    $editButton = '<a class="btn btn-success btn-sm waves-effect waves-light btn-edit" href="javascript:void(0)" datatip="Edit"  onclick="editStock('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    return [$data->sr_no,'['.$data->item_code.']'.$data->item_name,$data->qty,$data->system_stock,$variation,$editButton,];
}
?>