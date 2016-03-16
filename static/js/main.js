function setting_save(sub){
	var type=$('#sett_type').val();
	
	$.ajax({
			type:"POST",
			data:{
				'type' : type
				
				},
			url: '/lk/sett_save/'+sub,
			beforeSend:function(data){
			$('#temp').html('Пожалуйста, подождите..'); 
				$('#temp').show('fast');
			},
			success: function(data) {
				$('#temp').html(data);
				$('#temp').show('fast');
				
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			} 
		});
	
}

function delbad(sub,id){
	var i=confirm('Подтвердите удаление');
	console.log(i);
	if(i==true){
		$.ajax({
			url: '/lk/delbad/'+sub+'/'+id,
			beforeSend:function(data){
			$('#temp').html('Пожалуйста, подождите..'); 
				$('#temp').show('fast');
			},
			success: function(data) {
				$('#temp').html(data);
				$('#temp').show('fast');
				$('#bid'+id).hide();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			} 
		});
	}
	
}

function deluser(sub,uid,acc_id){
	
	var i=confirm(' Потвердите удаление пользователя ');
	//console.log(i);
	if(i==true){
		$.ajax({
			url: '/lk/userdel/'+sub+'/'+uid+'/'+acc_id,
			beforeSend:function(data){
			$('#temp').html('Пожалуйста, подождите..'); 
				$('#temp').show('fast');
			},
			success: function(data) {
				$('#temp').html(data);
				$('#temp').show('fast');
				$('#uid'+uid).hide();
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			} 
		});
	}
}
function edituser(sub,uid,username,ext,admin,acc_id){
//var i=confirm(' ЕЕЕЕЕЕdit it ??????!!!! sub='+sub+' uid='+ uid);
	$('#editspan').show('fast');
	$('#editform').show('fast');
	$('#editusername').val(username);
	$('#edituid').val(uid);
	$('#editext').val(ext);
	$('#edit_acc_id').val(acc_id);
	if(admin==1)$('#editadmin').prop('checked', true);
	
}
/* @param id - идентификатор блока для вставки паролей)
@param syllableNum - количество слогов в пароле
@param numPass - количество количество паролей вставляемых в блок
@param useNums - использовать числа или нет */
function jsPassGen(id, syllableNum, numPass, useNums) {
id = typeof(id) != 'undefined' ? id : 'jsPassGenForm';    // параметры по умолчанию
syllableNum = typeof(syllableNum) != 'undefined' ? syllableNum : 3;
numPass = typeof(numPass) != 'undefined' ? numPass : 10;
useNums = typeof(useNums) != 'undefined' ? useNums : true;

function rand(from, to) {
from = typeof(from) != 'undefined' ? from : 0;    // параметры
to = typeof(to) != 'undefined' ? to : from + 1;    // по умолчанию
return Math.round(from + Math.random()*(to - from));
};

function getRandChar(a) {
return a.charAt(rand(0,a.length-1));
}

var form = document.getElementById(id);
// Наиболее подходящие согласные для использования их в качестве заглавных
var cCommon = "bcdfghklmnprstvz";
var cAll = cCommon + "jqwx";    // Все согласные
var vAll = "aeiouy";    // Все гласные
var lAll = cAll + vAll;    // Все буквы
console.log(form);
form.value = '';
for(var j = 0; j < numPass; ++j) {
// Коэффициент определяющий вероятность появления числа между слогами
var numProb = 0, numProbStep = 0.25;
for(var i = 0; i < syllableNum; ++i) {
if(Math.round(Math.random())) {
form.value += getRandChar(cCommon).toUpperCase() +
getRandChar(vAll) +
getRandChar(lAll);
} else {
form.value += getRandChar(vAll).toUpperCase() +
getRandChar(cCommon);
}
if(useNums && Math.round(Math.random() + numProb)) {
form.value += rand(0,9);
numProb += numProbStep;
}
}

}
return false;
}

function runPassGen(elem) {
 if(elem==false)elem='pass';
jsPassGen(elem, 3, 1);
}
function isInt(n) {
   return n % 1 === 0;
}
// вставляем номер в  поле звонка
function callto(no){
	
	$('#nomer').val(no);

}

