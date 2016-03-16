function editun(id){
	$('#editform').show('fast');
	$('#editlink').show('fast');
	$('#editid').val(id);
	$('#editusername').val($('#un'+id).val());
	$.ajax({
			url: '/un/fillaccid/'+id,
			success: function(data) {
				$('#editselected').append(data);	
				
			},
			error: function (xhr, ajaxOptions, thrownError) {
				console.log(thrownError); 
			} 
		});
	
	
}
function delun(id){
	
	var i=confirm(' Потвердите удаление пользователя ');
	
	if(i==true){
		$.ajax({
			url: '/un/userdel/'+id,
			beforeSend:function(data){
			$('#temp').html('Пожалуйста, подождите..'); 
				$('#temp').show('fast');
			},
			success: function(data) {
				$('#temp').html(data);
				$('#temp').show('fast');
				$('#uid'+id).hide();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			} 
		});
	}
}