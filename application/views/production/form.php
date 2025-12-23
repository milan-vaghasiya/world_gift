<style>
	.muDetail td,.muDetail th{padding:5px 10px !important;font-size:0.9rem;}
</style>
<div class="modal fade" id="outwardModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="max-width:90%;">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1" style="width:100%;">Production Management</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body jpFWTab">
				<table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
					<tr class="">
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Product</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"></th>
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;">Process</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductProcessName"></th>
						<th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Pending Qty.</th>
						<th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="ProductPendingQty"></th>
					</tr>
				</table>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3 form-group shiftDiv">
							<label for="shift_id">Shift</label>
							<select name="shift_id" id="shift_id" class="form-control shiftOptions req single-select1">
								<option value="">Select Shift</option>
								<?php
									if(!empty($shiftData)):
										foreach($shiftData as $row){
											echo '<option value="'.$row->id.'" data-start_time="'.$row->start_time.'" data-shift_hours="'.$row->shift_hour.'">'.$row->shift_name.'</option>';
										}
									endif;
								?>
							</select>
							<div class="error shift_id"></div>
						</div>
						<div class="col-md-3 form-group operatorDiv">
							<label for="operator_id">Operator Name</label>
							<select name="operator_id" id="operator_id" class="form-control operatorOptions req"></select>
							<div class="error operator_id"></div>
						</div>
						<div class="col-md-3 form-group supervisorDiv">
							<label for="supervisor_id">Supervisor Name</label>
							<select name="supervisor_id" id="supervisor_id" class="form-control supervisorOptions"></select>
							<div class="error supervisor_id"></div>
						</div>
						<div class="col-md-3 form-group machineDiv">
							<label for="machine_id">Machine</label>
							<select name="machine_id" id="machine_id" class="form-control machineOptions"></select>
						</div>
					</div>
				</div>
				<nav id="forward">
					<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
						<a class="nav-item nav-link getOutWordQty active" id="OutwordTab" data-toggle="tab" href="#Outword" role="tab" aria-controls="nav-home" aria-selected="true"><i class="fa fa-paper-plane"></i> OK (Finish)</a>
						
						<!-- <a class="nav-item nav-link getRejectedQty" data-toggle="tab" href="#Rejection" role="tab" aria-controls="nav-profile" aria-selected="false"><i class="fa fa-window-close"></i> Rejection</a>
						
						<a class="nav-item nav-link getReworkQty" data-toggle="tab" href="#Rework" role="tab" aria-controls="nav-contact" aria-selected="false"><i class="fa fa-retweet"></i> Rework</a>
						
						<a class="nav-item nav-link getScrape" data-toggle="tab" href="#Scrape" role="tab" aria-controls="nav-about" aria-selected="false"><i class="fa fa-trash"></i> Scrape</a> -->
						
						<a class="nav-item nav-link getIdleTime" data-toggle="tab" href="#IdleTime" role="tab" aria-controls="nav-about" aria-selected="false"><i class="fas fa-clock"></i> Idle Time</a>

						
						<!-- <a class="nav-item nav-link getReturnStock" data-toggle="tab" href="#Return" role="tab" aria-controls="nav-about" aria-selected="false"><i class="fa fa-reply"></i>  Return Raw Material</a> -->
					</div>
				</nav>
				<div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
					<div class="col-md-12 mt-3">
						<input type="hidden" id="ref_id" value="" />
						<input type="hidden" id="product_id" value="" />
						<input type="hidden" id="in_process_id" value="" />
						<input type="hidden" id="job_card_id" value="" />
						<input type="hidden" id="PendingQty" value="" />
						<input type="hidden" id="issue_batch_no" value="" />
						<input type="hidden" id="issue_material_qty" value="" />
						<input type="hidden" id="material_used_id" value="" />
						<input type="hidden" id="cycle_time" value="" />
					</div>
					
					<!-- Outword Tab Start -->
					<div id="Outword" class="tab-pane active">
						<div class="col-md-12 ">
						    <input type="hidden" name="udQty" id="udQty" class="form-control numericOnly" min="0" value="0" />					
							<div class="row">	
								<div class="col-md-12 error out_form_error"></div>
								<div class="col-md-3 form-group">
									<label for="outEntryDate">Date</label>
									<input type="date" name="outEntryDate" id="outEntryDate" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>" required >
								</div>
								<div class="col-md-3 form-group">
									<label for="outQty">Ok Qty.</label>
									<input type="number" name="outQty" id="outQty" class="form-control numericOnly countWeightOut req" placeholder="Enter Quantity" data-col="w_pcs" value="0" min="0" />
									<div class="error outQty"></div>
								</div>
								<div class="col-md-2 form-group outWpcs">
									<label for="outWpcs">Weight/Pcs.</label>
									<input type="number" name="outWpcs" id="outWpcs" class="form-control floatOnly countWeightOut" min="0" data-col="w_pcs" value="0" />
									
								</div>
								<div class="col-md-2 form-group outTotalWeight">
									<label for="outTotalWeight">Total Weight</label>
									<input type="number" name="outTotalWeight" id="outTotalWeight" class="form-control floatOnly countWeightOut" min="0" data-col="total_weight" value="0" />									
								</div>
								<div class="col-md-2 form-group ptime">
									<label for="production_time">Prod. Time(HH:MM)</label>
									<input type="text" name="production_time" id="production_time" class="form-control inputmask-hhmm ptime" value="00:00">
								</div>
							</div>

							<div class="row">	
								<div class="col-md-2 form-group">
									<label for="rejQty">Rejected Qty.</label>
									<input type="number" name="rejQty" id="rejQty" class="form-control numericOnly countWeightRej req" placeholder="Enter Quantity" data-col="w_pcs" value="0" min="0" />									
								</div>								
								<div class="col-md-2 form-group">
									<label for="rejection_reason">Rejection Reason</label>
									<select name="rejection_reason" id="rejection_reason" class="form-control single-select req">
										<option value="">Select Reason</option>
									</select>
								</div>						
								<div class="col-md-2 form-group">
									<label for="rejection_stage">Rejection Belong To</label>
									<select name="rejection_stage" id="rejection_stage" class="form-control single-select req">
										<option value="">Select Stage</option>
									</select>
								</div>	
								<div class="col-md-2 form-group">
									<label for="rejection_from">Rejection From <span class="text-danger">*</span></label>
									<select name="rejection_from" id="rejection_from" class="form-control single-select req">
										<option value="">Select Rej. From</option>
									</select>
								</div>
								<div class="col-md-4 form-group">
									<label for="rejection_remark">Rejection Remark</label>
									<input type="text" name="rejection_remark" id="rejection_remark" class="form-control  ">
								</div>
							</div>
                            <div class="row" id="reworkRow">
								<div class="col-md-2 form-group">
									<label for="rewQty">Rework Qty.</label>
									<input type="number" name="rewQty" id="rewQty" class="form-control numericOnly countWeightRew req" placeholder="Enter Quantity" data-col="w_pcs" value="0" min="0" />
								</div>
								<div class="col-md-2 form-group">
									<label for="rework_reason">Rework Reason</label>
									<select name="rework_reason" id="rework_reason" class="form-control single-select req">
										<option value="">Select Reason</option>
									</select>
								</div>
								<div class="col-md-2 form-group">
									<label for="rework_process_id">Rework Belong To <span class="text-danger">*</span></label>
									<!-- <select name="rework_process" id="rework_process" data-input_id="rework_process_id" class="form-control jp_multiselect req" multiple="multiple"></select>
									
									<input type="hidden" name="rework_process_id" id="rework_process_id" class="req" value="" /> -->

									<select name="rework_process_id" id="rework_process_id" class="form-control single-select req">
										
									</select>
									<div class="error rework_process_id"></div>
								</div>	
								<div class="col-md-2 form-group">
									<label for="rework_from">Rework From <span class="text-danger">*</span></label>
										<select name="rework_from" id="rework_from" class="form-control single-select req">
										    <option value="">Select Rew. From</option>
										</select>
									</div>
								<div class="col-md-4 form-group">
									<label for="rework_remark">Rework Remark</label>
									<input type="text" name="rework_remark" id="rework_remark" class="form-control">
								</div>								
							</div>

							<div class="row">
								<div class="col-md-2 form-group challanNoDiv">
									<label for="outChallanNo">Challan No.</label>
									<input type="text" name="outChallanNo" id="outChallanNo" class="form-control" value="" />
								</div>
								<div class="col-md-2 form-group challanNoDiv">
									<label for="outChargeNo">Charge No.</label>
									<input type="text" name="outChargeNo" id="outChargeNo" class="form-control" value="" />
								</div>
								<div class="col-md-10 form-group remarkDiv">
									<label for="outRemark" style="width:100%;">Remark<strong class="error totalPT text-primary float-right"></strong></label>
									<input type="text" name="outRemark" id="outRemark" class="form-control" placeholder="Enter Remark" value="">
								</div>
								<div class="col-md-2 form-group">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-primary waves-effect waves-light btn-block save-form" onclick="saveOutQty();"><i class="fa fa-check"></i> Save</button>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 mt-10">
									<div class="error item_stock mb-3"></div>
									<div class="table-responsive">
										<table id="outwardTable" class="table table-bordered align-items-center" style="width: 100%;">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th style="width:15%;">Date</th>
													<th class="challanNoCol">Challan No.</th>
													<th class="challanNoCol">Charge No.</th>
													<th>OK Qty.</th>
													<th>UD Qty.</th>
													<th>Rej. Qty.</th>
													<th>Rew. Qty.</th>
													<th>Prod. Time</th>
													<th>Shift</th>
													<th>Operator</th>
													<th>Machine</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="outwardQtyData">
												
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Outword Tab End -->	
					
					<!-- Idle Time Tab Start -->
					<div id="IdleTime" class="tab-pane fade">
						<div class="col-md-12">
							<div class="row">									
								<div class="col-md-4 form-group">
									<label for="idleEntryDate">Date</label>
									<input type="date" name="idleEntryDate" id="idleEntryDate" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>" required>
								</div>
								<div class="col-md-4 form-group">
									<label for="breck_type">Breack Down Type</label>
									<select name="breck_type" id="breck_type" class="form-control single-select req">
										<option value="">Select Breack Down Type</option>
										<option value="0">Machine Breack</option>
										<option value="1">Other Breack</option>
									</select>
								</div>
								<div class="col-md-4 form-group ptime">
									<label for="idle_reason">Idle Reason</label>
									<select name="idle_reason" id="idle_reason" class="form-control single-select req">
									</select>
								</div>
								<!-- Change By Mansee @ 29-11-2021 -->
								<div class="col-md-2 form-group ptime">
									<label for="idle_time">Idle Time(HH:MM)</label>
									<input type="time" name="idle_time" id="idle_time" class="form-control inputmask-hhmm" value="00:00" />
								</div>
								<div class="col-md-2 form-group">
									<label for="idle_time_in_min">Idle Time (In Min.)</label>
									<input type="text" name="idle_time_in_min" id="idle_time_in_min" class="form-control" value="0" readonly />
								</div>
								<div class="col-md-3 form-group">
									<label for="idle_start_time">Start Time</label>
									<input type="datetime" name="idle_start_time" id="idle_start_time" class="form-control req claculateTime"  value="<?=date("Y-m-d H:i")?>" readonly>
								</div>
								<div class="col-md-3 form-group">
									<label for="idle_end_time">End Time</label>
									<input type="datetime" name="idle_end_time" id="idle_end_time" class="form-control req claculateTime" min="<?=date("Y-m-d H:i:s")?>" value="<?=date("Y-m-d H:i")?>" readonly>
								</div>
								<div class="col-md-2 form-group">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-primary waves-effect waves-light btn-block save-form" onclick="saveIdleTime();"><i class="fa fa-check"></i> Save</button>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 mt-10">
									<div class="error item_stock mb-3"></div>
									<div class="table-responsive">
										<table id="idleTable" class="table table-bordered align-items-center" style="width: 100%;">
											<thead class="thead-info">
												<tr class="text-center">
													<th style="width:5%;">#</th>
													<th style="width:15%;">Date</th>
													<th>Breack Down Type</th>
													<th>Idle Reason</th>
													<th>Idle Time</th>		
													<th>Shift</th>
													<th>Operator</th>
													<th>Machine</th>											
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="idleData">
												
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Idle Time Tab Start -->
					
				</div>
                

            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary btn-close save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>