function reset_cdr_filter(){
	console.log('reset');
	$('#startdate').val('');
	$('#enddate').val('');
	$('#incom').prop("checked", false) ;
	$('#outcom').prop("checked", false) ;
	$('#recyes').prop("checked", false) ;
	$('#recno').prop("checked", false) ;
	$('[name="exten"]').val('');
	$('[name="durtype"]').val('');
	$('[name="durtime"]').val('');
	$('[name="anstype"]').val('');

}
function resetivrform(){
	console.log('reset');
	$('#upload').val('');
	$('#setas').val('');
	$('#upload_link').val('Выберите Файл');
	
}

function save(fn,n,type){
	if(type=='cdr'){
		var string='&fn='+fn+'&cdr=true&date='+n;
		$.ajax({
			url: 'ajax/downloadivr/?'+string,
			beforeSend:function(data){
			$('#temp').html('Пожалуйста, подождите..'); 
				$('#temp').show('fast');
			},
			success: function(data) {
				//$('#temp').html('<div align="center">'+fn +' <audio  autoplay controls><source src="files/'+fn+'" type="audio/x-wav"> </audio></div>');
				$('#temp').html(data);
				$('#temp').show('fast');
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			} 
		});
	}else{
		var string='&fn='+fn;
		$.ajax({
			url: 'ajax/downloadivr/?'+string,
			beforeSend:function(data){
			$('#temp').html('Пожалуйста, подождите..'); 
			$('#temp').show('fast');
			},
			success: function(data) {
				$('#temp').html(data);
				$('#temp').show('500');
				// window.location.href = 'files/'+fn;
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			}
					  
		});
	}
}

function play(fn,n,type){
	var string='&fn='+fn;
	if(type=='cdr'){
		var string='&fn='+fn+'&cdr=true&date='+n;
		$.ajax({
			url: 'ajax/downloadivr/?'+string,
			beforeSend:function(data){
			$('#temp').html('Пожалуйста, подождите..'); 
				$('#temp').show('fast');
			},
			success: function(data) {
				$('#temp').html('<div align="center">'+fn +' <audio  autoplay controls><source src="files/'+fn+'" type="audio/x-wav"> </audio></div>');
				$('#temp').show('fast');
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			} 
		});
	}else{
		$.ajax({
			url: 'ajax/downloadivr/?'+string,
			beforeSend:function(data){
			$('#play'+n).html('Пожалуйста, подождите..'); 
				
			},
			success: function(data) {
				$('#play'+n).html('<audio  autoplay controls><source src="files/'+fn+'" type="audio/x-wav"> </audio>');
				//$('#temp').show('500');
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError); 
			} 
		});
	}
}

function export_xlsx(qs){
	
	var string=qs+'&export_xlsx=true';
	$.ajax({
		url: 'ajax/export_xlsx?'+string,
		success: function(data) {
			$('#temp').html(data);
			$('#temp').show('500');
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError);
		}
				  
	});
}
function export_csv(qs){
	var string=qs+'&export_csv=true';
	$.ajax({
		url: 'ajax/export_csv?'+string,
		success: function(data) {
			$('#temp').html(data);
			$('#temp').show('500');
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError);
		}
				  
	});
}

$(function(){
	$('#startdate').datepicker({ 
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		selectOtherMonths: true,
		firstDay: "1",
		onClose: function( selectedDate ) {
			$( "#enddate" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$('#enddate').datepicker({ 
		dateFormat: "yy-mm-dd",
		changeMonth: true,
		changeYear: true,
		showOtherMonths: true,
		selectOtherMonths: true,
		firstDay: "1",
		onClose: function( selectedDate ) {
			$( "#startdate" ).datepicker( "option", "maxDate", selectedDate ); 
		}
	});
	
	$.datepicker.regional['ru'] = { 
		closeText: 'Закрыть', 
		prevText: '&#x3c;Пред', 
		nextText: 'След&#x3e;', 
		currentText: 'Сегодня', 
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь', 
								'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'], 
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн', 
										'Июл','Авг','Сен','Окт','Ноя','Дек'], 
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'], 
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'], 
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'], 
		dateFormat:  "yy-mm-dd",
		firstDay: 1, 
		isRTL: false 
	}; 
	
	$.datepicker.setDefaults($.datepicker.regional['ru']); 				
});

