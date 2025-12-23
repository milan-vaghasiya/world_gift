<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />

            <div class="col-md-12 form-group">
                <label for="hsn">HSN Code</label>
                <input type="text" name="hsn" class="form-control "
                    value="<?=(!empty($dataRow->hsn))?$dataRow->hsn:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="type">HSN Type</label>
                <select name="type" id="type" class="form-control single-select">
                    <option value="">--Select Hsn Type--</option>
                    <option value="HSN" <?=(!empty($dataRow->type) && $dataRow->type=="HSN")?'selected':''?>>HSN
                    </option>
                    <option value="SAC" <?=(!empty($dataRow->type) && $dataRow->type=="SAC")?'selected':''?>>SAC
                    </option>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="igst">GST %</label>
                <select name="igst" id="igst" class="form-control single-select">
                    <?php
                    foreach ($gstPercentage as $row) :
                        $selected = (!empty($dataRow->igst) && $dataRow->igst == $row['rate']) ? "selected" : "";
                        echo '<option value="' . $row['rate'] . '" ' . $selected . '>' . $row['val'] . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
        </div>
    </div>