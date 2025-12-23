<?php /* Master Modal Ver. : 1.1  */
class MasterModel extends CI_Model{

    /* Get Paging Rows */
    public function pagingRows($data){
        $draw = $data['draw'];
		$start = $data['start'];
		$rowperpage = $data['length']; // Rows display per page
		$searchValue = $data['search']['value'];

        // Total Records without Filtering
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;
		
		$this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value);
                endforeach;
            endif;
        endif;

        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;
        
        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
		
        $totalRecords = $this->db->get($data['tableName'])->num_rows();
        
        // Total Records with Filtering
		if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;
		$this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
		
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value);
                endforeach;
            endif;
        endif;
        
        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;
        
		if(!empty($searchValue)):
            if(isset($data['searchCol'])):
                if(!empty($data['searchCol'])):
                    $this->db->group_start();
                    foreach($data['searchCol'] as $key=>$value):
                        if($key == 0):
                            $this->db->like($value,$searchValue);
                        else:
                            $this->db->or_like($value,$searchValue);
                        endif;
                    endforeach;
                    $this->db->group_end();
                endif;
            endif;
		endif;
		$totalRecordwithFilter = $this->db->get($data['tableName'])->num_rows();
        //print_r($this->db->last_query());exit;

        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;  
        
        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;
		
		$this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        
        if(!(isset($data['where'])) OR (isset($data['where']) AND !(array_key_exists($data['tableName'].'.is_delete',$data['where'])))):
			$this->db->where($data['tableName'].'.is_delete',0);
		endif;
        
    
        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;
        
        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;

        if(!empty($searchValue)):
            if(isset($data['searchCol'])):
                if(!empty($data['searchCol'])):
                    $this->db->group_start();
                    foreach($data['searchCol'] as $key=>$value):
                        if($key == 0):
                            $this->db->like($value,$searchValue);
                        else:
                            $this->db->or_like($value,$searchValue);
                        endif;
                    endforeach;
                    $this->db->group_end();
                endif;
            endif;
		endif;
        
        // Column Search
    	if(isset($data['searchCol'])):
    		if(!empty($data['searchCol'])):
    			foreach($data['searchCol'] as $key=>$value):
    				if(!empty($value)){
    					$csearch = $data['columns'][$key]['search']['value'];
    					if(!empty($csearch)){$this->db->like($value,$csearch);}
    				}
    			endforeach;
    		endif;
    	endif;
    	
        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;

        $resultData = $this->db->limit($rowperpage, $start)->get($data['tableName'])->result();
        //print_r($this->db->last_query());  
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordwithFilter,
            "data" => $resultData
        ];
        return $response;
    }

    /* Get All Rows */
    public function rows($data,$cm_id=0){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        if(!empty($cm_id)):
            $this->db->where_in($data['tableName'].'.cm_id',[$cm_id,0]);
        else:
		    $this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;

        if(isset($data['whereFalse'])):
            if(!empty($data['whereFalse'])):
                foreach($data['whereFalse'] as $key=>$value):
                    $this->db->where($key,$value,false); 
                endforeach;
            endif;            
        endif;
		
        if (isset($data['having'])) :
            if (!empty($data['having'])) :
                foreach ($data['having'] as $value) :
                    $this->db->having($value);
                endforeach;
            endif;
        endif;
        
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value,false);
                endforeach;
            endif;
        endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
		if(isset($data['limit'])):
            if(!empty($data['limit'])):
                $this->db->limit($data['limit']);
            endif;
        endif;
        
        if(isset($data['start']) && isset($data['length'])):
            if(!empty($data['length'])):
                $this->db->limit($data['length'],$data['start']);
            endif;
        endif;
        
        $result = $this->db->get($data['tableName'])->result();
        //print_r($this->db->last_query());
        return $result;
    }

    /* Get Single Row */
    public function row($data,$cm_id = 0){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

		if(!empty($cm_id)):
            $this->db->where_in($data['tableName'].'.cm_id',[$cm_id,0]);
        else:
		    $this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);
        endif;

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['whereFalse'])):
            if(!empty($data['whereFalse'])):
                foreach($data['whereFalse'] as $key=>$value):
                    $this->db->where($key,$value,false); 
                endforeach;
            endif;            
        endif;

        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
		
        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value);
                endforeach;
            endif;
        endif;
		
		$this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
		
		$result = $this->db->get($data['tableName'])->row();
        return $result;
    }

    public function customRow($data,$cm_id = 0){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;

        foreach($data['where'] as $key=>$value):
            $this->db->where($key,$value);
        endforeach;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;

		if(!empty($cm_id)):
            $this->db->where_in($data['tableName'].'.cm_id',[$cm_id,0]);
        else:
		    $this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);
        endif;
		
        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value);
                endforeach;
            endif;
        endif;
		
		$result = $this->db->get($data['tableName'])->row();
        return $result;
    }

    /* Save and Update Row */
    public function store($tableName,$data,$msg = "Record"){
        $id = $data['id'];
        unset($data['id']);
		
        if(empty($id)):
			// $data['cm_id'] = $this->CMID;
            $data['created_at'] = date('Y-m-d H:i:s');
            if(!isset($data['created_by'])):
                $data['created_by'] = $this->loginId;
            endif;
			if(!isset($data['cm_id'])):
				$data['cm_id'] = $this->CMID;  
			endif;
			
            $this->db->insert($tableName,$data);
            $insert_id = $this->db->insert_id();
            
            $result = ['status'=>1,'message'=>$msg." saved Successfully.",'field_error'=>0,'field_error_message'=>null,'insert_id'=>$insert_id];
            if(DEVELOPMENT == 1):
                $result['query'] = $this->db->last_query();
            endif;
            return $result;
        else:
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['updated_by'] = $this->loginId;
            if(!isset($data['updated_by'])):
                $data['updated_by'] = $this->loginId;
            endif;
            $this->db->where('id',$id);
            $this->db->update($tableName,$data);
            
            $result = ['status'=>1,'message'=>$msg." updated Successfully.",'field_error'=>0,'field_error_message'=>null,'insert_id'=>-1];
            if(DEVELOPMENT == 1):
                $result['query'] = $this->db->last_query();
            endif;
            return $result;
        endif;
    }

    /* Update Row */
    public function edit($tableName,$where,$data,$msg = "Record"){
        $data['updated_at'] = date('Y-m-d H:i:s');
        if(!isset($data['updated_by'])):
            $data['updated_by'] = $this->loginId;
        endif;
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        $this->db->update($tableName,$data);
        return ['status'=>1,'message'=>$msg." updated Successfully.",'field_error'=>0,'field_error_message'=>null,'insert_id'=>-1];
    }

    /* Update Row */
    public function editCustom($tableName,$customWhere,$data,$where=Array()){
        $data['updated_at'] = date('Y-m-d H:i:s');
        if(!isset($data['updated_by'])):
            $data['updated_by'] = $this->loginId;
        endif;
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
		if(isset($customWhere)):
            if(!empty($customWhere)):
                foreach($customWhere as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->update($tableName,$data);
        return ['status'=>1,'message'=>"Record updated Successfully.",'field_error'=>0,'field_error_message'=>null,'insert_id'=>-1];
    }

    /* Get Numbers of Rows */
    public function numRows($data,$deleteCheck=1){
        if(!empty($data['where'])):
            foreach($data['where'] as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
		$this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);
        if(!empty($deleteCheck)){$this->db->where($data['tableName'].'.is_delete',0);}
        return $this->db->get($data['tableName'])->num_rows();
    }

    /* Set Deleteed Flage */
    public function trash($tableName,$where,$msg = "Record"){
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
        /* if(!isset($where['cm_id'])):
		    $this->db->where_in('cm_id',[$this->CMID,0]);
        endif; */
        $this->db->update($tableName,['is_delete'=>1]);
        return ['status'=>1,'message'=>$msg." deleted Successfully.",'field_error'=>0,'field_error_message'=>null];
    }

    /* Delete Recored Permanent */
    public function remove($tableName,$where,$msg = ""){
        if(!empty($where)):
            foreach($where as $key=>$value):
                $this->db->where($key,$value);
            endforeach;
        endif;
		//$this->db->where_in('cm_id',[$this->CMID,0]);
        $this->db->delete($tableName);
        return ['status'=>1,'message'=>$msg." deleted Successfully.",'field_error'=>0,'field_error_message'=>null];
    }

    /* Get Specific Row. Like : SUM,MAX,MIN,COUNT ect... */
    public function specificRow($data){
        if(isset($data['select'])):
            if(!empty($data['select'])):
                $this->db->select($data['select']);
            endif;
        endif;

        if(isset($data['join'])):
            if(!empty($data['join'])):
                foreach($data['join'] as $key=>$value):
                    $this->db->join($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['leftJoin'])):
            if(!empty($data['leftJoin'])):
                foreach($data['leftJoin'] as $key=>$value):
                    $this->db->join($key,$value,'left');
                endforeach;
            endif;
        endif;
		$this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);

        if(isset($data['where'])):
            if(!empty($data['where'])):
                foreach($data['where'] as $key=>$value):
                    $this->db->where($key,$value);
                endforeach;
            endif;            
        endif;
        if(isset($data['customWhere'])):
            if(!empty($data['customWhere'])):
                foreach($data['customWhere'] as $value):
                    $this->db->where($value);
                endforeach;
            endif;
        endif;
        $this->db->where($data['tableName'].'.is_delete',0);

        if(isset($data['where_in'])):
            if(!empty($data['where_in'])):
                foreach($data['where_in'] as $key=>$value):
                    $this->db->where_in($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['like'])):
            if(!empty($data['like'])):
                $i=1;
                $this->db->group_start();
                foreach($data['like'] as $key=>$value):
                    if($i == 1):
                        $this->db->like($key,$value);
                    else:
                        $this->db->or_like($key,$value);
                    endif;
                    $i++;
                endforeach;
                $this->db->group_end();
            endif;
        endif;

        if(isset($data['order_by'])):
            if(!empty($data['order_by'])):
                foreach($data['order_by'] as $key=>$value):
                    $this->db->order_by($key,$value);
                endforeach;
            endif;
        endif;

        if(isset($data['group_by'])):
            if(!empty($data['group_by'])):
                foreach($data['group_by'] as $key=>$value):
                    $this->db->group_by($value);
                endforeach;
            endif;
        endif;
            
        if(isset($data['resultType'])):
            if($data['resultType'] == "numRows")
                return $this->db->get($data['tableName'])->num_rows();            
            if($data['resultType'] == "resultRows")
                return $this->db->get($data['tableName'])->result();
        endif;

        $result =  $this->db->get($data['tableName'])->row();
		// print_r($this->db->last_query());
		return $result;
    }

	/* Print Executed Query */
    public function printQuery(){  print_r($this->db->last_query());exit; }

	/* Custom Set OR Update Row */
    public function setValue($data){
        
		$this->db->where_in($data['tableName'].'.cm_id',[$this->CMID,0]);
		if(!empty($data['where']) || !empty($data['where_in']) || !empty($data['where_not_in'])):
			if(isset($data['where'])):
				if(!empty($data['where'])):
					foreach($data['where'] as $key=>$value):
						$this->db->where($key,$value);
					endforeach;
				endif;            
			endif;

            if(isset($data['where_in'])):
                if(!empty($data['where_in'])):
                    foreach($data['where_in'] as $key=>$value):
                        $this->db->where_in($key,$value,false);
                    endforeach;
                endif;
            endif;

            if(isset($data['where_not_in'])):
                if(!empty($data['where_not_in'])):
                    foreach($data['where_not_in'] as $key=>$value):
                        $this->db->where_not_in($key,$value,false);
                    endforeach;
                endif;
            endif;

            if(isset($data['order_by'])):
                if(!empty($data['order_by'])):
                    foreach($data['order_by'] as $key=>$value):
                        $this->db->order_by($key,$value);
                    endforeach;
                endif;
            endif;
			
			if(isset($data['set'])):
				if(!empty($data['set'])):
					foreach($data['set'] as $key=>$value):
						$v = explode(',',$value);
						$setVal = "`".$v[0]."` ".$v[1];
						$this->db->set($key, $setVal, FALSE);
					endforeach;
				endif;            
			endif;

            if(isset($data['set_value'])):
				if(!empty($data['set_value'])):
					foreach($data['set_value'] as $key=>$value):
						$this->db->set($key, $value, FALSE);
					endforeach;
				endif;            
			endif;

            if(isset($data['update'])):
				if(!empty($data['update'])):
					foreach($data['update'] as $key=>$value):
						$this->db->set($key, $value, FALSE);
					endforeach;
				endif;            
			endif;

            $this->db->update($data['tableName']);
            return ['status'=>1,'message'=>"Record updated Successfully.",'field_error'=>0,'field_error_message'=>null];
        endif;
		return ['status'=>0,'message'=>"Record updated Successfully.",'field_error'=>0,'field_error_message'=>null];
    }

    /* Company Information */
	public function getCompanyInfo($cm_id = ""){
		if(empty($cm_id)){$cm_id = $this->CMID;}
		$this->db->where('id',$cm_id);
		return $this->db->get('company_info')->row();
	}

	/* Master Options */
	public function getMasterOptions(){
		$data['tableName'] = 'master_options';
		$data['where']['id'] = 1;
		return $this->row($data);
	}

    /* Send notification to all users */
    public function notify($data)
    {
        $token = $this->checkPermissionForNotification($data['controller'], $data['action']);
        $result = array();
        if (!empty($token)) :
            $data['pushToken'] = $token;
            $result = $this->notification->sendMultipalNotification($data);
        endif;

        $logData = [
            'log_date' => date("Y-m-d H:i:s"),
            'notification_data' => json_encode($data),
            'notification_response' => json_encode($result),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ];
        $this->db->insert('notification_log',$logData);

        return $result;
    }

    public function checkPermissionForNotification($controllerNames = "", $action = "")
    {
        $tokens = array();
        if (!empty($controllerNames)) :
            $this->db->select('id,notify_on');
            $this->db->where_in('sub_controller_name', $controllerNames, false);
            $this->db->where('is_delete', 0);
            $subMenuData = $this->db->get('sub_menu_master')->result();

            if (!empty($subMenuData)) :
                foreach ($subMenuData as $row) :

                    $modualNotifyPermission = 1;
                    /* if (!empty($action)) :
                        $permission = explode(",", $row->notify_on);
                        if ($action == "W") :
                            $modualNotifyPermission = $permission[0];
                        elseif ($action == "M") :
                            $modualNotifyPermission = $permission[1];
                        elseif ($action == "D") :
                            $modualNotifyPermission = $permission[2];
                        endif;
                    endif; */

                    if (!empty($modualNotifyPermission)) :
                        $this->db->select('emp_id');
                        $this->db->where('sub_menu_id', $row->id);
                        $this->db->where('is_read', 1);
                        $this->db->where('is_delete', 0);
                        $empIds = $this->db->get('sub_menu_permission')->result();

                        if (!empty($empIds)) :
                            $empIds = array_column($empIds, 'emp_id');
                            $empIds = array_unique($empIds);

                            $this->db->select("device_token");
                            $this->db->where('is_delete', 0);
                            $this->db->where('device_token !=', "");
                            $this->db->where_in('id', $empIds);
                            $this->db->where('id !=', $this->loginId);
                            $appTokens = $this->db->get('employee_master')->result();

                            $this->db->select("web_token");
                            $this->db->where('is_delete', 0);
                            $this->db->where('web_token !=', "");
                            $this->db->where_in('id', $empIds);
                            $this->db->where('id !=', $this->loginId);
                            $webTokens = $this->db->get('employee_master')->result();

                            foreach ($appTokens as $row) :
                                $tokens[] = $row->device_token;
                            endforeach;

                            foreach ($webTokens as $row) :
                                $tokens[] = $row->web_token;
                            endforeach;
                        endif;
                    endif;
                endforeach;
            endif;
        endif;
        $tokens = (!empty($tokens)) ? array_unique($tokens) : array();
        return $tokens;
    }

    /* Stock Effect */
    public function stockEffect($stockData){
		
		$data = Array();
		$data['id']="";
		$data['location_id']=(isset($stockData['location_id']))?$stockData['location_id']:$this->RTD_STORE->id;
		if(!empty($stockData['batch_no'])){$data['batch_no'] = $stockData['batch_no'];}
		$data['item_id']=$stockData['item_id'];
		$data['qty'] = $stockData['qty'];
		$data['ref_type']=$stockData['ref_type'];
		$data['ref_id']=$stockData['ref_id'];
		$data['trans_ref_id'] = $stockData['trans_ref_id'];
		$data['ref_no']=$stockData['ref_no'];
		$data['ref_date']=$stockData['ref_date'];
		$data['created_by']=$this->loginID;
        $data['cm_id'] = (isset($stockData['cm_id']))?$stockData['cm_id']:$this->CMID;
		
		switch($data['ref_type'])
		{
			case 2 :  
					$data['trans_type']=1;
			case 5 :  
					$data['trans_type']=2;$data['qty'] = ($data['qty'] * -1);
		}
		
		$this->store('stock_transaction',$data);
    }

}
?>