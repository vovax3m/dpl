	<span class="title">Управление кабинетом <?php echo $sub?></span>
	<hr>
	<span id="temp" class="temp" ondblclick="$('#temp').hide();"><?php echo $this->input->cookie('STAT', TRUE); $this->input->set_cookie('STAT', '', '-3600'); ?></span>
	
	<a href="/lk/?sub=<?php echo $sub?>" class="hand"><i class="fa fa-home "></i> Вернуться назад</a>
	<br><br>
	<div>
	Версия кабинета:<span class="title">  <?php echo $ver;?></title></span>
	<br>
	Тип кабинета: <select id="sett_type" class="title">
		<option value="FULL" <?php if($type=='FULL') echo 'selected';?>>FULL</option>
		<option value="LITE" <?php if($type=='LITE') echo 'selected';?>>LITE</option>
	</select>
	<br>
		<input type="submit" value="изменить" onclick="setting_save('<?php echo $sub?>');">
	
	</div>