<span class="title"><?php echo $sub?> (<?php /* echo @file_get_contents('/var/www/html/'.$sub.'/version')*/ echo $type." ".$version;?>)</span> 
<hr class="hr_title">

<span class="topmenu"><a href="/lk/open/<?php echo $sub?>">Войти в кабинет</a></span>
<span class="topmenu"><a href="/lk/users/<?php echo $sub?>">Управление пользователями</a></span>
<span class="topmenu"><a href="/lk/confdel/<?php echo $sub?>" >Удалить</a></span>
<span class="topmenu"><a href="/lk/upgrade/<?php echo $sub?>">Обновить</a></span>
<span class="topmenu"><a href="/add/cabtargz/<?php echo $sub?>">Развернуть на сервер</a></span>
<span class="topmenu"><a href="/lk/settings/<?php echo $sub?>">Общие настройки</a></span>

<div> 
<br>

<hr class="hr_title">
<br>
<span id="temp" class="temp" ondblclick="$('#temp').hide();"><?php echo $this->input->cookie('STAT', TRUE); $this->input->set_cookie('STAT', '', '-3600'); ?></span>
<br>

<?php if(@$failed):?>
<span class="title">Ошибочные попытки входа</span>
<br>
<br>	

	<table>
		<tr>
			<td>
				Адрес
			</td>
			<td>
				Попытки
			</td>
			<td>
				Дата
			</td>
			<td>
				Браузер
			</td>
			<td>
				Удаление
			</td>
		</tr>
	 <?php

	 foreach ($failed as $one):
		?>
		<tr id="bid<?php echo $one['id'];?>">
			<td >
				<?php echo $one['ip'];?>
			</td>
			<td>
				<?php echo $one['try'];?>
			</td>
			<td>
				<?php echo $one['datetime'];?>
			</td>
			<td>
				<?php echo $one['useragent'];?>
			</td>
			<td onclick="delbad('<?php echo $sub?>','<?php echo $one['id']?>')">
				<i class="fa fa-trash-o  hand"></i>
			</td>
		</tr>
	<?php endforeach;?>
	</table>
<?php endif;?>
</div>