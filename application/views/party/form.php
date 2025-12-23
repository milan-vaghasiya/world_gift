<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <!-- <input type="hidden" name="party_category" id="party_category" value="<?=(!empty($dataRow->party_category))?$dataRow->party_category:$party_category; ?>" /> -->
			<input type="hidden" name="disc_per" class="form-control floatOnly" value="<?=(!empty($dataRow->disc_per))?$dataRow->disc_per:""?>" />
            <input type="hidden" name="party_type" id="party_type" value="" />
            <input type="hidden" name="currency" id="currency" value="INR" />

            <div class="col-md-3 form-group">
                <label for="party_name">Party Name</label>
                <input type="text" name="party_name" class="form-control text-capitalize req" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:""; ?>" />
            </div>
            <!-- <?php
				$pcategory = (!empty($dataRow->party_category))?$dataRow->party_category:$party_category;
            ?> -->
            
            <div class="col-md-2 form-group">
                <label for="party_code">Party Code</label>
                <input type="text" name="party_code" class="form-control" value="<?=(!empty($dataRow->party_code)) ? $dataRow->party_code : "" ?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="party_phone">Contact No.</label>
                <input type="number" name="party_phone" class="form-control numericOnly req" value="<?=(!empty($dataRow->party_phone))?$dataRow->party_phone:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="party_category">Party Type</label>
                <select name="party_category" id="party_category" class="form-control single-select " >
                    <option value="1" <?=(!empty($dataRow->party_category) && $dataRow->party_category == 1)?"selected":"" ;?>>Customer</option>
                    <option value="3" <?=(!empty($dataRow->party_category) && $dataRow->party_category == 3)?"selected":"" ;?>>Supplier</option>
                    <option value="5" <?=(!empty($dataRow->party_category) && $dataRow->party_category == 5)?"selected":"" ;?>>Both</option>
                    </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="contact_person">Contact Person</label>
                <input type="text" name="contact_person" class="form-control text-capitalize " value="<?=(!empty($dataRow->contact_person))?$dataRow->contact_person:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="party_mobile">Alt. No.</label>
                <input type="text" name="party_mobile" class="form-control numericOnly" value="<?=(!empty($dataRow->party_mobile))?$dataRow->party_mobile:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="party_email">Party Email</label>
                <input type="email" name="party_email" class="form-control" value="<?=(!empty($dataRow->party_email))?$dataRow->party_email:""; ?>" />
            </div>
			
			<div class="col-md-2 form-group">
                <label for="supplied_types">Supplied Types</label>
                <select name="supplied_types" id="supplied_types" class="form-control single-select" >
					<option value="">Supplied Types</option>
					<?php
						foreach($suppliedTypes as $types):
							$selected = (!empty($dataRow->supplied_types) && $dataRow->supplied_types == $types)?"selected":"";
							echo '<option value="'.$types.'" '.$selected.'>'.str_replace(',',' & ',$types).'</option>';
						endforeach;
					?>
				</select>
            </div>
			<div class="col-md-2 form-group">
                <label for="party_pan">Party PAN</label>
                <input type="text" name="party_pan" class="form-control" value="<?=(!empty($dataRow->party_pan))?$dataRow->party_pan:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="gst_status">Gst Status</label>
                <select name="gst_status" id="gst_status" class="form-control" >
                    <option>Select</option>
                    <option value="0" <?=(!empty($dataRow->gst_status) && $dataRow->gst_status == "0")?"selected":""?>>Unregistered</option>
                    <option value="1" <?=(!empty($dataRow->gst_status) && $dataRow->gst_status == "1")?"selected":""?>>Registered</option>
                    <option value="2" <?=(!empty($dataRow->gst_status) && $dataRow->gst_status == "2")?"selected":""?>>Consumer</option>
                    <option value="3" <?=(!empty($dataRow->gst_status) && $dataRow->gst_status == "3")?"selected":""?>>Composit</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="gstin">Party GSTIN</label>
                <input type="text" name="gstin" class="form-control " value="<?=(!empty($dataRow->gstin))?$dataRow->gstin:""; ?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="credit_days">Credit Days</label>
                <input type="number" name="credit_days" class="form-control numericOnly" value="<?=(!empty($dataRow->credit_days))?$dataRow->credit_days:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="vendor_code">Memo Type</label>
                <select name="vendor_code" id="vendor_code" class="form-control" >
                    <option value="">Memo Type</option>
                    <option value="CASH" <?=(!empty($dataRow->vendor_code) && $dataRow->vendor_code == "CASH")?"selected":""?>>CASH</option>
                    <option value="DEBIT" <?=(!empty($dataRow->vendor_code) && $dataRow->vendor_code == "DEBIT")?"selected":""?>>DEBIT</option>
                </select>
            </div>
            <!--	
            <div class="col-md-3 form-group">
                <label for="opening_balance">Opening Balance</label>
                <div class="input-group">
                    <select name="balance_type" id="balance_type" class="form-control" >
                        <option value="1" <?=(!empty($dataRow->balance_type) && $dataRow->balance_type == "1")?"selected":""?>>Credit</option>
                        <option value="-1" <?=(!empty($dataRow->balance_type) && $dataRow->balance_type == "-1")?"selected":""?>>Debit</option>
                    </select>
                    <input type="number" name="opening_balance" class="form-control floatOnly" value="<?=(!empty($dataRow->opening_balance))?abs($dataRow->opening_balance):""?>" />
                </div>
            </div>
            -->
            
            <div class="col-md-2 form-group">
                <label for="country_id">Select Country</label>
                <select name="country_id" id="country_id" class="form-control single-select req" tabindex="-1">
                    <option value="">Select Country</option>
                    <?php $i=1; foreach($countryData as $Country):
                        $selected = (!empty($dataRow->country_id) && $dataRow->country_id == $Country->id)?"selected":"";
                        if(empty($dataRow->country_id) && $Country->id == 101){$selected = "selected";}
                    ?>
                    <option value="<?=$Country->id?>" <?=$selected?>><?=$Country->name?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="state_id">Select State</label>
                <select name="state_id" id="state_id" class="form-control single-select req" tabindex="-1">
                    <?php 
                    if(empty($dataRow->id)): 
                        echo $state;
                    else: 
                        echo $dataRow->state; 
                    endif; ?>
                </select>
                <input type="hidden" id="statename" name="statename" value="" />
            </div>
            
            <div class="col-md-3 form-group">
                <label for="city_id">Select City</label>
                <select name="city_id" id="city_id" class="form-control single-select req" tabindex="-1">
                    <?php 
                    if(empty($dataRow->id)): 
                        echo $city;
                    else: 
                        echo $dataRow->city; 
                    endif;?>
                </select>
                <input type="hidden" id="ctname" name="ctname" value="" />
            </div>
            
            <div class="col-md-<?=($pcategory != 3)?"9":"9"?> form-group">
                <label for="party_address">Address</label>
                <textarea name="party_address" class="form-control " rows="1"><?=(!empty($dataRow->party_address))?$dataRow->party_address:""?></textarea>
            </div>

            <div class="col-md-3 form-group">
                <label for="party_pincode">Address Pincode</label>
                <input type="text" name="party_pincode" class="form-control numericOnly" value="<?=(!empty($dataRow->party_pincode))?$dataRow->party_pincode:""?>" />
            </div>
            
            <div class="col-md-9 form-group">
                <label for="delivery_address">Delivery Address</label>
                <textarea name="delivery_address" class="form-control" rows="1"><?=(!empty($dataRow->delivery_address))?$dataRow->delivery_address:""?></textarea>
            </div>

            <div class="col-md-3 form-group">
                <label for="delivery_pincode">Delivery Pincode</label>
                <input type="text" name="delivery_pincode" class="form-control numericOnly" value="<?=(!empty($dataRow->delivery_pincode))?$dataRow->delivery_pincode:""?>" />
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
    $(document).on('keyup','#city_idc',function(){
        $('#ctname').val($(this).val());
    });
    $(document).on('keyup','#state_idc',function(){
        $('#statename').val($(this).val());
    });
});
</script>