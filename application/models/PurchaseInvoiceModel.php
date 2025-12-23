<?php
class PurchaseInvoiceModel extends MasterModel
{
    private $transMain = "trans_main";
    private $transChild = "trans_child";
    private $grnMain = "grn_master";
    private $grnTrans = "grn_transaction";
    private $itemMaster = "item_master";
    private $stockTrans = "stock_transaction";
    private $purchaseOrderTrans = "purchase_order_trans";


    public function getDTRows($data)
    {
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,party_master.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.net_amount, trans_main.doc_no,trans_main.ref_id';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['leftJoin']['party_master'] = "party_master.id = trans_main.party_id";
        $data['where']['trans_child.entry_type'] = 12;
        $data['where']['trans_main.cm_id'] = $this->CMID;
        if(!empty($data['from_date']) AND !empty($data['to_date'])){$data['customWhere'][] = "trans_main.trans_date BETWEEN '".$data['from_date']."' AND '".$data['to_date']."'"; }
        else{ 
            $data['where']['trans_main.trans_date >='] = $this->startYearDate;
            $data['where']['trans_main.trans_date <='] = $this->endYearDate;
        }
        $data['group_by'][] = 'trans_child.trans_main_id';
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";

        $data['searchCol'][] = "trans_main.doc_no";
        $data['searchCol'][] = "DATE_FORMAT(trans_main.trans_date,'%d-%m-%Y')";
        $data['searchCol'][] = "party_master.party_name";
        $data['searchCol'][] = "trans_child.net_amount";

        $columns = array('', '', 'trans_main.doc_no', 'trans_main.trans_date', '', '', 'party_master.party_name', 'trans_child.item_name', 'trans_main.net_amount');
        if (isset($data['order'])) {
            $data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];
        }
        return $this->pagingRows($data);
    }

    public function getPartyOrders($id)
    {
        $queryData['tableName'] = 'purchase_order_master'; //$this->grnMain;
        $queryData['select'] = "id,po_prefix,po_no,po_date";
        $queryData['where']['order_status'] = 0;
        //$queryData['where']['is_approve != '] = 0;
        $queryData['where']['party_id'] = $id;
        $resultData = $this->rows($queryData);

        $html = "";
        if (!empty($resultData)) :
            $i = 1;
            foreach ($resultData as $row) :

                $partCode = array();
                $qty = array();
                $rqty = array();
                $partData = $this->getPoTransactions($row->id);
                foreach ($partData as $part) :
                    $partCode[] = $part->item_name;
                    $qty[] = floatval($part->qty);
                    if (($part->qty - $part->rec_qty) > 0) {
                        $rqty[] = $part->qty - $part->rec_qty;
                    }

                endforeach;

                $part_code = implode(",<br> ", $partCode);
                $part_qty = implode(",<br> ", $qty);
                $part_rqty = implode(",<br> ", $rqty);
                if ($part_rqty != '') :
                    $html .= '<tr>
                                <td class="text-center">
                                    <input type="checkbox" id="md_checkbox_' . $i . '" name="ref_id[]" class="filled-in chk-col-success" value="' . $row->id . '" ><label for="md_checkbox_' . $i . '" class="mr-3 check' . $row->id . '"></label>
                                </td>
                                <td class="text-center">' . getPrefixNumber($row->po_prefix, $row->po_no) . '</td>
                                <!-- <td class="text-center"></td> -->
                                <td class="text-center">' . formatDate($row->po_date) . '</td>
                                <td class="text-center">' . $part_code . '</td>
                                <td class="text-center">' . $part_rqty . '</td>
                            </tr>';
                    $i++;
                // else:
                //     $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
                endif;

            endforeach;
        else :
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status' => 1, 'htmlData' => $html, 'result' => $resultData];
    }

    public function getPoTransactions($order_id)
    {
        $queryData['tableName'] = 'purchase_order_trans'; //$this->grnTrans;
        $queryData['select'] = "purchase_order_trans.*,item_master.item_name";
        $queryData['join']['item_master'] = "purchase_order_trans.item_id = item_master.id";
        $queryData['where']['purchase_order_trans.order_id'] = $order_id;
        return $this->rows($queryData);
    }

    public function getPoItemsForInvoice($poIds)
    {
        $data['tableName'] = 'purchase_order_trans'; //$this->grnTrans;
        $data['select'] = "purchase_order_trans.*,item_master.item_name,item_master.item_code,unit_master.unit_name,item_master.item_type, item_master.gst_per";
        $data['join']['item_master'] = "item_master.id = purchase_order_trans.item_id";
        $data['join']['unit_master'] = "unit_master.id = purchase_order_trans.unit_id";
        $data['where_in']['purchase_order_trans.order_id'] = $poIds;
        $data['where']['purchase_order_trans.cm_id'] = $this->CMID;
        $data['where']['purchase_order_trans.order_status'] = 0;
        return $this->rows($data);
    }

    public function checkDuplicateINV($party_id, $inv_no, $id)
    {
        $data['tableName'] = $this->transMain;
        $data['where']['doc_no'] = $inv_no;
        $data['where']['party_id'] = $party_id;
        $data['where']['entry_type'] = 12;
        $data['where']['trans_date >='] = $this->startYearDate;
        $data['where']['trans_date <='] = $this->endYearDate;
        if (!empty($id))
            $data['where']['id != '] = $id;

        return $this->numRows($data);
    }

    public function getPoTransactionRow($id)
    {
        $data['tableName'] = $this->purchaseOrderTrans;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($masterData, $itemData, $expenseData)
    {
        try {
            $this->db->trans_begin();
            $purchaseId = $masterData['id'];
            $masterData['cm_id'] = (isset($masterData['cm_id']))?$masterData['cm_id']:$this->CMID;

            $checkDuplicate = $this->checkDuplicateINV($masterData['party_id'], $masterData['doc_no'], $purchaseId);
            if ($checkDuplicate > 0) :
                $errorMessage['doc_no'] = "Invoice No. is Duplicate.";
                return ['status' => 0, 'message' => "Some fields are duplicate.", 'field_error' => 1, 'field_error_message' => $errorMessage];
            else :
                //save purchase master data
                $purchaseInvSave = $this->store($this->transMain, $masterData);
                $purId = (empty($purchaseId)) ? $purchaseInvSave['insert_id'] : $masterData['id'];
                $masterData['id'] = $purId;

                if (!empty($purchaseId)) :
                    //purchaseTransactions
                    $transDataResult = $this->purchaseTransactions($purchaseId,$masterData['cm_id']);
                    foreach ($transDataResult as $row) :
                        if ($row->stock_eff == 1) :
						
                            /** Update Item Stock **/
                            $setData = array();
                            $setData['tableName'] = $this->itemMaster;
                            $setData['where']['id'] = $row->item_id;
                            $setData['set']['qty'] = 'qty, - ' . $row->qty;
                            $qryresult = $this->setValue($setData);

                            /** Remove Stock Transaction **/
                            $this->remove($this->stockTrans, ['ref_id' => $purchaseId, 'trans_ref_id' => $row->id, 'trans_type' => 1, 'ref_type' => 2]);
                        endif;

                        $this->trash($this->transChild, ['id' => $row->id,'cm_id'=>$masterData['cm_id']]);
                    endforeach;
                endif;

                //save purchase items
                foreach ($itemData['item_id'] as $key => $value) :
                    $batch_qty = array();
                    $batch_no = array();
                    $location_id = array();
                    $batch_qty[] = $itemData['qty'][$key];
                    $batch_no[] = (!empty($itemData['batch_no'][$key])) ? $itemData['batch_no'][$key] : "General Batch";
                    $location_id[] = $itemData['location_id'][$key];
                    $itemData['stock_eff'][$key] = 1;
                    $transData = [
                        'id' => $itemData['id'][$key],
                        'trans_main_id' => $purId,
                        'entry_type' => $masterData['entry_type'],
                        'currency' => $masterData['currency'],
                        'inrrate' => $masterData['inrrate'],
                        'from_entry_type' => $itemData['from_entry_type'][$key],
                        'ref_id' => $itemData['ref_id'][$key],
                        'item_id' => $value,
                        'item_name' => $itemData['item_name'][$key],
                        'item_type' => $itemData['item_type'][$key],
                        'item_code' => $itemData['item_code'][$key],
                        'item_desc' => $itemData['item_desc'][$key],
                        'unit_id' => $itemData['unit_id'][$key],
                        'unit_name' => $itemData['unit_name'][$key],
                        'location_id' => $itemData['location_id'][$key],
                        'batch_no' => (!empty($itemData['batch_no'][$key])) ? $itemData['batch_no'][$key] : "General Batch",
                        'hsn_code' => $itemData['hsn_code'][$key],
                        'qty' => $itemData['qty'][$key],
                        'p_or_m' => 1,
                        'stock_eff' => $itemData['stock_eff'][$key],
                        'price' => $itemData['price'][$key],
                        'amount' => $itemData['amount'][$key] + $itemData['disc_amount'][$key],
                        'taxable_amount' => $itemData['taxable_amount'][$key],
                        'gst_per' => $itemData['gst_per'][$key],
                        'gst_amount' => $itemData['igst_amount'][$key],
                        'igst_per' => $itemData['igst_per'][$key],
                        'igst_amount' => $itemData['igst_amount'][$key],
                        'cgst_per' => $itemData['cgst_per'][$key],
                        'cgst_amount' => $itemData['cgst_amount'][$key],
                        'sgst_per' => $itemData['sgst_per'][$key],
                        'sgst_amount' => $itemData['sgst_amount'][$key],
                        'disc_per' => $itemData['disc_per'][$key],
                        'disc_amount' => $itemData['disc_amount'][$key],
                        'item_remark' => $itemData['item_remark'][$key],
                        'net_amount' => $itemData['net_amount'][$key],
                        'created_by' => $masterData['created_by'],
                        'is_delete' => 0,
                        'cm_id' => $masterData['cm_id']
                    ];
                    $saveTrans = $this->store($this->transChild, $transData);

                    $transRefId = (empty($itemData['id'][$key])) ? $saveTrans['insert_id'] : $itemData['id'][$key];

                    if ($itemData['stock_eff'][$key] == 1) :
                        /** Update Item Stock **/
                        $setData = array();
                        $setData['tableName'] = $this->itemMaster;
                        $setData['where']['id'] = $itemData['item_id'][$key];
                        $setData['set']['qty'] = 'qty, + ' . $itemData['qty'][$key];
                        $this->setValue($setData);

                        /*** UPDATE STOCK TRANSACTION DATA ***/
                        foreach ($batch_qty as $bk => $bv) :
                            $stockQueryData['id'] = "";
                            $stockQueryData['location_id'] = $location_id[$bk];
                            if (!empty($batch_no[$bk])) {
                                $stockQueryData['batch_no'] = $batch_no[$bk];
                            }
                            $stockQueryData['trans_type'] = 1;
                            $stockQueryData['item_id'] = $itemData['item_id'][$key];
                            $stockQueryData['qty'] = $bv;
                            $stockQueryData['ref_type'] = 2;
                            $stockQueryData['ref_id'] = $purId;
                            $stockQueryData['ref_no'] = $masterData['trans_number'];
                            $stockQueryData['trans_ref_id'] = $transRefId;
                            $stockQueryData['ref_date'] = $masterData['trans_date'];
                            $stockQueryData['created_by'] = $masterData['created_by'];
                            $stockQueryData['cm_id'] = $masterData['cm_id'];
                            $this->store($this->stockTrans, $stockQueryData);
                        endforeach;
                    endif;

                    if (!empty($itemData['ref_id'][$key])) :
                        $setData = array();
                        $setData['tableName'] = $this->purchaseOrderTrans;
                        $setData['where']['id'] = $itemData['ref_id'][$key];
                        $setData['set']['rec_qty'] = 'rec_qty, + ' . $itemData['qty'][$key];
                        $qryresult = $this->setValue($setData);

                        /** If Po Order Qty is Complete then Close PO **/
                        $poTrans = $this->getPoTransactionRow($itemData['ref_id'][$key]);
                        if ($poTrans->rec_qty >= $poTrans->qty) :
                            $this->store($this->purchaseOrderTrans, ["id" => $itemData['ref_id'][$key], "order_status" => 1]);
                        else :
                            $this->store($this->purchaseOrderTrans, ["id" => $itemData['ref_id'][$key], "order_status" => 0]);
                        endif;
                    endif;
                endforeach;

                //Ledger Effect
                $this->transModel->ledgerEffects($masterData, $expenseData);

                $result = ['status' => 1, 'message' => 'Purchase Invoice saved successfully.', 'url' => base_url("purchaseInvoice"), 'field_error' => 0, 'field_error_message' => null];
            endif;

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(), 'field_error' => 0, 'field_error_message' => null];
        }
    }

    public function getInvoice($id)
    {
        $queryData['tableName'] = $this->transMain;
        $queryData['where']['id'] = $id;
        $invoiceData = $this->row($queryData);
        $invoiceData->itemData = $this->purchaseTransactions($id);

        $queryData = array();
        $queryData['tableName'] = "trans_expense";
        $queryData['where']['trans_main_id'] = $id;
        $invoiceData->expenseData = $this->row($queryData);
        return $invoiceData;
    }

    public function purchaseTransactions($id,$cm_id = 0)
    {
        $queryData['tableName'] = $this->transChild;
        $queryData['where']['trans_main_id'] = $id;
        return $this->rows($queryData,$cm_id);
    }

    public function delete($id)
    {
        try {
            $this->db->trans_begin();
            $invoiceData = $this->getInvoice($id);

            foreach ($invoiceData->itemData as $row) :
                if ($row->stock_eff == 1) :
                    /** Update Item Stock **/
                    $setData = array();
                    $setData['tableName'] = $this->itemMaster;
                    $setData['where']['id'] = $row->item_id;
                    $setData['set']['qty'] = 'qty, - ' . $row->qty;
                    $qryresult = $this->setValue($setData);

                    /** Remove Stock Transaction **/
                    $this->remove($this->stockTrans, ['ref_id' => $id, 'trans_ref_id' => $row->id, 'trans_type' => 1, 'ref_type' => 2]);
                endif;
                $this->trash($this->transChild, ['id' => $row->id]);

                if(!empty($row->ref_id)):
                    $setData = array();
                    $setData['tableName'] = $this->purchaseOrderTrans;
                    $setData['where']['id'] = $row->ref_id;
                    $setData['set']['rec_qty'] = 'rec_qty, - ' . $row->qty;
                    $qryresult = $this->setValue($setData);
                    $this->store($this->purchaseOrderTrans, ['id' => $row->ref_id, 'order_status' => 0]);
                endif;
            endforeach;
            $result = $this->trash($this->transMain, ['id' => $id], 'Purchase Invoice');

            //Ledger Effect
            $this->transModel->deleteLedgerTrans($id);
            $this->transModel->deleteExpenseTrans($id);

            if ($this->db->trans_status() !== FALSE) :
                $this->db->trans_commit();
                return $result;
            endif;
        } catch (\Exception $e) {
            $this->db->trans_rollback();
            return ['status' => 0, 'message' => "somthing is wrong. Error : " . $e->getMessage(), 'field_error' => 0, 'field_error_message' => null];
        }
    }

    public function getItemList($id)
    {
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name,trans_child.price,trans_child.amount";
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
        //print_r($queryData);exit;
        $resultData = $this->rows($queryData);
        $html = "";
        if (!empty($resultData)) :
            $i = 1;
            foreach ($resultData as $row) :
                $html .= '<tr>
                            <td class="text-center">' . $i . '</td>
                            <td class="text-center">' . $row->item_name . '</td>
                            <td class="text-center">' . $row->hsn_code . '</td>
                            <td class="text-center">' . $row->igst_per . '</td>
                            <td class="text-center">' . $row->qty . '</td>
                            <td class="text-center">' . $row->unit_name . '</td>
                            <td class="text-center">' . $row->price . '</td>
                            <td class="text-center">' . $row->amount . '</td>
                          </tr>';
                $i++;
            endforeach;
        else :
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';
        endif;
        return ['status' => 1, 'htmlData' => $html, 'result' => $resultData];
    }

    public function getPurchaseInvoiceList($party_id)
    {
        $data['tableName'] = $this->transMain;
        $data['where']['party_id'] = $party_id;
        $data['where']['entry_type'] = 12;
        return $this->rows($data);
    }

    public function checkGrnPendingStatus($grn_id)
    {
        $data['select'] = "COUNT(trans_status) as orderStatus";
        $data['where']['grn_id'] = $grn_id;
        $data['where']['trans_status'] = 0;
        $data['tableName'] = $this->grnTrans;
        return $this->specificRow($data)->orderStatus;
    }

    //Created By Avruti @5/03/2022
    public function getItemListForTag($id)
    {
        $queryData['tableName'] = $this->transChild;
        $queryData['select'] = "trans_child.item_name,trans_child.item_id,trans_child.hsn_code,trans_child.igst_per,trans_child.qty,trans_child.unit_name,trans_child.price,trans_child.amount";
        $queryData['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $queryData['where']['trans_main.id'] = $id;
        $resultData = $this->rows($queryData);
        return $resultData;
    }

    /*  Create By : Avruti @29-11-2021 4:00 PM
        update by : 
        note : 
    */
    //---------------- API Code Start ------//

    public function getCount($type = 0)
    {
        $data['tableName'] = $this->transChild;
        $data['where']['trans_child.entry_type'] = 12;
        $data['where']['cm_id'] = $this->CMID;
        return $this->numRows($data);
    }

    public function getPurchaseInvoiceList_api($limit, $start, $type = 0)
    {
        $data['tableName'] = $this->transChild;
        $data['select'] = 'trans_child.id,trans_child.trans_main_id,trans_child.item_name,trans_child.trans_status,trans_child.qty,trans_child.dispatch_qty, trans_child.cod_date,trans_main.trans_prefix,trans_main.trans_no,trans_main.trans_date,trans_main.party_id,trans_main.party_name,trans_main.remark,trans_main.ref_by,trans_main.order_type,trans_main.net_amount';
        $data['join']['trans_main'] = "trans_main.id = trans_child.trans_main_id";
        $data['where']['trans_child.entry_type'] = 12;
        $data['where']['trans_child.cm_id'] = $this->CMID;
        $data['group_by'][] = 'trans_child.trans_main_id';
        $data['order_by']['trans_main.trans_date'] = "DESC";
        $data['order_by']['trans_main.id'] = "DESC";
        $data['length'] = $limit;
        $data['start'] = $start;
        return $this->rows($data);
    }

    //------ API Code End -------//
}
