	
	<?php //echo sha1('superadmin'); ?>
	<div id="body">
		Something, your address is 
	<?php print_r($_SERVER['REMOTE_ADDR']) ?>	
	<h2><?php echo $pass; ?></h2>
	<?php
	/*
	$p='SymFylYg5';
	echo '<p>orig='.$p.'</p>';
	echo '<p>tuda</p>';
	$r13=str_rot13($p);
	echo '<p>rot13='.$r13.'</p>';;
	$b64=base64_encode($r13);
	echo '<p>b64='.$b64.'</p>'; ;
	echo '<p>ottuda</p>';
	$b64b=base64_decode($b64);
	echo '<p>b64b='.$b64b.'</p>'; ;
	$r13b=str_rot13($b64b);
	echo '<p>rot13b='.$r13b.'</p>';;
	if($p==$r13b){
		echo 'совпадают';
	}else{
		echo 'не совпадают';
		
	}
	*/
	?>
	</div>
