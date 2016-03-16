<!doctype html>
<html>
<head>
	<title>Личный кабинет</title>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="<?php echo $this->config->item('base_url');?>/static/style/style.css" type="text/css"/> 
	<link href='http://fonts.googleapis.com/css?family=Play&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
</head>
<body>
	<div id="pagewidth">
			<div id="header">
				<div class="lefthead">
					<img src="http://dialog64.ru/wp-content/themes/customizr/inc/img/dialog_logo2.png" class="logo">
				</div>
		</div>
		<br>
		<br>
		<div id="wrapper" class="clearfix" align="center">
			<span class="title">Вход в личный кабинет</span><br>
			<?php if($trynomer){ ?>
				<span class="temp">Осталось <?php $ost=10 - $trynomer; echo $ost;?> попыток входа</span><br>
			<?php } ?>   
			<span id="temp" class="temp" ondblclick="$('#temp').hide();"><?php echo $this->input->cookie('STAT', TRUE); ?></span>  
			<form action="/auth/enter" method="POST"> 
				<p><label>Имя пользователя или вн.номер</label><br>
				<input type="text" name="username" class="loginform"></p>
				<p><label>Пароль</label><br>
				<input type="password" name="passwd" class="loginform"></p>
				<p>
				<input type="submit" name="submit" value="Войти" class="loginform"></p> 
			<form>
			
		
		 </div><!---wrapper-->

         
		<div id="footer">
			<span>740740</span><br>
			<span>{elapsed_time} сек.</span>
		</div>

	</div><!---pagewidth-->
</body>

</html>
