<form>

    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="item_type" value="<?=(!empty($dataRow->item_type))?$dataRow->item_type:$item_type; ?>" />

            <div class="col-md-4 form-group">
                <label for="item_code">Item Code</label>
                <input type="text" name="item_code" class="form-control" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:""; ?>" />
            </div>
            <?php $cmID = $this->session->userdata('CMID'); ?>
            <div class="col-md-8 form-group">
                <label for="item_name">Item Name</label>
				<input type="text" name="item_name" class="form-control req" value="<?=htmlentities((!empty($dataRow->item_name)) ? $dataRow->item_name : "")?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="unit_id">Unit</label>
                <select name="unit_id" id="unit_id" class="form-control single-select req">
                    <option value="0">--</option>
                    <?php
                        foreach($unitData as $row):
                            $selected = (!empty($dataRow->unit_id) && $dataRow->unit_id == $row->id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>['.$row->unit_name.'] '.$row->description.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="hsn_code">HSN Code</label>
                <input type="text" name="hsn_code" class="form-control" value="<?=(!empty($dataRow->hsn_code))?$dataRow->hsn_code:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="gst_per">GST Per.</label>
                <select name="gst_per" id="gst_per" class="form-control single-select">
                    <?php
                        foreach($gstPercentage as $row):
                            $selected = (!empty($dataRow->gst_per) && $dataRow->gst_per == $row['rate'])?"selected":"";
                            echo '<option value="'.$row['rate'].'" '.$selected.'>'.$row['val'].'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <?php if($cmID == 1){ ?>
            <div class="col-md-3 form-group">
                <label for="price1">Price</label>
                <input type="text" name="price1" id="price1" min="0" class="form-control floatOnly" value="<?=(!empty($dataRow->price1))?$dataRow->price1:""?>" />
            </div>

            <?php }else{ ?>

            <div class="col-md-3 form-group">
            <label for="price2">Price</label>
            <input type="text" name="price2" id="price2" min="0" class="form-control floatOnly" value="<?=(!empty($dataRow->price2))?$dataRow->price2:""?>" />
            </div>
            <?php }?>
			<div class="col-md-3 form-group">
                <label for="min_qty">Min. Qty.</label>
                <input type="text" name="min_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
            </div>

            <div class="col-md-3 form-group">
                <label for="size">Size</label>
                <input type="text" name="size" class="form-control" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="cm_id">Comman Item</label>
                <select name="cm_id" id="cm_id" class="form-control single-select">
                   <option value="0" <?=(!empty($dataRow->cm_id) && $dataRow->cm_id==0)?'selected':''?>>YES</option>
                   <option value="<?=$this->CMID?>" <?=(!empty($dataRow->cm_id) && $dataRow->cm_id==$this->CMID)?'selected':''?>>NO</option>
                </select>
            </div>
			<div class="col-md-12 form-group">
                <label for="description">Remark</label>
                <input type="text" name="description" class="form-control" value="<?= (!empty($dataRow->description)) ? $dataRow->description : "" ?>" />
            </div>
        </div>
    </div>
</form>