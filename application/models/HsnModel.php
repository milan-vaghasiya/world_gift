<?php
class HsnModel extends MasterModel{
    private $hsn_master = "hsn_master";
   

    public function getDTRows($data){
        $data['tableName']= $this->hsn_master;

        $data['searchCol'][]="";
        $data['searchCol'][]="";
        $data['searchCol'][]="type";
        $data['searchCol'][]="gst_per";
        $data['searchCol'][]="igst";   
       
        $columns = array('','','type','gst_per','igst');
        if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function getHsn($id){
        $data['tableName'] = $this->hsn_master;
        $data['where']['id'] = $id;
        return $this->row($data);
    }

    public function save($data){
        $data['cgst'] = $data['sgst'] = $data['igst'] / 2;
        return $this->store($this->hsn_master,$data);
    }

    public function delete(){
        $id = $this->input->post('id');
        return $this->trash($this->hsn_master,['id'=>$id]);      
}
}
?>