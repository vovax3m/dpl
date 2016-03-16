<style>
.add, .halfadd{
 width: 400px;
  padding: 8px 4px 8px 10px;
  margin:  8px 4px 8px 10px;
    margin-bottom: 15px;

    /* Styles */
    border: 1px solid #4e3043; /* Fallback */
    border: 1px solid rgba(78,48,67, 0.8);
    background: rgba(255,255,255,0.5);
    border-radius: 2px;
    box-shadow: 
        0 1px 0 rgba(255,255,255,0.2), 
        inset 0 1px 1px rgba(0,0,0,0.1);
    -webkit-transition: all 0.3s ease-out;
    -moz-transition: all 0.3s ease-out;
    -ms-transition: all 0.3s ease-out;
    -o-transition: all 0.3s ease-out;
    transition: all 0.3s ease-out;

    /* Font styles
	font-family: 'Raleway', 'Lato', Arial, sans-serif;	*/
    font-family: 'Raleway', 'Lato', Arial, sans-serif;
    color: black;
    font-size: 16px;
}

</style>
<?php
 $grant= $this->input->cookie('grant', TRUE); 
 $this->input->set_cookie('STAT', '', '-3600'); 
if(isset($_POST['pass']) && !$grant){
		if($_POST['pass']=='admin2015'){
		
		$this->input->set_cookie('grant', 'accessgrant', '+3600'); 
		
		//$this->input->set_cookie('mes', 'Велкоме', '+3600'); 
		Header("Location: index.php");
		} else{ 
			echo "<h2>Не угадал</h2>";
		}
	}
	if(!$grant=='accessgrant'){
		echo ' <p><h2>Введите пароль</h2><br><form action="index.php" method="post"><input  type="password" name="pass" class="add" required><br><input type="submit" value="Вход" class="add"></b> ';
		exit;
	}
	?>