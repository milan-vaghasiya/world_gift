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
                                        <option value="1">Admin</option>
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
									<div class="panel-group wrap" id="bs-collapse">
                                    <?php
                                        foreach($permission as $row):
                                    ?>
										<div class="panel">
											<div class="panel-heading">
												<h4 class="panel-title">
													<a data-toggle="collapse" data-parent="#bs-collapse" href="#menu<?=$row->id?>"><?=$row->menu_name?></a>
													<input type="hidden" name="menu_id[]" value="<?=$row->id?>">
													<input type="hidden" name="is_master[]" value="<?=$row->is_master?>">
													<?php 
														if(empty($row->is_master)):
															echo '<input type="hidden" name="main_id[]" value="'.$row->id.'">';
														endif;
													?>
												</h4>
											</div>
											<div id="menu<?=$row->id?>" class="panel-collapse collapse show">
												<div class="panel-body">
													<table id='reportTable' class="table table-bordered table-striped">
														<tr class="bg-thinfo">
															<th class="text-center">#</th>
															<th>Menu/Page Name</th>
															<th class="text-center">Read</th>
															<th class="text-center">Write</th>
															<th class="text-center">Modify</th>
															<th class="text-center">Delete</th>
															<th class="text-center">Approve</th>
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
                                                $inputApproveName = "sub_menu_approve_".$subRow->id."_".$row->id;
                                            endif;
                                    ?>
                                       <tr>
                                            <td class="text-center"><?=$j++?></td>
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
                                            <td class="text-center">
                                                <?php if($subRow->is_approve_req == 1):?>
                                                <input type="checkbox" id="<?=$inputApproveName?>" name="<?=$inputApproveName?>[]" class="filled-in chk-col-success" value="1">
                                                <label for="<?=$inputApproveName?>"></label>
                                                <?php endif; ?>
                                            </td>
                                       </tr> 
									   
                                    <?php endforeach; ?>
											</table>
											</div>
										</div>
									</div>
                                <?php endforeach; ?>
							</div>							
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>

</form>

<div class="bottomBtn bottom-25 right-25 permission-write">
    <button type="button" class="btn btn-primary btn-rounded font-bold permission-write save-form" style="letter-spacing:1px;" onclick="saveOrder('empPermission');">SAVE PERMISSION</button>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/emp-permission.js?v=<?=time()?>"></script>
<script>
$(document).ready(function() {
	$('.collapse.in').prev('.panel-heading').addClass('active');
	$('#bs-collapse').on('show.bs.collapse', function(a) {
		$(a.target).prev('.panel-heading').addClass('active');
	  }).on('hide.bs.collapse', function(a) {
		$(a.target).prev('.panel-heading').removeClass('active');
	});
});
</script>