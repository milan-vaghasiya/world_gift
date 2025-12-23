<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Products extends MY_Controller
{
    private $indexPage = "product/index";
    private $productForm = "product/form";
    private $productProcessForm = "product/product_process";
    private $viewProductProcess = "product/view_product_process";
    private $productKitItem = "product/product_kit";
    private $fgRevision = "product/fg_revisions";
    private $tagIndexPage = "product/tag_index";
    private $indexTag = "product/index_tag";
    private $fg_item_list = "product/fg_item_list";
    private $incentivePage = "product/index_incentive";
    private $image_upload = "product/image_upload";
    
    private $automotiveArray = ["1"=>'Yes',"2"=>"No"];
    private $gstPercentage = Array(["rate"=>0,"val"=>'NIL'],["rate"=>0.25,"val"=>'0.25%'],["rate"=>3,"val"=>'3%'],["rate"=>5,"val"=>'5%'],["rate"=>12,"val"=>'12%'],["rate"=>18,"val"=>'18%'],["rate"=>28,"val"=>'28%']);
	
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Products";
		$this->data['headData']->controller = "products";
		$this->data['headData']->pageUrl = "products";
	}

    public function index(){
        $this->data['tableHeader'] = getSalesDtHeader($this->data['headData']->controller);
        $this->data['categoryList'] = $this->item->getCategoryList('1');
        $this->load->view($this->indexPage,$this->data);
    }

    public function getDTRows(){        
        $result = $this->item->getDTRows($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $itmStock = $this->store->getLocationWiseItemStock($row->id,$this->RTD_STORE->id);
            $row->qty = 0;
            if(!empty($itmStock->qty)){$row->qty = $itmStock->qty;}
            $sendData[] = getProductData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProduct(){
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['gstPercentage'] = $this->gstPercentage;
        $this->data['processData'] = array();//$this->process->getProcessList();
		$this->data['materialGrades'] = array();//explode(',', $this->item->getMasterOptions()->material_grade);
        $this->data['categoryList'] = $this->item->getCategoryList('1');
        $this->load->view($this->productForm,$this->data);
    }

    //Updated By Meghavi 15-03-2022 
    public function save(){
        $data = $this->input->post();
        $errorMessage = array();
        if(empty($data['item_name']))
            $errorMessage['item_name'] = "Item Name is required.";
        if(empty($data['unit_id']))
            $errorMessage['unit_id'] = "Unit is required.";
        if(empty($data['category_id']))
            $errorMessage['category_id'] = "Category is required.";
        if(empty($data['material_grade']))
        {
            if(!empty($data['gradeName']))
                $data['material_grade'] = $this->masterOption->saveGradeName($data['gradeName']);
        }
        unset($data['gradeName']);

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:

            // if($_FILES['drawing_file']['name'] != null || !empty($_FILES['drawing_file']['name'])):
            //     $this->load->library('upload');
			// 	$_FILES['userfile']['name']     = $_FILES['drawing_file']['name'];
			// 	$_FILES['userfile']['type']     = $_FILES['drawing_file']['type'];
			// 	$_FILES['userfile']['tmp_name'] = $_FILES['drawing_file']['tmp_name'];
			// 	$_FILES['userfile']['error']    = $_FILES['drawing_file']['error'];
			// 	$_FILES['userfile']['size']     = $_FILES['drawing_file']['size'];
				
			// 	$imagePath = realpath(APPPATH . '../assets/uploads/items/drawings');
			// 	$config = ['file_name' => time()."_order_item_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

			// 	$this->upload->initialize($config);
			// 	if (!$this->upload->do_upload()):
			// 		$errorMessage['drawing_file'] = $this->upload->display_errors();
			// 		$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
			// 	else:
			// 		$uploadData = $this->upload->data();
			// 		$data['drawing_file'] = $uploadData['file_name'];
			// 	endif;
			// else:
			// 	unset($data['drawing_file']);
			// endif;
			
			if($_FILES['item_image']['name'] != null || !empty($_FILES['item_image']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['item_image']['name'];
				$_FILES['userfile']['type']     = $_FILES['item_image']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['item_image']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['item_image']['error'];
				$_FILES['userfile']['size']     = $_FILES['item_image']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/product/');
				$config = ['file_name' => time()."_".$_FILES['userfile']['name'],'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['item_image'] = $this->upload->display_errors();
					$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['item_image'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['item_image']);
			endif;

            unset($data['processSelect'],$data['party_id'],$data['party_code']);
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->save($data));
        endif;
    }
 
    //Updated By Meghavi 15-03-2022
    public function edit(){ 
        $id = $this->input->post('id');
        $this->data['unitData'] = $this->item->itemUnits();
        $this->data['customerList'] = $this->party->getCustomerList();
        $this->data['automotiveArray'] = $this->automotiveArray;
        $this->data['dataRow'] = $this->item->getItem($id);
        $this->data['gstPercentage'] = $this->gstPercentage;
        // $this->data['processData'] = $this->process->getProcessList();
        $this->data['categoryList'] = $this->item->getCategoryList(1);
		// $this->data['materialGrades'] = explode(',', $this->item->getMasterOptions()->material_grade);
        // $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->load->view($this->productForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->item->delete($id));
        endif;
    }

    public function addProductProcess(){
        $id = $this->input->post('id');        
        $this->data['processData'] = $this->process->getProcessList();
        $this->load->view($this->productProcessForm,$this->data);
    }

    public function saveProductProcess(){
        $data = $this->input->post();
        $errorMessage = "";

        if(empty($data['item_id']))
            $errorMessage .= "Somthing went wrong.";
        /* if(empty($data['process'][0]))
            $errorMessage .= " Pelase select product process."; */

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            //$data['created_by'] = $this->session->userdata('loginId');
            $response = $this->item->saveProductProcess($data);
            $this->printJson($this->setProcessView($data['item_id']));
        endif;
    }

    public function setProcessView($id)
    {
        $processData = $this->item->getItemProcess($id);
        $operationData = $this->operation->getOperationList();
        $processHtml = '';
        if (!empty($processData)) :
            $i = 1; $html = ""; $options=Array(); $opt='';
            foreach ($processData as $row) :
                $opt='';
                $ops = $this->item->getProductOperationForSelect($row->id);
                foreach($operationData as $operation):
                    $selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
                     $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
                endforeach;
                $options[$row->id] = $opt;
            endforeach;

            foreach ($processData as $row) :
                $processHtml .= '<tr id="'.$row->id.'">
                        <td class="text-center">'.$i++.'</td>
                        <td>'.$row->process_name.'</td>
                        <td class="text-center">'.$row->sequence.'</td>
                        <td><select name="operationSelect" id="operationSelect'.$row->id.'" data-input_id="operation_id'.$row->id.'" class="form-control jp_multiselect operation_id" multiple="multiple">'.
                                $options[$row->id]
                            .'</select>
                            <input type="hidden" name="operation_id" id="operation_id'.$row->id.'" data-id="'.$row->id.'" value="'.$row->operation.'" /></td>
                      </tr>';
            endforeach;
        else :
            $processHtml .= '<tr><td colspan="3" class="text-center">No Data Found.</td></tr>';
        endif;
        return ['status' => 1, "processHtml" => $processHtml];
    }

    public function viewProductProcess(){
        $id = $this->input->post('id');
        $this->data['processDataList'] = $this->process->getProcessList();
        $this->data['operationDataList'] = $this->operation->getOperationList();
        $this->data['productProcess'] = $this->item->getProductProcessForSelect($id);
        $this->data['processData'] = $this->item->getItemProcess($id); 

		$this->data['productOperation']="";$options=Array();$opt='';
		foreach ($this->data['processData'] as $row) :
			$opt='';
			$ops = $this->item->getProductOperationForSelect($row->id);
			foreach($this->data['operationDataList'] as $operation):
				$selected = (!empty($ops) && (in_array($operation->id, explode(',',$ops)))) ? "selected" : "";
				 $opt .= '<option value="'.$operation->id.'" data-id="'.$row->id.'" '.$selected.'>'.$operation->operation_name.'</option>';
			endforeach;
			$options[$row->id] = $opt;
		endforeach;
		$this->data['productOperation'] = $options;
        $this->data['item_id'] = $id;   
        $this->load->view($this->viewProductProcess,$this->data);
    }

    public function updateProductProcessSequance(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['id']))
			$errorMessage['id'] = "Item ID is required.";
		
		if(empty($errorMessage)):
			$this->printJson($this->item->updateProductProcessSequance($data));			
		endif;
    }

    public function addProductKitItems(){
        $id = $this->input->post('id');
        $this->data['productKitData'] = $this->item->getProductKitData($id);
        $this->data['rawMaterial'] = $this->item->getItemLists("3");
        $this->data['process'] = $this->item->getProductWiseProcessList($id);
        $this->load->view($this->productKitItem,$this->data);
    }

    public function saveProductKit(){
        $data = $this->input->post();
		$errorMessage = array();
		if(empty($data['ref_item_id'][0])){
			$errorMessage['kit_item_id'] = "Item Name is required.";
		}
		if(empty($data['qty'][0])){
			$errorMessage['kit_item_qty'] = "Qty. is required.";
		}
		
		if(!empty($errorMessage)):
			$this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
		else:
			$this->printJson($this->item->saveProductKit($data));
		endif;
    }
	
    public function saveProductOperation(){
        $data = $this->input->post();
        $this->printJson($this->item->saveProductOperation($data));
    }
    
    public function getItemData(){
		$id = $this->input->post('itemId');
        $result = $this->item->getItem($id);
        $unitData = $this->item->itemUnit($result->unit_id);
        $result->unit_name = $unitData->unit_name;
        $result->description = $unitData->description;
		$this->printJson($result);
	}
    
    public function getFgRevision(){
        $item_id = $this->input->post('id');
        $this->data['dataRow'] = $this->item->getFgRevision($item_id);
        $this->data['item_id'] = $item_id;
        $this->load->view($this->fgRevision,$this->data);
    }

    public function updateFgRevision(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['date']))
            $errorMessage['date'] = "Date is required.";
        if(empty($data['change_reason']))
            $errorMessage['change_reason'] = "Change Reason is required.";
        if(empty($data['description']))
            $errorMessage['description'] = "Description is required.";
        if(empty($data['new_rev_no']))
            $errorMessage['new_rev_no'] = "Revision No is required.";
        if(empty($data['new_specs']))
            $errorMessage['new_specs'] = "Specification is required.";
       
        if($data['feasibility_status'] =='Yes')
            $errorMessage['feasibilty_remark'] = "Feasibilty Remarkis required.";
        if(empty($data['fg_stock']))
            $errorMessage['fg_stock'] = "Fg Stock is required.";
      
        if($data['cost_effect'] =='Yes')
            $errorMessage['cost_remark'] = "Cost Remark is required.";
        if(empty($data['auth_required']))
            $errorMessage['auth_required'] = "Cft Auth is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $itemData=$this->item->getItem($data['item_id']);
            $data['old_rev_no']=$itemData->rev_no;
            $data['old_specs']=$itemData->rev_specification;
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->item->saveFgRevision($data));
        endif;
    }
    
    public function printTags(){
		$id = $this->input->post('printsid');
		$tag_qty = $this->input->post('tag_qty');
        $itemData = $this->item->getItem($id);
		$price = ($this->CMID == 1)?$itemData->price1:$itemData->price2;
		$qrIMG=base_url('assets/product/tags/'.$itemData->id.'.png');
		if(!file_exists($qrIMG)){
			$qrText = $itemData->id;
			$file_name = $itemData->id;
			$qrIMG = $this->getQRCode($qrText,'assets/product/tags/',$file_name);
		}
		$styleData = '<style>body{margin:0px;}.itmnm{text-align:center;border:1px solid #555;border-radius:3px!important; font-size:10px;vertical-align:top;}</style>';
		$pageData = Array();$p=1;$pdata = $styleData;
		for($i=1;$i<=$tag_qty;$i++)
		{
			$pdata .= '<div style="width:45mm;text-align:center;float:left;padding:2mm;">
							<div class="itmnm">'.$itemData->item_name.'</div>
							<table style="width:100%;">
								<tr>
									<th style="vertical-align:top;"><img src="'.$qrIMG.'" style="height:18mm;"></th>
									<th style="font-size:14px;vertical-align:middle;">
										MRP &#8377; '.sprintf('%.2f',$price).'
									</th>
								</tr>
							</table>
						</div>';
			if($i==$tag_qty){$pageData[]=$pdata;}
			elseif($i%2==0){$pageData[]=$pdata;$pdata = '';}
			
		}
		
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 25]]);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->setTitle($itemData->item_name);
		
		foreach($pageData as $pg)
		{
			$mpdf->AddPage('P','','','','',0,0,0,0,0,0);
			$mpdf->WriteHTML($pg);
		}
		$mpdf->Output($itemData->item_code.'.pdf','I');
	}
	
    public function printTagsIndex($category_id=0){
        $this->data['tableHeader'] = getSalesDtHeader('printTags');
        $this->data['categoryList'] = $this->item->getCategoryList('1');
        $this->data['category_id'] = $category_id;
        $this->load->view($this->indexTag, $this->data);
    }

    public function getTagsDTRow(){        
        $result = $this->item->getTagsDTRow($this->input->post(),1);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            
            $row->qty = 0; $qty = 0;
            $itmStock = $this->store->getItemStock($row->id);
            if(!empty($itmStock->qty)){$qty = $itmStock->qty;}
             
            $row->selectBox = '<input type="checkbox" name="item_id[]" id="item_id_'.$i.'" data-rowid="'.$i.'" data-id="'.$row->id.'" data-stockqty="'.floatVal($qty).'" class="filled-in chk-col-success bulkTags" value="'.$row->id.'"><label for="item_id_'.$i.'"></label>';
            $row->inputBox = '<input type="text" name="tag_qty[]" id="tag_qty_'.$row->id.'"  class="form-control " value="" disabled>';
            if(!empty($row->item_image)):
                $row->productImg = '<img src="'.base_url('assets/uploads/product/'.$row->item_image).'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
            else:
                $row->productImg = '<img src="'.base_url('assets/uploads/product/default.png').'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
            endif;

            if(!empty($itmStock->qty)){$row->qty = $itmStock->qty;}
            $sendData[] = [$row->sr_no,$row->selectBox,$row->inputBox,$row->productImg,$row->item_name,$row->category_name,$row->price,floatVal($row->qty)];
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function printMultiTags(){ 
		$data = $this->input->post();
		$styleData = '<style>body{margin:0px;}.itmnm{text-align:center;border:1px solid #555;border-radius:3px!important; font-size:10px;vertical-align:top;}</style>';
		$pageData = Array();$p=1;$pdata = $styleData.'';
		foreach($data['tag_qty'] as $key=>$value){
			if($value > 0){
				$itemData = $this->item->getItem($data['item_id'][$key]);
				$price = ($this->CMID == 1)?$itemData->price1:$itemData->price2;
				$qrIMG=base_url('assets/product/tags/'.$itemData->id.'.png');
				if(!file_exists($qrIMG)){
					$qrText = $itemData->id;
					$file_name = $itemData->id;
					$qrIMG = base_url().$this->getQRCode($qrText,'assets/product/tags/',$file_name);
				}
				
				for($i=1;$i<=$value;$i++){
					$pdata .= '<div style="width:45mm;height:25mm;text-align:center;float:left;padding:0mm 2mm;">
									<div class="itmnm">'.$itemData->item_name.'</div>
									<table style="width:100%;margin-bottom:2mm;">
										<tr>
											<th style="vertical-align:top;"><img src="'.$qrIMG.'" style="height:18mm;"></th>
											<th style="font-size:14px;vertical-align:middle;">
												MRP <br>&#8377; '.sprintf('%.2f',$price).'
											</th>
										</tr>
									</table>
								</div>';			
				}
			}
		}
		$result = json_encode(['status'=>1,'printData'=>'<div style="width:100mm;height:25mm;">'.$pdata.'</div>']);
		
		//return $result;
		if(!empty($data['tp'])){echo $result;}
		else
		{
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 25]]);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->setTitle($itemData->item_name);
			$mpdf->AddPage('P','','','','',0,0,3,2,2,2);
			$mpdf->WriteHTML($pdata);
			$mpdf->Output('tags_print.pdf','I');
		}		
    }
    
    //Created By Meghavi 28/04/2022
    public function printTagsByPinv($id){ 
       
		$styleData = '<style>body{margin:0px;}.itmnm{text-align:center;border:1px solid #555;border-radius:3px!important; font-size:10px;vertical-align:top;}</style>';
		$pageData = Array();$pdata = $styleData.'';
        $transItems=$this->purchaseInvoice->getItemListForTag($id);
		foreach($transItems as $row){
          
				$itemData = $this->item->getItem($row->item_id);
				$price = ($this->CMID == 1)?$itemData->price1:$itemData->price2;
				$qrIMG=base_url('assets/product/tags/'.$itemData->id.'.png');
				if(!file_exists($qrIMG)){
					$qrText = $itemData->id;
					$file_name = $itemData->id;
					$qrIMG = base_url().$this->getQRCode($qrText,'assets/product/tags/',$file_name);
				}
                
				for($i=1;$i<=$row->qty;$i++){
					$pdata .= '<div style="width:45mm;height:25mm;text-align:center;float:left;padding:0mm 2mm;">
									<div class="itmnm">'.$itemData->item_name.'</div>
									<table style="width:100%;margin-bottom:2mm;">
										<tr>
											<th style="vertical-align:top;"><img src="'.$qrIMG.'" style="height:18mm;"></th>
											<th style="font-size:14px;vertical-align:middle;">
												MRP <br>&#8377; '.sprintf('%.2f',$price).'
											</th>
										</tr>
									</table>
								</div>';			
				}
		}
		$result = json_encode(['status'=>1,'printData'=>'<div style="width:100mm;height:25mm;">'.$pdata.'</div>']);
		
		if(!empty($data['tp'])){echo $result;}
		else
		{
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 25]]);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->setTitle($itemData->item_name);
			$mpdf->AddPage('P','','','','',0,0,3,2,2,2);
			$mpdf->WriteHTML($pdata);
			$mpdf->Output('tags_print.pdf','I');
		}		
    }
      
    public function catalogue_pdf(){ 
        $id = $this->input->post('printsid'); 
        $category_ids = $this->input->post('category_id_footer');
        $prod_per_page = $this->input->post('prod_per_page');
        $with_qty = $this->input->post('with_qty');
		if(empty($prod_per_page) OR $prod_per_page <= 1){$prod_per_page=5;}
		if($prod_per_page > 6){$prod_per_page=6;}
		
        $logo=base_url('assets/images/logo.png');
		
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 25]]);
        $pdfFileName='WG-PRODUCT-CATALOGUE.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        //$mpdf->SetProtection(array('print'));
		
		$catIds = explode(',',$category_ids);
	
		foreach($catIds as $key=>$value):
		    $catData = $this->itemCategory->getCategory($value);
            $itemData = $this->item->getItemForCatalogue($value);
            
    		$tblData =''; $i=1;
    		if(!empty($itemData)):
    			foreach($itemData as $row):
                    if($row->stock_qty > 0 && !empty($row->item_image)){
    					$productImg = base_url('assets/uploads/product/'.$row->item_image);
    					$price = $row->price1;
    					if($this->CMID==2){ 
                            $catalogue_type = $this->input->post('catelog_type_footer');
    					    if($catalogue_type == 'Regular'){ 
    					        $price = $row->price2; 
    					    }elseif($catalogue_type == 'SemiWholesale'){
    					        $price = $row->wholesale1; 
    					    }else{
    					        $price = $row->wholesale2; 
    					    }
    					}
    					$qty = (!empty($with_qty) && $with_qty == 'Yes') ? '|Pcs:'.floatval($row->stock_qty) : '';
    					$mrg = '17px 12px';
    					if($prod_per_page==2){$mrg = '22px 12px';}
    					if($prod_per_page==3){$mrg = '25px 12px';}
    					if($prod_per_page==4){$mrg = '15px 10px';}
    					if($prod_per_page==6){$mrg = '4px 4px';}
    					$size = (!empty($row->size))?$row->size:'';
    					
    					$tblData .= '<div class="col-'.$prod_per_page.' text-center">
        					    <div class="citem" style="margin:'.$mrg.';">
    								<img src="'.$productImg.'" class="citem-img"> <br>
    								<div class="prod-name">'.$row->item_name.' '.$size.'</div>
    								<div class="prod-price">&#x20B9; '.$price.$qty.'</div>
        						</div>
        					</div>';
    				    $i++;
                    }
    			endforeach;
    		endif;
    		
            $pdfData = '<div>'.$tblData.'</div>';
            
            $htmlHeader = "<table class='table' style='border-bottom:1px solid #ccc;'>
                <tr>
                    <td style='width:70px;'><img src='".$logo."' style='width:70px;'></td>
                    <td class='org_title text-left' style='font-size:1.5rem;width:50%;'>World's Gift Mall - ".$catData->category_name."</td>
                    <td class='text-right' style='font-size:1.5rem;'>Product Catalogue</td>
                </tr>
            </table>";
            
            $mpdf->setHtmlHeader($htmlHeader);
            $mpdf->AddPage('P','','','','',5,5,28,5,2,2,'','','','','','','','','','A4-P');
    		$mpdf->WriteHTML($pdfData);
		endforeach;
		$mpdf->Output($pdfFileName,'I');
    }
    
    public function printItemCatalogue(){ 
        $item_id = $this->input->post('item_id'); 
        $prod_per_page = $this->input->post('prod_per_page');
        $with_qty = $this->input->post('with_qty');
		if(empty($prod_per_page) OR $prod_per_page <= 1){$prod_per_page=5;}
		if($prod_per_page > 6){$prod_per_page=6;} 
		
        $itemData = $this->item->getMultipleItems(implode(',', $item_id));
        
        $logo=base_url('assets/images/logo.png');
        
        $itemList='<table class="table align-items-center progrid"><tbody>'; 
		$tblData='<div class="col-row">';$i=1;
		if(!empty($itemData)):
			foreach($itemData as $row):
                if($row->stock_qty > 0 && !empty($row->item_image)){
					
					$productImg = base_url('assets/uploads/product/'.$row->item_image);
					$price = $row->price1;
					if($this->CMID==2){ 
                        $catalogue_type = $this->input->post('catelog_type_footer');
					    if($catalogue_type == 'Regular'){ 
					        $price = $row->price2; 
					    }elseif($catalogue_type == 'SemiWholesale'){
					        $price = $row->wholesale1; 
					    }else{
					        $price = $row->wholesale2; 
					    }
					}
					$qty = (!empty($with_qty) && $with_qty == 'Yes') ? '|Pcs:'.floatval($row->stock_qty) : '';
					$mrg = '17px 12px';
					if($prod_per_page==2){$mrg = '22px 12px';}
					if($prod_per_page==3){$mrg = '25px 12px';}
					if($prod_per_page==4){$mrg = '15px 10px';}
					if($prod_per_page==6){$mrg = '4px 4px';}
					$size = (!empty($row->size))?$row->size:'';
					$tblData .= '<div class="col-'.$prod_per_page.' text-center"><div class="citem" style="margin:'.$mrg.';">
									<img src="'.$productImg.'" class="citem-img"> <br>
									<div class="prod-name">'.$row->item_name.' '.$size.'</div>
									<div class="prod-price">&#x20B9; '.$price.$qty.'</div></div></div>';
				    $i++;
                }
			endforeach;
		endif;
		$htmlHeader = "<table class='table' style='border-bottom:1px solid #ccc;'>
            <tr>
                <td style='width:70px;'><img src='".$logo."' style='width:70px;'></td>
                <td class='org_title text-left' style='font-size:1.7rem;width:40%;'>World's Gift Mall</td>
                <td class='text-right' style='font-size:1.7rem;'>Product Catalogue</td>
            </tr>
        </table>";
        
        $pdfData = '<div>'.$tblData.'</div>';
        //print_r($pdfData); exit;
		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 25]]);
        $pdfFileName='WG-PRODUCT-CATALOGUE.pdf';
        $stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
        $mpdf->WriteHTML($stylesheet,1);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetProtection(array('print'));
        $mpdf->setHtmlHeader($htmlHeader);
        $mpdf->AddPage('P','','','','',5,5,28,5,2,2,'','','','','','','','','','A4-P');
        $mpdf->WriteHTML($pdfData);
        $mpdf->Output($pdfFileName,'I');
    }

    //Created By Karmi @08/04/2022
    public function getFinishedGoodList(){
        $mfg_type=$this->input->post('manufacture_type');
        $this->data['item_type']=$this->input->post('item_type');
        $this->data['entry_type']=$this->input->post('entry_type');
        $this->load->view($this->fg_item_list,$this->data);
    }
    
    public function searchItem($item_type=1, $entry_type=0){
        $data = $this->input->post(); 
        
        $data['item_type'] = 1;
        
        $resultData = $this->item->getItemListOnSearch($data);
        $sendData = array();$i=0;
        foreach($resultData['data'] as $row):       
            $itmStock = $this->store->getLocationWiseItemStock($row->id,$this->RTD_STORE->id);
            $row->qty = 0;$row->item_type = $item_type;
            if(!empty($itmStock->qty)){$row->qty = $itmStock->qty;}
            if(!empty($entry_type)){
                $item_name = "<a href='javascript:void(0)' class='' onclick='getFGSelect(".json_encode($row).")' >". $row->item_name ."</a>";//($row->qty > 0)? "<a href='javascript:void(0)' class='' onclick='getFGSelect(".json_encode($row).")' >". $row->item_name ."</a>" : $row->item_name;
            }else{
                $item_name = "<a href='javascript:void(0)' class='' onclick='getFGSelect(".json_encode($row).")' >". $row->item_name ."</a>";
            }
            $sendData[] = [
                "",
                $item_name,
                $row->item_code,
                $row->category_name,
                $row->price,
                $row->qty
            ];
            $i+=1;
        endforeach; 
          $resultData['data'] = $sendData;
        $this->printJson($resultData);
    }
    
    /*** Get Data For Dynamic Select2 ***/	
    public function getDynamicItemList()
    {
		$postData = Array();
		$postData = $this->input->post();
        if(!empty($postData['location_id'])){
            $htmlOptions = $this->item->getDynamicItemListOnLocation($postData);
        }else{
            $htmlOptions = $this->item->getDynamicItemList($postData);
        }
		$this->printJson($htmlOptions);
    }
    
    public function getDynamicHSNList(){
        $postData = Array();
		$postData = $this->input->post();
        
        $htmlOptions = $this->item->getHsnList($postData);
		$this->printJson($htmlOptions);
    }
    
    public function getCategoryList(){
        $postData = $this->input->post();
        $categoryData = $this->item->getCategoryList();

        $options = '';
        if(empty($postData['skip_all_category'])){
            $options='<option value="">All Category</option>';
        }
		foreach($categoryData as $row):
			$options.= '<option value="'.$row->id.'">'.$row->category_name.'</option>';
		endforeach;
		$this->printJson(['status'=>1,'options'=>$options]);
    }

    public function createExcel($category_id=0){
        $paramData = $this->item->getItemForCatalogue($category_id,'excel');
        $table_column = array('id', 'item_name','category_name');
        $spreadsheet = new Spreadsheet();
        $inspSheet = $spreadsheet->getActiveSheet();
        $inspSheet = $inspSheet->setTitle('Item');
        $xlCol = 'A';
        $rows = 1;
        foreach ($table_column as $tCols) {
            $inspSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++; 
        }
        $rows = 2;
        foreach ($paramData as $row) {
            $inspSheet->setCellValue('A' . $rows, $row->id);
            $inspSheet->setCellValue('B' . $rows, $row->item_name);
            $inspSheet->setCellValue('C' . $rows, $row->category_name);
            $rows++;
        }

        $fileDirectory = realpath(APPPATH . '../assets/uploads/finish_goods');
        $fileName = '/finish_goods_' . time() . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel"); 
        redirect(base_url('assets/uploads/finish_goods') . $fileName);
        //unlink(APPPATH . '../assets/uploads/finish_goods/' . $fileName);
    }
    
    //Created By Karmi
    public function indexIncentive(){
        $this->data['pageHeader'] = 'Incentive';
        $this->data['categoryList'] = $this->item->getCategoryList('1');
        $this->load->view($this->incentivePage,$this->data);
    }

    public function getItemIncentive(){
        $data = $this->input->post();
        
        $incentiveData = $this->item->getItemIncentive($data);
        $i=1; $tbody =""; $tfoot=""; $tqty=0;$tamt=0;
        foreach($incentiveData as $row):
            $itmStock = $this->store->getItemStock($row->id);
            $row->qty = 0;
            if(!empty($itmStock->qty)){$row->qty = $itmStock->qty;}
            $selectBox = '<input type="checkbox" name="item_id[]" id="item_id_'.$i.'" data-rowid="'.$i.'" data-id="'.$row->id.'"  class="filled-in chk-col-success bulkIncentive" value="'.$row->id.'"><label for="item_id_'.$i.'"></label>';
            if($row->qty > 0):
                $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . $selectBox . '</td>
                    <td>[' . $row->item_code.']' .$row->item_name. '</td>
                    <td>' . $row->category_name . '</td>
                    <td>' . $row->qty . '</td>
                    <td>'.$row->incentive.'</td>
                    
                </tr>';
           endif;
        endforeach;
                    
        $this->printJson(['status' => 1, 'tbody' => $tbody]);

    }

    public function saveMultipleInsentive()
    {
        $data = $this->input->post();
        if(empty($data['item_id'])):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.']);
        else:
            $this->printJson($this->item->saveMultipleInsentive($data));
        endif;
    }
    
    public function getImageUpload(){
        $item_id = $this->input->post('id'); 
        $this->data['imageData'] = $this->item->getImageItems($item_id); 
        $this->data['item_id'] = $item_id;
        $this->load->view($this->image_upload, $this->data);
    }
    
    public function uploadImage(){
        $data = $this->input->post();

        $errorMessage = array();
        if (empty($_FILES['image_path']['name']))
            $errorMessage['image_path'] = "Image is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => $errorMessage]);
        else :
            $imgData = $this->item->getImageItems($data['item_id']);

            if($_FILES['image_path']['name'] != null || !empty($_FILES['image_path']['name'])):
                $this->load->library('upload');
				$_FILES['userfile']['name']     = $_FILES['image_path']['name'];
				$_FILES['userfile']['type']     = $_FILES['image_path']['type'];
				$_FILES['userfile']['tmp_name'] = $_FILES['image_path']['tmp_name'];
				$_FILES['userfile']['error']    = $_FILES['image_path']['error'];
				$_FILES['userfile']['size']     = $_FILES['image_path']['size'];
				
				$imagePath = realpath(APPPATH . '../assets/uploads/item_image/');
				$config = ['file_name' => $data['item_id'].'_'.(count($imgData)+1),'allowed_types' => '*','max_size' => 10240,'overwrite' => FALSE, 'upload_path'	=>$imagePath];

				$this->upload->initialize($config);
				if (!$this->upload->do_upload()):
					$errorMessage['image_path'] = $this->upload->display_errors();
					$this->printJson(["status"=>0,"message"=>$errorMessage]);
				else:
					$uploadData = $this->upload->data();
					$data['image_path'] = $uploadData['file_name'];
				endif;
			else:
				unset($data['image_path']);
            endif;
            $response = $this->item->uploadImage($data);
            $result = $this->item->getImageItems($data['item_id']);
            $i = 1;
            $tbodyData = "";$productImg='';
            if (!empty($result)) :
                foreach ($result as $row) :
                    if(!empty($row->image_path)):
                        $productImg = '<img src="'.base_url('assets/uploads/item_image/'.$row->image_path).'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    else:
                        $productImg = '<img src="'.base_url('assets/uploads/item_image/default.png').'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    endif;
                    $tbodyData .= '<tr>
                                <td>' . $i++ . '</td>
                                <td>' . $productImg . '</td>
                                <td>' . $row->remark . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="deleteImage('. $row->id .')"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="4" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "item_id" => $data['item_id']]);
        endif;
    }
   
    public function deleteImage(){
        $data = $this->input->post();
        if (empty($data['id'])) :
            $this->printJson(['status' => 0, 'message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->item->deleteImage($data['id']);

            $result = $this->item->getImageItems($data['item_id']);
            $i = 1;
            $tbodyData = "";
            if (!empty($result)) :
                foreach ($result as $row) :
                    if(!empty($row->image_path)):
                        $productImg = '<img src="'.base_url('assets/uploads/item_image/'.$row->image_path).'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    else:
                        $productImg = '<img src="'.base_url('assets/uploads/item_image/default.png').'" width="60" height="60" style="border-radius:0%;border: 0px solid #ccc;padding:3px;">';
                    endif;
                    $tbodyData .= '<tr>
                                <td>' .  $i++ . '</td>
                                <td>' . $productImg . '</td>
                                <td>' . $row->remark . '</td>
                                <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-outline-danger btn-delete" onclick="deleteImage(' . $row->id . ')"><i class="ti-trash"></i></a>
                                </td>
                            </tr> ';
                endforeach;
            else :
                $tbodyData .= '<tr><td colspan="5" style="text-align:center;">No Data Found</td></tr>';
            endif;
            $this->printJson(['status' => 1, "tbodyData" => $tbodyData, "item_id" => $data['id']]);
        endif;
    }
    
	/*** Get Scanned Item Data ***/
    public function getScannedItem(){
		$id = $this->input->post('scan_id');
		$this->printJson($this->item->getScannedItem($id));
	}
	
	public function createSampleExcel()
    {
        
        $table_column = array('item_name', 'item_code', 'category_id', 'hsn_code', 'unit_id', 'gst_per', 'default_price', 'price', 'comman', 'description');
        $spreadsheet = new Spreadsheet();
        $inspSheet = $spreadsheet->getActiveSheet();
        $inspSheet = $inspSheet->setTitle('Item');
        $xlCol = 'A';
        $rows = 1;
        foreach ($table_column as $tCols) {
            $inspSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }
        for ($i = 2; $i <= 3; $i++) {
            $objValidation2 = $inspSheet->getCell('I' . $i)->getDataValidation();
            $objValidation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $objValidation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
            $objValidation2->setAllowBlank(false);
            $objValidation2->setShowInputMessage(true);
            $objValidation2->setShowDropDown(true);
            $objValidation2->setPromptTitle('Pick from list');
            $objValidation2->setPrompt('Please pick a value from the drop-down list.');
            $objValidation2->setErrorTitle('Input error');
            $objValidation2->setError('Value is not in list');
            $objValidation2->setFormula1('"Yes,No"');
            $objValidation2->setShowDropDown(true);
        }
        /** Category Master */
        $catSheet = $spreadsheet->createSheet();
        $catSheet = $catSheet->setTitle('Item Category');
        $xlCol = 'A';
        $rows = 1;
        $table_column_category = array('id', 'category_name');

        foreach ($table_column_category as $tCols) {
            $catSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }
        $rows = 2;

        $catData = $this->item->getCategoryList('1');
        foreach ($catData as $row) {
            $catSheet->setCellValue('A' . $rows, $row->id);
            $catSheet->setCellValue('B' . $rows, $row->category_name);
            $rows++;
        }

        /** Unit Master */
        $unitSheet = $spreadsheet->createSheet();
        $unitSheet = $unitSheet->setTitle('Unit');
        $xlCol = 'A';
        $rows = 1;
        $table_column_category = array('id', 'Unit Name');

        foreach ($table_column_category as $tCols) {
            $unitSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++;
        }
        $rows = 2;
        
        $unitData = $this->item->itemUnits();
        foreach ($unitData as $row) {
            $unitSheet->setCellValue('A' . $rows, $row->id);
            $unitSheet->setCellValue('B' . $rows, $row->unit_name);
            $rows++;
        }

        $fileDirectory = realpath(APPPATH . '../assets/uploads/product');
        $fileName = '/products_' . time() . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        redirect(base_url('assets/uploads/product') . $fileName);
    }

    public function importExcel(){
        $fileData = $this->importExcelFile($_FILES['item_excel'], 'product', 'Item');
        $row = 0;
        if (!empty($fileData)) {
            $fieldArray = $fileData[0][1];
            for ($i = 2; $i <= count($fileData[0]); $i++) {
                
                $rowData = array();
                $c = 'A';
                
                foreach ($fileData[0][$i] as $key => $colData) :
                    $rowData[strtolower($fieldArray[$c])] = $colData;
                    $c++;
                endforeach;
                
                if(!empty($rowData['comman']) AND $rowData['comman'] == 'Yes'){
                    $rowData['cm_id'] = 0;
                }
                elseif(!empty($rowData['comman']) AND $rowData['comman'] == 'No'){
                    $rowData['cm_id'] = $this->CMID;
                }
                
                unset($rowData['comman']);
                
                if(!empty($rowData['item_name'])){
                    $rData = [
                        'id'=>'',
                        'item_type'=>1,
                        'item_name'=>(!empty($rowData['item_name'])?$rowData['item_name']:''),
                        'item_code'=>(!empty($rowData['item_code'])?$rowData['item_code']:''),
                        'category_id'=>(!empty($rowData['category_id'])?$rowData['category_id']:''),
                        'unit_id'=>(!empty($rowData['unit_id'])?$rowData['unit_id']:''),
                        'price1'=>(!empty($rowData['default_price'])?$rowData['default_price']:''),
                        'prc_price1'=>(!empty($rowData['price'])?$rowData['price']:''),
                        'cm_id'=>(!empty($rowData['cm_id'])?$rowData['cm_id']:''),
                        'description'=>(!empty($rowData['description'])?$rowData['description']:''),
                        'gst_per'=>(!empty($rowData['gst_per'])?$rowData['gst_per']:''),
                        'hsn_code'=>(!empty($rowData['hsn_code'])?$rowData['hsn_code']:''),
                        'source' => 'Excel'
                    ];
                    
                    $result = $this->item->save($rData);
                    
                    if($result['status'] == 0 || $result['status'] == 2)
                    {
                        $this->printJson(['status' => 2, 'message' => $result['message'] ]);
                    }
                    else
                    {
                        $row++;
                    }
                }
            }
        }
        $this->printJson(['status' => 1, 'message' => $row . ' Record updated successfully.']);
    }

    //Download Excell Format In Item Code
    public function createItemCodeExcel($category_id=0){
        $paramData = $this->item->getItemForCatalogue($category_id,'excel');
        $table_column = array('id', 'item_name', 'item_code', 'category_name');
        $spreadsheet = new Spreadsheet();
        $inspSheet = $spreadsheet->getActiveSheet();
        $inspSheet = $inspSheet->setTitle('Item Code');
        $xlCol = 'A';
        $rows = 1;
        foreach ($table_column as $tCols) {
            $inspSheet->setCellValue($xlCol . $rows, $tCols);
            $xlCol++; 
        }
        foreach (['A', 'B', 'C', 'D'] as $col) {
            $inspSheet->getColumnDimension($col)->setAutoSize(true);
        }
        $rows = 2;
        foreach ($paramData as $row) {
            $inspSheet->setCellValue('A' . $rows, $row->id);
            $inspSheet->setCellValue('B' . $rows, $row->item_name);
            $inspSheet->setCellValue('C' . $rows, $row->item_code);
            $inspSheet->setCellValue('D' . $rows, $row->category_name);
            $rows++;
        }

        $fileDirectory = realpath(APPPATH . '../assets/uploads/finish_goods');
        $fileName = '/item_code_' . time() . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        $writer->save($fileDirectory . $fileName);
        header("Content-Type: application/vnd.ms-excel"); 
        redirect(base_url('assets/uploads/finish_goods') . $fileName);
    } 


    public function importCodeExcel(){
        if(empty($_FILES['item_code_excel']['name'])){
            $errorMessage['item_code_excel'] = "File is required.";
            $this->printJson(['status' => 0, 'message' => $errorMessage]); 
        }
        $fileData = $this->importExcelFile($_FILES['item_code_excel'], 'product', 'Item Code');

        $row = 0;
        if (!empty($fileData)) {
            $fieldArray = $fileData[0][1];

            //Validate format in excel
            $expectedColumns = ['id','item_name','item_code','category_name'];
            if (array_values($fieldArray) !== $expectedColumns) {
                $this->printJson(['status'  => 2, 'message' => 'Excel Format is not valid.']);
            }

            for ($i = 2; $i <= count($fileData[0]); $i++) {                
                $rowData = array();
                $c = 'A';
                
                foreach ($fileData[0][$i] as $key => $colData){
                    $rowData[strtolower($fieldArray[$c])] = $colData;
                    $c++;
                }
                
                if(!empty($rowData['id']) && !empty($rowData['item_name']) && !empty($rowData['item_code'])){
                    $rData = [
                        'id' => $rowData['id'],
                        'item_type' => 1,
                        'item_name' => $rowData['item_name'],
                        'item_code' => $rowData['item_code']
                    ];
                    $result = $this->item->save($rData);
                    
                    if($result['status'] == 0 || $result['status'] == 2)
                    {
                        $this->printJson(['status' => 2, 'message' => $result['message'] ]);
                    }
                    else
                    {
                        $row++;
                    }
                }
            }
        }
        if($row > 0){
            $this->printJson(['status' => 1, 'message' => $row . ' Record updated successfully.']);
        } else{
            $this->printJson(['status' => 2, 'message' => 'Record not updated.']);  
        }
    }
}
?>