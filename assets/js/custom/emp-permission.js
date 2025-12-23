$(document).ready(function(){
    $(document).on('change',"#emp_id",function(){
        var emp_id = $(this).val();
        $("#empPermission")[0].reset();
        $(".error").html("");
        $(this).val(emp_id);
        $(this).comboSelect();
        $(".chk-col-success").removeAttr("checked");
        
        $.ajax({
            type: "POST",   
            url: base_url + controller + '/editPermission',   
            data: {emp_id:emp_id},
            dataType:"json"
        }).done(function(response){
            var permission = response.empPermission;
            if(permission.length > 0){
                $.each(response.empPermission,function(key, value) {
                    $("#"+value).attr("checked","checked");
                }); 
            }
        });
    });
});
function saveOrder(formId){
	var fd = $('#'+formId)[0];
    var formData = new FormData(fd);
    
	$.ajax({
		url: base_url + controller + '/savePermission',
		data:formData,
        processData: false,
        contentType: false,
		type: "POST",
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
            if(data.field_error == 1){
				$(".error").html("");
				$.each( data.field_error_message, function( key, value ) {$("."+key).html(value);});
			}else{
				toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
			}
		}else if(data.status==1){
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            $("#empPermission")[0].reset();
            $(".chk-col-success").removeAttr("checked");
		}else{
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}				
	});
}