<form>
    <div class="col-md-12">

        <div class="table-responsive">
            <table id='itemTable' class="table table-bordered ssTable-search ssTable-cf1" data-ninput='[0,-1]' data-url='products/searchItem/<?=$item_type?>/<?=$entry_type?>'>
                <thead class="thead-info">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>M.R.P.</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    

                </tbody>
            </table>

        </div>
    </div>

</form>
<script>
    $(document).ready(function() {
        searchingDatatableInit();
    });
</script>