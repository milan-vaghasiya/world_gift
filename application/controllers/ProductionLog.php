<?php
class ProductionLog extends MY_Controller
{
    private $indexPage = "production_log/index";
    private $prdForm = "production_log/form";
    public function __construct(){
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Production Log";
        $this->data['headData']->controller = "productionLog";
        $this->data['headData']->pageUrl = "productionLog";
    }

    public function index(){
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows(){
        $result = $this->productionLog->getDTRows($this->input->post());
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getProductionLogData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addProductionLog(){
        $this->data['rmItemList'] = $this->item->getItemList(1);
        $this->data['fgItemList'] = $this->item->getItemList(1);
        $this->data['locationList'] = $this->store->getLocationList();
        $this->data['nextTransNo'] = $this->productionLog->getNextTransNo();
        $this->load->view($this->prdForm, $this->data);
    }

    public function getItemStock(){
        $data = $this->input->post();
        $itmStock = $this->store->getItemStock($data['id']);
        $this->printJson($itmStock);
    }

    public function save(){
        $data = $this->input->post(); 
        $errorMessage = array();

        $rm_count=0;
        $fg_count=0;
        if (empty($data['item_id'][0])) :
            $errorMessage['generaL_item_error'] = "Finish Item Is Required";
        else :
            
            $i=1;
            foreach ($data['item_id'] as $key => $value) {
                if ($data['item_type'][$key] == 2) {
                    $stockQty=$this->store->getLocationWiseItemStock($value,$data['location_id'][$key]); 
                    
                    $oldQty = 0;
                    if (!empty($data['trans_id'][$key])) {
                        $oldTrans = $this->productionLog->getItemStockTransactions($data['trans_id'][$key]);
                        $oldQty = ($oldTrans->item_id == $value)?$oldTrans->qty:0;
                    }
                   
                    $pendingQty = $stockQty->qty + abs($oldQty);
                    if ($data['qty'][$key] > $pendingQty){
                        $errorMessage['qty'.$i] = "Qty is invalid";
                    }
                    $rm_count+=$data['qty'][$key];
                } else {
                    $fg_count+=$data['qty'][$key];
                }$i++;
            }
        endif;
       
        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'field_error' => 1, 'field_error_message' => $errorMessage]);
        else :
            $countItemtype=array_count_values($data['item_type']);
            $masterData=[
                'id'=>$data['id'],
                'trans_no'=>$data['trans_no'],
                'prd_date'=>$data['prd_date'],
                'remark'=>$data['remark'],
                'total_rm_qty'=>$rm_count,
                'total_fg_qty'=>$fg_count,
                'created_by'=>$this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],
                'item_type' => $data['item_type'],
                'qty' => $data['qty'],
                'location_id' => $data['location_id']
            ];
            $this->printJson($this->productionLog->save($masterData,$itemData));
        endif;
    }

    public function delete(){
        $id = $this->input->post('id');
        if (empty($id)) :
            $this->printJson(['status' => 0, 'field_error' => 1, 'field_error_message' => 'Somthing went wrong...Please try again.']);
        else :
            $this->printJson($this->productionLog->delete($id));
        endif;
    }

    public function edit($id){
        $logData = $this->productionLog->getProductionDetail($id);
        $this->data['dataRow'] = $logData;
        $this->data['batchTrans'] = $this->productionLog->getItemTransactions($id);
        $this->data['locationList'] = $this->store->getLocationList();
        $this->load->view($this->prdForm, $this->data);
    }
    
    //Created By Meghavi 28/04/2022
    public function printTagsByProduction($id){ 
		$styleData = '<style>body{margin:0px;}.itmnm{text-align:center;border:1px solid #555;border-radius:3px!important; font-size:10px;vertical-align:top;}</style>';
		$pageData = Array();$pdata = $styleData.'';
        $transItems=$this->productionLog->getItemListForTag($id); 
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
		else{
			$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => [100, 25]]);
			$mpdf->SetDisplayMode('fullpage');
			$mpdf->setTitle($itemData->item_name);
			$mpdf->AddPage('P','','','','',0,0,3,2,2,2);
			$mpdf->WriteHTML($pdata);
			$mpdf->Output('tags_print.pdf','I');
		}		
    }
    
    public function productionLog_pdf($id){ 
		$this->data['logData'] = $this->productionLog->getProductionDetail($id);
		$this->data['companyData'] = $this->purchaseOrder->getCompanyInfo();
		
		$logData = $this->data['logData'];
	
		$logo=base_url('assets/images/logo.png'); 
		$this->data['letter_head']=base_url('assets/images/letterhead_top.png');
        $itemList='';
            $itemList='<table class="table table-bordered itemList">
                        <tr class="text-center">
                            <th style="width:6%;">Sr.No.</th>
                            <th class="text-left">Item Name</th>
                            <th style="width:20%;">Store</th>
                            <th style="width:20%;">Item Type</th>
                            <th style="width:7%;">Qty</th>
                        </tr>';
                $plogData = $this->productionLog->getItemTransactions($id);
                $i=1;
                if(!empty($plogData))
                {
                    foreach ($plogData as $row)
                    {
                        $transType = ($row->trans_type == 1)?"Finish Goods":"Row Material";
                        $itemList.='<tr>
                            <td class="text-center" height="37">'.$i.'</td>
                            <td class="text-left">'.$row->item_name.'</td>
                            <td class="text-center">'.$row->location.'</td>
                            <td class="text-center">'.$transType.'</td>
                            <td class="text-center">'.abs($row->qty).'</td>
                        </tr>';
                        $i++;
                    }
                }
            $itemList.='</table>';
       
        
        $baseDetail='<table class="table table-bordered itemList" style="margin-bottom:5px;">
            <tr>
                <td style="width:50%;border-bottom:1px solid #000000;border-right:0px;padding:2px;">
                    <b>Voucher No. : '.$logData->trans_no.'</b><br><br>                   
                     <b>Remark : '.$logData->remark.'</b>

                </td>
                <td style="width:20%;border-bottom:1px solid #000000;border-left:0px;text-align:right;padding:2px 5px;">
                    <b>Voucher Date : '.date('d/m/Y', strtotime($logData->prd_date)).'</b><br><br>
                    <b></b>
				</>
            </tr>
        </table>';

		$htmlHeader = '<table class="table">
							<tr>
								<td style="width:25%;"><img src="'.$logo.'" style="height:70px;"></td>
								<td class="org_title text-center" style="font-size:1.5rem;width:50%">PRODUCTION REPORT</td>
                                <td></td>
							</tr>
						</table><hr>';
        $originalCopy = '<div style="width:210mm;height:140mm;">'.$baseDetail.$itemList.'</div>';


        $pdfData = $originalCopy."<br>";
		
		$mpdf = $this->m_pdf->load();
		$pdfFileName='DC-REG-'.$id.'.pdf';
		$stylesheet = file_get_contents(base_url('assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css'));
		$stylesheet = file_get_contents(base_url('assets/css/style.css?v='.time()));
		$stylesheet = file_get_contents(base_url('assets/css/pdf_style.css'));
		$mpdf->WriteHTML($stylesheet,1);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetWatermarkImage($logo,0.05,array(120,60));$mpdf->showWatermarkImage = true; 
		$mpdf->SetProtection(array('print'));
		
		$mpdf->SetHTMLHeader($htmlHeader);
		$mpdf->AddPage('P','','','','',5,5,30,5,5,5,'','','','','','','','','','A4-P');
		$mpdf->WriteHTML($pdfData);
		$mpdf->Output($pdfFileName,'I');		
	}
	
	public function productionCatalogue($id){ 
        $prod_per_page = 5;
		if(empty($prod_per_page) OR $prod_per_page <= 1){$prod_per_page=5;}
		if($prod_per_page > 6){$prod_per_page=6;}
		
        $plogData = $this->productionLog->getItemTransactions($id);
        
        $logo=base_url('assets/images/logo.png');
        
        $itemList='<table class="table align-items-center progrid"><tbody>'; 
		$tblData='<div class="col-row">';$i=1;
		if(!empty($plogData)):
			foreach($plogData as $row):
				$productImg = base_url('assets/uploads/product/'.$row->item_image);
				$price = $row->price1;
				if($this->CMID==2){ 
				   $price = $row->price2; 
				}
				$mrg = '17px 12px';
				if($prod_per_page==2){$mrg = '22px 12px';}
				if($prod_per_page==3){$mrg = '25px 12px';}
				if($prod_per_page==4){$mrg = '15px 10px';}
				if($prod_per_page==6){$mrg = '4px 4px';}
				$size = (!empty($row->size))?$row->size:'';
				$tblData .= '<div class="col-'.$prod_per_page.' text-center"><div class="citem" style="margin:'.$mrg.';">
								<img src="'.$productImg.'" class="citem-img"> <br>
								<div class="prod-name">'.$row->item_name.' '.$size.'</div>
								<div class="prod-price">&#x20B9; '.$price.'</div></div></div>';
			    $i++;
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
}
