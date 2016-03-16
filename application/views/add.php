<script type="text/javascript">
function gen_subdomen(){
	var first=$("#3oct").val();
	var sec=$("#4oct").val();
	if(sec==''){
		$("#domen").val('http://'+first+'.sip64.ru'); 
		$("#subdomen").val(first);
		return false;
	}
	var bukva;
	if(isInt(sec)==true){
		if(first=='a'){
			if((sec>131) &&(sec < 255)){
				$("#domen").val('http://'+first+sec+'.sip64.ru'); 
				$("#subdomen").val(first+sec);
			}else{
				alert('Недопустимое значение. В данной сети возможно значения только в диапазоне  132 < n < 255'); 
			}
		}else if(first=='b'){
			if((sec>0) && (sec < 255)){
				$("#domen").val('http://'+first+sec+'.sip64.ru'); 
				$("#subdomen").val(first+sec);
			}else{
				alert('Недопустимое значение. В данной сети возможно значения только в диапазоне  0 < n < 255');
			}
		}else{
			alert('Недопустимое значение. Выберите значение из выпадающего списка');
		}
	}else{
		alert('Недопустимое значение. Допустимы только целые числа от 1 до 254');
	}
	
	
}
</script>
<div>
	<form action="/add/handler" method="POST"> 
		<label>
			IP ВАТС <br>
			<select name="ip_3oct" class="addform rightdir" id="3oct" onchange="gen_subdomen();" required>
				<option value="false">выберите</option>
				<option value="b">159.253.121</option>
				<option value="a">91.196.5</option>
			</select>.
			<input type="text" name="ip_4oct" class="addform oktet" id="4oct" onchange="gen_subdomen();" required>
		</label>
		<br>
		<label>
			Название <br>
			<input type="text" name="nazv" class="addform" required>
		</label>
		<br>
		<label>
			Адрес кабинета <br>
			<input type="text" name="domen" id ="domen" class="addform" value="" readonly required>
			<br>
			Поддомен<br>
			<input type="text" name="subdomen" id="subdomen" class="addform" value="" readonly required>
		</label>
		<br>
		<label>
			Имя пользователя администратора <br> 
			<input type="text" name="login" class="addform" required>
		</label>
		<br>
			<label>
			Внутренний номер <br> 
			<input type="text" name="exten" class="addform" required>
		</label>
		<br>
		<label>
			Пароль<br>
			<input type="text" id="pass" name="pass" class="addform" required>
			<span  OnClick="runPassGen('pass')" >Сгенерировать</span>
		</label>
		<br>
		<label>
			Количество строк на странице в истории звонков<br>
			<input type="text"  name="pagin" class="addform" value="25" required>
		</label>
		<br>
		<label>
			Версия астериска<br>
			<input type="text"  name="ver" class="addform" value="2.5" required>
		</label>
		<br>
		<label>
			Тип <br>
			<select name="type" class="addform rightdir" id="type" >
				<option value="LITE">Легкая</option>
				<option value="FULL">Полная</option>
			</select>
			
		</label>
		<br>
		<label>
		<br>
			<input type="submit"  name="submit" class="addform" value="Развернуть">
		</label>
	</form>
</div>