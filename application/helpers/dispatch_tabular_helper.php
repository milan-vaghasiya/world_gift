<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getDispatchDtHeader($page)
{	   
    	   
    /* packing Header */
    $data['packing'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['packing'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['packing'][] = ["name"=>"Dispatch Date"];
    $data['packing'][] = ["name"=>"Item Code"];
    $data['packing'][] = ["name"=>"Item Name"];
    $data['packing'][] = ["name"=>"Qty."];
    $data['packing'][] = ["name"=>"Packed Qty."];
    $data['packing'][] = ["name"=>"Packing Date"];
    $data['packing'][] = ["name"=>"Remark"];
    $data['packing'][] = ["name"=>"Status","textAlign"=>"center"];

    /* packing bom Header */
    $data['packingBom'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['packingBom'][] = ["name"=>"Product"];
    $data['packingBom'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];
	

     /* Delivery Challan Header */
    $data['deliveryChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['deliveryChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['deliveryChallan'][] = ["name"=>"Challan. No.","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"DC. Date","textAlign"=>"center"];
    $data['deliveryChallan'][] = ["name"=>"Customer Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Product Name"]; 
    $data['deliveryChallan'][] = ["name"=>"Dispatch Qty.","textAlign"=>"center"]; 

    return tableHeader($data[$page]);
}

function getPackingData($data)
{
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editPacking', 'title' : 'Update Packing Quantity', 'fnEdit' : 'editPacking', 'fnsave' : 'savePacking'}";
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="fa fa-check" ></i></a>';
    
    $selfParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editSelfPacking', 'title' : 'Update Self Packing', 'fnEdit' : 'editPacking', 'fnsave' : 'saveSelfPacking'}";
    $selfButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$selfParam.');"><i class="fa fa-check" ></i></a>';

    if(!empty($data->ref_id))
    {
        $action = getActionButton($editButton);
        $dispatchDate = formatDate($data->dispatch_date);
    } else 
    {
        $action = getActionButton($selfButton);
        $dispatchDate = "Self Stock";
    } if(!empty($data->packing_date)){ $packingDate = formatDate($data->packing_date); } else { $packingDate = ''; }
    return [$action,$data->sr_no,$dispatchDate,$data->item_code,$data->item_name,floatVal($data->qty),floatVal($data->packing_qty),$packingDate,$data->remark,$data->packing_status_label];
}

function getPackingBomData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editPackingBom', 'title' : 'Update Packing BOM'}";

    $btn = '<div class="btn-group" role="group" aria-label="Basic example">
                <a class="btn btn-success btn-sm btn-edit permission-modify" href="javascript:void(0)" datatip="BOM" flow="down" onclick="edit('.$editParam.');"><i class="fas fa-dolly-flatbed"></i></a>
            </div>';

    return [$data->sr_no,$data->item_code,$btn];
}

/* Delivery Challan */
function getDeliveryChallansData($data){
    $deleteParam = $data->trans_main_id.",'Delivery Challan'";
    $invoice = "";$edit = "";$delete = "";
    if(empty($data->trans_status)):
        $invoice = '<a href="javascript:void(0)" class="btn btn-primary createInvoice permission-write" data-id="'.$data->party_id.'" data-party_name="'.$data->party_name.'" datatip="Invoice" flow="down"><i class="fa fa-file-alt" ></i></a>';    

        $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

        $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;	

    $action = getActionButton($invoice.$edit.$delete);

    return [$action,$data->sr_no,getPrefixNumber($data->trans_prefix,$data->trans_no),formatDate($data->trans_date),$data->party_name,$data->item_name,floatVal($data->qty)];
}


?>