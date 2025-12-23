<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4><u>Stock Transfer</u></h4>
                    </div>
                    <div class="card-body">
                        <form autocomplete="off" id="saveSalesInvoice">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label for="trans_no">Voucher No.</label>
                                        <input type="text" class="form-control" name="trans_no" value="<?= (!empty($dataRow)) ? $dataRow->trans_no : $nextTransNo ?>" readonly>
                                        <input type="hidden" class="form-control" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : '' ?>" readonly>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="trans_date">Voucher Date</label>
                                        <input type="date" class="form-control" name="trans_date" value="<?= (!empty($dataRow)) ? $dataRow->trans_date : date("Y-m-d") ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="doc_no">Doc. No.</label>
                                        <input type="text" class="form-control" name="doc_no" value="<?= (!empty($dataRow)) ? $dataRow->doc_no : '' ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="doc_date">Doc. Date</label>
                                        <input type="date" class="form-control" name="doc_date" value="<?= (!empty($dataRow)) ? $dataRow->doc_date :  date("Y-m-d") ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="from_location_id">From Store Location</label>
                                        <select name="from_location_id" id="from_location_id" class="form-control single-select  req">
                                            <option value="">Select Location</option>
                                            <?php
                                            foreach ($locationData as $lData) :
                                                foreach ($lData['location'] as $row) :
                                                    $selected = (!empty($dataRow->from_location_id) && $dataRow->from_location_id == $row->id) ? 'selected' : '';
                                                    echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->location . ' </option>';

                                                endforeach;
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="to_location_id">To Store Location</label>
                                        <select name="to_location_id" id="to_location_id" class="form-control single-select req">
                                            <option value="">Select Location</option>
                                            <?php
                                            foreach ($locationData as $lData) :
                                                foreach ($lData['location'] as $row) :
                                                    $selected = (!empty($dataRow->to_location_id) && $dataRow->to_location_id == $row->id) ? 'selected' : '';
                                                    echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->location . ' </option>';
                                                endforeach;
                                            endforeach;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="remark">Note</label>
                                        <input type="text" class="form-control" name="remark" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""; ?>">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12 invoiceItemForm">
                                <div class="row form-group">
                                    <div id="itemInputs">
                                        <input type="hidden" name="trans_id" id="trans_id" value="" />
                                        <input type="hidden" name="item_name" id="item_name" value="" />
                                        <input type="hidden" name="row_index" id="row_index" value="">
                                        <input type="hidden" name="item_id" id="item_id" value="">
                                    </div>

                                    <div class="col-md-7 form-group">
                                        <label for="item_id">Product Name</label>

                                        <div for="party_id1" class="float-right">
                                            <b>
												<span class="dropdown text-primary">Stock : </span>
												<span class="dropdown" id="stockQty"></span>
											</b>
                                        </div>
                                        <!-- <div class="input-group">
                                            <input type="text" id="item_name_dis" class="form-control" value="" readonly />
                                            <button type="button" class="btn btn-outline-primary" onclick="searchFGItemsOnLocation();"><i class="fa fa-plus"></i></button>
                                        </div> -->
                                        <select name="item_id" id="item_id" class="form-control large-select2 req" data-item_type="" data-category_id="" data-family_id="" autocomplete="off" data-default_id="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : "" ?>" data-default_text="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : "" ?>" data-url="products/getDynamicItemList">
                                            <option value="">Select Item</option>
                                        </select>
                                        <div class="error item_name"></div>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="qty">Quantity</label>
                                        <input type="number" name="qty" id="qty" class="form-control floatOnly req" value="0">
                                    </div>
                                    <div class="col-md-1 form-group">
                                        <button type="button" class="btn btn-outline-success waves-effect  mt-30 saveItem"><i class="fa fa-plus"></i> Add</button>
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
                                                    <th>Qty.</th>
                                                    <th class="text-center" style="width:10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tempItem" class="temp_item">
                                                <?php $totalQty=0;
                                                if (!empty($dataRow->itemData)) :
                                                    $i = 1; 
                                                    foreach ($dataRow->itemData as $row) :
                                                        $totalQty += $row->qty;
                                                        if ($this->uri->segment(2) == "addSalesInvoiceOnSalesOrder") :
                                                            $row->id = "";
                                                        endif;
                                                ?>
                                                        <tr>
                                                            <td style="width:5%;">
                                                                <?= $i ?>
                                                            </td>
                                                            <td>
                                                                <?= $row->item_name ?>
                                                                <input type="hidden" name="item_id[]" value="<?= $row->item_id ?>">
                                                                <input type="hidden" name="item_name[]" value="<?= htmlentities($row->item_name) ?>">
                                                                <input type="hidden" name="trans_id[]" value="<?= $row->id ?>">

                                                            </td>
                                                            <td>
                                                                <?= abs($row->qty) ?>
                                                                <input type="hidden" name="qty[]" value="<?= abs($row->qty) ?>">
                                                                <div class="error qty<?= $i ?>"></div>
                                                            </td>
                                                            <td class="text-center" style="width:10%;">
                                                                <?php
                                                                $row->trans_id = $row->id;
                                                                unset($row->entry_type);
                                                                $row = json_encode($row);
                                                                ?>
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
													<th colspan="2">Total Qty.</th>
													<th><span class="totalQty"><?= abs($totalQty) ?></span></th>
													<th></th>
												</tr>
											</tfoot>
                                        </table>
                                    </div>
                                </div>



                            </div>

                        </form>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveStockTrasfer('saveSalesInvoice');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url(); ?>assets/js/custom/stock-transfer-form.js?v=<?= time() ?>"></script>
<script src="<?php echo base_url(); ?>assets/js/custom/master-form.js?v=<?= time() ?>"></script>
<script>
    $(document).ready(function() {
        <?php
        if (!empty($dataRow->id)) {
           
        ?>
            // setTimeout(function() {
                var from_location_id = $("#from_location_id").val();
                $("#to_location_id option").attr("disabled", false);
                $("#to_location_id option[value=" + from_location_id + "]").attr("disabled", "disabled");
                dataSet = {
                    'location_id': from_location_id
                };

                getDynamicItemList(dataSet);
                setPlaceHolder();
            // }, 1500);


        <?php
        }
        ?>
    });
</script>