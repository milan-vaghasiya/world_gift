<?php
class OffersModel extends MasterModel{
    private $offerMaster = "offers";

    public function getDTRows($data){
        $data['tableName'] = $this->offerMaster;
        $data['select'] = "offers.*,item_master.item_name";
        $data['leftJoin']['item_master'] = "FIND_IN_SET(`item_master`.`id`, `offers`.`item_id`)";
        
        $data['where']['DATE_FORMAT(offers.offer_date,"%Y-%m-%d") >='] = $this->startYearDate;
        $data['where']['DATE_FORMAT(offers.offer_date,"%Y-%m-%d") <='] = $this->endYearDate;

        $data['searchCol'][] = "DATE_FORMAT(offers.offer_date,'%d-%m-%Y')";
        $data['searchCol'][] = "offers.offer_title";
        $data['searchCol'][] = "DATE_FORMAT(offers.valid_from,'%d-%m-%Y')";
        $data['searchCol'][] = "DATE_FORMAT(offers.valid_to,'%d-%m-%Y')";
        $data['searchCol'][] = "item_master.item_name";
        $data['searchCol'][] = "offers.percentage";
        $data['searchCol'][] = "offers.amount";
        $data['searchCol'][] = "offers.remark";

		$columns =array('','','offers.offer_date','offers.offer_title','offers.valid_from','offers.valid_to','item_master.item_name','offers.percentage','offers.amount','offers.remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
		$result = $this->pagingRows($data);
        return $result;
    }

    public function getOffer($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->offerMaster;
        return $this->row($data);
    }

    public function save($data){
        $this->store($this->offerMaster,$data,'Offer');
        return ['status'=>1,'message'=>'Offer saved successfully.','url'=>base_url("offers")];
    }
    
    public function delete($id){
        return $this->trash($this->offerMaster,['id'=>$id],'Offer');
    }

    //Created By Karmi @31/03/2022
    public function getOfferItems($data){
        $offerData = array();
        foreach($data['items'] as $key=>$value){
            $queryData=[];
            $queryData['tableName'] = $this->offerMaster;
            $data['select'] = "offers.*";
            $queryData['where']['offers.valid_from <= '] = $data['inv_date'];
            $queryData['where']['offers.valid_to >= '] = $data['inv_date'];
            $queryData['customWhere'][] = "FIND_IN_SET('". $value."', offers.item_id)";
            $offerData = $this->rows($queryData);
        }

        $html="";
        if(!empty($offerData)):
            $i=1;
            foreach($offerData as $row):
               $itemData = $this->item->getItems($row->item_id);
               $z = 1; $items="";
               foreach($itemData as $itm):
                    if($z==1){ 
                        $items .= $itm->item_name;
                    }else{
                        $items .= ',<br> '.$itm->item_name;
                    }$z++;
                endforeach;
                
                
                $html .= '<tr>
                            <td class="text-center">
                                <input type="checkbox" id="md_checkbox_'.$i.'" name="chk_id[]" class="filled-in chk-col-success" value="'.$row->id.'" data-itmid="'.$row->item_id.'" ><label for="md_checkbox_'.$i.'" class="mr-3"></label>
                            </td>
                            <td class="text-center">'.$row->offer_title.'</td>
                            <td class="text-center">'.$items.'</td>
                            <td class="text-center">'.$row->percentage.'</td>
                            <td class="text-center">'.$row->amount.'</td>
                          </tr>';
                $i++;
            endforeach;
        else:
            $html = '<tr><td class="text-center" colspan="5">No Data Found</td></tr>';

        endif;
        return ['status'=>1,'htmlData'=>$html,'result'=>$offerData];
       
       
    }
}
?>