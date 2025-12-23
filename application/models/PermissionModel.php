<?php
class PermissionModel extends MasterModel{
    private $menuMaster = "menu_master";
    private $subMenuMaster = "sub_menu_master";
    private $menuPermission = "menu_permission";
    private $subMenuPermission = "sub_menu_permission";

    public function getMainMenus(){
        $queryData = array();
        $queryData['tableName'] = $this->menuMaster;
        $queryData['order_by']['menu_seq'] = "ASC";
        // $queryData['where']['NOCMID'] = "";
        return $this->rows($queryData);
    }

    public function getSubMenus($menu_id){
        $queryData = array();
        $queryData['tableName'] = $this->subMenuMaster;
        $queryData['where']['menu_id'] = $menu_id;
        // $queryData['where']['NOCMID'] = "";
        $queryData['order_by']['sub_menu_seq'] = "ASC";
        return $this->rows($queryData);
    }

    public function getPermission(){
        $mainPermission = $this->getMainMenus();
        $dataRows = array();$subData = new stdClass();
        foreach($mainPermission as $row):
            if($row->is_master == 1):
                $subData->id = $row->id;
                $subData->sub_menu_seq = 1;
                $subData->sub_menu_icon = $row->menu_icon;
                $subData->sub_menu_name = $row->menu_name;
                $subData->sub_controller_name = $row->controller_name;
                $subData->menu_id = 0;
                $subData->is_report = 0;

                $subMenus = $subData;
                $row->subMenus = $subMenus;
            else:
                $subMenus = $this->getSubMenus($row->id);
                $row->subMenus = $subMenus;
            endif;
            $dataRows[] = $row;
        endforeach;
        return $dataRows;
    }

    public function getEmployeePermission($emp_id){
        $queryData = array();
        $queryData['tableName'] = $this->menuPermission;
        $queryData['where']['emp_id'] = $emp_id;
        // $queryData['where']['NOCMID'] = "";
        $result['mainPermission'] = $this->rows($queryData);

        $queryData = array();
        $queryData['tableName'] = $this->subMenuPermission;
        $queryData['where']['emp_id'] = $emp_id;
        // $queryData['where']['NOCMID'] = "";
        $result['subMenuPermission'] = $this->rows($queryData);
        return $result;
    }

