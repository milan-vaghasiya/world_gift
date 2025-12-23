<?php $this->load->view('includes/header'); ?>
<form id="empPermission">
    <div class="page-wrapper">
        <div class="container-fluid bg-container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="card-title pageHeader">Employee Permission</h4>
                                </div>
                                <div class="col-md-4">
                                    <select name="emp_id" id="emp_id" class="form-control single-select">
                                        <option value="">Select Employee</option>
                                        <?php   
                                            foreach($empList as $row):
                                                echo '<option value="'.$row->id.'">['.$row->emp_code.'] '.$row->emp_name.'</option>';
                                            endforeach; 
                                        ?>
                                    </select>
                                </div>                             
                            </div>                                         
                        </div>
                        <div class="card-body reportDiv" style="min-height:75vh">
                            <div class="table-responsive">
                                <table id='reportTable' class="table table-bordered table-striped">
                                    <?php
                                        foreach($permission as $row):
                                    ?>
                                        <tr>
                                            <th class="bg-facebook text-white" colspan="6">
                                                <?=$row->menu_name?>
                                                <input type="hidden" name="menu_id[]" value="<?=$row->id?>">
                                                <input type="hidden" name="is_master[]" value="<?=$row->is_master?>">
                                                <?php 
                                                    if(empty($row->is_master)):
                                                        echo '<input type="hidden" name="main_id[]" value="'.$row->id.'">';
                                                    endif;
                                                ?>
                                            </th>
                                        </tr>
                                        <tr class="bg-thinfo">
                                            <th>#</th>
                                            <th>Menu/Page Name</th>
                                            <th class="text-center">Read</th>
                                        <th class="text-center">Write</th>
                                        <th class="text-center">Modify</th>
                                        <th class="text-center">Delete</th>
                                    </tr>
                                    <?php
                                        $j=1;
                                        foreach($row->subMenus as $subRow):
                                            if(empty($subRow->menu_id)):
                                                $inputReadName = "menu_read_".$row->id;
                                                $inputWriteName = "menu_write_".$row->id;
                                                $inputModifyName = "menu_modify_".$row->id;
                                                $inputDeleteName = "menu_delete_".$row->id;
                                            else:
                                                $inputReadName = "sub_menu_read_".$subRow->id."_".$row->id;
                                                $inputWriteName = "sub_menu_write_".$subRow->id."_".$row->id;
                                                $inputModifyName = "sub_menu_modify_".$subRow->id."_".$row->id;
                                                $inputDeleteName = "sub_menu_delete_".$subRow->id."_".$row->id;
                                            endif;
                                    ?>
                                       <tr>
                                            <td><?=$j++?></td>
                                            <td>
                                                <?=$subRow->sub_menu_name?>
                                                <?php 
                                                    if(!empty($subRow->menu_id)):
                                                        echo '<input type="hidden" name="sub_menu_id_'.$row->id.'[]" value="'.$subRow->id.'">';
                                                    endif;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" id="<?=$inputReadName?>" name="<?=$inputReadName?>[]" class="filled-in chk-col-success" value="1">
                                                <label for="<?=$inputReadName?>"></label>
                                            </td>
                                            <td class="text-center">
                                                <?php if($subRow->is_report == 0):?>
                                                <input type="checkbox" id="<?=$inputWriteName?>" name="<?=$inputWriteName?>[]" class="filled-in chk-col-success" value="1">
                                                <label for="<?=$inputWriteName?>"></label>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($subRow->is_report == 0):?>
                                                <input type="checkbox" id="<?=$inputModifyName?>" name="<?=$inputModifyName?>[]" class="filled-in chk-col-success" value="1">
                                                <label for="<?=$inputModifyName?>"></label>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($subRow->is_report == 0):?>
                                                <input type="checkbox" id="<?=$inputDeleteName?>" name="<?=$inputDeleteName?>[]" class="filled-in chk-col-success" value="1">
                                                <label for="<?=$inputDeleteName?>"></label>
                                                <?php endif; ?>
                                            </td>
                                       </tr> 
                                    <?php endforeach; ?>
                                    <tr height="50px">
                                        <th style="border: none;background-color: #ffffff;" colspan="6"></th>
                                    </tr>
                                <?php endforeach; ?>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>

</form>

<div class="bottomBtn bottom-25 right-25">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="saveOrder('empPermission');">SAVE PERMISSION</button>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/emp-permission.js?v=<?=time()?>"></script>