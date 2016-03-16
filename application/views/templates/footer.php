		
		
	</div> <!---twocols-->
	<div id="leftcol">  
		<?php
		$ci =&get_instance();
		$ci->load->model('deploy_model');
		$lklist=$ci->deploy_model->get_lk();
		foreach($lklist as $lk):	?>
			<span class="menuitem"><a href="/lk/?sub=<?php echo $lk['sub']?>"><?php echo $lk['sub']?> : <?php echo $lk['name']?></a></span><hr> 	
		<?php	endforeach;	?>
	</div>


</body>

</html>