    public function save($data){
        try{
            $this->db->trans_begin();
        $employeePermission = $this->getEmployeePermission($data['emp_id']);
        $mainPermissionData = array();
        foreach($employeePermission['mainPermission'] as $row):
            $mainPermissionData[] = $row->menu_id;
        endforeach;

        $subPermissionData = array();
        foreach($employeePermission['subMenuPermission'] as $row):
            $subPermissionData[] = $row->sub_menu_id;
        endforeach;

        $mainPermission = array();
        foreach($data['menu_id'] as $key=>$value):
            if(in_array($value,$mainPermissionData)):
                $menuRead = (isset($data['menu_read_'.$value]))?$data['menu_read_'.$value][0]:0;
                $menuWrite = (isset($data['menu_write_'.$value]))?$data['menu_write_'.$value][0]:0;
                $menuModify = (isset($data['menu_modify_'.$value]))?$data['menu_modify_'.$value][0]:0;
                $menuDelete = (isset($data['menu_delete_'.$value]))?$data['menu_delete_'.$value][0]:0;
                $mainPermission = [
                    'emp_id' => $data['emp_id'],
                    'menu_id' => $value,
                    'is_read' => $menuRead,
                    'is_write' => $menuWrite,
                    'is_modify' => $menuModify,
                    'is_remove' => $menuDelete,
                    'is_master' => $data['is_master'][$key],
                    'creted_by' => $this->loginId
                ];
                if(in_array($value,$data['main_id'])):
                    $subPermission = array();
                    foreach($data['sub_menu_id_'.$value] as $subKey => $subValue):
                        if(in_array($subValue,$subPermissionData)):
                            $subMenuRead = (isset($data['sub_menu_read_'.$subValue.'_'.$value]))?$data['sub_menu_read_'.$subValue.'_'.$value][0]:0;
                            $subMenuWrite = (isset($data['sub_menu_write_'.$subValue.'_'.$value]))?$data['sub_menu_write_'.$subValue.'_'.$value][0]:0;
                            $subMenuModify = (isset($data['sub_menu_modify_'.$subValue.'_'.$value]))?$data['sub_menu_modify_'.$subValue.'_'.$value][0]:0;
                            $subMenuDelete = (isset($data['sub_menu_delete_'.$subValue.'_'.$value]))?$data['sub_menu_delete_'.$subValue.'_'.$value][0]:0;
                            $subMenuApprove = (isset($data['sub_menu_approve_'.$subValue.'_'.$value]))?$data['sub_menu_approve_'.$subValue.'_'.$value][0]:0;
                            $subPermission = [
                                'emp_id' => $data['emp_id'],
                                'menu_id' => $value,
                                'sub_menu_id' => $subValue,
                                'is_read' => $subMenuRead,
                                'is_write' => $subMenuWrite,
                                'is_modify' => $subMenuModify,
                                'is_remove' => $subMenuDelete,
                                'is_approve' => $subMenuApprove,
                                'creted_by' => $this->loginId
                            ];
                            $this->db->where('menu_id',$value)->where('sub_menu_id',$subValue)->where('emp_id',$data['emp_id'])->update($this->subMenuPermission,$subPermission);
                        else:
                            $subMenuRead = (isset($data['sub_menu_read_'.$subValue.'_'.$value]))?$data['sub_menu_read_'.$subValue.'_'.$value][0]:0;
                            $subMenuWrite = (isset($data['sub_menu_write_'.$subValue.'_'.$value]))?$data['sub_menu_write_'.$subValue.'_'.$value][0]:0;
                            $subMenuModify = (isset($data['sub_menu_modify_'.$subValue.'_'.$value]))?$data['sub_menu_modify_'.$subValue.'_'.$value][0]:0;
                            $subMenuDelete = (isset($data['sub_menu_delete_'.$subValue.'_'.$value]))?$data['sub_menu_delete_'.$subValue.'_'.$value][0]:0;
                            $subMenuApprove = (isset($data['sub_menu_approve_'.$subValue.'_'.$value]))?$data['sub_menu_approve_'.$subValue.'_'.$value][0]:0;
                            $subPermission = [
                                'emp_id' => $data['emp_id'],
                                'menu_id' => $value,
                                'sub_menu_id' => $subValue,
                                'is_read' => $subMenuRead,
                                'is_write' => $subMenuWrite,
                                'is_modify' => $subMenuModify,
                                'is_remove' => $subMenuDelete,
                                'is_approve' => $subMenuApprove,
                                'creted_by' => $this->loginId
                            ];
                            $this->db->insert($this->subMenuPermission,$subPermission);
                        endif;
                    endforeach;
                endif;
                $this->db->where('menu_id',$value)->where('emp_id',$data['emp_id'])->update($this->menuPermission,$mainPermission);
            else:
                $menuRead = (isset($data['menu_read_'.$value]))?$data['menu_read_'.$value][0]:0;
                $menuWrite = (isset($data['menu_write_'.$value]))?$data['menu_write_'.$value][0]:0;
                $menuModify = (isset($data['menu_modify_'.$value]))?$data['menu_modify_'.$value][0]:0;
                $menuDelete = (isset($data['menu_delete_'.$value]))?$data['menu_delete_'.$value][0]:0;
                $mainPermission = [
                    'emp_id' => $data['emp_id'],
                    'menu_id' => $value,
                    'is_read' => $menuRead,
                    'is_write' => $menuWrite,
                    'is_modify' => $menuModify,
                    'is_remove' => $menuDelete,
                    'is_master' => $data['is_master'][$key],
                    'creted_by' => $this->loginId
                ];
                if(in_array($value,$data['main_id'])):
                    $subPermission = array();
                    foreach($data['sub_menu_id_'.$value] as $subKey => $subValue):
                        $subMenuRead = (isset($data['sub_menu_read_'.$subValue.'_'.$value]))?$data['sub_menu_read_'.$subValue.'_'.$value][0]:0;
                        $subMenuWrite = (isset($data['sub_menu_write_'.$subValue.'_'.$value]))?$data['sub_menu_write_'.$subValue.'_'.$value][0]:0;
                        $subMenuModify = (isset($data['sub_menu_modify_'.$subValue.'_'.$value]))?$data['sub_menu_modify_'.$subValue.'_'.$value][0]:0;
                        $subMenuDelete = (isset($data['sub_menu_delete_'.$subValue.'_'.$value]))?$data['sub_menu_delete_'.$subValue.'_'.$value][0]:0;
                        $subMenuApprove = (isset($data['sub_menu_approve_'.$subValue.'_'.$value]))?$data['sub_menu_approve_'.$subValue.'_'.$value][0]:0;
                        $subPermission = [
                            'emp_id' => $data['emp_id'],
                            'menu_id' => $value,
                            'sub_menu_id' => $subValue,
                            'is_read' => $subMenuRead,
                            'is_write' => $subMenuWrite,
                            'is_modify' => $subMenuModify,
                            'is_remove' => $subMenuDelete,
                            'is_approve' => $subMenuApprove,
                            'creted_by' => $this->loginId
                        ];
                        $this->db->insert($this->subMenuPermission,$subPermission);
                        
                    endforeach;
                endif;
                $this->db->insert($this->menuPermission,$mainPermission);
            endif;
        endforeach;

        $result = ['status'=>1,'message'=>'Employee Permission saved successfully.'];
        if ($this->db->trans_status() !== FALSE):
            $this->db->trans_commit();
            return $result;
        endif;
    }catch(\Exception $e){
        $this->db->trans_rollback();
       return ['status'=>2,'message'=>"somthing is wrong. Error : ".$e->getMessage()];
    }	
    }

