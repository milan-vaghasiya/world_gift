<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getQualityDtHeader($page)
{	   
	//avruti 14-9-21
	/* Purchase Material Inspection Header */
    $data['materialInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['materialInspection'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['materialInspection'][] = ["name"=>"Inv No."];
    $data['materialInspection'][] = ["name"=>"Inv Date"];
	$data['materialInspection'][] = ["name"=>"Challan No."];
    $data['materialInspection'][] = ["name"=>"Order No."];
    $data['materialInspection'][] = ["name"=>"Supplier/Customer"];
    $data['materialInspection'][] = ["name"=>"Item Name"];
    $data['materialInspection'][] = ["name"=>"Finish Goods"];
    $data['materialInspection'][] = ["name"=>"Received Qty"];
    $data['materialInspection'][] = ["name"=>"Batch/Heat No."];
    $data['materialInspection'][] = ["name"=>"Color Code"];
    $data['materialInspection'][] = ["name"=>"Status"];

    /* Final Inspection Header */
    $data['finalInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['finalInspection'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['finalInspection'][] = ["name"=>"Rejection Type"];
    $data['finalInspection'][] = ["name"=>"Item Name"];
    $data['finalInspection'][] = ["name"=>"Qty."];
    $data['finalInspection'][] = ["name"=>"Pending Qty."];

    
    /* Job Work Inpection Header */
    $data['jobWorkInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['jobWorkInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobWorkInspection'][] = ["name"=>"Date"];
    $data['jobWorkInspection'][] = ["name"=>"Challan No."];
    $data['jobWorkInspection'][] = ["name"=>"Job No."];
    $data['jobWorkInspection'][] = ["name"=>"Vendor"];
    $data['jobWorkInspection'][] = ["name"=>"Part Code"];
    $data['jobWorkInspection'][] = ["name"=>"Charge No."];
    $data['jobWorkInspection'][] = ["name"=>"Process"];
    $data['jobWorkInspection'][] = ["name"=>"OK Qty."];
    $data['jobWorkInspection'][] = ["name"=>"UD Qty."];

	/* RM Inspection Data */
	$data['inspectionParam'][] = ["name"=>"#","style"=>"width:5%;","srnoPosition"=>0];
	$data['inspectionParam'][] = ["name"=>"Part Name"];
	$data['inspectionParam'][] = ["name"=>"Action","style"=>"width:10%;","textAlign"=>"center"];
      
    
	/* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['rejectionComments'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['rejectionComments'][] = ["name"=>"Rejection/Rework Comment"];
    $data['rejectionComments'][] = ["name"=>"Type"];
	
    
    /* Gauge Header */
    $data['gauges'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['gauges'][] = ["name"=>"Thread Size"];
    $data['gauges'][] = ["name"=>"Inst. Code No."];
    $data['gauges'][] = ["name"=>"Make"];
    $data['gauges'][] = ["name"=>"Thread Type"];
    $data['gauges'][] = ["name"=>"Required"];
    $data['gauges'][] = ["name"=>"Frequency"];
    $data['gauges'][] = ["name"=>"Agency"];
    $data['gauges'][] = ["name"=>"Remark"];

	/* Pre Dispatch Inspect Header */
	$data['preDispatchInspect'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['preDispatchInspect'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['preDispatchInspect'][] = ["name"=>"Part Code"];
	$data['preDispatchInspect'][] = ["name"=>"Param. Count"];
    

	/* Instrument Header */
	$data['instrument'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['instrument'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['instrument'][] = ["name"=>"Description of Instrument"];
	$data['instrument'][] = ["name"=>"Inst. Code No."];
	$data['instrument'][] = ["name"=>"Make"];
	$data['instrument'][] = ["name"=>"Range (mm)"];
	$data['instrument'][] = ["name"=>"Least Count"];
	$data['instrument'][] = ["name"=>"Permissible Error"];
	$data['instrument'][] = ["name"=>"Required"];
	$data['instrument'][] = ["name"=>"Frequency"];
	$data['instrument'][] = ["name"=>"Inhouse/Outside"];
	$data['instrument'][] = ["name"=>"Remark"];
   
       
    /* In Challan Header */
    $data['inChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['inChallan'][] = ["name"=>"Challan No."];
    $data['inChallan'][] = ["name"=>"Challan Date"];
    $data['inChallan'][] = ["name"=>"Party Name"];
    $data['inChallan'][] = ["name"=>"Item Name"];
    $data['inChallan'][] = ["name"=>"Qty."];
    $data['inChallan'][] = ["name"=>"Remark"];

    
    /* Out Challan Header */
    $data['outChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['outChallan'][] = ["name"=>"Challan No."];
    $data['outChallan'][] = ["name"=>"Challan Date"];
    $data['outChallan'][] = ["name"=>"Party Name"];
    $data['outChallan'][] = ["name"=>"Item Name"];
    $data['outChallan'][] = ["name"=>"Qty."];
    $data['outChallan'][] = ["name"=>"Remark"];

    /* Assign Inspector Header */
    $data['assignInspector'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['assignInspector'][] = ["name"=>"Req. Date"];
    $data['assignInspector'][] = ["name"=>"Job Card No."];
    $data['assignInspector'][] = ["name"=>"Product Name"];
    $data['assignInspector'][] = ["name"=>"Process Name"];    
    $data['assignInspector'][] = ["name"=>"Machine No."];
    $data['assignInspector'][] = ["name"=>"Setter Name"];
    $data['assignInspector'][] = ["name"=>"Inspector Name"];
    $data['assignInspector'][] = ["name"=>"Status"];
    $data['assignInspector'][] = ["name"=>"Note"];

    /* Setup Inspection Header */
    $data['setupInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['setupInspection'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['setupInspection'][] = ["name"=>"Req. Date"];
    $data['setupInspection'][] = ["name"=>"Status"];
    $data['setupInspection'][] = ["name"=>"Setup Type"];
    $data['setupInspection'][] = ["name"=>"Setter Name"];
    $data['setupInspection'][] = ["name"=>"Setter Note"];
    $data['setupInspection'][] = ["name"=>"Job No"];
    $data['setupInspection'][] = ["name"=>"Part Name"];
    $data['setupInspection'][] = ["name"=>"Process Name"];
    $data['setupInspection'][] = ["name"=>"Machine"];
    $data['setupInspection'][] = ["name"=>"Inspector Name"];
    $data['setupInspection'][] = ["name"=>"Start Date"];
    $data['setupInspection'][] = ["name"=>"End Date"];
    $data['setupInspection'][] = ["name"=>"Duration"];
    $data['setupInspection'][] = ["name"=>"Remark"];
    $data['setupInspection'][] = ["name"=>"Attachment","textAlign"=>"center"];

 

    return tableHeader($data[$page]);
}


/* RM Inspection Data */
function getInspectionParamData($data){
    $btn = '<button type="button" class="btn btn-twitter addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="getPreInspection" data-form_title="Product Inspection" data-srposition="0" datatip="Inspection" flow="left"><i class="fas fa-info"></i></button>';

    return [$data->sr_no,$data->item_name,$btn];
}

function getJobWorkInspectionData($data)
{
    $reportButton = '<a href="'.base_url('jobWorkInspection/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>';
    $pdfButton = '<a href="'.base_url('jobWorkInspection/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $action = getActionButton($reportButton.$pdfButton);
    return [$action,$data->sr_no,formatDate($data->entry_date),$data->challan_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->charge_no,$data->process_name,$data->in_qty,$data->ud_qty];
} 

/*
* Create By : 
* Updated By : NYN @04-11-2021 12:48 AM 
* Note : Reject BTN
*/
/* Purchase Material Inspection Table Data */
function getPurchaseMaterialInspectionData($data){
    $inspection = ''; $approve = '';
    if(!empty($data->paramCount)){
        $inspection = '<a href="javscript:voide(0);" type="button" class="btn btn-success  getInspectedMaterial permission-modify" data-grn_id="'.$data->grn_id.'" data-trans_id="'.$data->id.'" data-grn_prefix="'.$data->grn_prefix.'" data-grn_no="'.$data->grn_no.'" data-grn_date="'.date("d-m-Y",strtotime($data->grn_date)).'" data-item_name="'.$data->item_name.'" data-toggle="modal" data-target="#inspectionModel" datatip="Inspection" flow="down"><i class="fas fa-search"></i></a>';
        if($data->is_approve == 0){
            $approve = '<a href="javascript:void(0)" class="btn btn-facebook approveInspection permission-approve" data-id="'.$data->id.'" data-val="1" data-msg="Approve" datatip="Approve Inspection" flow="down" ><i class="fa fa-check" ></i></a>';
        } else {
            $approve = '<a href="javascript:void(0)" class="btn btn-dark rejectInspection permission-approve" data-id="'.$data->id.'" data-val="2" data-msg="Reject" datatip="Reject" flow="down" ><i class="ti-close" ></i></a>';
        } 
    }   
    
    $reportButton = '<a href="'.base_url('grn/inInspection/'.$data->id).'" type="button" class="btn btn-info " datatip="Incoming Inspection Report" flow="down"><i class="fa fa-file-alt"></i></a>';
    $pdfButton = '<a href="'.base_url('grn/inInspection_pdf/'.$data->id).'" type="button" class="btn btn-warning " datatip="Incoming Inspection Report Pdf" flow="down" target="_blank"><i class="fas fa-print"></i></a>';
    $order_no = "";
	
	if(!empty($data->po_no) and !empty($data->po_prefix)):
		$order_no = getPrefixNumber($data->po_prefix,$data->po_no);
	endif;
    
	$action = getActionButton($inspection.$reportButton.$approve.$pdfButton);
    return [$action,$data->sr_no,getPrefixNumber($data->grn_prefix,$data->grn_no),date("d-m-Y",strtotime($data->grn_date)),$data->challan_no,$order_no,$data->party_name,$data->item_name,$data->product_code,$data->qty,$data->batch_no,$data->color_code, $data->status_label];
}

/* get PreDispatch Inspect Data */
function getPreDispatchInspectData($data){
    $deleteParam = $data->id.",'PreDispatch Inspection'";
    $editButton = '<a href="'.base_url($data->controller.'/edit/'.$data->id).'" class="btn btn-success btn-edit permision-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->item_code,$data->param_count];
}

function getOutChallanData($data){
    $deleteParam = $data->trans_main_id.",'Challan'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $returnBtn = "";
    if($data->is_returnable == 1):
        $returnParams = ['item_name'=>htmlentities($data->item_name),'item_id'=>$data->item_id,'location_id'=>$data->location_id,'batch_no'=>$data->batch_no,'ref_no'=>getPrefixNumber($data->challan_prefix,$data->challan_no),'ref_id'=>$data->id,'pending_qty'=>($data->qty - $data->return_qty)];
        $returnBtn = "<a href='javascript:void(0)' class='btn btn-info returnItem permission-modify' datatip='Receive' flow='down' data-row='".json_encode($returnParams)."' ><i class='fas fa-reply'></i></a>";
    endif;

    $action = getActionButton($returnBtn.$edit.$delete);
    return [$action,$data->sr_no,getPrefixNumber($data->challan_prefix,$data->challan_no),formatDate($data->challan_date),$data->party_name,$data->item_name,$data->qty,$data->item_remark];
}

/* Get In Challan Data */
function getInChallanData($data){
    $deleteParam = $data->trans_main_id.",'Challan'";

    $edit = '<a href="'.base_url($data->controller.'/edit/'.$data->trans_main_id).'" class="btn btn-success btn-edit permission-modify" datatip="Edit" flow="down"><i class="fa fa-edit"></i></a>';

    $delete = '<a href="javascript:void(0)" class="btn btn-danger btn-delete permission-remove" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $returnBtn = "";
    if($data->is_returnable == 1):
        $returnParams = ['item_name'=>htmlentities($data->item_name),'item_id'=>$data->item_id,'location_id'=>$data->location_id,'batch_no'=>$data->batch_no,'ref_no'=>$data->doc_no,'ref_id'=>$data->id,'pending_qty'=>($data->qty - $data->return_qty)];
        $returnBtn = "<a href='javascript:void(0)' class='btn btn-info returnItem permission-modify' datatip='Return' flow='down' data-row='".json_encode($returnParams)."' ><i class='fas fa-share'></i></a>";
    endif;

    $action = getActionButton($returnBtn.$edit.$delete);
    return [$action,$data->sr_no,$data->doc_no,formatDate($data->challan_date),$data->party_name,$data->item_name,$data->qty,$data->item_remark];
}

/* Instrument Data */
function getInstrumentData($data){
    $deleteParam = $data->id.",'Instrument'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editInstrument', 'title' : 'Update Instrument', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->item_name,$data->item_code,$data->make_brand,$data->instrument_range,$data->least_count,$data->permissible_error,$data->cal_required,$data->cal_freq,$data->cal_agency,$data->description];
} 

/* Gauge Data */
function getGaugeData($data){
    $deleteParam = $data->id.",'Gauge'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editGauge', 'title' : 'Update Gauge', 'fnsave' : 'save'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);    

    return [$action,$data->sr_no,$data->size,$data->item_code,$data->make_brand,$data->thread_type,$data->cal_required,$data->cal_freq,$data->cal_agency,$data->description];
}

function getFinalInspectionData($data){
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'finalInspection', 'title' : 'Final Inspection', 'product_name': '".trimQuotes($data->item_name)."' , 'pending_qty' : '".$data->pending_qty."','rejection_type_id': '".$data->rejection_type_id."', 'product_id': '".$data->product_id."', 'job_card_id' : '".$data->job_card_id."', 'job_inward_id' : '".$data->job_inward_id."', 'operator_id':'".$data->operator_id."', 'machine_id' : '".$data->machine_id."',  'button':'close'}";

    $edParam = [
        'id' => $data->id, 'modal_id' => 'modal-lg', 'form_id' => 'finalInspection', 'title' => 'Final Inspection', 'product_name'=> $data->item_name , 'pending_qty' => $data->pending_qty,'rejection_type_id'=> $data->rejection_type_id, 'product_id'=> $data->product_id, 'job_card_id' => $data->job_card_id, 'job_inward_id' => $data->job_inward_id, 'operator_id'=>$data->operator_id, 'machine_id' => $data->machine_id,  'button'=>'close'        
    ];

    $editButton = "<a class='btn btn-success btn-edit permission-modify' href='javascript:void(0)' datatip='Edit' flow='down' onclick='inspection(".json_encode($edParam).");'><i class='ti-pencil-alt' ></i></a>";

    $action = getActionButton($editButton);
    return [$action,$data->sr_no,(!empty($data->process_name))?$data->process_name:"Material Fault",$data->item_name,$data->qty,$data->pending_qty];
}

/* Rejection Comment Table Data */
function getRejectionCommentData($data){
    if($data->type == 1 || $data->type == 4):
        $rejection_type = ($data->type == 1 ? "Rejection": ($data->type == 4 ? "Rework":"Idle reason"));

        $deleteParam = $data->id.",".($data->type == 1 ? "Rejection": ($data->type == 4 ? "Rework":"Idle reason"));
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Rejection/Rework Comment'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->remark,$rejection_type];
    elseif($data->type == 2):
        $deleteParam = $data->id.",'Idle Reason'";
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editRejectionComment', 'title' : 'Update Idle Reason'}";
    
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
   
	    $action = getActionButton($editButton.$deleteButton);
        return [$action,$data->sr_no,$data->code,$data->remark];
    endif;
}


/* Assign Inspector Data */
function getAssignInspectorData($data){
    $editButton = "";
    if($data->status != 3):
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editAssignInspector', 'title' : 'Assign Inspector', 'fnEdit' : 'assignInspector'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Assign Inspector" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;

    $action = getActionButton($editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,$data->machine_no,$data->setter_name,$data->inspector_name,$data->assign_status,$data->remark];
}

/* Setup Inspector Data */
function getSetupInspectionData($data){
    $editButton = "";$attachmentLink = "";$acceptInspection = "";

    if(!empty($data->inspection_start_date)):
        if(!empty($data->setup_end_time) && !empty($data->qci_id)):
            $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editSetupInspection', 'title' : 'Setup Inspection', 'fnEdit' : 'setupInspection'}";

            $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Setup Inspection" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
        endif;
        
        if(!empty($data->attachment)):
            $attachmentLink = '<a href="'.base_url('assets/uploads/setup_ins_report/'.$data->attachment).'" class="btn btn-outline-info waves-effect waves-light"><i class="fa fa-arrow-down"> Download</a>';
        endif;
    else:
        if(!empty($data->qci_id)):
            $acceptInspection = '<a class="btn btn-success btn-start permission-modify" href="javascript:void(0)" datatip="Accept Inspection" flow="down" onclick="acceptInspection('.$data->id.');"><i class="fas fa-check" ></i></a>';
        endif;
    endif;

    $action = getActionButton($acceptInspection.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setter_note,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->inspection_start_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_start_date)):"",(!empty($data->inspection_date))?date("d-m-Y h:i:s A",strtotime($data->inspection_date)):"",$data->duration,$data->qci_note,$attachmentLink];
}



?>