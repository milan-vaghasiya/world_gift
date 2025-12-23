<?php
class ItemCategoryModel extends MasterModel{
    private $itemCategory = "item_category";
    private $toolType = "tool_types";
    
	public function getDTRows($data){
        $data['tableName'] = $this->itemCategory;
        $data['select'] = 'item_category.*';
        $data['where']['item_category.ref_id'] = 0;

        $data['searchCol'][] = "item_category.category_name";
        $data['searchCol'][] = "item_category.remark";
        
		$columns =array('','','category_name','remark');
		if(isset($data['order'])){$data['order_by'][$columns[$data['order'][0]['column']]] = $data['order'][0]['dir'];}
        return $this->pagingRows($data);
    }

    public function mainCategoryList(){
        $data['tableName'] = $this->itemCategory;
        $data['where']['final_category'] = 0;
        $data['order_by']['category_level'] = 'ASC';
        return $this->rows($data);
    }
    
    public function categoryList(){
        $data['tableName'] = $this->itemCategory;
        $data['where']['final_category'] = 1;
        $data['order_by']['category_level'] = 'ASC';
        return $this->rows($data);
    }
    
    public function getCategoryList($type=0){
		if(!empty($type)){$data['where']['ref_id'] = $type;}
        $data['where']['final_category'] = 1;
        $data['tableName'] = $this->itemCategory;
        return $this->rows($data);
    }

    public function getCategory($id){
        $data['where']['id'] = $id;
        $data['tableName'] = $this->itemCategory;
        return $this->row($data);
    }

    public function save($data){
        if($this->checkDuplicate($data['category_name'],$data['id']) > 0):
            $errorMessage['category_name'] = "Category Name is duplicate.";
            return ['status'=>0,'message'=>$errorMessage];
        else:
            return $this->store($this->itemCategory,$data,'Item Category');
        endif;
    }

    public function checkDuplicate($name,$id=""){
        $data['tableName'] = $this->itemCategory;
        $data['where']['category_name'] = $name;
        
        if(!empty($id))
            $data['where']['id !='] = $id;
        return $this->numRows($data);
    }

    public function delete($id){
        return $this->trash($this->itemCategory,['id'=>$id],'Item Category');
    }
    
    /* 
        Create By : Karmi @03-12-2021 
        update by : 
        note :
    */
    public function getToolTypeList(){
        $data['tableName'] = $this->toolType;
        return $this->rows($data);
    }

    /**
     * Created By Mansee 03-12-2021
     */
    public function getCategoryByToolType($tool_type)
    {
        $data['where']['tool_type'] = $tool_type;
        $data['tableName'] = $this->itemCategory; 
        return $this->rows($data);
    }

    public function getNextCategoryLevel($ref_id){
        $data['tableName'] = $this->itemCategory;
        $data['where']['ref_id'] = $ref_id;
        return $this->rows($data);
    }

    //Created By Karmi @25/02/2022
    public function getSubCategory($id)
    {
        $data['where']['ref_id'] = $id;
        $data['tableName'] = $this->itemCategory; 
        $result= $this->rows($data);
        return $result;
    }
}
?>