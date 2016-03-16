<!-- getlist of klients-->
<?php
$klients='';
foreach ($list as $k=>$n):
					$name=iconv("windows-1251","UTF-8",$n);
					$klients.="<option value='{$k}'>{$name}</option>";
endforeach;?>	
	<span class="title">Управление учетными данными </span>
	<hr>
	<span id="temp" class="temp" ondblclick="$('#temp').hide();"><?php echo $this->input->cookie('STAT', TRUE); $this->input->set_cookie('STAT', '', '-3600'); ?>
	</span>
	<br>
<!------------------------------------ ADD USER ------------------------------------>
	<span title="Добавить" class="title hand" onclick="$('#addform').toggle('100')">Добавить пользователя</span>
	<div id="addform" style="display:none">
<table>
	<tr>
		<td>
			<form action="/un/useradd/" method="POST">
			<p>Логин <span  OnClick="runPassGen('username')" class="blue hand">придумать</span><br>
				<input type="text" class="addform" name="username" id="username">
			</p>
			<p>Пароль <span  OnClick="runPassGen('pass')" class="blue hand">придумать</span><br>
				<input type="text" class="addform" name="pass" id="pass"> <br>
			</p>
		</td>
		<td>
			<p>Выберите клиента<br> 
				<select class="form_option"  id="list "title="клиенты УН" size="20" onchange="$('#selected').append('<option value='+this.value+'>'+this.options[selectedIndex].text+'</option>');"> 
				<?php echo $klients?>
				</select>
			</p>
		</td>
		<td>
			
			<p>Добавленые<br>
				<select class="form_option" name="selected"  id="selected" title="клиенты УН" size="20" onchange='$("#selected :selected").remove();' >
				
				</select>
			<textarea style="display:none"   name="accid" id="sel"></textarea>
			</p>
		<td>
				
			<p>
				<input type="submit"  name="submit" class="addform" value="Добавить" onclick="$('#sel').empty();$('#selected option').each(function(){ $('#sel').append(this.value+',');});">
			</p>
		</td>
			</form>
			</table>
		</div>
		
<!------------------------------------ EDIT USER ------------------------------------>

		<br>
<span  class="title hand" onclick="$('#editform').toggle('100')" id="editlink" style="display:none">Редактировать пользователя</span>
	<div id="editform" style="display:none">
<table>
	<tr>
		<td>
			<form action="/un/useradd/1" method="POST">
			<p>Логин <span  OnClick="runPassGen('editusername')" class="blue hand">придумать</span><br>
				<input type="hidden" class="addform" name="editid" id="editid">	
				<input type="text" class="addform" name="username" id="editusername">
			</p>
			<p>Пароль <span  OnClick="runPassGen('editpass')" class="blue hand">придумать</span><br>
				<input type="text" class="addform" name="pass" id="editpass"> <br>
			</p>
		</td>
		<td>
			<p>Выберите клиента<br> 
				<select class="form_option"  id="editlist "title="клиенты УН" size="20" onchange="$('#editselected').append('<option value='+this.value+'>'+this.options[selectedIndex].text+'</option>');"> 
				<?php echo $klients?>
				</select>
			</p>
		</td>
		<td>
			
			<p>Добавленые<br>
				<select class="form_option" name="selected"  id="editselected" title="клиенты УН" size="20" onchange='$("#editselected :selected").remove();' >
				</select>
			<textarea style="display:none"   name="accid" id="editsel"></textarea>
			</p>
		<td>
				
			<p>
				<input type="submit"  name="submit" class="addform" value="Изменить" onclick="$('#editsel').empty();$('#editselected option').each(function(){ $('#editsel').append(this.value+',');});">
			</p>
		</td>
			</form>
			</table>
		</div>		
	<br>
	
<!------------------------------------ USER LIST ------------------------------------>
	<span title="Добавить" class="title hand" onclick="$('#list').toggle('100')">Пользователи</span><br>
 <table id="list">
	<tr>
		<td>
			Имя
		</td>
		
		<td>
			Пароль
		</td>
		
		<td>
			Последний ip
		</td>
		<td>
			Дата входа
		</td>
		<td>
			Редактирование
		</td>
		<td>
			Удаление
		</td>
		<td>
			компании
		</td>
		<td>
			Войти
		</td>
	</tr>
 <?php
	$sub='lk';
	//echo sha1('vova');
	//print_r();
 foreach ($auth as $user):
 //print_r(base64_decode($user['passwd']));
	?>
	<input type="hidden" id="id<?php echo $user['id'];?>" value="<?php echo $user['id'];?>">
	<input type="hidden" id="un<?php echo $user['id'];?>" value="<?php echo $user['username'];?>">
	<input type="hidden" id="accid<?php echo $user['id'];?>" value="<?php echo $user['accid'];?>">
	<tr id="uid<?php echo $user['id'];?>">
		<td>
			<?php echo $user['username'];?>
		</td>
		<td> 
			<span id="hidepass<?php echo $user['id'];?>" onclick="$('#hidepass<?php echo $user['id'];?>').text('<?php echo str_rot13(base64_decode($pass[$user['id']]));?>');$('#hidepass<?php echo $user['id'];?>').prop('onclick',null).off('click');">*****</span>
			
		</td>
		
		<td>
			<?php echo $user['ip'];?>
		</td>
		<td>
			<?php echo $user['date'];?>
		</td>
		<td>
			<i class="fa fa-gear  hand" onclick="editun('<?php echo$user['id'] ?>')"></i>
		</td>
		<td>
			<i class="fa fa-trash-o  hand" onclick="delun('<?php echo$user['id'] ?>')"></i>
		</td>
		<td align="left">
			<?php  //echo $user['accid'];
			$one=explode(',',$user['accid']);
			$names='';
			foreach($one as $aid){
				$names.= $list[$aid].'<br>';
			};
			echo iconv("windows-1251","UTF-8",$names);
			?>
		</td>
		<td>
			<form  action="http://lk.sip64.ru/auth/login" method="POST">
				<input type="hidden" name="u" value="<?php echo $user['username'];?>">
				<input type="hidden" name="p" value="<?php echo str_rot13(base64_decode($pass[$user['id']]));?>">
				<input type="submit" value="Залогиниться">
			</form>
		</td>
	</tr>
<?php endforeach;?>
</table>

</div>

