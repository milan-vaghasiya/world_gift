<?php
class StockVerification extends MY_Apicontroller{
    public function __construct(){
        parent::__construct();
    }

    public function save(){
        $data = $this->input->post();
        
        if(empty($data['item_data'])):
            $this->printJson(['status'=>0,'message'=>'Please select items.','field_error'=>0,'field_error_message'=>null,'data'=>null]);
        endif;

        $this->printJson($this->stockVerify->save($data));
    }
}
?>