$(document).ready(function(){
	claculateColumn();
	gstType();
	gstApplicable();
	
	var numberOfChecked = $('.termCheck:checkbox:checked').length;
	$("#termsCounter").html(numberOfChecked);
	
	$(document).on("click",".termCheck",function(){
        var id = $(this).data('rowid');
		var numberOfChecked = $('.termCheck:checkbox:checked').length;
		$("#termsCounter").html(numberOfChecked);
        if($("#md_checkbox"+id).attr('check') == "checked"){
            $("#md_checkbox"+id).attr('check','');
            $("#md_checkbox"+id).removeAttr('checked');
            $("#term_id"+id).attr('disabled','disabled');
            $("#term_title"+id).attr('disabled','disabled');
            $("#condition"+id).attr('disabled','disabled'); 
        }else{
            $("#md_checkbox"+id).attr('check','checked');
            $("#term_id"+id).removeAttr('disabled');
            $("#term_title"+id).removeAttr('disabled');
            $("#condition"+id).removeAttr('disabled');
        }
    });

	$(document).on('click','.createSalesInvoice',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		$('.party_id').html("");

		if(party_id != "" || party_id != 0){
			$.ajax({
				url : base_url + '/salesOrder/getPartyOrders',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#orderModal").modal();
					$("#exampleModalLabel1").html('Create Invoice');
					$("#party_so").attr('action',base_url + 'salesInvoice/createInvoice');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Invoice');
					$("#partyName").html(party_name);
					$("#party_name_so").val(party_name);
					$("#from_entry_type_so").val(4);
					$("#party_id_so").val(party_id);
					$("#orderData").html("");
					$("#orderTable").DataTable().clear().destroy();
					$("#orderData").html(data.htmlData);
					orderTable();
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});

	$(document).on('click','.createDCSalesInvoice',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		$('.party_id').html("");

		if(party_id != "" || party_id != 0){
			$.ajax({
				url : base_url + '/deliveryChallan/getPartyOrders',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#orderModal").modal();
					$("#exampleModalLabel1").html('Create Invoice');
					$("#party_so").attr('action',base_url + 'salesInvoice/createInvoice');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Invoice');
					$("#partyName").html(party_name);
					$("#party_name_so").val(party_name);
					$("#from_entry_type_so").val(5);
					$("#party_id_so").val(party_id);
					$("#orderData").html("");
					$("#orderTable").DataTable().clear().destroy();
					$("#orderData").html(data.htmlData);
					orderTable();
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});
	
	$(document).on('click','.createPInvSalesInvoice',function(){
		var party_id = $('#party_id').val();
		var party_name = $('#party_idc').val();
		$('.party_id').html("");

		if(party_id != "" || party_id != 0){
			$.ajax({
				url : base_url + '/proformaInvoice/getPartyOrders',
				type: 'post',
				data:{party_id:party_id},
				dataType:'json',
				success:function(data){
					$("#orderModal").modal();
					$("#exampleModalLabel1").html('Create Invoice');
					$("#party_so").attr('action',base_url + 'salesInvoice/createInvoice');
					$("#btn-create").html('<i class="fa fa-check"></i> Create Invoice');
					$("#partyName").html(party_name);
					$("#party_name_so").val(party_name);
					$("#from_entry_type_so").val(9);
					$("#party_id_so").val(party_id);
					$("#orderData").html("");
					$("#orderTable").DataTable().clear().destroy();
					$("#orderData").html(data.htmlData);
					orderTable();
				}
			});
		} else {
			$('.party_id').html("Party is required.");
		}
	});

	$(document).on("change","#gst_applicable",function(){
		var gstType = $("#gst_type").val();
		var gstApplicable = $(this).val();
		if(gstApplicable == 1){
			if($("#party_id").val() != ""){
				var partyData = $("#party_id").find(":selected").data('row');
				var gstin = partyData.gstin;		
				var stateCode = "";
				if(gstin != ""){
					stateCode = gstin.substr(0, 2);
					if(stateCode == 24 || stateCode == "24"){gstType= 1;}else{gstType= 2;}
				}else{
					gstType = 1;
				}
			}else{ gstType = 1; }

			if(gstType == 1){ 
				$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else if(gstType == 2){
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else{
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
				$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			}
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			$("#gst_type").val(3);
		}
		claculateColumn();
		
		$("#sales_type").trigger('change');
	});
	
	$(document).on("change","#gst_type",function(){
		var gstType = $(this).val();
		var gstApplicable = $("#gst_applicable").val();
		if(gstApplicable == 1){
			if(gstType == 1){ 
				$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else if(gstType == 2){
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
				$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
			}else{
				$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
				$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			}
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			$("#gst_type").val(3);
		}
		claculateColumn();
	});
	
	/**Updated BY Mansee @ 29-12-2021 121-133*/
	$(document).on('change keyup','#party_id',function(){ 
		$("#party_name").val("");
		$("#gstin").html("");
		if($(this).val() != ""){
			var partyData = $(this).find(":selected").data('row');
			var gstArr = (partyData.json_data != '') ? JSON.parse(partyData.json_data) : '';
			
			if (gstArr != '') {
				$.each(gstArr, function (key, row) {
					$("#gstin").append("<option value='" + key + "' data-pincode='" + row.delivery_pincode + "' data-address='" + row.delivery_address + "'>" + key + "</option>");
				});
			} else {
				$("#gstin").append("<option value='' data-pincode='' data-address=''>Select GSTIN</option>");
			}
			
			if(partyData.vendor_code != '' && partyData.vendor_code != null){  
				$("#memo_type").val(partyData.vendor_code);
				$("#memo_type").comboSelect();
				$("#memo_type").trigger('change');
			}
			
			$("#party_name").val(partyData.party_name);
			$("#gstin").trigger('change');

			if(cm_id == 1){
				if(partyData.id == 119){
					$("#org_price_div").hide();
					$("#price").removeAttr('readonly');
					$("#item_remark_div").removeClass("col-md-6");
					$("#item_remark_div").addClass("col-md-10");
				}else{
					$("#org_price_div").show();
					$("#price").attr('readonly','readonly');
					$("#item_remark_div").removeClass("col-md-10");
					$("#item_remark_div").addClass("col-md-6");
				}
			}			
		}
    });

	$(document).on("change", "#gstin", function(){
		var stateCode = "";
		var gstin = $(this).val();
		var gst_type= 1; 
		if(gstin != "" && gstin != null){
			stateCode = gstin.substr(0, 2);
			if(stateCode == 24 || stateCode == "24"){gst_type= 1;}else{gst_type= 2;}
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}			
		}else{
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}	
			$("#party_state_code").val("");
		}
		$("#party_state_code").val(stateCode);

		if($("#sales_type").val() == 2){			
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(2);
			}else{
				$("#gst_type").val(3);
			}
		}
		gstApplicable();
	});
	
	$(document).on('change','#sales_type',function(){
	    var sales_type = $(this).val();	
		if(sales_type == 2){
			var gst_type= 2;			
			if($("#gst_applicable").val() == 1){
				$("#gst_type").val(gst_type);
			}else{
				$("#gst_type").val(3);
			}
		}else{
			var gst_type= 1;
			if($("#party_id").val() != ""){
				var partyData = $("#party_id").find(":selected").data('row');	
				var gstin = partyData.gstin;		
				var stateCode = "";
				if(gstin != ""){
					stateCode = gstin.substr(0, 2);
					if(stateCode == 24 || stateCode == "24"){gst_type= 1;}else{gst_type= 2;}
				}				
				if($("#gst_applicable").val() == 1){
					$("#gst_type").val(gst_type);
				}else{
					$("#gst_type").val(3);
				}
			}else{
				if($("#gst_applicable").val() == 1){
					$("#gst_type").val(gst_type);
				}else{
					$("#gst_type").val(3);
				}
			}		
		}
		//gstType();
		gstApplicable();
        
        if($("#sales_id").val() == ""){
    		$.ajax({
    			url:base_url+controller+'/getInvoiceNo',
    			type:'post',
    			data:{sales_type:sales_type},
    			dataType:'json',
    			success:function(data){
    				$("#inv_prefix").val(data.trans_prefix);
    				$("#inv_no").val(data.nextTransNo);
    				$("#entry_type").val(data.entry_type);
    			}
    		});
        }
	});

	/*$(document).on('change keyup','#item_id',function(){
        $("#item_name").val($('#item_idc').val());
    });*/
	
	$(document).on("keyup change",".calculatePrice",function(){
        if(cm_id == 1 && $("#party_id").val() != "119"){
    		var gstPer = parseFloat($("#gst_per").val()).toFixed(2);
    		var org_price = parseFloat($("#org_price").val()).toFixed(2);
    		var gstReverse = parseFloat(((parseFloat(gstPer)+100)/100)).toFixed(2);
    		var discAmt = parseFloat($("#disc_amt").val()).toFixed(2);
    		
    		if(discAmt != "" && discAmt > 0){
    		    var qty = parseFloat($('#qty').val());
        		var amt = parseFloat((org_price * qty)).toFixed(2);
        		var discountedAmt = parseFloat(parseFloat(amt) - parseFloat(discAmt)).toFixed(2);
    			discountedPrice = parseFloat(parseFloat(discountedAmt) / parseFloat(qty)).toFixed(2);
    			var qtyDisc = parseFloat(parseFloat(discAmt) / parseFloat(qty)).toFixed(2);
    			if(qtyDisc > 0)
            	{
            		var new_price = parseFloat(discountedPrice/gstReverse).toFixed(2);
            		new_price = parseFloat(new_price) + parseFloat(qtyDisc);
        		    $("#price").val(new_price);
            	}
    		}else{
    		    var new_price = parseFloat(org_price/gstReverse).toFixed(2);
    		    $("#price").val(new_price);
    		}
        }else{
            var price = parseFloat($("#price").val()).toFixed(2);
            $("#org_price").val(price);
        }
	});
    
	$(document).on('change','#item_id',function(){
		var item_id = $(this).val();
		var batchQtySum = 0;
		$('#totalQty').html(batchQtySum.toFixed(3));
		$("#qty").val(batchQtySum.toFixed(3));
		if(item_id == ""){
			$("#item_type").val("");
			$("#item_code").val("");
			$("#item_name").val("");
			$("#item_desc").val("");
			$("#hsn_code").val("");
			$("#gst_per").val("");
			$("#price").val("");
			$("#unit_name").val("");
			$("#unit_id").val("");
			$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
		}else{
			var itemData = $('#item_id :selected').data('row');
		
			$("#item_type").val(itemData.item_type);
			$("#item_code").val(itemData.item_code);
			$("#item_name").val(itemData.item_name);
			$("#item_desc").val(itemData.description);
			$("#hsn_code").val(itemData.hsn_code);
			$("#gst_per").val(itemData.gst_per);
			$("#price").val(itemData.price);
			$("#unit_name").val(itemData.unit_name);
			$("#unit_id").val(itemData.unit_id);
		}	
	});
	
    $(document).on('click','.saveItem',function(){
        var fd = $('.invoiceItemForm').find('input,select,textarea').serializeArray();
        var formData = {};
        $.each(fd,function(i, v) {
            formData[v.name] = v.value;
        });

        $(".item_id").html("");
		$(".qty").html("");
        $(".unit_id").html("");
        if(formData.item_id == ""){
			$(".item_name").html("Item Name is required.");
		}else{
			var item_ids = $("input[name='item_id[]']").map(function(){return $(this).val();}).get();
			if ($.inArray(formData.item_id,item_ids) >= 0  && formData.row_index == "") {
				addItemQty('edit_btn_'+formData.item_id,formData.qty);
				//$(".item_name").html("Item already added.");
			}else {
				var IsValid = 1;
				if(formData.qty == "" || formData.qty == "0"){ $(".qty").html("Qty is required."); IsValid = 0; }
				if(formData.price == "" || formData.price == "0"){ $(".price").html("Price is required."); IsValid = 0; }
				if(formData.location_id == "" || formData.location_id == "0"){ $(".location_id").html("Location is required."); IsValid = 0; }
				
				if(IsValid){
					var amount = 0;var total = 0;var disc_per = 0;var igst_amt = 0;
					var cgst_amt = 0;var sgst_amt = 0;var net_amount = 0;var cgst_per = 0;var sgst_per = 0; var igst_per = 0;
					if(parseFloat(formData.disc_amt) > 0){
					    total = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
						disc_per = parseFloat((parseFloat(formData.disc_amt) * 100) / total).toFixed(2);
						amount = parseFloat(total - parseFloat(formData.disc_amt)).toFixed(2);
					}else{
						amount = parseFloat(parseFloat(formData.qty) * parseFloat(formData.price)).toFixed(2);
						disc_per = 0;
						formData.disc_amt = 0;
					}
					
					cgst_per = parseFloat(parseFloat(formData.gst_per)/2).toFixed(2);
					sgst_per = parseFloat(parseFloat(formData.gst_per)/2).toFixed(2);
					
					cgst_amt = parseFloat((cgst_per * amount )/100).toFixed(2);
					sgst_amt = parseFloat((sgst_per * amount )/100).toFixed(2);
					
					igst_per = parseFloat(formData.gst_per).toFixed(2);
					igst_amt = parseFloat((igst_per * amount )/100).toFixed(2);
					
					net_amount = parseFloat(parseFloat(amount) + parseFloat(igst_amt)).toFixed(2);

                    formData.gst_type = $('#gst_type').val();
					formData.qty = parseFloat(formData.qty).toFixed(2);
					formData.cgst_per = cgst_per;
                    formData.cgst_amt = cgst_amt;
                    formData.sgst_per = sgst_per;
                    formData.sgst_amt = sgst_amt;
                    formData.igst_per = igst_per;
                    formData.igst_amt = igst_amt;
                    formData.disc_per = disc_per;
                    formData.amount = amount;
                    formData.net_amount = net_amount;					
                    AddRow(formData); 
					
				// 	var location = 0;
				// 	if($('#cmid').val() == 2){ location = formData.location_id; }
					resetFormByClass('invoiceItemForm');

                    if($('#cmid').val() == 2){ 
                        $('#location_id').val(formData.location_id);
                        $("#location_id").comboSelect();
                    }


					$("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
                    if($(this).data('fn') == "save"){
                        $("#item_id").comboSelect();
                        $("#item_idc").focus();
                    }else if($(this).data('fn') == "save_close"){
                        $("#item_id").comboSelect();
                        $("#itemModel").modal('hide');
                    } 
				}
			}
		}
		$("#scan_qr").focus();
    }); 

	$(document).on('click','.add-item',function(){
		var party_id = $('#party_id').val();	
		$(".party_id").html("");	
		$("#row_index").val("");
		if(party_id){
			$.ajax({ 
				type: "POST",   
				url: base_url + controller + '/getPartyItems',   
				data: {party_id:party_id},
				dataType:'json',
			}).done(function(response){
				$("#item_id").html(response.partyItems);
				$("#item_id").comboSelect();
				setPlaceHolder();
				$("#itemModel").modal();
				$(".btn-close").show();
				$(".btn-save").show();
				
			});			
		}else{$(".party_id").html("Party name is required.");$(".modal").modal('hide');}
	});
	
    $(document).on('click','.btn-close',function(){
        $('#invoiceItemForm')[0].reset();
        $("#item_id").comboSelect();
        $("#batchData").html('<tr><td class="text-center" colspan="5">No Data Found.</td></tr>');
        $("#invoiceItemForm .error").html("");
    });

	$(document).on("change","#apply_round",function(){
		claculateColumn();
	});

	$(document).on('keyup','.calculateSummary',function(){
		calculateSummary();
	}); 

	$(document).on("change","#trans_mode",function(){
		var payment_mode=$("#trans_mode").val();
	
		$.ajax({
			url:base_url + controller + '/getLedgerListONPaymentMode',
			type:'post',
			data:{payment_mode:payment_mode},
			dataType:'json',
			success:function(data){
				$("#vou_acc_id").html("");
				$("#vou_acc_id").html(data.options);
				$("#vou_acc_id").comboSelect();
			}
		});
	});

	//Created By Karmi @31/03/2022
	$(document).on('click','.get-offers',function(){
		var party_id = $('#party_id').val();
		var inv_date = $('#inv_date').val();
		var itemArray=$("input[name='item_id[]']").map(function(){return $(this).val();}).get();
		var myJsonItem = JSON.stringify(itemArray);
		
		$(".party_id").html("");	
		$("#row_index").val("");
		if(itemArray){
			$.ajax({ 
				type: "POST",   
				url: base_url + controller + '/getOfferItems',   
				data: {party_id:party_id,inv_date:inv_date,myJsonItem:myJsonItem},
				dataType:'json',
				success:function(data){
				$("#offerModal").modal();
				//$("#exampleModalLabel1").html('Create Invoice');
				//$("#offer").attr('action',base_url + 'salesInvoice/applyOffer');
				$("#btn-create").html('<i class="fa fa-check"></i> Apply Ofeers');
				//$("#partyName").html(party_name);
				//$("#party_name_so").val(party_name);
				//$("#from_entry_type_so").val(4);
				//$("#party_id_so").val(party_id);
				$("#offerData").html("");
				$("#offerTable").DataTable().clear().destroy();
				$("#offerData").html(data.htmlData);
				//orderTable();
			}	
		});	
		}else{$(".item_id").html("Product is required.");}
	});

	$('#offerData').on('change', ':checkbox', function () {
		if(this.checked){
			var itemArray=$("input[name='item_id[]']").map(function(){return $(this).val();}).get();
			var offer_ids=$(this).data('itmid');
			var offerIdArray=offer_ids.split(",");
			for(var i=0;i<itemArray.length;i++)
			{
				if(jQuery.inArray(itemArray[i],offerIdArray) !=-1)
				{
					$("#itemModel").modal();
					$(".btn-close").show();
					$(".btn-save").hide();
					$.each(data,function(key, value) {
						$("#"+key).val(value);
					}); 
					$("#item_id").comboSelect();
				}
			}
			var offer = $("input[name='chk_id[]']").val();
		} else {
			var offer = $("input[name='chk_id[]']").val();
		}
	});

	/** LOAD SCANNED(QR) ITEM ON ENTER KEY */
	$(document).on('keypress','#scan_qr',function(e){ 
		if(e.which == 13) {
			var scan_id = $(this).val();
			$.ajax({
				type: "POST",
				url: base_url + 'products/getScannedItem',
				data:{scan_id:scan_id},
				dataType:'json'
			}).done(function (response) {
				
				getFGSelect(response);
				$('#qty').val('1');
				$('.btn.saveItem').trigger('click');
			});
			$('#scan_qr').val('');
		}
    });

	$(document).on('change',"#memo_type", function () { 
		var memo_type = $(this).val();
		if(memo_type == 'CASH'){
			$('.voucherDetails').show();

// 			$.ajax({
// 				url:base_url + controller + '/getCustomerData',
// 				type:'post',
// 				data:{memo_type:memo_type},
// 				dataType:'json',
// 				success:function(data){
// 					$('#gstin').html("");
// 					$('#party_id').html("");
// 					$("#party_id").html(data.options);
// 					$('#party_id').comboSelect();
// 				}
// 			});

		}else if(memo_type == 'DEBIT'){
			$('.voucherDetails').hide();
		
// 			$.ajax({
// 				url:base_url + controller + '/getCustomerData',
// 				type:'post',
// 				data:{memo_type:memo_type},
// 				dataType:'json',
// 				success:function(data){
// 					$('#gstin').html("");
// 					$('#party_id').html("");
// 					$("#party_id").html(data.options);
// 					$('#party_id').comboSelect();
// 				}
// 			});
		}
	});
});

function gstType(){
	var gstType = $("#gst_type").val();
	var gstApplicable = $("#gst_applicable").val();
	if(gstApplicable == 1){
		if(gstType == 1){ 
			$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else if(gstType == 2){
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
			$("#gst_type").val(3);
		}
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
		$("#gst_type").val(3);
	}
	claculateColumn();
}

function gstApplicable(){
	var gstType = $("#gst_type").val();
	var gstApplicable = $("#gst_applicable").val();
	if(gstApplicable == 1){
		if(gstType == 1){ 
			$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else if(gstType == 2){
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
			$(".amountCol").hide();$(".netAmtCol").show();$(".itemGst").show();
		}else{
			$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
			$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
		}
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();$(".itemGst").hide();
		$("#gst_type").val(3);
	}
	claculateColumn();
}

function AddRow(data) {
	$('table#invoiceItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "invoiceItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	if(data.row_index != ""){
		var trRow = data.row_index;
		//$("tr").eq(trRow).remove();
		$("#"+tblName+" tbody tr:eq("+trRow+")").remove();
	}
	var ind = (data.row_index == "")?-1:data.row_index;
	row = tBody.insertRow(ind);
	
	//Add index cell
	var countRow = (data.row_index == "")?($('#'+tblName+' tbody tr:last').index() + 1):(parseInt(data.row_index) + 1);
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var itemNameInput = $("<input/>",{type:"hidden",name:"item_name[]",value:data.item_name});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});
	var formEnteryTypeInput = $("<input/>",{type:"hidden",name:"from_entry_type[]",value:data.from_entry_type});
	var refIdInput = $("<input/>",{type:"hidden",name:"ref_id[]",value:data.ref_id});
	var itemTypeInput = $("<input/>",{type:"hidden",name:"item_type[]",value:data.item_type});
	var itemCodeInput = $("<input/>",{type:"hidden",name:"item_code[]",value:data.item_code});
	var itemDescInput = $("<input/>",{type:"hidden",name:"item_desc[]",value:data.item_desc});	
	var gstPerInput = $("<input/>",{type:"hidden",name:"gst_per[]",value:data.gst_per});
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id});
	var batchQtyInput = $("<input/>",{type:"hidden",name:"batch_qty[]",value:data.batch_qty});
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
	var stockEffInput = $("<input/>",{type:"hidden",name:"stock_eff[]",value:data.stock_eff});
	var bacthErrorDiv = $("<div></div>",{class:"error batch_no"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(itemNameInput);
	cell.append(transIdInput);
	cell.append(formEnteryTypeInput);
	cell.append(refIdInput);
	cell.append(itemTypeInput);
	cell.append(itemCodeInput);
	cell.append(itemDescInput);
	cell.append(gstPerInput);
	cell.append(locationIdInput);
	cell.append(batchQtyInput);
	cell.append(batchNoInput);
	cell.append(stockEffInput);
	cell.append(bacthErrorDiv);

	var hsnCodeInput = $("<input/>",{type:"hidden",name:"hsn_code[]",value:data.hsn_code});
	cell = $(row.insertCell(-1));
	cell.html(data.hsn_code);
	cell.append(hsnCodeInput);
	
	var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	var qtyErrorDiv = $("<div></div>",{class:"error qty"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	cell.append(qtyErrorDiv);
	
	var unitIdInput = $("<input/>",{type:"hidden",name:"unit_id[]",value:data.unit_id});
	var unitNameInput = $("<input/>",{type:"hidden",name:"unit_name[]",value:data.unit_name});
	cell = $(row.insertCell(-1));
	cell.html(data.unit_name);
	cell.append(unitIdInput);
	cell.append(unitNameInput);
	
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	var orgPriceInput = $("<input/>",{type:"hidden",name:"org_price[]",value:data.org_price});
	var priceErrorDiv = $("<div></div>",{class:"error price"+countRow});
	cell = $(row.insertCell(-1));
	cell.html(data.price);
	cell.append(priceInput);
	cell.append(priceErrorDiv);
	cell.append(orgPriceInput);
	
	var cgstPerInput = $("<input/>",{type:"hidden",name:"cgst[]",value:data.cgst_per});
	var cgstAmtInput = $("<input/>",{type:"hidden",name:"cgst_amt[]",value:data.cgst_amt});
	//cell = $(row.insertCell(-1));
	//cell.html(data.cgst_amt+ '(' + data.cgst_per + '%)');
	cell.append(cgstPerInput);
	cell.append(cgstAmtInput);
	//cell.attr("class","cgstCol");
	
	var sgstPerInput = $("<input/>",{type:"hidden",name:"sgst[]",value:data.sgst_per});
	var sgstAmtInput = $("<input/>",{type:"hidden",name:"sgst_amt[]",value:data.sgst_amt});
	//cell = $(row.insertCell(-1));
	//cell.html(data.sgst_amt+ '(' + data.sgst_per + '%)');
	cell.append(sgstPerInput);
	cell.append(sgstAmtInput);
	//cell.attr("class","sgstCol");

	var igstPerInput = $("<input/>",{type:"hidden",name:"igst[]",value:data.igst_per});
	var igstAmtInput = $("<input/>",{type:"hidden",name:"igst_amt[]",value:data.igst_amt});
	//cell = $(row.insertCell(-1));
	//cell.html(data.igst_amt + '(' + data.igst_per + '%)');
	cell.append(igstPerInput);
	cell.append(igstAmtInput);
	//cell.attr("class","igstCol");
	
	var discPerInput = $("<input/>",{type:"hidden",name:"disc_per[]",value:data.disc_per});
	var discAmtInput = $("<input/>",{type:"hidden",name:"disc_amt[]",value:data.disc_amt});
    cell = $(row.insertCell(-1));
	cell.html(data.disc_amt + '(' + data.disc_per + '%)');
	cell.append(discPerInput);
	cell.append(discAmtInput);
	
	var amountInput = $("<input/>",{type:"hidden",name:"amount[]",value:data.amount});
	cell = $(row.insertCell(-1));
	cell.html(data.amount);
	cell.append(amountInput);
	cell.attr("class","amountCol");
	
	var netAmtInput = $("<input/>",{type:"hidden",name:"net_amount[]",value:data.net_amount});
	cell = $(row.insertCell(-1));
	cell.html(data.net_amount);
	cell.append(netAmtInput);
	cell.attr("class","netAmtCol");

	var itemRemarkInput = $("<input/>",{type:"hidden",name:"item_remark[]",value:data.item_remark});
	cell = $(row.insertCell(-1));
	cell.html(data.item_remark);
	cell.append(itemRemarkInput);
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

    var btnEdit = $('<button><i class="ti-pencil-alt"></i></button>');
    btnEdit.attr("type", "button");
    btnEdit.attr("onclick", "Edit("+JSON.stringify(data)+",this);");
    btnEdit.attr("class", "btn btn-outline-warning waves-effect waves-light edit_btn_"+data.item_id);

    cell.append(btnEdit);
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
	
	if(data.gst_type == 1){ 
		$(".cgstCol").show();$(".sgstCol").show();$(".igstCol").hide();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else if(data.gst_type == 2){
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").show();
		$(".amountCol").hide();$(".netAmtCol").show();
	}else{
		$(".cgstCol").hide();$(".sgstCol").hide();$(".igstCol").hide();
		$(".amountCol").show();$(".netAmtCol").hide();
	}
	
	var qtySum = 0; $('.totalQty').html("0");
	var qtyArray = $("input[name='qty[]']").map(function(){return $(this).val();}).get();
	$.each(qtyArray,function(){qtySum += parseFloat(this) || 0;});
	$(".totalQty").html(qtySum.toFixed(2));	
	
	$(row).attr('data-item_data',JSON.stringify(data));

	claculateColumn();
};

function Remove(button) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#invoiceItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#invoiceItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#invoiceItems tbody tr:last').index() + 1;
	if(countTR == 0){
		if($("#gst_type").val() == 1){
			$("#tempItem").html('<tr id="noData"><td colspan="12" align="center">No data available in table</td></tr>');
		}else if($("#gst_type").val() == 2){
			$("#tempItem").html('<tr id="noData"><td colspan="11" align="center">No data available in table</td></tr>');
		}else{
			$("#tempItem").html('<tr id="noData"><td colspan="10" align="center">No data available in table</td></tr>');
		}
	}
	
	claculateColumn();
};

function Edit(data,button){
	var row_index = $(button).closest("tr").index();
    //$("#itemModel").modal();
	$(".btn-close").show();
	$(".btn-save").hide(); 
    $.each(data,function(key, value) {
        $("#"+key).val(value);
        if(key == 'cm_id'){ cm_id = value; }
    }); 
	$("#item_name_dis").val(data.item_name); 	   
	$("#row_index").val(row_index);
	if(cm_id != 1){	$("#location_id").comboSelect(); }
    //$("#item_id").comboSelect();	
    //Remove(button);
}

/* function claculateColumn()
{
	var amountArray = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});
	
	var netAmtArray = $("input[name='net_amount[]']").map(function(){return $(this).val();}).get();
    var netAmtSum = 0;
	$.each(netAmtArray,function(){netAmtSum += parseFloat(this) || 0;});
			
	var igstAmtArr = $("input[name='igst_amt[]']").map(function(){return $(this).val();}).get();;
    var igstAmtSum = 0;
	$.each(igstAmtArr,function(){igstAmtSum += parseFloat(this) || 0;});
	$('#igst_amt_total').val("");
	$('#igst_amt_total').val(igstAmtSum.toFixed(2));
	
	var cgstAmtArr = $("input[name='cgst_amt[]']").map(function(){return $(this).val();}).get();;
    var cgstAmtSum = 0;
	$.each(cgstAmtArr,function(){cgstAmtSum += parseFloat(this) || 0;});
	$('#cgst_amt_total').val("");
	$('#cgst_amt_total').val(cgstAmtSum.toFixed(2));
	
	var sgstAmtArr = $("input[name='sgst_amt[]']").map(function(){return $(this).val();}).get();;
    var sgstAmtSum = 0;
	$.each(sgstAmtArr,function(){sgstAmtSum += parseFloat(this) || 0;});
	$('#sgst_amt_total').val("");
	$('#sgst_amt_total').val(sgstAmtSum.toFixed(2));
	
	var discAmtArr = $("input[name='disc_amt[]']").map(function(){return $(this).val();}).get();;
    var discAmtSum = 0;
	$.each(discAmtArr,function(){discAmtSum += parseFloat(this) || 0;});
	$('#disc_amt_total').val("");
	$('#disc_amt_total').val(discAmtSum.toFixed(2));

	var frAmt = parseFloat($("#freight_amt").val());
	var frGst = parseFloat(parseFloat(frAmt * 18)/100).toFixed(2);
	var totalFrAmt = parseFloat(frAmt) + parseFloat(frGst);
	$("#freight_gst").val(frGst);
	if($("#gst_type").val() == 3 || $("#gst_type").val() == 4){
		var amount = parseFloat(amountSum + totalFrAmt).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			//if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			//else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);} 
			if(decimal>=50){
				if($('#apply_round').val()==0){roundOff=(100-decimal)/100;}
				netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{
				if($('#apply_round').val()==0){roundOff=(decimal-(decimal*2))/100;} 
				netAmount = parseFloat(amount) + parseFloat(roundOff);
			}
		}
		$(".subTotal").html("");
		$(".subTotal").html(amountSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}else{
		var amount = parseFloat(netAmtSum + totalFrAmt).toFixed(2);
		var decimal = amount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		var total = 0;
		if(decimal!==0)
		{
			//if(decimal>=50){roundOff=(100-decimal)/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}
			//else{roundOff=(decimal-(decimal*2))/100;netAmount = parseFloat(amount) + parseFloat(roundOff);}

			if(decimal>=50){
				if($('#apply_round').val()==0){roundOff=(100-decimal)/100;}
				netAmount = parseFloat(amount) + parseFloat(roundOff);}
			else{
				if($('#apply_round').val()==0){roundOff=(decimal-(decimal*2))/100;} 
				netAmount = parseFloat(amount) + parseFloat(roundOff);
			}
		}
		$(".subTotal").html("");
		$(".subTotal").html(netAmtSum.toFixed(2));
		$(".roundOff").html("");
		$(".roundOff").html(roundOff.toFixed(2));
		$(".netAmountTotal").html("");
		$(".netAmountTotal").html(netAmount.toFixed(2));
		
		$("#amount_total").val("");
		$("#amount_total").val(amountSum.toFixed(2));
		$("#round_off").val("");
		$("#round_off").val(roundOff.toFixed(2));
		$("#net_amount_total").val("");
		$("#net_amount_total").val(netAmount.toFixed(2));
	}
} */

function claculateColumn(){	
	var amountArray = $("input[name='amount[]']").map(function(){return $(this).val();}).get();
    var amountSum = 0;
	$.each(amountArray,function(){amountSum += parseFloat(this) || 0;});
	$("#taxable_amount").val(amountSum.toFixed(2));	
	calculateSummary();		
}

function calculateSummary(){
	$(".calculateSummary").each(function(){
		var row = $(this).data('row');
		var map_code = row.map_code;
		var amtField = $("#"+map_code+"_amt");
		var netAmountField = $("#"+map_code+"_amount");
		var perField = $("#"+map_code+"_per");		
		var sm_type = amtField.data('sm_type');

		if(sm_type == "exp"){
			if(row.position == "1"){
				var itemGstArray = $("input[name='gst_per[]']").map(function(){return $(this).val();}).get();
				var gstPer = [];
				$.each(itemGstArray,function(){gstPer.push(parseFloat(this))});
				var maxGstPer = Math.max.apply(Math, gstPer);
				maxGstPer = (maxGstPer != "")?maxGstPer:0;

				if(row.calc_type == "1"){
					var amount = (amtField.val() != "")?amtField.val():0;
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
					//var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount))/100).toFixed(2);

					if(row.add_or_deduct == 1){
						var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount)) / 100).toFixed(3);
					}else{
						var gstAmount = 0, expGstAmt = 0;
						var taxable_amount = ($("#taxable_amount").val() != "") ? $("#taxable_amount").val() : 0;
						var itemCount = ($('#tempItem tr:last').attr('id') !== 'noData')?($('#tempItem tr:last').index() + 1):0;
						if(itemCount > 0){
							var taxablePer = 0, taxableAmt = 0;
							$.each($("#tempItem tr"),function(){
								formData = $(this).data('item_data');
								taxablePer = (parseFloat(formData.amount) > 0)?parseFloat((parseFloat((formData.amount - formData.disc_amt)) * 100) / parseFloat(taxable_amount)).toFixed(3):0;
								taxableAmt = parseFloat((parseFloat(amount) * parseFloat(taxablePer)) / 100).toFixed(3);
								expGstAmt = 0;
								expGstAmt = (parseFloat(formData.gst_per) > 0)?parseFloat((parseFloat(formData.gst_per) * parseFloat(taxableAmt)) / 100).toFixed(3):0;
								gstAmount += parseFloat(expGstAmt);
							});
						}
					}
				}else{
					var taxable_amount = ($("#taxable_amount").val() != "")?$("#taxable_amount").val():0;
					var per = (perField.val() != "")?perField.val():0;
					
					var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
					amtField.val(amount);
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
					//var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount))/100).toFixed(2);

					if(row.add_or_deduct == 1){
						var gstAmount = parseFloat((parseFloat(maxGstPer) * parseFloat(amount)) / 100).toFixed(3);
					}else{
						var gstAmount = 0, expGstAmt = 0;
						var itemCount = ($('#tempItem tr:last').attr('id') !== 'noData')?($('#tempItem tr:last').index() + 1):0;
						if(itemCount > 0){
							var taxablePer = 0, taxableAmt = 0;
							$.each($("#tempItem tr"),function(){
								formData = $(this).data('item_data');
								taxablePer = (parseFloat(formData.amount) > 0)?parseFloat((parseFloat(formData.amount - formData.disc_amt) * 100) / parseFloat(taxable_amount)).toFixed(3):0;
								taxableAmt = parseFloat((parseFloat(amount) * parseFloat(taxablePer)) / 100).toFixed(3);
								expGstAmt = 0;
								expGstAmt = (parseFloat(formData.gst_per) > 0)?parseFloat((parseFloat(formData.gst_per) * parseFloat(taxableAmt)) / 100).toFixed(3):0;
								gstAmount += parseFloat(expGstAmt);
							});
						}
					}
				}
				$("#other_"+map_code+"_amount").val(gstAmount);
			}else{
				if(row.calc_type == "1"){
					var amount = (amtField.val() != "")?amtField.val():0;
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
					netAmountField.val(amount);
				}else{
					var taxable_amount = ($("#taxable_amount").val() != "")?$("#taxable_amount").val():0;
					var per = (perField.val() != "")?perField.val():0;
					var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
					amtField.val(amount);
					amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);				
					netAmountField.val(amount);
				}
			}
		}

		if(sm_type == "tax"){
			if(row.calculation_type == 1){
				var taxable_amount = ($("#taxable_amount").val() != "")?$("#taxable_amount").val():0;
				var per = (perField.val() != "")?perField.val():0;
				var amount = parseFloat((parseFloat(taxable_amount) * parseFloat(per)) / 100).toFixed(2);
				amtField.val(amount);
				amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
				netAmountField.val(amount);
			}else{
				var qtyArray = $("input[name='qty[]']").map(function(){return $(this).val();}).get();
				var qtySum = 0;
				$.each(qtyArray,function(){qtySum += parseFloat(this) || 0;});

				var per = (perField.val() != "")?perField.val():0;
				var amount = parseFloat(parseFloat(qtySum) * parseFloat(per)).toFixed(2);
				amtField.val(amount);
				amount = parseFloat(parseFloat(amount) * parseFloat(row.add_or_deduct)).toFixed(2);
				netAmountField.val(amount);
			}			
		}
	});
	calculateSummaryAmount();
}

function calculateSummaryAmount(){
	var gst_type = $("#gst_type").val();
	$('#cgst_amount').val("0");
	$('#sgst_amount').val("0");
	if(gst_type == 1){
		var cgstAmtArr = $("input[name='cgst_amt[]']").map(function(){return $(this).val();}).get();
		var cgstAmtSum = 0;
		$.each(cgstAmtArr,function(){cgstAmtSum += parseFloat(this) || 0;});
		$('#cgst_amount').val(parseFloat(cgstAmtSum).toFixed(2));
		var sgstAmtArr = $("input[name='sgst_amt[]']").map(function(){return $(this).val();}).get();
		var sgstAmtSum = 0;
		$.each(sgstAmtArr,function(){sgstAmtSum += parseFloat(this) || 0;});
		$('#sgst_amount').val(parseFloat(sgstAmtSum).toFixed(2));
	}
	
	$('#igst_amount').val("0");
	if(gst_type == 2){
		var igstAmtArr = $("input[name='igst_amt[]']").map(function(){return $(this).val();}).get();
		var igstAmtSum = 0;
		$.each(igstAmtArr,function(){igstAmtSum += parseFloat(this) || 0;});
		$('#igst_amount').val(parseFloat(igstAmtSum).toFixed(2));
	}
	
	var otherGstAmtArray = $(".otherGstAmount").map(function(){return $(this).val();}).get();
	var otherGstAmtSum = 0;
	$.each(otherGstAmtArray,function(){otherGstAmtSum += parseFloat(this) || 0;});

	var cgstAmt = 0;
	var sgstAmt = 0;
	var igstAmt = 0;
	if(gst_type == 1){
		cgstAmt = parseFloat(parseFloat(otherGstAmtSum) / 2).toFixed(2);
		sgstAmt = parseFloat(parseFloat(otherGstAmtSum) / 2).toFixed(2);
		$("#cgst_amount").val(parseFloat(parseFloat($("#cgst_amount").val()) + parseFloat(cgstAmt)).toFixed(2));
		$("#sgst_amount").val(parseFloat(parseFloat($("#sgst_amount").val()) + parseFloat((sgstAmt))).toFixed(2));						
	}else if(gst_type == 2){
		igstAmt = otherGstAmtSum;
		$("#igst_amount").val(parseFloat(parseFloat($("#igst_amount").val()) + parseFloat((igstAmt))).toFixed(2));
	}	

	var summaryAmtArray = $(".summaryAmount").map(function(){return $(this).val();}).get();
	var summaryAmtSum = 0;
	$.each(summaryAmtArray,function(){summaryAmtSum += parseFloat(this) || 0;});	

	if($("#roff_amount").length > 0){
		var totalAmount = parseFloat(summaryAmtSum).toFixed(2);
		var decimal = totalAmount.split('.')[1];
		var roundOff = 0;
		var netAmount = 0;
		if (decimal !== 0) {
			if (decimal >= 50) {
				if ($('#apply_round').val() == "0") { roundOff = (100 - decimal) / 100; }
				netAmount = parseFloat(parseFloat(totalAmount) + parseFloat(roundOff)).toFixed(2);
			}else {
				if ($('#apply_round').val() == "0") { roundOff = (decimal - (decimal * 2)) / 100; }
				netAmount = parseFloat(parseFloat(totalAmount) + parseFloat(roundOff)).toFixed(2);
			}
			$("#roff_amount").val(parseFloat(roundOff).toFixed(2));
		}
		$("#net_inv_amount").val(netAmount);
	}else{
		$("#net_inv_amount").val(summaryAmtSum.toFixed(2));
	}
}

function saveInvoice(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
	$.ajax({
		url: base_url + controller + '/save',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.field_error_message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.field_error_message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = data.url;
		}else{
			toastr.error(data.field_error_message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

function saveOrder(formId){
	var fd = $('#'+formId).serialize();
	$.ajax({
		url: base_url + controller + '/save',
		data:fd,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {
				$("."+key).html(value);
			});
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            window.location = data.url;
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}

// Created By : Karmi 15/12/2021
function orderTable()
{
	var orderTable = $('#orderTable').DataTable( 
	{
		responsive: true,
		//'stateSave':true,
		"autoWidth" : false,
		order:[],
		"columnDefs": 	[
							{ type: 'natural', targets: 0 },
							{ orderable: false, targets: "_all" }, 
							{ className: "text-left", targets: [0,1] }, 
							{ className: "text-center", "targets": "_all" } 
						],
		pageLength:100,
		language: { search: "" },
		lengthMenu: [
            [ 10, 25, 50, 100, -1 ],[ '10 rows', '25 rows', '50 rows', '100 rows', 'Show all' ]
        ],
		dom: "<'row'<'col-sm-7'B><'col-sm-5'f>>" +"<'row'<'col-sm-12't>>" + "<'row'<'col-sm-5'i><'col-sm-7'p>>",
		buttons: [] //[ 'pageLength', 'excel', {text: 'Refresh',action: function ( e, dt, node, config ) {loadAttendanceSheet();}}]
	});
	orderTable.buttons().container().appendTo( '#orderTable_wrapper toolbar' );
	$('.dataTables_filter .form-control-sm').css("width","97%");
	$('.dataTables_filter .form-control-sm').attr("placeholder","Search.....");
	$('.dataTables_filter').css("text-align","left");
	$('.dataTables_filter label').css("display","block");
	$('.btn-group>.btn:first-child').css("border-top-right-radius","0");
	$('.btn-group>.btn:first-child').css("border-bottom-right-radius","0");
	return orderTable;
}

function getFGSelect(row) {
	var itemData = row;
	var price = 0;
	if(itemData.price != "")
	{
		var p = parseFloat(itemData.price);
		if(cm_id == 1 && $("#party_id").val() != "119"){price = parseFloat(p/(parseFloat((parseFloat(itemData.gst_per) + 100) / 100).toFixed(2))).toFixed(2);}else{price = p;}
	}
	$("#item_id").val(itemData.id);
	$("#item_type").val(itemData.item_type);
	$("#item_code").val(itemData.item_code);
	$("#item_name_dis").val(itemData.item_name);
	$("#item_name").val(itemData.item_name);
	$("#item_desc").val(itemData.description);
	$("#hsn_code").val(itemData.hsn_code);
	$("#gst_per").val(itemData.gst_per);
	$("#unit_name").val(itemData.unit_name);
	$("#unit_id").val(itemData.unit_id);
	$("#disc_amt").val(0);
	
	if(cm_id == 1){
		$("#org_price").val(itemData.price);
    	$("#price").val(price);
	}else{
		var invoice_type = $('#invoice_type').val();
		if(invoice_type == 'Regular'){ 
			$("#org_price").val(itemData.price);
			$("#price").val(price);
		}else if(invoice_type == 'Wholesale'){  
			$("#org_price").val(itemData.wholesale2); 
			$("#price").val(itemData.wholesale2);
		}else{  
			$("#org_price").val(itemData.wholesale1); 
			$("#price").val(itemData.wholesale1);
		}
	}
	
	var item_id = itemData.id;
	$("#modal-xl").modal('hide');
}

function addItemQty(btnClass,qty=1){
	$('.'+btnClass).trigger('click');
	$('#qty').val(parseFloat($('#qty').val()) + parseFloat(qty));
	$('.saveItem').trigger('click');
}