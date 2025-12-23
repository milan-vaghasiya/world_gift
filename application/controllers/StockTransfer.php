<?php
class StockTransfer extends MY_Controller
{
    private $indexPage = "stock_transfer/index";

    public function __construct()
    {
        parent::__construct();
        $this->isLoggedin();
        $this->data['headData']->pageTitle = "Stock Transfer";
        $this->data['headData']->controller = "stockTransfer";
        $this->data['headData']->pageUrl = "stockTransfer";
    }

    public function index()
    {
        $this->data['tableHeader'] = getStoreDtHeader($this->data['headData']->controller);
        $this->data['categoryList'] = $this->item->getCategoryList(1);
        $this->load->view($this->indexPage, $this->data);
    }

    public function getDTRows($type)
    {
        $result = $this->store->getStoreTranfDTRows($this->input->post(), $type);
        $sendData = array();
        $i = 1;
        $count = 0;
        foreach ($result['data'] as $row) :
            $row->sr_no = $i++;
            $sendData[] = getStockTransferData($row);
        endforeach;
        $result['data'] = $sendData;
        $this->printJson($result);
    }

    public function stockTransfer()
    {
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['nextTransNo'] = $this->store->getNextTransNo();
        $this->load->view('stock_transfer/stock_transfer_form', $this->data);
    }
    public function saveStockTransfer()
    {
        $data = $this->input->post();
        //print_r($data);exit;
        $errorMessage = array();


        if (empty($data['item_id'][0]))
            $errorMessage['item_name_error'] = "Product is required.";

        if (empty($data['to_location_id']))
            $errorMessage['to_location_id'] = "Location is required.";

        if (!empty($errorMessage)) :
            $this->printJson(['status' => 0, 'message' => 'Some fields are required.', 'field_error' => 1, 'field_error_message' => $errorMessage]);
        else :
            $masterData = [
                'id' => $data['id'],
                'trans_no' => $data['trans_no'],
                'trans_date' => $data['trans_date'],
                'doc_no' => $data['doc_no'],
                'doc_date' => $data['doc_date'],
                'from_location_id' => $data['from_location_id'],
                'to_location_id' => $data['to_location_id'],
                'remark' => $data['remark'],
                'created_by' => $this->session->userdata('loginId')
            ];

            $itemData = [
                'id' => $data['trans_id'],
                'item_id' => $data['item_id'],
                'qty' => $data['qty']
            ];

            $result = $this->store->saveStockTransferMultiple($masterData, $itemData);
            $this->printJson($result);
        endif;
    }

    public function edit($id){
        $this->data['locationData'] = $this->store->getStoreLocationList();
        $this->data['dataRow'] = $this->store->getStockTransferLog($id);
        $this->load->view('stock_transfer/stock_transfer_form', $this->data);
    }

    public function delete(){
        $id = $this->input->post('id');
        if(empty($id)):
            $this->printJson(['status'=>0,'message'=>'Somthing went wrong...Please try again.','field_error'=>0,'field_error_message'=>NULL]);
        else:
            $this->printJson($this->store->deleteStockTransfer($id));
        endif;
    }
}
