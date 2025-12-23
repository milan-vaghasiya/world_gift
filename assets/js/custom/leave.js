$(document).ready(function(){
	$(document).on("change",".leave_type_id",function(){
		var leave_type_id = $(this).val();
		if(leave_type_id)
		{
			$.ajax({
				url: base_url + controller + '/getEmpLeaves',
				type:'post',
				data:{leave_type_id:leave_type_id},
				dataType:'json',
				success:function(data){
					$(".max-leave").html('Maximum Leave : ' + data.max_leave);
					$(".used-leave").html('Taken Leave : ' + data.used_leaves);
					$(".remain-leave").html('Remain Leave : ' + data.remain_leaves);
				}
			});
		}
		else{$(".max-leave").html('');$(".used-leave").html('');$(".remain-leave").html('');}
	});
	$(document).on("change","#start_date",function(){
		$('#end_date').val($(this).val());
		$('#end_date').attr('min',$(this).val());
		
		const startDate  = $('#start_date').val();
		const endDate    = $('#end_date').val();

		const diffInMs   = new Date(endDate) - new Date(startDate)
		const diffInDays = diffInMs / (1000 * 60 * 60 * 24);
		$(".leave-days").css('padding','5px');
		$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
		$("#total_days").val(diffInDays);
	});
	$(document).on("change","#end_date",function(){
		const startDate  = $('#start_date').val();
		const endDate    = $('#end_date').val();

		const diffInMs   = new Date(endDate) - new Date(startDate)
		const diffInDays = diffInMs / (1000 * 60 * 60 * 24);
		$(".leave-days").css('padding','5px');
		$(".leave-days").html('You are applying ' + diffInDays + ' Days Leave');
		$("#total_days").val(diffInDays);
	});
});