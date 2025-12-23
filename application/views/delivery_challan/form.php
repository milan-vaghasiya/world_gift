<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Delivery Challan</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveDeliveryChallan">
                            <div class="col-md-12">
								<input type="hidden" name="dc_id" value="<?=(!empty($challanData->id))?$challanData->id:""?>" />

								<input type="hidden" name="entry_type" value="5">

								<input type="hidden" name="reference_entry_type" value="<?=(!empty($challanData->from_entry_type))?$challanData->from_entry_type:$from_entry_type?>">

								<input type="hidden" name="reference_id" value="<?=(!empty($challanData->ref_id))?$challanData->ref_id:$ref_id?>">

								<div class="row form-group">
									<div class="col-md-3">
										<label for="dc_no">DC No.</label>
                                        <div class="input-group">
                                            <input type="text" name="dc_prefix" id="dc_prefix" class="form-control req" value="<?=(!empty($challanData->trans_prefix))?$challanData->trans_prefix:$trans_prefix?>" />
										    <input type="text" name="dc_no" class="form-control" placeholder="Enter DC No." value="<?=(!empty($challanData->trans_no))?$challanData->trans_no:$nextTransNo?>" readonly />
                                        </div>
										
									</div>
									<div class="col-md-3">
										<label for="dc_date">DC Date</label>
										<input type="date" id="dc_date" name="dc_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($challanData->trans_date))?$challanData->trans_date:date("Y-m-d")?>" />
										
									</div>
									<div class="col-md-6">
										<label for="party_id">Party Name</label>
										<div for="party_id1" class="float-right">	
											<a href="javascript:void(0)" class="text-primary font-bold createDeliveryChallan permission-write1" datatip="Sales Order" flow="down">+ Sales Order</a>
										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="">Select Party</option>
											<?php
												foreach($customerData as $row):
													$selected = (!empty($challanData->party_id) && $challanData->party_id == $row->id)?"selected":((!empty($orderMaster->party_id) && $orderMaster->party_id == $row->id)?"selected":"");
													echo "<option data-row='".json_encode($row)."' value='".$row->id."' ".$selected.">".$row->party_name."</option>";
												endforeach;
											?>
										</select>
										<input type="hidden" name="party_name" id="party_name" value="<?=(!empty($challanData->party_name))?$challanData->party_name:((!empty($orderMaster->party_name))?$orderMaster->party_name:"")?>">

										<input type="hidden" name="party_state_code" id="party_state_code" value="<?=(!empty($challanData->party_state_code))?$challanData->party_state_code:((!empty($orderMaster->gstin))?substr($orderMaster->gstin,0,2):"")?>">
									</div>
								</div>
								<div class="row form-group">
									
									<div class="col-md-3">
										<label for="order_type">Order Type</label>
										<select name="order_type" id="order_type" class="form-control">
											<option value="1" <?=(!empty($challanData->order_type) && $challanData->order_type == 1)?"selected":((!empty($orderMaster->order_type) && $orderMaster->order_type == 1)?"selected":"")?>>Manufacturing</option>
											<option value="2" <?=(!empty($challanData->order_type) && $challanData->order_type == 2)?"selected":((!empty($orderMaster->order_type) && $orderMaster->order_type == 2)?"selected":"")?>>Job Work</option>
										</select>
									</div>

                                    <div class="col-md-3 form-group">
										<label>Dispatched Through (Transport)</label>
										<input type="text" name="dispatched_through" id="dispatched_through" value="<?=(!empty($challanData->transport_name))?$challanData->transport_name:''?>" class="form-control" />
										
									</div>
									<!-- <div class="col-md-2">
										<label for="so_no">SO. NO.</label>
										<input type="text" name="so_no" class="form-control" placeholder="Enter SO. NO." value="<?=(!empty($challanData->doc_no))?$challanData->doc_no:(!empty($soTransNo)?$soTransNo:"")?>" />
									</div> -->

									<input type="hidden" name="so_no" id="so_no" value="<?=(!empty($challanData->doc_no))?$challanData->doc_no:(!empty($soTransNo)?$soTransNo:"")?>">

									<div class="col-md-2">
										<label>L.R. No.</label>
										<input type="text" name="lr_no" id="lr_no" value="<?=(!empty($challanData->lr_no))?$challanData->lr_no:''?>" class="form-control" />
										
									</div>
                                    <div class="col-md-2">
										<label>Vehicle No.</label>
										<input type="text" name="vehicle_no" id="vehicle_no" value="<?=(!empty($challanData->vehicle_no))?$challanData->vehicle_no:''?>" class="form-control" />
										
									</div>
									
								</div>
                                <div class="row form-group">
                                    <div class="col-md-12 form-group">
                                        <label for="remark">Remark</label>
                                        <input type="text" name="remark" class="form-control" value="<?=(!empty($challanData->remark))?$challanData->remark:""?>"/>
                                    </div>
                                </div>
							</div>
							<hr>
							<div class="col-md-12 row">
                                <div class="col-md-6"><h4>Item Details : </h4></div>
                                <div class="col-md-6"><button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button></div>
                            </div>
							<div class="col-md-12 mt-3">
                                <div class="error item_name_error"></div>
								<div class="row form-group">
									<div class="table-responsive ">
										<table id="invoiceItems" class="table table-striped table-borderless">
											<thead class="table-info">
												<tr>
													<th style="width:5%;">#</th>
													<th>Item Name</th>
													<th>Qty.</th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php 
													if(!empty($challanData->itemData)): 
													$i=1;
													foreach($challanData->itemData as $row):
												?>
													<tr>
														<td style="width:5%;">
															<?=$i?>
														</td>
														<td>
															<?=$row->item_name?>
															<input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
															<input type="hidden" name="item_name[]" value="<?=htmlentities($row->item_name)?>">
															<input type="hidden" name="trans_id[]" value="<?=$row->id?>">
															<input type="hidden" name="from_entry_type[]" value="<?=$row->from_entry_type?>">
															<input type="hidden" name="ref_id[]" value="<?=$row->ref_id?>">
															<input type="hidden" name="stock_eff[]" value="<?=$row->stock_eff?>">
															<input type="hidden" name="unit_id[]" value="<?=$row->unit_id?>"><input type="hidden" name="unit_name[]" value="<?=$row->unit_name?>" />

															<input type="hidden" name="item_type[]" value="<?=$row->item_type?>" /><input type="hidden" name="item_code[]" value="<?=$row->item_code?>" /><input type="hidden" name="item_desc[]" value="<?=$row->item_desc?>" /><input type="hidden" name="hsn_code[]" value="<?=$row->hsn_code?>" /><input type="hidden" name="gst_per[]" value="<?=$row->gst_per?>" /><input type="hidden" name="price[]" value="<?=$row->price?>" />
															
														</td>
														<td>
															<?=$row->qty?>
															<input type="hidden" name="qty[]" value="<?=$row->qty?>">
															<div class="error qty<?=$i?>"></div>
															<input type="hidden" name="batch_no[]" value="<?=$row->batch_no?>">
															<input type="hidden" name="location_id[]" value="<?=$row->location_id?>">
															<input type="hidden" name="batch_qty[]" value="<?=$row->batch_qty?>">
															<input type="hidden" name="oldQty[]" value="<?=$row->qty?>">
															<div class="error batch_no<?=$i?>"></div>
															<input type="hidden" name="grn_data[]"  value="<?=htmlentities($row->grn_data)?>" />
														</td>
														<td>
															<?=$row->item_remark?>
															<input type="hidden" name="item_remark[]" value="<?=$row->item_remark?>">
														</td>
														<td class="text-center" style="width:10%;">
															<?php 
																$row->trans_id = $row->id;
																$row->oldQty = $row->qty;
																$row = json_encode($row);
															?>
															<button type="button" onclick='Edit(<?=$row?>,this);' class="btn btn-outline-warning waves-effect waves-light"><i class="ti-pencil-alt"></i></button>
															
															<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
														</td>
													</tr>
												<?php $i++; endforeach; else: ?>
												<tr id="noData">
													<td colspan="6" class="text-center">No data available in table</td>
												</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
								</div>								
							</div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveChallan('saveDeliveryChallan');" ><i class="fa fa-check"></i> Save</button>
                            <a href="<?=base_url($headData->controller)?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="itemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Add or Update Item</h4>
				<button type="button" id="grnItems" class="btn btn waves-effect waves-light btn-outline-info float-right">GRN</button>
            </div>
            <div class="modal-body">
                <form id="challanItemForm">	
                    <div class="col-md-12">
                        <div class="row form-group">                          

							<div id="itemInputs">
								<input type="hidden" name="trans_id" id="trans_id" value="" />
								<input type="hidden" name="from_entry_type" id="from_entry_type" value="" />
								<input type="hidden" name="ref_id" id="ref_id" value="" />
								<input type="hidden" name="stock_eff" id="stock_eff" value="1">
								<input type="hidden" name="oldQty" id="oldQty" value="">
								
                                <input type="hidden" name="item_name" id="item_name" value="" />
								<input type="hidden" name="item_type" id="item_type" value="" />
								<input type="hidden" name="item_code" id="item_code" value="" />
								<input type="hidden" name="item_desc" id="item_desc" value="" />
                                <input type="hidden" name="unit_id" id="unit_id" value="">
								<input type="hidden" name="unit_name" id="unit_name" value="" />
                                <input type="hidden" name="hsn_code" id="hsn_code" value="" />
								<input type="hidden" name="gst_per" id="gst_per" value="" />
								<input type="hidden" name="price" id="price" value="" />
								<input type="hidden" name="row_index" id="row_index" value="">
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="item_id">Product Name</label>
								<!--<div for="party_id1" class="float-right">	
									<span class="dropdown float-right">
										<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
										<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
											<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
											
											<a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addProduct/1" data-controller="products" data-class_name="itemOptions" data-form_title="Add Product" > + Product</a>
										</div>
									</span>
								</div>-->
                                <select name="item_id" id="item_id" class="form-control single-select itemOptions req">
                                    <option value="">Select Product Name</option>
                                    <?php
                                        foreach($itemData as $row):		
                                            echo "<option value='".$row->id."' data-row='".json_encode($row)."'>[".$row->item_code."] ".$row->item_name."</option>";
                                        endforeach;                                        
                                    ?>
                                </select>								
                            </div>
							
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Qty.</label>
                                <input type="text" name="qty" id="qty" class="form-control" value="" />
                            </div>
							
                            <div class="col-md-12 form-group">
                                <label for="item_remark">Remark</label>
                                <textarea rows="2" name="item_remark" id="item_remark" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
					<input type="hidden" name="grn_data" id="grn_data" value="" />
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-success saveItem btn-save" data-fn="save"><i class="fa fa-check"></i> Save</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-primary saveItem btn-save-close" data-fn="save_close"><i class="fa fa-check"></i> Save & Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="grnItemModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title">GRN Items</h4>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3 form-group">
							<label for="grn_id">Challan No.</label>
							<select name="grn_id" id="grn_id" class="form-control single-select">
								<option value="">Select Challan No.</option>
							</select>
						</div>
						<div class="col-md-4 form-group">
							<label for="grn_item_id">Item Name</label>
							<select name="grn_item_id" id="grn_item_id" class="form-control single-select">
								<option value="" data-remaining_qty="">Select Item Name</option>
							</select>
						</div>
						<div class="col-md-3 form-group">
							<label for="grn_qty">Qty</label>
							<input type="number" name="grn_qty" id="grn_qty" class="form-control floatOnly" min="0" value="0">
						</div>
						<div class="col-md-2 form-group">
							<label for="">&nbsp;</label>
							<button type="button" class="btn btn-outline-success waves-effect waves-light btn-block addGrnItem"><i class="fas fa-plus"></i> Add</button>
						</div>
					</div>
					<hr>
					<form id="grnItemForm">
						<div class="row">						
							<div class="col-md-12">
								<div class="table-responsive">
									<table id='grnItemTable' class="table table-bordered">
										<thead class="thead-info" id="theadData">
											<tr>
												<th>#</th>
												<th>GRN NO.</th>	
												<th>Item Name</th>
												<th>Qty.</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody id="grnItemTableData">
											<tr id="noData"><td class="text-center" colspan="5">No data available in table</td></tr>
										</tbody>								
									</table>
								</div>
							</div>						
						</div>
					</form>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn waves-effect waves-light btn-outline-success saveGrnItems" data-dismiss="modal"><i class="fa fa-check"></i> OK</button>
				<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document" style="max-width:65%;">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Create Challan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id_so" value="">
                    <input type="hidden" name="party_name" id="party_name_so" value="">
                    <input type="hidden" name="from_entry_type" id="from_entry_type" value="4">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">SO. No.</th>
                                        <th class="text-center">SO. Date</th>
                                        <th class="text-center">Cust. PO.NO.</th>
                                        <th class="text-center">Part Code</th>
                                        <th class="text-center">Qty.</th>
                                    </tr>
                                </thead>
                                <tbody id="orderData">
                                    <tr>
                                        <td class="text-center" colspan="5">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create Challan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/delivery-challan-form.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<?php
	if(!empty($orderItems)){
		foreach($orderItems as $row):
			$row->qty = $row->qty - $row->dispatch_qty;
			if(!empty($row->qty)):
				$row->trans_id = "";
				$row->from_entry_type = $row->entry_type;
				$row->ref_id = $row->id;
				$row->hsn_code = (!empty($row->hsn_code))?$row->hsn_code:"";
				$row->location_id = "";
				$row->batch_no = "";
				$row->stock_eff = "1";
				$row->oldQty = $row->qty;
				$row = json_encode($row);
				echo '<script>AddRow('.$row.');</script>';
			endif;
		endforeach;
	}
?>