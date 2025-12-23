<?php
class ProductionLogModel extends MasterModel
{
    private $production_log = "production_log";
    private $itemMaster = "item_master";
    private $stockTransaction = "stock_transaction";
    private $stockTrans = "stock_transaction";

    public function getDTRows($data)
    {
        $data['tableName'] = $this->production_log;
        
        $data['searchCol'][] = "DATE_FORMAT(production_log.prd_date,'%d-%m-%Y')";
        $data['searchCol'][] = "production_log.trans_no";
        $data['searchCol'][] = "production_log.remark";
        $data['searchCol'][] = "production_log.total_rm_qty";
        $data['searchCol'][] = "production_log.total_fg_qty";

        $columns = array('', '', 'DATE_FORMAT(production_log.prd_date,"%d-%m-%Y")', 'production_log.trans_no', 'production_log.remark', 'production_log.total_rm_qty', 'production_log.total_fg_qty');
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getProductionDetail($id)
    {

        $data['tableName'] = $this->production_log;
        $data['select'] = 'production_log.*';
        $data['where']['production_log.id'] = $id;
        return $this->row($data);
    }

    public function save($masterdata, $itemData, $redirect_url = "productionLog"){ //print_r($itemData); exit;
        if (!empty($masterdata['id'])) :
            /*$transDataResult = $this->getItemTransactions($masterdata['id']);
            foreach ($transDataResult as $row) :
                if (!in_array($row->id, $itemData['id'])) :
                    $this->remove($this->stockTrans, ['id' => $row->id]);
                endif;
            endforeach;*/
            $this->trash($this->stockTransaction, ['ref_id' => $masterdata['id'], 'ref_type' => 7]);
        endif;

        $result = $this->store($this->production_log, $masterdata);

        $id = (!empty($masterdata['id'])) ? $masterdata['id'] : $result['insert_id'];

        foreach ($itemData['item_id'] as $key => $value) :
            $stockTrans = [
                'id' => (!empty($itemData['id'][$key]))?$itemData['id'][$key]:'',
                'location_id' => $itemData['location_id'][$key],
                'trans_type' => ($itemData['item_type'][$key] == 1) ? 1 : 2,
                'item_id' => $value,
                'qty' => (($itemData['item_type'][$key] == 1) ? '' : '-') . $itemData['qty'][$key],
                'ref_type' => 7,
                'ref_id' => $id,
                'ref_no' => $masterdata['trans_no'],
                'ref_date' => $masterdata['prd_date'],
                'created_by' => $masterdata['created_by'],
                'is_delete' => 0
            ];
            $this->store('stock_transaction', $stockTrans);
            
            /*if (empty($itemData['id'][$key])) :
                
            else:
                $stockTrans = [
                    'id' => $itemData['id'][$key],
                    // 'location_id' => $itemData['location_id'][$key],
                    // 'trans_type' => ($itemData['item_type'][$key] == 1) ? 1 : 2,
                    // 'item_id' => $value,
                    // 'qty' => (($itemData['item_type'][$key] == 1) ? '' : '-') . $itemData['qty'][$key],
                    // 'ref_type' => 7,
                    // 'ref_id' => $id,
                    // 'ref_no' => $masterdata['trans_no'],
                    'ref_date' => $masterdata['prd_date'],
                    'created_by' => $masterdata['created_by']
                ];
                $this->store('stock_transaction', $stockTrans);
            endif;*/
        endforeach;

        $result = ['status' => 1, 'message' => "Data Save successfully.", 'url' => base_url($redirect_url)];
        return $result;
    }
    
    public function delete($id)
    {
        $this->remove($this->stockTransaction, ['ref_id' => $id, 'ref_type' => 7]);
        return $this->trash($this->production_log, ['id' => $id], 'Production Log');
    }

    public function getItemTransactions($id)
    {
        $data['tableName'] = "stock_transaction";
        $data['select'] = "stock_transaction.*,location_master.store_name,location_master.location,item_master.item_name,,item_master.price1,,item_master.price2,item_master.size,item_master.item_image";
        $data['leftJoin']['location_master'] = 'location_master.id = stock_transaction.location_id';
        $data['leftJoin']['item_master'] = 'item_master.id = stock_transaction.item_id';
        $data['where']['stock_transaction.ref_id'] = $id;
        $data['where']['stock_transaction.ref_type'] = 7;
        return $this->rows($data);
    }

    public function getNextTransNo()
    {
        $data['select'] = "MAX(trans_no) as trans_no";
        $data['tableName'] = "production_log";
        $trans_no = $this->specificRow($data)->trans_no;
        $nextTransNo = (!empty($trans_no)) ? ($trans_no + 1) : 1;
        return $nextTransNo;
    }

    public function getItemStockTransactions($id)
    {
        $data['tableName'] = "stock_transaction";
        $data['select'] = "stock_transaction.*";
        $data['where']['stock_transaction.id'] = $id;
        return $this->row($data);
    }
    
    //Created By MEGF @5/03/2022
    public function getItemListForTag($id) {
        $queryData['tableName'] = $this->stockTransaction;
        $queryData['where']['ref_type'] = '7';
        $queryData['where']['ref_id'] = $id;
        $queryData['where']['trans_type'] = '1';
        return $this->rows($queryData); 
    }
}
