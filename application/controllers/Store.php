<?php
class Store extends MY_Controller
{
    private $indexPage = "store/index";
    private $storeForm = "store/form";
    
	public function __construct(){
		parent::__construct();
		$this->isLoggedin();
		$this->data['headData']->pageTitle = "Store";
		$this->data['headData']->controller = "store";
	    $this->data['refTypes'] = array('','GRN','Purchase Invoice','Material Issue','Delivery Challan','Sales Invoice','Manual Manage Stock','Production Finish','Visual Inspection','Store Transfer','Return Stock From Production','In Challan','Out Challan','Tools Issue','Stock Journal','Packing Material','Packing Product','Rejection Scrap','Production Scrap');
	}
	
	public function index(){
        $this->data['headData']->pageUrl = "store";
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->load->view($this->indexPage,$this->data);
    }
	
    public function getDTRows(){
        $result = $this->store->getDTRows($this->input->post());
        $sendData = array();$i=1;$count=0;
        foreach($result['data'] as $row):          
            $row->sr_no = $i++;         
            $sendData[] = getStoreData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function addStoreLocation(){
        $this->data['storeNames'] = $this->store->getStoreNames();
        $this->load->view($this->storeForm, $this->data);
    }

    public function save(){
        $data = $this->input->post();
		$errorMessage = array();		
		if(empty($data['store_name']))
            if(empty($data['storename']))
			    $errorMessage['store_name'] = "Store Name is required.";
            else
            $data['store_name'] = $data['storename'];
        unset($data['storename']);
        if(empty($data['location']))
			$errorMessage['location'] = "Location is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $data['created_by'] = $this->session->userdata('loginId');
            $this->printJson($this->store->save($data));
        endif;
    }

    public function edit(){     
        $id = $this->input->post('id');
        $this->data['dataRow'] = $this->store->getStoreLocation($id);
        $this->data['storeNames'] = $this->store->getStoreNames();
        $this->load->view($this->storeForm,$this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->store->delete($id));
        endif;
    }

    public function items(){
        $this->data['headData']->pageUrl = "store/items";
        $this->data['tableHeader'] = getStoreDtHeader('storeItem');
        $this->data['categoryList'] = $this->item->getCategoryList('1');
        $this->load->view("store/item_list",$this->data);
    }

    public function itemList($type,$category_id="0"){
        $data = $this->input->post();  $data['category_id'] = $category_id;
        $result = $this->item->getDTRows($data,$type);
        $sendData = array();$i=1;
        foreach($result['data'] as $row):
            $row->sr_no = $i++;
            $itmStock = $this->store->getLocationWiseItemStock($row->id,$this->RTD_STORE->id);
            $row->qty = 0;
            if(!empty($itmStock->qty)){$row->qty = $itmStock->qty;}
            $sendData[] = getStoreItemData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }
    
    public function stockRegister(){
        $this->data['headData']->pageUrl = "store/stockRegister";
        $this->data['locationList'] = $this->store->getLocationList();
        $this->data['hsnList'] = $this->item->getItemWiseHsnList();
        $this->data['categoryList'] = $this->item->getCategoryList('1');
        $this->load->view("store/stock_register",$this->data);
    }
    
	public function getStockRegister(){
        $data = $this->input->post();
        $errorMessage = array();

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>$errorMessage]);
        else:
			$stockType = '';
			//if($data['stock_type'] == '0'){$stockType = 'stockQty = 0';}
			//if($data['stock_type'] == 1){$stockType = 'stockQty > 0';}
			//$location_id=$data['location_id'];
            $itemData = $this->store->getStockRegister($data,$data['location_id']); //$this->store->printQuery();
			
            $thead="";$tbody="";$i=1;$receiptQty=0;$issuedQty=0;$totalAmt=0;$totalAmtPrc=0;$totIQty=0;$totRQty=0;$totBQty=0;
            
