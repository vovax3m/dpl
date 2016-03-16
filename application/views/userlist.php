	<span class="title">Управление пользователями кабинета <?php echo $sub?></span>
	<hr>
	<span id="temp" class="temp" ondblclick="$('#temp').hide();"><?php echo $this->input->cookie('STAT', TRUE); $this->input->set_cookie('STAT', '', '-3600'); ?></span>
	
	<a href="/lk/?sub=<?php echo $sub?>" class="hand"><i class="fa fa-home "></i> Вернуться назад</a>
	<br><br>
 <table>
	<tr>
		<td>
			Имя
		</td>
		<td>
			Вн номер
		</td>
		<td>
			Пароль
		</td>
		<td>
			Доступ
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
			Войти
		</td>
	</tr>
 <?php

 foreach ($auth as $user):
	?>
	<tr id="uid<?php echo $user['id'];?>">
		<td>
			<?php echo $user['username'];?>
		</td>
		<td>
			<?php echo $user['exten'];?>
		</td>
		<td> 
			<span id="hidepass<?php echo $user['id'];?>" onclick="$('#hidepass<?php echo $user['id'];?>').text('<?php echo str_rot13(base64_decode($pass[$user['username']]));?>');$('#hidepass<?php echo $user['id'];?>').prop('onclick',null).off('click');">*****</span>
			
		</td>
		<td>
			<?php if($user['is_admin']==1){  echo 'Полный'; }else{ echo  'Ограниченный';}?> 
		</td>
		<td>
			<?php echo $user['ip'];?>
		</td>
		<td>
			в перспективе
		</td>
		<td>
			<i class="fa fa-gear  hand" onclick="edituser('<?php echo $sub;?>','<?php echo $user['id'];?>','<?php echo $user['username'];?>','<?php echo $user['exten'];?>','<?php echo $user['is_admin'];?>','<?php echo $acc_id[$user['username']]?>')"></i>
		</td>
		<td>
			<i class="fa fa-trash-o  hand" onclick="deluser('<?php echo $sub;?>','<?php echo $user['id'];?>','<?php echo $acc_id[$user['username']]?>')"></i>
		</td>
		<td>
			<form  action="http://<?php echo $sub;?>.sip64.ru/auth/login" method="POST">
				<input type="hidden" name="u" value="<?php echo $user['username'];?>">
				<input type="hidden" name="p" value="<?php echo str_rot13(base64_decode($pass[$user['username']]));?>">
				<input type="submit" value="Залогиниться">
			</form>
		</td>
	</tr>
<?php endforeach;?>
</table>
<hr>
<table>
	<tr>
		<td>
			<span title="Добавить" class="title hand" onclick="$('#addform').toggle('100')">Добавить пользователя</span>
			
			<div id="addform" style="display:none">
			<form action="/lk/useradd/<?php echo $sub ;?>" method="POST">
			<p>Имя пользователя<br>
				<input type="text" class="addform" name="username">
			</p>
			<p>Внутренний номер<br>
				<select class="addform" name="ext" title="абоненты ватс" >
					
				<?php  foreach ($ext as $n):;?>
					<option value="<?php echo $n[0];?>">	<?php echo $n[0];?></option>
				<?php endforeach;?>
				</select>
			</p>
			<p>Админские права <br>
				<input type="checkbox" class="addform" name="admin">
			</p>
			<p>Пароль <span  OnClick="runPassGen('pass')" class="blue hand">придумать</span><br>
				<input type="text" class="addform" name="pass" id="pass"> <br>
			</p>
			<p>
				<input type="submit"  name="submit" class="addform" value="Добавить">
			</p>
			</form>
			</div>
		</td>
		<td>
			<span title="Редактировать" class="title hand" onclick="$('#editform').toggle('100')" id="editspan" style="display:none">Редатирование </span>
			
			<div id="editform" style="display:none">
		<form action="/lk/useredit/<?php echo $sub ;?>" method="POST">
	<p>Имя пользователя<br>
				<input type="text" class="addform" name="username" id="editusername" value="" >
				<input type="hidden" class="addform" name="uid" id="edituid" value="" readonly>
				<input type="hidden" class="addform" name="acc_id" id="edit_acc_id" value="" readonly>
			</p>
			<p>Внутренний номер<br>
				<select class="addform" name="ext" title="абоненты ватс" id="editext" >
					<option value="false" >вн номер</option>
				<?php  foreach ($ext as $n):;?>
					<option value="<?php echo $n[0];?>">	<?php echo $n[0];?></option>
				<?php endforeach;?>
				</select>
					<p>Админские права<br>
				<input type="checkbox" class="addform" name="admin" id="editadmin">
			</p>
			</p>
				<p>Новый пароль <span  OnClick="runPassGen('editpass')" class="blue hand">придумать</span><br>
				<input type="text" class="addform" name="pass" id="editpass"> <br>
			</p>
			<p>
				<input type="submit"  name="submit" class="addform" value="Изменить">
			</p>
		</div>
		</td>
	
		
			
		
		
		
		
	</tr>
</table>


