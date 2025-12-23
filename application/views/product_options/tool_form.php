<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="item_id" class="item_id" value="<?=$item_id?>" />

            <div class="col-md-6 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control single-select req">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptData as $row):
                            echo '<option value="'.$row->id.'">'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error dept_id"></div>
            </div>

            <div class="col-md-6 form-group">
                <label for="operation_id">Production Operation</label>
                <select name="operationSelect" id="operationSelect" data-input_id="operation_id" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                        foreach ($operationData as $row) :  
                            $selected = (!empty($operation) && (in_array($row->id, $operation))) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-operation_name="'.$row->operation_name.'" ' . $selected . '>' . $row->operation_name . '</option>';
                        endforeach;
                    ?>
                </select>     
               <input type="hidden" name="operation_id" id="operation_id" value="<?=(!empty($operation) ? implode(',',$operation):"")?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="ref_item_id">Consumable Item</label>
                <select name="ref_item_id" id="ref_item_id" class="form-control single-select req">
                    <option value="">Select Item</option>
                    <?php
                        foreach($consumableData as $row):
                            echo '<option value="'.$row->id.'">'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error ref_item_id"></div>
            </div>

            <div class="col-md-6 form-group">
                <label for="setup">Setup</label>
                <select name="setup" id="setup" class="form-control single-select req">
                    <option value="">Select Setup</option>
                    <?php
                        foreach($setupData as $row):
                            echo '<option value="'.$row->process_id.'">'.$row->process_name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error setup"></div>
            </div>

            <div class="col-md-3 form-group">
                <label for="tool_life">Tool Life Per Corner</label>
                <input type="text" id="tool_life" class="form-control floatOnly req" placeholder="Tool Life Per Corner" value="" min="0" />
                <div class="error tool_life"></div>
            </div>
            <div class="col-md-3 form-group">
                <label for="number_corner">Number of Corner</label>
                <input type="text" id="number_corner" class="form-control floatOnly req" placeholder="Number of Corner" value="" min="0" />
                <div class="error number_corner"></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="price">Price</label>
                <input type="text" id="price" class="form-control floatOnly req" placeholder="Price" value="" min="0" />
                <div class="error price"></div>
            </div>
            
            <div class="col-md-2 form-group">
                <button type="button" class="btn btn-outline-success waves-effect waves-light float-right mt-30 save-form" onclick="AddRow();" ><i class="fa fa-plus"></i> Add</button>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive">
                <table id="toolConsumption" class="table table-bordered align-items-center">
                    <thead class="thead-info">
                        <tr>
                            <th style="width:5%;">#</th>
                            <th>Department</th> 
                            <th>Operation</th>
                            <th>Item Name</th>
                            <th>Setup</th>
                            <th>Tool Life Of Corner</th>
                            <th>Number Of Corner</th>
                            <th>Price</th>
                            <th class="text-center" style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="kitItems">
                        <?php
                            if(!empty($toolConsumptionData)):
                                $i=1;
                                foreach($toolConsumptionData as $row):
                                    echo '<tr>
                                                <td>'.$i++.'</td>
                                                <td>
                                                    '.$row->dept_name.'
                                                    <input type="hidden" name="dept_id[]" value="'.$row->dept_id.'">
                                                </td>
                                                <td>
                                                    '.$row->ops_name.'
                                                    <input type="hidden" name="operation_id[]" value="'.$row->operation.'">
                                                </td>
                                                <td>
                                                    '.$row->item_name.'
                                                    <input type="hidden" name="ref_item_id[]" value="'.$row->ref_item_id.'">
                                                    <input type="hidden" name="id[]" value="'.$row->id.'">
                                                </td>
                                                <td>
                                                    '.$row->process_name.'
                                                    <input type="hidden" name="setup[]" value="'.$row->setup.'">
                                                </td>
                                                <td>
                                                    '.$row->tool_life.'
                                                    <input type="hidden" name="tool_life[]" value="'.$row->tool_life.'">
                                                </td>
                                                <td>
                                                    '.$row->number_corner.'
                                                    <input type="hidden" name="number_corner[]" value="'.$row->number_corner.'">
                                                </td>
                                                <td>
                                                    '.$row->price.'
                                                    <input type="hidden" name="price[]" value="'.$row->price.'">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" onclick="Remove(this);" class="btn btn-sm btn-outline-danger waves-effect waves-light permission-remove"><i class="ti-trash"></i></button>
                                                </td>
                                            </tr>';
                                endforeach;
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</form>
<script>
    $(document).ready(function() {
        initMultiSelect();
    });
</script>  