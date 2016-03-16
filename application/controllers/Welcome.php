<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	
	public function index()
	{ 
		require('/var/www/html/deploy/application/views/auth.php');
		//$mes=$this->input->cookie('mes', TRUE); 
		//if($mes){
			//echo '<h2 >'.$mes.'</h2>';
		//$this->input->set_cookie('mes', '', '-3600'); 
		//}
		if(isset($_GET['p'])){
			$data['pass']=base64_encode(str_rot13($_GET['p']));
		}else{
			$data['pass']='?p=123456';
		}
		$this->load->view('templates/header');  
		$this->load->view('welcome_message',$data);
		$this->load->view('templates/footer');
	}
}
