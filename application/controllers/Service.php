<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends CI_Controller {
	/*
	 Контроллер для выполения общих задач на всех кабинетах
	 через  $url='http://deploy.sip64.ru/service/getsaldo?no='.$f;
			$res=file_get_contents($url);
	 или curl
	*/
	public function index()
	{ 
	
	}
	public function maillog()
	{ 	
		if($_POST['mess']){
			$this->load->model('add_model');
			return $this->add_model->maillog($_POST);
		}
	}
	function getsaldo(){
		// номер по которому ищем остаток
		$f=$_GET['no']; 
		if($f){
			$res='';
			$dbhandle = mssql_connect($this->config->item('mssql_host'), $this->config->item('mssql_username'), $this->config->item('mssql_password'));
			$selected = mssql_select_db($this->config->item('mssql_database'), $dbhandle);
			$result = mssql_query('SELECT Saldo FROM Account WHERE (NUMBER="'.$f.'")');
			while($row = mssql_fetch_assoc($result)){
				$res.=$row['Saldo']; 
			}
			mssql_close($dbhandle);
			echo $res;
			return $res;	
		}
		else{
			return '999';	
		}	
	}
}