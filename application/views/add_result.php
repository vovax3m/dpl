<?php 
if(@$mess['OK']){
?>
<span class='title'>Успешно выполнено</span><br>
<?php
	foreach($mess['OK']as $one){
		echo '<p>'.$one.'</p>';
	}
}
if(@$mess['ERROR']){
?>
<span class='title'>Ошибки</span>
<?php

	foreach($mess['ERROR']as $one){
		echo '<p>'.$one.'</p>';
	}
}
?>