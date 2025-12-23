<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/* get Pagewise Table Header */
function getProductionHeader($page){
    /* Process Header */
    $data['process'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['process'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['process'][] = ["name"=>"Process Name"];
    $data['process'][] = ["name"=>"Department"];
    $data['process'][] = ["name"=>"Remark"];

    /* Job Card Header */
    $data['jobcard'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['jobcard'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
	$data['jobcard'][] = ["name"=>"Job No.","style"=>"width:9%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Delivery Date","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Customer","textAlign"=>"center"];
    //$data['jobcard'][] = ["name"=>"Ch. No.","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobcard'][] = ["name"=>"Remark"];
    $data['jobcard'][] = ["name"=>"Last Activity"];

    /* Material Request */
    $data['materialRequest'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['materialRequest'][] = ["name"=>"#","style"=>"width:5%;"];
    $data['materialRequest'][] = ["name"=>"Job No."];
    $data['materialRequest'][] = ["name"=>"Request Date"];
    $data['materialRequest'][] = ["name"=>"Request Item Name"];
    $data['materialRequest'][] = ["name"=>"Request Item Qty"];

    /* Jobwork Order Header */
    $data['jobWorkOrder'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['jobWorkOrder'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['jobWorkOrder'][] = ["name"=>"Order Date"];
    $data['jobWorkOrder'][] = ["name"=>"Order No."];
    $data['jobWorkOrder'][] = ["name"=>"Vendor Name"];
    $data['jobWorkOrder'][] = ["name"=>"Product"];
    $data['jobWorkOrder'][] = ["name"=>"Qty"];
    $data['jobWorkOrder'][] = ["name"=>"Rate"];
    $data['jobWorkOrder'][] = ["name"=>"Status","textAlign"=>"center"];
    $data['jobWorkOrder'][] = ["name"=>"Process"];
    $data['jobWorkOrder'][] = ["name"=>"Remark"];

    /* Job Work Header */
    $data['jobWork'][] = ["name" => "Action", "style" => "width:5%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "#", "style" => "width:4%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Job No.", "style" => "width:9%;", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Vendor"];
    $data['jobWork'][] = ["name" => "Product", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Process"];
    $data['jobWork'][] = ["name" => "Status", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Out Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "In Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Reject Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Rework Qty", "textAlign" => "center"];
    $data['jobWork'][] = ["name" => "Pending Qty", "textAlign" => "center"]; 

    /* Rejection Header */
    $data['rejectionComments'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['rejectionComments'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['rejectionComments'][] = ["name"=>"Rejection/Rework Comment"];
    $data['rejectionComments'][] = ["name"=>"Type"];

	/* Production Operation Header */
    $data['productionOperation'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['productionOperation'][] = ["name"=>"#","style"=>"width:5%;"];
	$data['productionOperation'][] = ["name"=>"Operation Name"];

    /* Product Option Header */
    $data['productOption'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center","srnoPosition"=>0];
    $data['productOption'][] = ["name"=>"Part Code"];
    $data['productOption'][] = ["name"=>"BOM","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Process","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Cycle Time","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Tool","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Inspection","style"=>"width:10%;","textAlign"=>"center"];
    $data['productOption'][] = ["name"=>"Action","style"=>"width:15%;","textAlign"=>"center"];

    /* Idle Reason Header */
    $data['idleReason'][] = ["name"=>"Action","style"=>"width:5%;"];
	$data['idleReason'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"]; 
    $data['idleReason'][] = ["name"=>"Idle Code","style"=>"width:10%;","textAlign"=>"center"];
    $data['idleReason'][] = ["name"=>"Idle Reason"];

    /* Process Setup Header */
    $data['processSetup'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['processSetup'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['processSetup'][] = ["name"=>"Req. Date"];
    $data['processSetup'][] = ["name"=>"Status"];
    $data['processSetup'][] = ["name"=>"Setup Type"];
    $data['processSetup'][] = ["name"=>"Setter Name"];
    $data['processSetup'][] = ["name"=>"Setup Note"];
    $data['processSetup'][] = ["name"=>"Job No"];
    $data['processSetup'][] = ["name"=>"Part Name"];
    $data['processSetup'][] = ["name"=>"Process Name"];
    $data['processSetup'][] = ["name"=>"Machine"];
    $data['processSetup'][] = ["name"=>"Inspector Name"];
    $data['processSetup'][] = ["name"=>"Start Time"];
    $data['processSetup'][] = ["name"=>"End Time"];
    $data['processSetup'][] = ["name"=>"Duration"];
    $data['processSetup'][] = ["name"=>"Remark"];

    /* Line Inspection Header */
    $masterCheckBox = '<input type="checkbox" id="masterSelect" class="filled-in chk-col-success BulkLineInspection" value=""><label for="masterSelect">ALL</label>';
    $data['lineInspection'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['lineInspection'][] = ["name"=>"#","style"=>"width:10%;","textAlign"=>"center"];
    $data['lineInspection'][] = ["name"=>$masterCheckBox,"style"=>"width:10%;","textAlign"=>"center","orderable"=>"false"];
    $data['lineInspection'][] = ["name"=>"Jobcard No."];
    $data['lineInspection'][] = ["name"=>"Process Name"];
    $data['lineInspection'][] = ["name"=>"Product Code"];
    $data['lineInspection'][] = ["name"=>"Vendor Name"];
    $data['lineInspection'][] = ["name"=>"In Qty."];
    $data['lineInspection'][] = ["name"=>"Out Qty."];
    $data['lineInspection'][] = ["name"=>"Rej. Qty."];
    $data['lineInspection'][] = ["name"=>"ReW. Qty."];
    $data['lineInspection'][] = ["name"=>"Status"];    
    
    /* vendor Challan Header */
    $data['vendorChallan'][] = ["name"=>"Action","style"=>"width:5%;"];
    $data['vendorChallan'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
    $data['vendorChallan'][] = ["name"=>"Challan Date"];
    $data['vendorChallan'][] = ["name"=>"Challan No."];
    $data['vendorChallan'][] = ["name"=>"Vendor"];
    $data['vendorChallan'][] = ["name"=>"Product"];
    $data['vendorChallan'][] = ["name"=>"Qty"];

    /* Process Approval */
    /* $data['processApproval'][] = ["name"=>"#","style"=>"width:5%;","textAlign"=>"center"];
	$data['processApproval'][] = ["name"=>"Job No.","textAlign"=>"center"];
    $data['processApproval'][] = ["name"=>"Job Date"];
    $data['processApproval'][] = ["name"=>"Delivery Date"];
    $data['processApproval'][] = ["name"=>"Job Type"];
    $data['processApproval'][] = ["name"=>"Customer"];
    $data['processApproval'][] = ["name"=>"Challan No."];
    $data['processApproval'][] = ["name"=>"Product"];
    $data['processApproval'][] = ["name"=>"Order Qty."];
    $data['processApproval'][] = ["name"=>"Status"];
    $data['processApproval'][] = ["name"=>"Remark"]; */

    /* Scrap Header */
    $data['scrap'][] = ["name"=>"Action","style"=>"width:5%;","textAlign"=>"center"];
	$data['scrap'][] = ["name"=>"#","style"=>"width:4%;","textAlign"=>"center"];
	$data['scrap'][] = ["name"=>"Job No.","style"=>"width:9%;","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Job Date","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Product","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Order Qty.","textAlign"=>"center"];
    $data['scrap'][] = ["name"=>"Rejection Qty","textAlign"=>"center"];

    return tableHeader($data[$page]);
}

/* Process Table Data */
function getProcessData($data){
    $deleteParam = $data->id.",'Process'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcess', 'title' : 'Update Process'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
	
	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->process_name,$data->dept_name,$data->remark];
}

/* Job Card Table Data */
function getJobcardData($data){
    $deleteParam = $data->id.",'Jobcard'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editJobcard', 'title' : 'Update Jobcard'}";
    $reqParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'requiredTest', 'title' : 'Requirement'}";

    $editButton="";$deleteButton = "";$startOrder = "";$holdOrder = "";$restartOrder = '';$closeOrder="";$reopenOrder = "";$dispatchBtn = ''; $shortClose = '';

    if($data->loginID == 1):
        $shortClose = '<a class="btn btn-instagram btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="6" data-id="'.$data->id.'"><i class="sl-icon-close"></i></a>';
    endif;
    if($data->order_status == 0):
        if(empty($data->md_status)):
            $dispatchBtn = '<a class="btn btn-success btn-request permission-write" href="javascript:void(0)" datatip="Material Request" flow="down" data-id="'.$data->id.'" data-function="materialRequest"><i class="fas fa-paper-plane" ></i></a>';
        else:
            if($data->mr_status ==0):
                $startOrder = '<a class="btn btn-success btn-start materialReceived permission-modify" href="javascript:void(0)" datatip="Material Received" flow="down" data-val="1" data-id="'.$data->id.'"><i class="fa fa-check" ></i></a>';
            else:
                $startOrder = '<a class="btn btn-success btn-start changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Start" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
            endif;
        endif;
    elseif($data->order_status == 2):
        $holdOrder = '<a class="btn btn-danger btn-hold changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Hold" flow="down" data-val="3" data-id="'.$data->id.'"><i class="ti-control-pause" ></i></a>';
    elseif($data->order_status == 3):
        $restartOrder = '<a class="btn btn-success btn-restart changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Restart" flow="down" data-val="2" data-id="'.$data->id.'"><i class="ti-control-play" ></i></a>';
    elseif($data->order_status == 4):
        $shortClose = '';
        $closeOrder = '<a class="btn btn-dark btn-close changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Close" flow="down" data-val="5" data-id="'.$data->id.'"><i class="ti-close" ></i></a>';
    elseif($data->order_status == 5):
        $shortClose = '';
        $reopenOrder = '<a class="btn btn-primary btn-reoprn changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Reopen" flow="down" data-val="4" data-id="'.$data->id.'"><i class="ti-reload" ></i></a>';
    elseif($data->order_status == 6):
        $editButton="";$deleteButton = "";$startOrder = "";$holdOrder = "";$restartOrder = '';$closeOrder="";$reopenOrder = "";$dispatchBtn = '';$shortClose='';
    endif;

    //Regular Order
    if(empty($data->md_status) && empty($data->ref_id) && empty($data->order_status)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';        
    endif;

    //Rework Order
    if(!empty($data->ref_id) && empty($data->order_status)):
        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

        $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    endif;

    $produtionButton = '<a class="btn btn-info btn-view" href="'.base_url('productions/index/'.$data->id).'"  datatip="Production" flow="down"><i class="fa fa-cogs"></i></a>';

    /* $requiredTest = '<a class="btn btn-warning btn-view" href="javascript:void(0)" onclick="requiredTest('.$reqParam.');" datatip="Required Test" flow="down"><i class="fa fa-search"></i></a>'; */

	$jobNo = '<a href="'.base_url($data->controller."/view/".$data->id).'">'.(getPrefixNumber($data->job_prefix,$data->job_no)).'</a>';
	
	// last activity
    $firstdate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
    $seconddate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
    $thirdate = date('Y-m-d', strtotime('-3 day', strtotime(date('Y-m-d'))));
    $lastAdate = date('Y-m-d', strtotime($data->last_activity)); 

    $color='';
    if($lastAdate >= $firstdate) { $color="text-primary"; } 
	elseif($lastAdate == $seconddate) { $color="text-dark"; } 
	else { $color="text-danger"; }

    $last_activity = '<a href="javascript:void(0);" class="'.$color.' viewLastActivity" data-trans_id="'.$data->id.'" data-job_no="'.(getPrefixNumber($data->job_prefix,$data->job_no)).'" datatip="View Last Activity" flow="down"><b>'.$data->last_activity.'</b></a>';

	//$last_activity = '<a href="javascript:void(0);" class="viewLastActivity" data-trans_id="'.$data->trans_id.'" data-job_no="'.(getPrefixNumber($data->job_prefix,$data->job_no)).'" datatip="View Last Activity" flow="down">'.$data->last_activity.'</a>';

    $type = ($data->job_category == 0) ? 'Manufacturing' : 'Jobwork';

    $generateScrape = "";
    if($data->order_status == 5):
        $generateScrapeParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'generateScrape', 'title' : 'Generate Scrape' , 'fnEdit' : 'generateScrape' , 'fnsave' : 'saveScrape' }";

        $generateScrape = '<a class="btn btn-dark btn-edit permission-modify" href="javascript:void(0)" datatip="Generate Scrape" flow="down" onclick="edit('.$generateScrapeParam.');"><i class="icon-Trash-withMen" style="font-size:18px;font-weight: 900;" ></i></a>';
    endif;

    $action = getActionButton($dispatchBtn.$startOrder.$holdOrder.$restartOrder.$closeOrder.$shortClose.$reopenOrder.$generateScrape.$produtionButton.$editButton.$deleteButton);
    return [$action,$data->sr_no,$jobNo,date("d-m-Y",strtotime($data->job_date)),date("d-m-Y",strtotime($data->delivery_date)),$data->party_code,$data->item_code,floatVal($data->qty),$data->order_status_label,$data->remark,$last_activity];
}

/* Material Request Data */
function getMaterialRequest($data){
    $deleteParam = $data->id.",'Request'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'materialRequest', 'title' : 'Material Request'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    $action = getActionButton($editButton.$deleteButton);

    return [$action,$data->sr_no,(!empty($data->job_no))?$data->job_prefix.$data->job_no:"General Request",(!empty($data->req_date))?date("d-m-Y",strtotime($data->req_date)):"",$data->req_item_name,$data->req_qty." ( ".$data->unit_name." )"];
}

/* Jobwork Order Data */
function getJobWorkOrderData($data){
    $deleteParam = $data->id.",'Job Work Order'"; $approve = "";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editJobOrder', 'title' : 'Update Job Work Order'}";

    if(empty($data->is_approve)){
        $approve = '<a href="javascript:void(0)"  class="btn btn-facebook approveJobWorkOrderView permission-approve" data-id="'.$data->id.'" data-val="1" data-msg="Approve" datatip="Approve Job Work Order" flow="down" ><i class="fa fa-check" ></i></a>';
    } else {
        $approve = '<a href="javascript:void(0)"  class="btn btn-facebook approveJobWorkOrder permission-approve" data-id="'.$data->id.'" data-val="2" data-msg="Reject" datatip="Reject Job Work Order" flow="down" ><i class="fa fa-ban" ></i></a>';
    }
    
    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

    //$printBtn = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOrderChallan/'.$data->id).'" target="_blank" datatip="Regular Print" flow="down"><i class="fas fa-print" ></i></a>';
    $printBtnFull = '<a class="btn btn-warning btn-edit" href="'.base_url($data->controller.'/jobworkOrderChallanFull/'.$data->id).'" target="_blank" datatip="Full Page Print" flow="down"><i class="fas fa-print" ></i></a>';
	
	if(empty($data->is_close)){
        $shortClose = '<a class="btn btn-dark btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Short Close" flow="down" data-val="1" data-id="'.$data->id.'"><i class="ti-close"></i></a>';
        $action = getActionButton($approve.$shortClose.$printBtnFull.$editButton.$deleteButton);
    }else{
        $shortClose = '<a class="btn btn-dark btn-shortClose changeOrderStatus permission-modify" href="javascript:void(0)" datatip="Re-open" flow="down" data-val="0" data-id="'.$data->id.'"><i class="ti-loop"></i></a>';
        $action = getActionButton($shortClose);
    }
    
    $productName ="";
    if($data->item_type == 1){
        $productName = $data->item_code;
    }else{
        $productName = $data->item_name;
    }
	
	$qty = ($data->rate_per == 1) ? $data->qty : $data->qty_kg ;
    return [$action,$data->sr_no,formatDate($data->jwo_date),getPrefixNumber($data->jwo_prefix,$data->jwo_no),$data->party_name,$productName,floatVal($qty),sprintf('%0.2f',$data->rate),$data->approve_status,$data->process,$data->remark];
    
}

/* Job Work Table Data */
function getJobWorkData($data){
    $returnBtn=""; $printBtn="";
    //$printBtn = '<a class="btn btn-success btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';

    if(empty($data->accepted_by)):         
        $button = '<a class="btn btn-success permission-write" onclick="acceptJob('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>';        
    else:
        $dataRow = ['product_name'=>$data->item_code,'ref_id'=>$data->id,'product_id'=>$data->product_id,'in_process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'issue_batch_no'=>$data->issue_batch_no,'issue_material_qty'=>$data->issue_material_qty,'material_used_id'=>$data->material_used_id,'minDate'=>$data->minDate];

        $button = "<a class='btn btn-warning getForward permission-modify' href='javascript:void(0)' datatip='Inward' flow='down' data-row='".json_encode($dataRow)."' data-toggle='modal' data-target='#outwardModal'><i class='fas fa-paper-plane' ></i></a>";

        if(!empty($data->pending_qty)):
            $returnParams = ['product_name'=>$data->item_code,'job_trans_id'=>$data->id,'job_approval_id'=>$data->job_approval_id,'product_id'=>$data->product_id,'in_process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'minDate'=>$data->minDate,'job_process_ids'=>$data->job_process_ids,'fnEdit'=>"jobWorkReturn",'fnsave'=>"jobWorkReturnSave",'modal_id'=>"modal-lg",'title'=>"Return",'form_id'=>"jobWorkReturnSave"];
    
            $returnBtn = "<a class='btn btn-info btn-edit ' href='javascript:void(0)' datatip='Return' flow='down' onclick='jobWorkReturn(".json_encode($returnParams).");'><i class='fas fa-reply'></i></a>";
        endif;
    endif;
    $action = getActionButton($returnBtn.$button.$printBtn);
    return [$action,$data->sr_no,getPrefixNumber($data->job_prefix,$data->job_no),$data->party_name,$data->item_code,$data->process_name,$data->status,floatVal($data->in_qty),floatVal($data->out_qty),floatVal($data->rejection_qty),floatVal($data->rework_qty),floatVal($data->pending_qty)];
}

/* Production Opration Data */
function getProductionOperationData($data){
    $deleteParam = $data->id.",'Production Operation'";
    $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProductionOperation', 'title' : 'Update Production Operation'}";

    $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Edit" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';

    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';

	$action = getActionButton($editButton.$deleteButton);
    return [$action,$data->sr_no,$data->operation_name];
}

/* Product Option Data */
function getProductOptionData($data){

	$btn = '<div class="btn-group" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-twitter productKit permission-modify printbtn" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-lg" data-function="addProductKitItems" data-form_title="Create Material BOM" datatip="BOM" flow="down"><i class="fas fa-dolly-flatbed"></i></button>
				
				<button type="button" class="btn btn-info viewItemProcess permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="close" data-modal_id="modal-lg" data-function="viewProductProcess" data-form_title="Set Product Process" datatip="View Process" flow="down"><i class="fa fa-list"></i></button>
				
				<button type="button" class="btn btn-twitter addProductOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addCycleTime" data-fnsave="saveCT" data-form_title="Set Cycle Time" datatip="Cycle Time" flow="down"><i class="fa fa-clock"></i></button>
				
				<button type="button" class="btn btn-info addProductOption permission-modify printbtn" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-button="both" data-modal_id="modal-md" data-function="addToolConsumption" data-fnsave="saveToolConsumption" data-form_title="Set Tool Consumption" datatip="Tool Consumption" flow="left"><i class="fas fa-wrench"></i></button>
			
		        <button type="button" class="btn btn-twitter addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-param_type="0" data-button="both" data-modal_id="modal-md" data-function="getPreInspection" data-form_title="Pre-Dispatch Inspection" datatip="PDI" flow="left"><b>P</b></button>

                <button type="button" class="btn btn-info addInspectionOption permission-modify" data-id="'.$data->id.'" data-product_name="'.$data->item_name.'" data-param_type="1" data-button="both" data-modal_id="modal-md" data-function="getPreInspection" data-form_title="Final Inspection" datatip="Final Inspection" flow="left"><b>F</b></button>
             </div>';

    return [$data->sr_no,$data->item_code,$data->bom,$data->process,$data->cycleTime,$data->tool,$data->inspection,$btn];
}

/* Process Setup Data */
function getProcessSetupData($data){
    $acceptBtn = "";$editButton = "";
    if(empty($data->setup_start_time)):
        $acceptBtn = '<a class="btn btn-success permission-write" onclick="acceptJob('.$data->id.')" href="javascript:void(0)"  datatip="Accept" flow="down"><i class="fa fa-check"></i></a>'; 
    else:
        $editParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-md', 'form_id' : 'editProcessSetup', 'title' : 'Process Setup', 'fnEdit' : 'processSetup'}";

        $editButton = '<a class="btn btn-success btn-edit permission-modify" href="javascript:void(0)" datatip="Finish Setup" flow="down" onclick="edit('.$editParam.');"><i class="ti-pencil-alt" ></i></a>';
    endif;    

    $action = getActionButton($acceptBtn.$editButton);
    return [$action,$data->sr_no,date("d-m-Y",strtotime($data->request_date)),$data->status,$data->setup_type_name,$data->setter_name,$data->setup_note,getPrefixNumber($data->job_prefix,$data->job_no),$data->item_code,$data->process_name,(!empty($data->machine_code) || !empty($data->machine_name))?'[ '.$data->machine_code.' ] '.$data->machine_name:"",$data->inspector_name,(!empty($data->setup_start_time))?date("d-m-Y h:i:s A",strtotime($data->setup_start_time)):"",(!empty($data->setup_end_time))?date("d-m-Y h:i:s A",strtotime($data->setup_end_time)):"",$data->duration,$data->setter_note];
}

/* Line Inspection Data */
function getLineInspectionData($data){
    $btnParam = ['ref_id'=>$data->id,'product_id'=>$data->product_id,'process_id'=>$data->process_id,'job_card_id'=>$data->job_card_id,'product_name'=>$data->product_code,'process_name'=>$data->process_name,'pending_qty'=>$data->pending_qty,'mindate'=>$data->minDate,'modal_id'=>'modal-xxl','form_id'=>'lineInspectionFrom','title'=>'Line Inspection'];

    $button = "<a class='btn btn-warning getForward permission-modify' href='javascript:void(0)' datatip='Forward' flow='down' onclick='lineInspection(".json_encode($btnParam).");'><i class='fas fa-paper-plane' ></i></a>";

    $action = getActionButton($button);
    $selectBox = '<input type="checkbox" name="ref_id[]" id="ref_id_'.$data->sr_no.'" class="filled-in chk-col-success BulkLineInspection" value="'.$data->id.'"><label for="ref_id_'.$data->sr_no.'"></label>';
    if($data->inspected_qty >= $data->in_qty):
        $selectBox = "";
    endif;
    return [$action,$data->sr_no,$selectBox,getPrefixNumber($data->job_prefix,$data->job_no),$data->process_name,$data->product_code,(!empty($data->party_name))?$data->party_name:"In House",$data->in_qty,$data->out_qty,$data->rejection_qty,$data->rework_qty,$data->status];
}

/* Vendor Challan Data */
function getVendorChallanData($data){
    $deleteParam = $data->id.",'Vendor Challan'";
    $deleteButton = '<a class="btn btn-danger btn-delete permission-remove" href="javascript:void(0)" onclick="trash('.$deleteParam.');" datatip="Remove" flow="down"><i class="ti-trash"></i></a>';
    $printBtn = '<a class="btn btn-success btn-edit" href="'.base_url($data->controller.'/jobworkOutChallan/'.$data->id).'" target="_blank" datatip="Print" flow="down"><i class="fas fa-print" ></i></a>';
    
    
    $returnParam = "{'id' : ".$data->id.", 'modal_id' : 'modal-lg', 'form_id' : 'editVendorChallan', 'title' : 'Return Vendor Material', 'fnEdit':'returnVendorMaterial','fnsave':'saveReturnMaterial'}";
    $returnBtn = '<a class="btn btn-info btn-edit permission-modify" href="javascript:void(0)" datatip="Return Vendor Material" flow="down" onclick="edit('.$returnParam.');"><i class="fas fa-reply"></i></a>';

	$action = getActionButton($returnBtn.$printBtn.$deleteButton);
    return [$action,$data->sr_no,formatDate($data->challan_date),getPrefixNumber($data->challan_prefix,$data->challan_no),$data->party_name,$data->item_code,$data->qty];
}

///--------------------
/* Process Approval Table Data */
/* function getProcessApprovalData($data){
    $jobNo = '<a href="'.base_url($data->controller.'/list/'.$data->id).'">'.$data->job_prefix.$data->job_no.'</a>';

    $type = (empty($data->ref_id))?'Regular':'Rework';

    return [$data->sr_no,$jobNo,date("d-m-Y",strtotime($data->job_date)),date("d-m-Y",strtotime($data->delivery_date)),$type,$data->party_code,$data->challan_no,$data->item_code,$data->qty,$data->order_status,$data->remark];
} */

/* Scrap Table Data */
function getScrapData($data){
    
        
            $scrapBtn = '<a class="btn btn-success btn-request permission-write" href="javascript:void(0)" datatip="Scrap" flow="down" data-id="'.$data->id.'" data-function="addScrap"><i class="fas fa-paper-plane" ></i></a>';
     
           


    $action = getActionButton($scrapBtn);
    return [$action,$data->sr_no,$data->job_no,date("d-m-Y",strtotime($data->job_date)),$data->item_code,floatVal($data->qty),$data->total_reject_qty];
}














?>