            if(!empty($itemData)):
               foreach($itemData as $row):  
                    $data['item_id'] = $row->id;
                    $bQty = 0;
					$receiptQty = $row->rqty;$issuedQty = $row->iqty;
					$itmStock = $row->stockQty;
                    if(!empty($row->stockQty)){$bQty = $row->stockQty;}
                    $balanceQty=0;
                    if($row->item_type == 1){ $balanceQty = round($bQty,3); } 
					else { $balanceQty = round($receiptQty - abs($issuedQty),3); } 
                    $balanceQty = round($bQty,3);
                    $price = $row->price; $tamt = ($balanceQty > 0)? round($balanceQty * $price, 2) : 0;
                    $prc_price = $row->prc_price; $prc_tamt = ($balanceQty > 0)? round($balanceQty * $prc_price, 2) : 0;
					$tbody .= '<tr>
                                <td>'.$i++.'</td>
                                <td><a href="'.base_url("store/getItemHistory/".$row->id."/".$data['location_id']).'" target="_blank">'.$row->item_name.'</a></td>
                                <td class="text-right">'.floatVal($receiptQty).'</td>
                                <td class="text-right">'.abs(floatVal($issuedQty)).'</td>
                                <td class="text-right">'.floatVal($balanceQty).'</td>
                                <td class="text-right">'.number_format($price,2).'</td>
                                <td class="text-right">'.number_format($tamt,2).'</td>
                                <td class="text-right">'.number_format($prc_price,2).'</td>
                                <td class="text-right">'.number_format($prc_tamt,2).'</td>
                            </tr>';
					$totRQty += $receiptQty;$totIQty += $issuedQty;$totBQty += $balanceQty;$totalAmt += $tamt;$totalAmtPrc += $prc_tamt;
                endforeach;
                $thead .= '<tr>
                            <th class="text-center" colspan="2">Stock Register</th>
                            <th class="text-right">'.number_format($totRQty,2).'</th>
                            <th class="text-right">'.number_format(abs($totIQty),2).'</th>
                            <th class="text-right">'.number_format($totBQty,2).'</th>
                            <th class="text-right"> - </th>
                            <th class="text-right">'.number_format($totalAmt,2).'</th>
                            <th class="text-right"> - </th>
                            <th class="text-right">'.number_format($totalAmtPrc,2).'</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Item Description</th>
                            <th class="text-right">Receipt Qty.</th>
                            <th class="text-right">Issued Qty.</th>
                            <th class="text-right">Balance Qty.</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Prc. Price</th>
                            <th class="text-right">Prc. Amt.</th>
                        </tr>';
            else:
                //$tbody .= '<tr style="text-align:center;"><td colspan="5">Data not found</td></tr>';
                $thead .= '<tr class="text-center">
                            <th colspan="7">Stock Register</th>
                        </tr>
                        <tr>
                            <th>#</th>
                            <th>Item Description</th>
                            <th>Receipt Qty.</th>
                            <th>Issued Qty.</th>
                            <th>Balance Qty.</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Prc. Price</th>
                            <th>Prc. Amt.</th>
                        </tr>';
            endif;
            $this->printJson(['status'=>1, 'thead'=>$thead ,'tbody'=>$tbody]);
        endif;
    }
    
    // Updated By Meghavi @01/02/2023
    public function getItemHistory($item_id,$location_id=""){

        $itemData = $this->store->getItemHistory($item_id,$location_id);

        $i=1; $tbody =""; $tfoot=""; $credit=0;$debit=0; $tcredit=0;$tdebit=0; $tbalance=0;
        foreach($itemData as $row):
                $credit=0;$debit=0;
                $transType = ($row->ref_type >= 0)?$this->data['stockTypes'][$row->ref_type] : "Opening Stock";
                if($row->trans_type == 1){ $credit = abs($row->qty);$tbalance +=abs($row->qty); } else { $debit = abs($row->qty);$tbalance -=abs($row->qty); }
                if($transType == 'Material Issue'){$row->ref_no = $row->batch_no;} 
                $party_name='-';
                if($row->ref_type == 2 || $row->ref_type == 5){
                        $partyData = $this->store->getTransMainData($row->ref_id);
                        $party_name = (!empty($partyData->party_name)) ? $partyData->party_name : '';
                }
                    $tbody .= '<tr>
                    <td>' . $i++ . '</td>
                    <td>'.$transType.' [ '.$row->location.']</td>
                    <td>'.$row->ref_no.'</td>
                    <td>'.formatDate($row->ref_date).'</td>
                    <td>'.floatVal(round($credit)).'</td>
                    <td>'.floatVal(round($debit)).'</td>
                    <td>'.floatVal(round($tbalance)).'</td>
                    <td>'.$party_name.'</td>
                </tr>';
                $tcredit += $credit; $tdebit += $debit;
        endforeach;
        $tfoot .= '<tr class="thead-info">
                <th colspan="4">Total</th>
                <th>' .floatVal(round($tcredit,2)). '</th>
                <th>' .floatVal(round($tdebit,2)). '</th>
                <th>' .floatVal(round($tbalance,2)). '</th>
                <th></th>
            </tr>';
        $this->data['itemId'] = $item_id;
        $this->data['tbody'] = $tbody;
        $this->data['tfoot'] = $tfoot;
        $this->data['itemId'] = $item_id;
        $this->load->view('store/item_history',$this->data);
    }

    public function itemStockTransfer($item_id=""){
		$this->data['itemId'] = $item_id;
		$this->data['itemId'] = $item_id;
		$this->data['itemId'] = $item_id;
		$this->data['itemId'] = $item_id;
        $this->load->view('store/stock_transfer',$this->data);
    }

    public function getstockTransferData(){
        $data = $this->input->post();
        $result = $this->store->getItemWiseStock($data);
        $this->printJson($result);
    }

    public function stockTransfer(){
        $this->data['dataRow'] = $this->input->post();
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->load->view('store/stock_transfer_form',$this->data);
    }

    public function saveStockTransfer(){
        $data = $this->input->post();
        $errorMessage = array();

        if(empty($data['to_location_id']))
            $errorMessage['to_location_id'] = "Store Location is required.";
        if(empty($data['transfer_qty']))
            $errorMessage['transfer_qty'] = "Qty is required.";

        if(!empty($errorMessage)):
            $this->printJson(['status'=>0,'message'=>'Some fields are required.','field_error'=>1,'field_error_message'=>$errorMessage]);
        else:
            $checkStock = $this->store->checkBatchWiseStock($data);
            if($checkStock->qty < $data['transfer_qty']):
                $this->printJson(['status'=>2,'message'=>'Stock not avalible.','stock_qty'=>$checkStock->qty]);
            else:
                $data['created_by'] = $this->session->userdata('loginId');
                $this->printJson($this->store->saveStockTransfer($data));
            endif;
        endif;
    }
}