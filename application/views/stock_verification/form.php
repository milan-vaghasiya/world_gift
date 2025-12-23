<form enctype="multipart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:'' ?>" />
            <input type="hidden" name="trans_number" id="trans_number" value="<?=(!empty($dataRow->trans_number))?$dataRow->trans_number:'' ?>" />
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow->item_id))?$dataRow->item_id:'' ?>" />

			<div class="col-md-6">
				<label for="entry_date">Effect Date</label>
				<input type="date" id="entry_date" name="entry_date" class=" form-control req"  value="<?= (!empty($dataRow->entry_date)) ? $dataRow->entry_date : date("Y-m-d") ?>" />
			</div>
			<div class="col-md-6 form-group">
                <label for="qty">Physical Stock</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly" value="<?=(!empty($dataRow->qty))?$dataRow->qty:""; ?>" />
            </div>
			<div class="col-md-6 form-group">
                <label for="system_stock">System Stock</label>
                <input type="text" name="system_stock" id="system_stock" class="form-control floatOnly" value="<?=(empty($dataRow->system_stock))?$system_stock:$system_stock; ?>" readonly/>
            </div>
			<div class="col-md-6 form-group">
                <label for="variation">Variation</label>
                <input type="text" name="variation" id="variation" class="form-control floatOnly" value="<?= $variation; ?>" readonly/>
            </div>
			<div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" rows="2" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""; ?></textarea>
            </div>
        </div>
    </div>
</form>