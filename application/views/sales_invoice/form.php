<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header text-center">
						<h4><u>Tax Invoice</u></h4>
					</div>
					<div class="card-body">
						<form autocomplete="off" id="saveSalesInvoice">
							<div class="col-md-12"> 
								<input type="hidden" name="sales_id" value="<?= (!empty($invoiceData->id)) ? $invoiceData->id : "" ?>" />
								<input type="hidden" name="entry_type" id="entry_type" value="<?= (!empty($invoiceData->entry_type)) ? $invoiceData->entry_type : "6" ?>">
								<input type="hidden" name="reference_entry_type" id="reference_entry_type" value="<?= (!empty($invoiceData->from_entry_type)) ? $invoiceData->from_entry_type : $from_entry_type ?>">
								<input type="hidden" name="reference_id" value="<?= (!empty($invoiceData->ref_id)) ? $invoiceData->ref_id : $ref_id ?>">
								<input type="hidden" name="gst_type" id="gst_type" value="<?= (!empty($invoiceData->gst_type)) ? $invoiceData->gst_type : $gst_type ?>">
								<input type="hidden" name="apply_round" id="apply_round" value="<?= (!empty($invoiceData->apply_round)) ? $invoiceData->apply_round : '0' ?>">
								<input type="hidden" id="cmid" value="<?= $this->CMID ?>">

								<div class="row form-group">
								    
									<div class="col-md-2">
										<label for="memo_type">Memo Type</label>
										<select name="memo_type" id="memo_type" class="form-control single-select req">
											<?php
										        if(!empty($invoiceData->memo_type)) 
										        {
										            echo '<option value="CASH" '.(($invoiceData->memo_type == "CASH") ? "selected" : "").'>CASH</option>';
										            echo '<option value="DEBIT" '.(($invoiceData->memo_type == "DEBIT") ? "selected" : "").'>DEBIT</option>';
										        }
										        else
										        {
										            if($this->CMID == 1) 
										            {
										                echo '<option value="CASH">CASH</option><option value="DEBIT">DEBIT</option>';
										            }
										            else
										            {
										                echo '<option value="DEBIT">DEBIT</option>';
										            }
										        }
											?>
										</select>
									</div>
									<div class="col-md-2">
										<label for="inv_no">Invoice No.</label>
										<div class="input-group">
											<input type="text" name="inv_prefix" id="inv_prefix" class="form-control req" value="<?= (!empty($invoiceData->trans_prefix)) ? $invoiceData->trans_prefix : $trans_prefix ?>" readonly />
											<input type="text" name="inv_no" id="inv_no" class="form-control" placeholder="Enter Invoice No." value="<?= (!empty($invoiceData->trans_no)) ? $invoiceData->trans_no : $nextTransNo ?>" readonly />
										</div>

									</div>
									<div class="col-md-3">
										<label for="sp_acc_id">Sales A/c.</label>
										<select name="sp_acc_id" id="sp_acc_id" class="form-control single-select req">
											<!--<option value="">Select Account</option>-->
											<?php
											foreach ($spAccounts as $row) :
												if ($row->system_code != "SALESACC") :
													$selected = (!empty($invoiceData->sp_acc_id) && $invoiceData->sp_acc_id == $row->id) ? "selected" : "";
													echo "<option value='" . $row->id . "' " . $selected . ">" . $row->party_name . "</option>";
												endif;
											endforeach;
											?>
										</select>
									</div>
									<div class="col-md-5">
										<label for="party_id">Party Name</label>
										<div for="party_id1" class="float-right">	
											<span class="dropdown float-right">
												<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down"> + Add New</a>
												<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
													<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
													
													<a class="dropdown-item leadAction addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-xl" data-function="addParty/1" data-controller="parties" data-class_name="partyOptions" data-form_title="Add Customer">+ Customer</a>
													
												</div>
											</span>
										</div>
										<div for="party_id1" class="float-right">
											<!--<a href="javascript:void(0)" class="text-primary font-bold createSalesInvoice permission-write1" datatip="Sales Order" flow="down">+ SO</a>-->
											<a href="javascript:void(0)" class="text-primary font-bold createPInvSalesInvoice permission-write1" datatip="Proforma Invoice" flow="down">+ PInv </a>
										</div>
										<select name="party_id" id="party_id" class="form-control single-select partyOptions req">
											<option value="">Select Party</option>
											<?php
											$pName = ""; $selected = "";
    											foreach ($customerData as $row) :
    												if(empty($invoiceData->party_id) &&  empty($invMaster->id)){
    													if( $row->coust_type == 1):
    														$selected = "selected";
    														$pName =   $row->party_name ;
    													else:
    														$selected = "";
    													endif;
    												}else{	
    													$selected = (!empty($invoiceData->party_id) && $invoiceData->party_id == $row->id) ? "selected" : ((!empty($invMaster->id) && $invMaster->id == $row->id) ? "selected" : "");
    												}
    												echo "<option data-row='" . json_encode($row) . "' value='" . $row->id . "' " . $selected . ">" . $row->party_name . "</option>";
    												if (!empty($selected)) :
    													$partyData = $row;
    												endif;
    											endforeach;
											?>
										</select>

										<input type="hidden" name="party_name" id="party_name" value="<?= (!empty($invoiceData->party_name)) ? $invoiceData->party_name : ((!empty($invMaster->party_name)) ? $invMaster->party_name : "") ?>">
										<input type="hidden" name="party_state_code" id="party_state_code" value="<?= (!empty($invoiceData->party_state_code)) ? $invoiceData->party_state_code : ((!empty($invMaster->gstin)) ? substr($invMaster->gstin, 0, 2) : "") ?>">
									</div>
								</div>
								<div class="row form-group">
								    
									<div class="col-md-2">
										<label for="inv_date">Invoice Date</label>
										<input type="date" id="inv_date" name="inv_date" class=" form-control req" placeholder="dd-mm-yyyy" value="<?= (!empty($invoiceData->trans_date)) ? $invoiceData->trans_date : date("Y-m-d") ?>" min="<?=$startYearDate?>" max="<?=$endYearDate?>" />
									</div>
									<div class="col-md-3">
										<label for="party_alias">Customer Name</label>
										<input type="text" id="party_alias" name="party_alias" class=" form-control" value="<?= (!empty($invoiceData->party_alias)) ? $invoiceData->party_alias : "" ?>" />
									</div>
									<div class="col-md-2 form-group">
										<label for="gstin">GST No.</label>
										<select name="gstin" id="gstin" class="form-control ">
											<option value="" data-pincode='' data-address=''>Select GSTIN</option>
											<?php
											if (!empty($invMaster)) :
												$json_data = json_decode($invMaster->json_data);
												foreach ($json_data as $key => $row) :
													$selected = (!empty($invoiceData->gstin) && trim($invoiceData->gstin) == trim($key)) ? "selected" : "";
													echo "<option value='" . $key . "' data-pincode='" . $row->delivery_pincode . "' data-address='" . $row->delivery_address . "' " . $selected . ">" . $key . "</option>";
												endforeach;
											endif;
											?>
										</select>
									</div>
									<div class="col-md-2 form-group">
										<label for="gst_applicable">GST Applicable</label>
										<select name="gst_applicable" id="gst_applicable" class="form-control req">
											<?php if($this->CMID == 1){ ?>
												<option value="1" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 1) ? "selected" : "" ?>>Yes</option>
												<option value="0" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 0) ? "selected" : "" ?>>No</option>
											<?php }else{ ?> 
												<option value="1" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 1) ? "selected" : "" ?>>Yes</option>
												<option value="0" <?= (!empty($invoiceData) && $invoiceData->gst_applicable == 0) ? "selected" : "" ?>>No</option>
											<?php } ?>
										</select>
									</div>
									<!--
									<div class="col-md-2 form-group">
										<label for="apply_round">Apply Round Off</label>
										<select name="apply_round" id="apply_round" class="form-control single-select">
											<option value="0" <?= (!empty($invoiceData) && $invoiceData->apply_round == 0) ? "selected" : "" ?>>Yes</option>
											<option value="1" <?= (!empty($invoiceData) && $invoiceData->apply_round == 1) ? "selected" : "" ?>>No</option>
										</select>
									</div>-->
									<?php if($this->CMID == 1){ ?>
									    <div class="col-md-3">
									        <input type="hidden" name="invoice_type" id="invoice_type" value="Regular">
									<?php }else{ ?>
    									<div class="col-md-2">
    										<label for="invoice_type">Invoice Type</label>
    										<select name="invoice_type" id="invoice_type" class="form-control single-select">
    											<option value="Regular" <?= (!empty($invoiceData->invoice_type) && $invoiceData->invoice_type == 'Regular') ? "selected" : ""; ?>>Regular</option>
    											<option value="Semi-Wholesale" <?= (!empty($invoiceData->invoice_type) && $invoiceData->invoice_type == 'Semi-Wholesale') ? "selected" : ""; ?>>Semi-Wholesale</option>
    											<option value="Wholesale" <?= (!empty($invoiceData->invoice_type) && $invoiceData->invoice_type == 'Wholesale') ? "selected" : ""; ?>>Wholesale</option>
    										</select>
    									</div>
									    <div class="col-md-2">
									<?php } ?>
									
										<label><i class="fa fa-qrcode text-primary"></i> SCAN QR CODE</label>
										<input type="text" id="scan_qr" value="" class="form-control numericOnly" style="background:#93d2ff;color:#000000;font-weight:bold;" />
									</div>
									<input type="hidden" name="sales_type" id="sales_type" value="">
									<input type="hidden" name="challan_no" value="<?= (!empty($invoiceData->challan_no)) ? $invoiceData->challan_no : (!empty($dcTransNo) ? $dcTransNo : "") ?>" />
									<input type="hidden" name="so_no" value="<?= (!empty($invoiceData->doc_no)) ? $invoiceData->doc_no : (!empty($soTransNo) ? $soTransNo : "") ?>" />
									<input type="hidden" name="gross_weight" value="<?= (!empty($invoiceData->gross_weight)) ? $invoiceData->gross_weight : '' ?>" />
									<input type="hidden" name="eway_bill_no" value="<?= (!empty($invoiceData->eway_bill_no)) ? $invoiceData->eway_bill_no : '' ?>" />
									<input type="hidden" name="lrno" value="<?= (!empty($invoiceData->lr_no)) ? $invoiceData->lr_no : '' ?>" />
									<input type="hidden" name="transport" value="<?= (!empty($invoiceData->transport_name)) ? $invoiceData->transport_name : '' ?>" />
									<input type="hidden" name="supply_place" value="<?= (!empty($invoiceData->supply_place)) ? $invoiceData->supply_place : '' ?>" />
								</div>
							</div>
							<hr>
							<div class="col-md-12 invoiceItemForm">
								<div class="row form-group">
									<div id="itemInputs">
										<input type="hidden" name="trans_id" id="trans_id" value="" />
										<input type="hidden" name="from_entry_type" id="from_entry_type" value="">
										<input type="hidden" name="ref_id" id="ref_id" value="">
										<input type="hidden" name="stock_eff" id="stock_eff" value="1" data-resetval="1">
										<input type="hidden" name="item_name" id="item_name" value="" />
										<input type="hidden" name="item_type" id="item_type" value="" />
										<input type="hidden" name="item_code" id="item_code" value="" />
										<input type="hidden" name="item_desc" id="item_desc" value="" />
										<input type="hidden" name="hsn_code" id="hsn_code" value="" />
										<input type="hidden" name="gst_per" id="gst_per" value="" />
										<input type="hidden" name="row_index" id="row_index" value="">
										<input type="hidden" name="item_id" id="item_id" value="">
									</div>

									<div class="col-md-4 form-group">
										<label for="item_id">Product Name</label>
										<div for="party_id1" class="float-right">
											<span class="dropdown float-right">
												<a class="text-primary font-bold waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" datatip="Progress" flow="down">+ Add New</a>
												<div class="dropdown-menu dropdown-menu-left user-dd animated flipInY" x-placement="start-left">
													<div class="d-flex no-block align-items-center p-10 bg-primary text-white">ACTION</div>
													<a class="dropdown-item leadActionStatic addNewMaster" href="javascript:void(0)" data-button="both" data-modal_id="modal-lg" data-function="addProduct/1" data-controller="products" data-class_name="itemOptions" data-form_title="Add Product"> + Product</a>
												</div>
											</span>
										</div>
										<div class="input-group">
											<input type="text" id="item_name_dis" class="form-control" value="" readonly /> 
											<button type="button" class="btn btn-outline-primary" onclick="searchFGItems(1,6);" ><i class="fa fa-plus"></i></button>
										</div>
										<div class="error item_name"></div>
									</div>
									<div class="col-md-2 form-group">
										<label for="unit_id">Unit</label>
										<input type="text" name="unit_name" id="unit_name" class="form-control" value="" readonly />
										<input type="hidden" name="unit_id" id="unit_id" value="">
									</div>
									<div class="col-md-2 form-group">
										<label for="qty">Quantity</label>
										<input type="number" name="qty" id="qty" class="form-control floatOnly calculatePrice req" value="0">
									</div>
									<?php if($this->CMID == 1){ ?>
										<input type="hidden" name="location_id" id="location_id" value="<?=$this->RTD_STORE->id?>" data-resetval="<?=$this->RTD_STORE->id?>">	
									<?php }else{ ?>          
    									<div class="col-md-4 form-group">
    										<label for="location_id">Location</label>
    										<select name="location_id" id="location_id" class="form-control single-select req">
    											<?php
    												foreach($locationData as $row):													
    													echo '<option value="'.$row->id.'">'.$row->location.'</option>';													
    												endforeach;
    											?>
    										</select>
    									</div>
									<?php } ?>
									<div id="org_price_div" class="col-md-2 form-group" style="<?=($this->CMID == 2)?"display:none;":""?>">
									    <label for="org_price">MRP</label>
									    <input type="number" name="org_price" id="org_price" class="form-control floatOnly calculatePrice req" value="" />
									</div>
									<div class="col-md-2 form-group">
										<label for="price">Price</label>
										<input type="number" name="price" id="price" class="form-control floatOnly req" value="" <?=($this->CMID == 1)?"readonly":""?> />
									</div>
									<div class="col-md-2 form-group">
										<label for="disc_amt">Disc Amt.</label>
										<input type="number" name="disc_amt" id="disc_amt" class="form-control floatOnly calculatePrice" value="0" />
									</div>
									<div id="item_remark_div" class="col-md-<?=($this->CMID == 1)?"8":"6";?> form-group">
										<label for="item_remark">Remark</label>
										<input type="text" name="item_remark" id="item_remark" class="form-control" value="" />
									</div>
									<div class="col-md-2">
										<button type="button" class="btn btn-success waves-effect float-right btn-block mt-30 saveItem"><i class="fa fa-plus"></i> Add</button>
									</div>
								</div>
							</div>
							<div class="col-md-12 row">
								<div class="col-md-6">
									<h4>Item Details : </h4>
								</div>
								    <!-- <div class="col-md-3"><button type="button" class="btn btn-outline-success waves-effect float-right get-offers"><i class="fa fa-plus"></i>Get My Offers</button></div> -->
								<div class="col-md-6">
								    <!-- <button type="button" class="btn btn-outline-success waves-effect float-right add-item"><i class="fa fa-plus"></i> Add Item</button>-->
								</div> 
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
													<th>HSN Code</th>
													<th>Qty.</th>
													<th>Unit</th>
													<th>Price</th>
													<!--<th class="igstCol">IGST</th>
													<th class="cgstCol">CGST</th>
													<th class="sgstCol">SGST</th>-->
													<th>Disc.</th>
													<th class="amountCol">Amount</th>
													<th class="netAmtCol">Amount</th>
													<th>Remark</th>
													<th class="text-center" style="width:10%;">Action</th>
												</tr>
											</thead>
											<tbody id="tempItem" class="temp_item">
												<?php $totalQty=0;
												if (!empty($invoiceData->itemData)) :
													$i = 1; 
													foreach ($invoiceData->itemData as $row) :
													    $totalQty += $row->qty;
														if ($this->uri->segment(2) == "addSalesInvoiceOnSalesOrder") :
															$row->id = "";
														endif;
												?>
														<tr data-item_data='<?= htmlentities(json_encode($row)) ?>'>
															<td style="width:5%;">
																<?= $i ?>
															</td>
															<td>
																<?= $row->item_name ?>
																<input type="hidden" name="item_id[]" value="<?= $row->item_id ?>">
																<input type="hidden" name="item_name[]" value="<?= htmlentities($row->item_name) ?>">
																<input type="hidden" name="trans_id[]" value="<?= $row->id ?>">
																<input type="hidden" name="from_entry_type[]" value="<?= $row->from_entry_type ?>">
																<input type="hidden" name="ref_id[]" value="<?= $row->ref_id ?>">
																<input type="hidden" name="stock_eff[]" value="<?= $row->stock_eff ?>">
																<input type="hidden" name="location_id[]" value="<?= $row->location_id ?>">
																<input type="hidden" name="batch_no[]" value="<?= $row->batch_no ?>">
																<input type="hidden" name="batch_qty[]" value="<?= $row->batch_qty ?>">

																<input type="hidden" name="item_type[]" value="<?= $row->item_type ?>" /><input type="hidden" name="item_code[]" value="<?= $row->item_code ?>" /><input type="hidden" name="item_desc[]" value="<?= $row->item_desc ?>" /><input type="hidden" name="gst_per[]" value="<?= $row->gst_per ?>" />
																<div class="error batch_no<?= $i ?>"></div>
															</td>
															<td>
																<?= $row->hsn_code ?>
																<input type="hidden" name="hsn_code[]" value="<?= $row->hsn_code ?>">
															</td>
															<td>
																<?= $row->qty ?>
																<input type="hidden" name="qty[]" value="<?= $row->qty ?>">
																<div class="error qty<?= $i ?>"></div>
															</td>
															<td>
																<?= $row->unit_name ?>
																<input type="hidden" name="unit_id[]" value="<?= $row->unit_id ?>">
																<input type="hidden" name="unit_name[]" value="<?= $row->unit_name ?>">
															</td>
															<td>
																<?= $row->price ?>
																<input type="hidden" name="price[]" value="<?= $row->price ?>">
																<div class="error price<?= $i ?>"></div>
																<input type="hidden" name="org_price[]" value="<?= $row->org_price ?>" />
																<input type="hidden" name="cgst_amt[]" value="<?= $row->cgst_amount ?>">
																<input type="hidden" name="cgst[]" value="<?= $row->cgst_per ?>">
																<input type="hidden" name="sgst_amt[]" value="<?= $row->sgst_amount ?>">
																<input type="hidden" name="sgst[]" value="<?= $row->sgst_per ?>">
																<input type="hidden" name="igst_amt[]" value="<?= $row->igst_amount ?>">
																<input type="hidden" name="igst[]" value="<?= $row->igst_per ?>">
																<input type="hidden" name="igst[]" value="<?= $row->igst_per ?>">
															</td>
															<!--<td class="cgstCol">
																<?= $row->cgst_amount ?>(<?= $row->cgst_per ?>%)
																<input type="hidden" name="cgst_amt[]" value="<?= $row->cgst_amount ?>">
																<input type="hidden" name="cgst[]" value="<?= $row->cgst_per ?>">
															</td>
															<td class="sgstCol">
																<?= $row->sgst_amount ?>(<?= $row->sgst_per ?>%)
																<input type="hidden" name="sgst_amt[]" value="<?= $row->sgst_amount ?>">
																<input type="hidden" name="sgst[]" value="<?= $row->sgst_per ?>">
															</td>
															<td class="igstCol">
																<?= $row->igst_amount ?>(<?= $row->igst_per ?>%)
																<input type="hidden" name="igst_amt[]" value="<?= $row->igst_amount ?>">
																<input type="hidden" name="igst[]" value="<?= $row->igst_per ?>">
															</td>-->
															<td>
																<?= $row->disc_amount ?>(<?= $row->disc_per ?>%)
																<input type="hidden" name="disc_per[]" value="<?= $row->disc_per ?>">
																<input type="hidden" name="disc_amt[]" value="<?= $row->disc_amount ?>">
															</td>
															<td class="amountCol">
																<?= $row->taxable_amount ?>
																<input type="hidden" name="amount[]" value="<?= $row->taxable_amount ?>">
															</td>
															<td class="netAmtCol">
																<?= $row->net_amount ?>
																<input type="hidden" name="net_amount[]" value="<?= $row->net_amount ?>">
															</td>
															<td>
																<?= $row->item_remark ?>
																<input type="hidden" name="item_remark[]" value="<?= $row->item_remark ?>">
															</td>
															<td class="text-center" style="width:10%;">
																<?php
																$item_id = $row->item_id;
																$row->trans_id = $row->id;
																unset($row->entry_type);
																$row = json_encode($row);
																?>
																<button type="button" onclick='Edit(<?= $row ?>,this);' class="btn btn-outline-warning waves-effect waves-light edit_btn_<?=$item_id?>"><i class="ti-pencil-alt"></i></button>

																<button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
															</td>
														</tr>
													<?php $i++;
													endforeach;
												else : ?>
													<tr id="noData">
														<td colspan="13" class="text-center">No data available in table</td>
													</tr>
												<?php endif; ?>
											</tbody>
											<tfoot class="table-info">
												<tr>
													<th colspan="3">Total Qty.</th>
													<th><span class="totalQty"><?=$totalQty?></span></th>
													<th colspan="8"></th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<hr>
								<div class="col-md-12 row mb-3">
									<h4>Summary Details : </h4>
								</div>
								<!-- Created By Mansee @ 29-12-2021 -->
								<div class="row form-group">
									<div style="width:100%;">
										<table id="summaryTable" class="table">
											<thead class="table-info">
												<tr>
													<th style="width: 30%;">Descrtiption</th>
													<th style="width: 30%;">Ledger</th>
													<th style="width: 10%;">Percentage</th>
													<th style="width: 10%;">Amount</th>
													<th style="width: 20%;">Net Amount</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>Sub Total</td>
													<td></td>
													<td></td>
													<td></td>
													<td>
														<input type="text" name="taxable_amount" id="taxable_amount" class="form-control summaryAmount" value="0" readonly />
													</td>
												</tr>
												<?php
												$beforExp = "";
												$afterExp = "";
												$tax = "";
												$invExpenseData = (!empty($invoiceData->expenseData)) ? $invoiceData->expenseData : array();

												foreach ($expenseList as $row) :

													$expPer = 0;
													$expAmt = 0;
													$perFiledName = $row->map_code . "_per";
													$amtFiledName = $row->map_code . "_amount";
													if (!empty($invExpenseData) && $row->map_code != "roff") :
														$expPer = $invExpenseData->{$perFiledName};
														$expAmt = abs($invExpenseData->{$amtFiledName});
													endif;

													$options = '<select class="form-control single-select" name="' . $row->map_code . '_acc_id" id="' . $row->map_code . '_acc_id">';

													foreach ($ledgerList as $ledgerRow) :
														if ($ledgerRow->group_code != "DT") :
															$filedName = $row->map_code . "_acc_id";
															if (!empty($invExpenseData->{$filedName})) :
																if ($row->map_code != "roff") :
																	$selected = ($ledgerRow->id == $invExpenseData->{$filedName}) ? "selected" : (($ledgerRow->id == $row->acc_id) ? 'selected' : '');
																else :
																	$selected = ($ledgerRow->id == $invoiceData->round_off_acc_id) ? "selected" : (($ledgerRow->id == $row->acc_id) ? 'selected' : '');
																endif;
															else :
																$selected = ($ledgerRow->id == $row->acc_id) ? 'selected' : '';
															endif;

															$options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
														endif;
													endforeach;
													$options .= '</select>';

													if ($row->position == 1) :
														$beforExp .= '<tr>
															<td>' . $row->exp_name . '</td>
															<td>' . $options . '</td>
															<td>';

														$readonly = "";
														$perBoxType = "number";
														$calculateSummaryPer = "calculateSummary";
														$calculateSummaryAmt = "calculateSummary";
														if ($row->calc_type != 1) :
															$perBoxType = "number";
															$readonly = "readonly";
															$calculateSummaryPer = "calculateSummary";
															$calculateSummaryAmt = "";
														else :
															$perBoxType = "hidden";
															$readonly = "";
															$calculateSummaryPer = "";
															$calculateSummaryAmt = "calculateSummary";
														endif;



														$beforExp .= "<input type='" . $perBoxType . "' name='" . $row->map_code . "_per' id='" . $row->map_code . "_per' data-row='" . json_encode($row) . "' value='" . $expPer . "' class='form-control " . $calculateSummaryPer . "'> ";

														$beforExp .= "</td>
														<td><input type='number' id='" . $row->map_code . "_amt' class='form-control " . $calculateSummaryAmt . "' data-sm_type='exp' data-row='" . json_encode($row) . "' value='" . $expAmt . "' " . $readonly . "></td>
														<td><input type='number' name='" . $row->map_code . "_amount' id='" . $row->map_code . "_amount'  value='0' class='form-control summaryAmount' readonly /> <input type='hidden' id='other_" . $row->map_code . "_amount' class='otherGstAmount' value='0'> </td>
														</tr>";

													else :

														$afterExp .= '<tr>
															<td>' . $row->exp_name . '</td>
															<td>' . $options . '</td><td>';

														$readonly = "";
														$perBoxType = "number";
														$calculateSummaryPer = "calculateSummary";
														$calculateSummaryAmt = "calculateSummary";
														if ($row->map_code != "roff" && $row->calc_type != 1) :
															$perBoxType = "number";
															$readonly = "readonly";
															$calculateSummaryPer = "calculateSummary";
															$calculateSummaryAmt = "";
														else :
															$perBoxType = "hidden";
															$readonly = "";
															$calculateSummaryPer = "";
															$calculateSummaryAmt = "calculateSummary";
														endif;

														$afterExp .= "<input type='" . $perBoxType . "' name='" . $row->map_code . "_per' id='" . $row->map_code . "_per' data-row='" . json_encode($row) . "' value='" . $expPer . "' class='form-control " . $calculateSummaryPer . "'> ";

														$readonly = ($row->map_code == "roff") ? "readonly" : $readonly;
														$amtType = ($row->map_code == "roff") ? "hidden" : "number";
														$afterExp .= "</td>
														<td><input type='" . $amtType . "' id='" . $row->map_code . "_amt' class='form-control " . $calculateSummaryAmt . "' data-sm_type='exp' data-row='" . json_encode($row) . "' value='" . $expAmt . "' " . $readonly . "></td>
														<td><input type='number' name='" . $row->map_code . "_amount' id='" . $row->map_code . "_amount' value='0' class='form-control " . (($row->map_code == "roff") ? "" : "summaryAmount") . "' readonly /> </td>
														</tr>";
													endif;
												endforeach;

												foreach ($taxList as $taxRow) :
													$options = '<select class="form-control single-select" name="' . $taxRow->map_code . '_acc_id" id="' . $taxRow->map_code . '_acc_id">';

													foreach ($ledgerList as $ledgerRow) :
														if ($ledgerRow->group_code == "DT") :
															$filedName = $taxRow->map_code . "_acc_id";
															if (!empty($invoiceData->{$filedName})) :
																$selected = ($ledgerRow->id == $invoiceData->{$filedName}) ? "selected" : (($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '');
															else :
																$selected = ($ledgerRow->id == $taxRow->acc_id) ? 'selected' : '';
															endif;

															$options .= '<option value="' . $ledgerRow->id . '" ' . $selected . '>' . $ledgerRow->party_name . '</option>';
														endif;
													endforeach;
													$options .= '</select>';

													$taxClass = "";
													$perBoxType = "number";
													$calculateSummary = "calculateSummary";
													$taxPer = 0;
													$taxAmt = 0;
													if (!empty($invoiceData->id)) :
														$taxPer = $invoiceData->{$taxRow->map_code . '_per'};
														$taxAmt = $invoiceData->{$taxRow->map_code . '_amount'};
													endif;
													if ($taxRow->map_code == "cgst") :
														$taxClass = "cgstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													elseif ($taxRow->map_code == "sgst") :
														$taxClass = "sgstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													elseif ($taxRow->map_code == "igst") :
														$taxClass = "igstCol";
														$perBoxType = "hidden";
														$calculateSummary = "";
													endif;

													$tax .= '<tr class="' . $taxClass . '">
														<td>' . $taxRow->name . '</td>
														<td>' . $options . '</td>
														<td>';

													$tax .= "<input type='" . $perBoxType . "' name='" . $taxRow->map_code . "_per' id='" . $taxRow->map_code . "_per' data-row='" . json_encode($taxRow) . "' value='" . $taxPer . "' class='form-control " . $calculateSummary . "'> ";

													$tax .= "</td>
														<td><input type='" . $perBoxType . "' id='" . $taxRow->map_code . "_amt' class='form-control' data-sm_type='tax'data-row='" . json_encode($taxRow) . "' value='" . $taxAmt . "' readonly ></td>
														<td><input type='number' name='" . $taxRow->map_code . "_amount' id='" . $taxRow->map_code . "_amount'  value='0' class='form-control summaryAmount' readonly /> </td>
													</tr>";
												endforeach;

												echo $beforExp;
												echo $tax;
												echo $afterExp;
												?>

											</tbody>
											<tfoot class="table-info">
												<tr>
													<th>Net. Amount</th>
													<th></th>
													<th></th>
													<th></th>
													<td>
														<input type="text" name="net_inv_amount" id="net_inv_amount" class="form-control" value="0" readonly />
													</td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<hr>
								<div class="row form-group">
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-9 form-group">
												<label for="remark">Remark</label>
												<input type="text" name="remark" class="form-control" value="<?= (!empty($invoiceData->remark)) ? $invoiceData->remark : "" ?>" />
											</div>
											<div class="col-md-3 form-group">
												<label for="">&nbsp;</label>
												<button type="button" class="btn btn-outline-success waves-effect btn-block" data-toggle="modal" data-target="#termModel">Terms & Conditions (<span id="termsCounter">0</span>)</button>
												<div class="error term_id"></div>
											</div>
										</div>
									</div>
								</div>
								
								<hr>
								<div class="row form-group voucherDetails1" style="display:none;">
									<div class="col-md-12 row mb-3">
										<h4>Voucher Details : </h4>
									</div>
									<div class="col-md-12">
										<div class="row">
											<div class="col-md-3 form-group">
												<label>Doc. No.</label>
												<input type="text" class="form-control" id="voucher_doc_no" name="voucher_doc_no" value="<?= (!empty($voucherData->doc_no)) ? $voucherData->doc_no : ""; ?>">
												<input type="hidden" id="voucher_id" name="voucher_id" value="<?=!empty($voucherData->id)?$voucherData->id:''?>">
											</div>
											<div class="col-md-3 form-group">
												<label>Doc. Date</label>
												<input type="date" class="form-control" id="voucher_doc_date" name="voucher_doc_date" value="<?= (!empty($voucherData->doc_date)) ? $voucherData->doc_date : date("Y-m-d"); ?>">
											</div>
											<div class="col-md-3 form-group">
												<label>Payment Mode</label>
												<select name="trans_mode" id="trans_mode" class="form-control single-select">
													<option value="">Select Payment Mode</option>
													<?php
														if(!empty($paymentMode)):
															foreach ($paymentMode as $row) :
																$selected = (!empty($voucherData->trans_mode) && $row == $voucherData->trans_mode) ? "selected" : "";
																echo '<option value="' . $row . '" ' . $selected . '>' . $row . '</option>';
															endforeach;
														endif;
													?>
												</select>
											</div>
											<div class="col-md-3 form-group">
												<label>Ledger Name</label>
												<select name="vou_acc_id" id="vou_acc_id" class="form-control single-select">
													<option value="">Select Ledger</option>
													<?php
														if(!empty($ledgerData)):
															foreach ($ledgerData as $row) :
																$selected = ($row->id == $voucherData->vou_acc_id) ? "selected" : "";
																echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->party_name . '</option>';
															endforeach;
														endif;
													?>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="modal fade" id="termModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
								<div class="modal-dialog modal-lg" role="document" style="max-width:70%;">
									<div class="modal-content animated slideDown">
										<div class="modal-header">
											<h4 class="modal-title">Terms & Conditions</h4>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										</div>
										<div class="modal-body">
											<div class="col-md-12 mb-10">
												<table id="terms_condition" class="table table-bordered dataTable no-footer">
													<thead class="thead-info">
														<tr>
															<th style="width:10%;">#</th>
															<th style="width:25%;">Title</th>
															<th style="width:65%;">Condition</th>
														</tr>
													</thead>
													<tbody>
														<?php
														if (!empty($terms)) :
															$termaData = (!empty($dataRow->terms_conditions)) ? json_decode($dataRow->terms_conditions) : array();
															$i = 1;
															$j = 0;
															foreach ($terms as $row) :
																$checked = "";
																$disabled = "disabled";
																if (in_array($row->id, array_column($termaData, 'term_id'))) :
																	$checked = "checked";
																	$disabled = "";
																	$row->conditions = $termaData[$j]->condition;
																	$j++;
																endif;
																if (empty($dataRow->id)) :
																	if ($row->default == 1) :
																		$checked = "checked";
																		$disabled = "";
																	endif;
																endif;
														?>
																<tr>
																	<td style="width:10%;">
																		<input type="checkbox" id="md_checkbox<?= $i ?>" class="filled-in chk-col-success termCheck" data-rowid="<?= $i ?>" check="<?= $checked ?>" <?= $checked ?> />
																		<label for="md_checkbox<?= $i ?>"><?= $i ?></label>
																	</td>
																	<td style="width:25%;">
																		<?= $row->title ?>
																		<input type="hidden" name="term_id[]" id="term_id<?= $i ?>" value="<?= $row->id ?>" <?= $disabled ?> />
																		<input type="hidden" name="term_title[]" id="term_title<?= $i ?>" value="<?= $row->title ?>" <?= $disabled ?> />
																	</td>
																	<td style="width:65%;">
																		<input type="text" name="condition[]" id="condition<?= $i ?>" class="form-control" value="<?= $row->conditions ?>" <?= $disabled ?> />
																	</td>
																</tr>
															<?php
																$i++;
															endforeach;
														else :
															?>
															<tr>
																<td class="text-center" colspan="3">No data available in table</td>
															</tr>
														<?php
														endif;
														?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
											<button type="button" class="btn waves-effect waves-light btn-outline-success" data-dismiss="modal"><i class="fa fa-check"></i> Save</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="card-footer">
						<div class="col-md-12">
							<button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveInvoice('saveSalesInvoice');"><i class="fa fa-check"></i> Save</button>
							<a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1">Create Invoice - <span id="partyName"></span></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="party_so" method="post" action="">
				<div class="modal-body">
				    <div class="row">
    					<div class="col-md-4 from-group">
    					    <label for="bill_per">Bill(%)</label>
    					    <input type="text" name="bill_per" id="bill_per" class="form-control" value="100">
    	                </div>
    	                
    					<input type="hidden" name="party_id" id="party_id_so" value="">
    					<input type="hidden" name="party_name" id="party_name_so" value="">
    					<input type="hidden" name="from_entry_type" id="from_entry_type_so" value="">
    					
    					<div class="col-md-12 from-group">
    						<div class="error general"></div>
    						<div class="table-responsive">
    							<table id="orderTable" class="table table-bordered">
    								<thead class="thead-info">
    									<tr>
    										<th class="text-center">#</th>
    										<th class="text-center">P.Inv. No.</th>
    										<th class="text-center">P.Inv. Date</th>
    										<th class="text-center">Customer</th>
    										<th class="text-center">Part Name</th>
    										<th class="text-center">Price</th>
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
				</div>
				<div class="modal-footer">
					<button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
					<button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create Challan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="offerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content animated slideDown">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel1">Current Offers</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<form id="offer" method="post" action="">
				<div class="modal-body">
					
					<input type="hidden" name="id" id="id" value="">
					<div class="col-md-12">
						<div class="error general"></div>
						<div class="table-responsive">
							<table id="offerTable" class="table table-bordered">
								<thead class="thead-info">
									<tr>
										<th class="text-center">#</th>
										<th class="text-center">Title</th>
										<th class="text-center">Product</th>
										<th class="text-center">Percentage</th>
										<th class="text-center">Amount</th>
									</tr>
								</thead>
								<tbody id="offerData" >
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
					<button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Apply Offers</button>
					<!-- <button type="button" class="btn waves-effect waves-light btn-outline-success float-right " onclick="applyOffer('offer');"><i class="fa fa-check"></i> Save</button> -->

				</div>
			</form>
		</div>
	</div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/sales-invoice-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>
<script>
$(document).ready(function(){
	$('#scan_qr').focus();
	$('#party_id').trigger('change');
	$('#memo_type').trigger('change');
	//if(cm_id==1)
	//{
    	$(document).on('change','#memo_type',function(){
    	   var memo_type = $(this).val();
    	   $.ajax({
    			url: base_url + 'salesInvoice/getInvNo',
    			type:'post',
    			data:{memo_type:memo_type},
    			dataType:'json',
    			success:function(data){
    				if(data.status==1)
    				{
    					$('#inv_prefix').val(data.inv_prefix);
    					$('#inv_no').val(data.inv_no);
    				}
    				else
    				{
    				    if(cm_id==1){$('#memo_type').val('CASH');}
    				    else{$('#memo_type').val('DEBIT');}
    				    
    				}
    			}
    		});
    	    
    	});
	//}
	
	/*$(document).on('change','#party_id',function(){
	   if(cm_id==1)
	   {
	       var party_id = $(this).val();
	       console.log(party_id);
	       if(party_id == 112){$('.gstField').hide();$('.guestParty').show();}else{$('.guestParty').hide();$('.gstField').show();}
    	   
	   }
	   else{$('.guestParty').hide();$('.gstField').show();}
	    
	});*/
});
</script>
<?php
if (!empty($invItems)) {
	foreach ($invItems as $row) :
		$row->qty = $row->qty;
		if (!empty($row->qty)) :
			$row->trans_id = "";
			$row->row_index = "";
			$row->from_entry_type = $row->entry_type;
			$row->ref_id = $row->id;
			$row->hsn_code = (!empty($row->hsn_code)) ? $row->hsn_code : "";
			$row->gst_type = $gst_type;
			$row->location_id = $this->RTD_STORE->id;
			if($row->entry_type == 9 && !empty($bill_per)){
			    $row->price = round(($row->price * $bill_per / 100),2);
			}
			$row->price = $row->price;
			
			if (empty($row->disc_per)) :
				$row->disc_per = 0;
				$row->disc_amt = 0;
				$row->amount = round($row->qty * $row->price, 2);
			else :
				$row->disc_amt = round((($row->qty * $row->price) * $row->disc_per) / 100, 2);
				$row->amount = round(($row->qty * $row->price) - $row->disc_amt, 2);
			endif;
			$row->igst_per = $row->gst_per;
			$row->igst_amt = round(($row->amount * $row->igst_per) / 100, 2);

			$row->cgst_per = round(($row->igst_per / 2), 2);
			$row->cgst_amt = round(($row->igst_amt / 2), 2);
			$row->sgst_per = round(($row->igst_per / 2), 2);
			$row->sgst_amt = round(($row->igst_amt / 2), 2);

			$row->net_amount = round($row->amount + $row->igst_amt, 2);
			$row->stock_eff = ($row->stock_eff == 1) ? 0 : 1;
			unset($row->entry_type);
			$row = json_encode($row);
			echo '<script>AddRow(' . $row . ');</script>';
		endif;
	endforeach;
}
?>