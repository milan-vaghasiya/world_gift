<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">ITEM/PRODUCT LEDGER</h4>
                            </div>
                            <div class="col-md-6">
                                <select id="category_id" class="form-control float-right" style="width: 40%;">
                                    <option value="">Select Category</option>
                                    <?php
                                        foreach ($categoryList as $row) :
                                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                                        endforeach;
                                    ?>
                                </select>
                                <select id="item_type" class="form-control float-right mr-2" style="width: 40%;">
                                    <option value="1">Finish Good</option>
                                    <!-- <option value="2">Consumable</option>
                                    <option value="3">Raw Material</option>
                                    <option value="4">Capital Goods</option>
                                    <option value="5">Machineries</option>
                                    <option value="6">Instruments</option>    
                                    <option value="7">Gauges</option> -->
                                </select>
                            </div>                                         
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='itemTable' class="table table-bordered ssTable" data-url='/itemList/1'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    var item_type = $("#item_type").val();
    $("#itemTable").attr("data-url",'/itemList/'+item_type);
    initTable(0);
    $(document).on('change',"#item_type",function(){
        var item_type = $(this).val();
        $("#itemTable").attr("data-url",'/itemList/'+item_type);
        initTable(0);
    });
    $(document).on('change',"#category_id",function(){
        var category_id = $(this).val();
        $("#itemTable").attr("data-url",'/itemList/'+item_type+'/'+category_id);
        initTable(0);
    });
});
</script>