    public function editPermission($emp_id){
        
        $employeePermission = $this->getEmployeePermission($emp_id);
        $empPermission = array();
        foreach($employeePermission['mainPermission'] as $row):
            if(!empty($row->is_read))
                $empPermission[] = "menu_read_".$row->menu_id;
            if(!empty($row->is_write))
                $empPermission[] = "menu_write_".$row->menu_id;
            if(!empty($row->is_modify))
                $empPermission[] = "menu_modify_".$row->menu_id;
            if(!empty($row->is_remove))
                $empPermission[] = "menu_delete_".$row->menu_id;
        endforeach;

        foreach($employeePermission['subMenuPermission'] as $row):
            if(!empty($row->is_read))
                $empPermission[] = "sub_menu_read_".$row->sub_menu_id."_".$row->menu_id;
            if(!empty($row->is_write))
                $empPermission[] = "sub_menu_write_".$row->sub_menu_id."_".$row->menu_id;
            if(!empty($row->is_modify))
                $empPermission[] = "sub_menu_modify_".$row->sub_menu_id."_".$row->menu_id;
            if(!empty($row->is_remove))
                $empPermission[] = "sub_menu_delete_".$row->sub_menu_id."_".$row->menu_id;
            if(!empty($row->is_approve))
                $empPermission[] = "sub_menu_approve_".$row->sub_menu_id."_".$row->menu_id;
        endforeach;

        return ['status'=>1,'message'=>'Record Found','empPermission'=>$empPermission];
    }

    public function getEmployeeMenus(){
        $queryData = array();
        $queryData['tableName'] = $this->menuPermission;
        $queryData['select'] = 'menu_permission.*,menu_master.menu_name,menu_master.controller_name,menu_master.menu_icon';
        $queryData['leftJoin']['menu_master'] = "menu_master.id = menu_permission.menu_id";
        $queryData['where']['menu_permission.emp_id'] = $this->loginId;
        $queryData['where']['menu_master.is_delete'] = 0;
        // $queryData['where']['NOCMID'] = "";
        $queryData['order_by']['menu_master.menu_seq'] = "ASC";
		
        $menuData = $this->rows($queryData);

        $html = ""; $employeePermission = array();
        foreach($menuData as $row):
            if(!empty($row->permission)):
                if(!empty($row->is_read)):
                    if(!empty($row->is_read) || !empty($row->is_write) || !empty($row->is_modify) || !empty($row->is_remove)):
                        $url = (!empty($row->controller_name))?$row->controller_name:"#";
                        $employeePermission[$url] = ['is_read'=>$row->is_read,'is_write'=>$row->is_write,'is_modify'=>$row->is_modify,'is_remove'=>$row->is_remove];
                        $html .= '<li class="sidebar-item"><a href="'.base_url($url).'" class="sidebar-link waves-effect waves-dark" aria-expanded="false"><i class="'.$row->menu_icon.'"></i><span class="hide-menu">'.$row->menu_name.'</span></a></li>';
                    endif;
                endif;
            else:
                $subMenus = "";
                
                $queryData = array();
                $queryData['tableName'] = $this->subMenuPermission;
                $queryData['select'] = 'sub_menu_permission.*,sub_menu_master.sub_menu_name,sub_menu_master.sub_controller_name,sub_menu_master.sub_menu_icon';
                $queryData['leftJoin']['sub_menu_master'] = "sub_menu_master.id = sub_menu_permission.sub_menu_id";
                $queryData['where']['sub_menu_permission.emp_id'] = $this->loginId;
                $queryData['where']['sub_menu_permission.menu_id'] = $row->menu_id;
				// $queryData['where']['NOCMID'] = "";
                $queryData['order_by']['sub_menu_master.sub_menu_seq'] = "ASC";
                $subMenuData = $this->rows($queryData);

                $subMenuHtml = "";$show_menu = false; 
                foreach($subMenuData as $subRow):
                    if(!empty($subRow->is_read)):
                        if(!empty($subRow->is_read) || !empty($subRow->is_write) || !empty($subRow->is_modify) || !empty($subRow->is_remove)):
                            $show_menu = true; 
                            $sub_url = (!empty($subRow->sub_controller_name))?$subRow->sub_controller_name:"#";
                            $employeePermission[$sub_url] = ['is_read'=>$subRow->is_read,'is_write'=>$subRow->is_write,'is_modify'=>$subRow->is_modify,'is_remove'=>$subRow->is_remove,'is_approve'=>$subRow->is_approve];
                            $subMenus .= '<li class="sidebar-item"><a href="'.base_url($sub_url).'" class="sidebar-link"><i class="icon-Record"></i><span class="hide-menu"> '.$subRow->sub_menu_name.'</span></a></li>';
                        endif;
                    endif;
                endforeach;

                if($show_menu == true):
                    $html .= '<li class="sidebar-item">
                    <a href="javaScript:void();" class="sidebar-link has-arrow waves-effect waves-dark" aria-expanded="false">
                        <i class="'.$row->menu_icon.'"></i><span  class="hide-menu">'.$row->menu_name.'</span>
                    </a>
                    <ul aria-expanded="false" class="collapse  first-level">'.$subMenus.'</ul>
                    </li>';
                endif;
            endif;
        endforeach;
        if(!$this->session->userdata('emp_permission')):
            $this->session->set_userdata('emp_permission',$employeePermission);
        else:
            $this->session->set_userdata('emp_permission',$employeePermission);
        endif;
        return $html;
    }
}
?>