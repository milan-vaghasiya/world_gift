$(document).ready(function(){
	
	$(document).on("click",".btn-leaveAction",function(){
		$('#id').val($(this).data('id'));
		$('#approved_date').val($(this).data('min_date'));
		$('#approved_date').attr('min',$(this).data('min_date'));
		$("#approveLeaveModal").modal();
	});
	$(document).on("click",".btn-approveLeave",function(){
		var fd = $('#approveLeaveForm').serialize();
		$.ajax({
			url: base_url + controller + '/approveLeave',
			data:fd,
			type: "POST",
			dataType:"json",
		}).done(function(data){
			if(data.status===0){
				if(data.field_error == 1){
					$(".error").html("");
					$.each( data.field_error_message, function( key, value ) {$("."+key).html(value);});
				}else{
					initTable(); $(".modal").modal('hide');
					toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
				}
			}else if(data.status==1){
				initTable(); $(".modal").modal('hide');
				toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}else{
				initTable(); $(".modal").modal('hide');
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
					
		});
	});
});