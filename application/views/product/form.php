<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
        <?php 
                $cmID = $this->session->userdata('CMID');
                $col_md = ($cmID == 1)? 'col-md-4' : 'col-md-3';
                $col_md_r = ($cmID == 1)? 'col-md-12' : 'col-md-9';
        ?>
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : 1; ?>" />
			
			<input type="hidden" name="min_qty" value="0" />
            <div class="col-md-3 form-group">
                <label for="item_name">Item Name</label>
                <input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?= (!empty($dataRow->item_code)) ? $dataRow->item_code : "" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="size">Size</label>
                <input type="text" name="size" class="form-control" value="<?= (!empty($dataRow->size)) ? $dataRow->size : "" ?>" />
            </div>
            <div class="<?=$col_md?> form-group">
                <label for="hsn_code">HSN Code</label>
                <select name="hsn_code" id="hsn_code" class="form-control large-select2" data-default_id="<?= (!empty($dataRow->hsn_code) ? $dataRow->hsn_code : '') ?>" data-default_text="<?= (!empty($dataRow->hsn_code) ? $dataRow->hsn_code : '') ?>" data-url="products/getDynamicHSNList">
                    <option value="">Select HSN</option>
                </select>
            </div>
            <?php if($cmID == 1){ ?>
                <div class="<?=$col_md?> form-group">
                    <label for="price1">Default Price</label>
                    <input type="text" name="price1" id="price1" min="0" class="form-control floatOnly" value="<?= (!empty($dataRow->price1)) ? $dataRow->price1 : "" ?>" />
                </div>
                <div class="<?=$col_md?> form-group">
                    <label for="prc_price1">Purchase Price</label>
                    <input type="text" name="prc_price1" id="prc_price1" min="0" class="form-control floatOnly" value="<?= (!empty($dataRow->prc_price1)) ? $dataRow->prc_price1 : "" ?>" />
                </div>
            <?php }else{ ?>
                <div class="<?=$col_md?> form-group">
                    <label for="price2">Default Price</label>
                    <input type="text" name="price2" id="price2" min="0" class="form-control floatOnly" value="<?= (!empty($dataRow->price2)) ? $dataRow->price2 : "" ?>" />
                </div>
                <div class="<?=$col_md?> form-group">
                    <label for="prc_price2">Purchase Price</label>
                    <input type="text" name="prc_price2" id="prc_price2" min="0" class="form-control floatOnly" value="<?= (!empty($dataRow->prc_price2)) ? $dataRow->prc_price2 : "" ?>" />
                </div>
                <div class="<?=$col_md?> form-group">
                    <label for="wholesale1">Semi Wholesale Price</label>
                    <input type="text" name="wholesale1" id="wholesale1" min="0" class="form-control floatOnly" value="<?= (!empty($dataRow->wholesale1)) ? $dataRow->wholesale1 : "" ?>" />
                </div>
                <div class="<?=$col_md?> form-group">
                    <label for="wholesale2">Wholesale Price</label>
                    <input type="text" name="wholesale2" id="wholesale2" min="0" class="form-control floatOnly" value="<?= (!empty($dataRow->wholesale2)) ? $dataRow->wholesale2 : "" ?>" />
                </div>
            <?php }?>
           
            <div class="col-md-3">
                <label for="unit_id">Unit</label>
                <select name="unit_id" id="unit_id" class="form-control single-select req">
                    <option value="0">--</option>
                    <?php
                    foreach ($unitData as $row) :
                        $selected = (!empty($dataRow->unit_id) && $dataRow->unit_id == $row->id) ? "selected" : "";
						if(empty($dataRow) && $row->id == 27){$selected = "selected";}
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->unit_name . '] ' . $row->description . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            
            <div class="col-md-3 form-group">
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select">
                    <?php
                    foreach ($gstPercentage as $row) :
                        $selected = (!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate']) ? "selected" : "";
                        echo '<option value="' . $row['rate'] . '" ' . $selected . '>' . $row['val'] . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="cm_id">Comman Item</label>
                <select name="cm_id" id="cm_id" class="form-control single-select">
                   <option value="0" <?=(!empty($dataRow->cm_id) && $dataRow->cm_id==0)?'selected':''?>>YES</option>
                   <option value="<?=$this->CMID?>" <?=(!empty($dataRow->cm_id) && $dataRow->cm_id==$this->CMID)?'selected':''?>>NO</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="item_image">Item Image</label>
                <input type="file" name="item_image" class="form-control-file" />
            </div>
            
            <div class="<?=$col_md_r?> form-group">
                <label for="description">Product Description</label>
                <input type="text" name="description" class="form-control" value="<?=(!empty($dataRow->description)) ? $dataRow->description:"" ?>" />
            </div>
        </div>
    </div>
</form>
<script src="<?php echo base_url();?>assets/js/custom/master-form.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
    <?php if(empty($dataRow->hsn_code)): ?>
        dataSet = {};
        getDynamicHSNList(dataSet);
    <?php else: ?>
        dataSet = {'id':<?=$dataRow->hsn_code?>, 'text':<?=$dataRow->hsn_code?>};
        getDynamicHSNList(dataSet);
    <?php endif; ?>
    
    setPlaceHolder();
    $(document).on('keyup','#material_gradec',function(){
        $('#gradeName').val($(this).val());
    });
});
