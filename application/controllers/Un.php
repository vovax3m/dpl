<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Un extends CI_Controller {
	public function __construct()	{
			parent::__construct();
			// подключаем помощники, библиотеки, модели
			$this->load->helper('url');
			$this->load->helper('cookie');
			$this->load->model('un_model');
			
     	}
		public function getlist($json=false){
			/*
			get list if all subscribers
			*/
			$dbhandle = mssql_connect($this->config->item('mssql_host'), $this->config->item('mssql_username'), $this->config->item('mssql_password'));
			$selected = mssql_select_db($this->config->item('mssql_database'), $dbhandle);
			// получаем исходящие звонки клиента
			$list= mssql_query("SELECT subscriber.Name as name,subscriber.ID
						  FROM Subscriber
						  ORDER BY name ");
			while($row = mssql_fetch_assoc($list)){
				$res[$row['ID']]=$row['name'];
			}
			//print_r($res);
			if($json){
					foreach($res as $k=>$v){
						$res[$k]=iconv("windows-1251","UTF-8",$v); 
					}
					echo json_encode($res);
			}else{
				return $res;
			}
		}
		public function fillaccid($id){ 
		/* called by ajax
		*/
			$user=$this->un_model->get_auth($id);
			$list=$this->getlist();
			$one=explode(',',$user[0]['accid']);
			$names='';
			foreach($one as $aid){
				$names.="<option value='{$aid}'>{$list[$aid]}</option>";
			};
			echo iconv("windows-1251","UTF-8",$names);
			
		}
		public function index(){ 
	
		// get names from bill
		$data['list']=$this->getlist();
		//get auser from localdb
		$data['auth']=$this->un_model->get_auth();
		//get  all passw
		$pass=$this->un_model->get_pass();
		foreach($pass as $one){
			$data['pass'][$one['auth_id']]=$one['pass'];
		}
		//render index page
		$this->load->view('templates/header');  
		$this->load->view('un',$data);
		$this->load->view('templates/footer');
	}
	
	public function useradd($edit){
		$authid  =$this->input->post('editid');
		$username=$this->input->post('username');
		$passwd  =$this->input->post('pass');
		$accid   =$this->input->post('accid'); 
		if(substr($accid, -1)==','){
			$accid=substr($accid,0, -1);
		}
		if($edit){
			$this->un_model->useredit($authid,$username,$passwd,$accid);
		}else{
			$this->un_model->useradd($username,$passwd,$accid);
		}
		$bu= base_url();
		header('Location: '.$bu.'/un/'); 
	}
	public function book($id,$accid){
		$dbhandle = mssql_connect($this->config->item('mssql_host'), $this->config->item('mssql_username'), $this->config->item('mssql_password'));
			$selected = mssql_select_db($this->config->item('mssql_database'), $dbhandle);
			// получаем исходящие звонки клиента
			$in='';
			$one=explode('_',$accid);
			foreach($one as $no){
				$in.="'{$no}',";
			};
			$in=substr($in,0, -1);
			
			$list= mssql_query("SELECT a.Number,b.Name
								FROM ACCOUNT a
								JOIN Subscriber b  ON a.SubscriberID=b.ID
								WHERE b.ID in ({$in}) AND a.AccType=1 ");
								
			while($row = mssql_fetch_assoc($list)){
				if(is_numeric($row['Number'])){
					$n=iconv("windows-1251","UTF-8",$row['Name']);
					if(strlen($row['Number'])==6){
						$no='78452'.$row['Number'];
					}else{
						$no=$row['Number'];
					}
					$res[$no]=$n;
				}
			}
			//print_r($res);
			$this->un_model->addtobook($id,$res);
			//return $res;
	}
	public function userdel($id){
		$this->un_model->userdel($id);
		echo 'пользователь  удален';
		
	}
	function CallEvent($accid='10393',$id='1'){
		
		/*
		получаем параметры для формирования запроса
		*/
		$res=array();
		// ид клиента в биллинге
		$accid=str_replace("_",",",$accid);
		//$accid='10393';
		// начальная дата
		$from=$_GET['startdate'].' 00:00:00';
		//$from='2016-01-08 00:00:00';
		// конечная дата
		$to=$_GET['enddate'].' 23:59:59';
		//$to='2016-01-08 23:59:59';
		#$name=$_GET['name'];
		// имя кабинета, базы
		$name='lk';
		
		// подключаемся к базке 	
		$dbhandle = mssql_connect($this->config->item('mssql_host'), $this->config->item('mssql_username'), $this->config->item('mssql_password'));
		$selected = mssql_select_db($this->config->item('mssql_database'), $dbhandle);
		// получаем исходящие звонки клиента
		$out= mssql_query("SELECT convert(varchar, SetupTime, 120) as SetupTime,Called,Calling,DisconnectCause,Duration FROM IPCallEvent WHERE OriginateAccID in (SELECT ID FROM Account WHERE SubscriberID IN ({$accid})) AND SetupTime between '{$from}' and '{$to}' ");
		//сохраняем в массив
		while($row = mssql_fetch_assoc($out)){
				$row['Direct']='out';
				$res[]=$row;
			//	print_r($row);
		}
		
		//получаем входящие звонки клиента
		$un='';
		$numlist=$this->getnums($accid,true);
		foreach($numlist[1] as $n){
			if(strlen($n)==11)$un.='"'.$n.'",';
		};
		$un=substr($un,0,-1);
		$inc= mssql_query("SELECT convert(varchar, SetupTime, 120) as SetupTime,Called,Calling,DisconnectCause,Duration FROM IPCallEvent WHERE TerminateAccID in (SELECT ID FROM Account WHERE SubscriberID IN ({$accid})) AND SetupTime between '{$from}' and '{$to}' ");
		if(mssql_rows_affected($dbhandle)==0){
			$inc= mssql_query("SELECT convert(varchar, SetupTime, 120) as SetupTime ,Called,Calling,DisconnectCause,Duration FROM IPCallEvent WHERE Called in ({$un}) AND SetupTime between '{$from}' and '{$to}' ");
		}
		
		//так же сохраняем в массив
		//echo mssql_rows_affected($dbhandle);
		//echo '_';
		while($row = mssql_fetch_assoc($inc)){
				$row['Direct']='inc';
				$res[]=$row;
				//print_r($row);
		}
		//закрываем подключение к базе
		mssql_close($dbhandle);
		// если в результирующем массиве есть записи, записываем в локальную базу
		if(count($res)>0){
			// сортируем массив по перовому значению (дата)
			asort($res);
			//echo 'kolvo= '.$c;
			
			$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', $name);
			$db->query("TRUNCATE callevent_{$id}");
			//echo $db->error;
			$q="INSERT INTO callevent_{$id} (id,CallDate,Called,Calling,DisconnectCause,Duration,Direct) VALUES ";
			foreach($res as $call){
				$q.="('','{$call['SetupTime']}','{$call['Called']}','{$call['Calling']}','{$call['DisconnectCause']}','{$call['Duration']}','{$call['Direct']}'),";
			}
			$q=substr($q,0,-1);
			//echo $q;
			$db->query($q);
			//echo $db->error;
			echo $db->affected_rows;
		}
		else{
			echo 'no';
		}
		
		
	}
	
		function getnums($accid='10080',$return=false){
			$accid =str_replace("_",",",$accid);
			// подключаемся к базке 	
			$dbhandle = mssql_connect($this->config->item('mssql_host'), $this->config->item('mssql_username'), $this->config->item('mssql_password'));
			$selected = mssql_select_db($this->config->item('mssql_database'), $dbhandle);
			$out= mssql_query("SELECT Number,AccType FROM Account WHERE SubscriberID IN ({$accid}) AND (AccType=1 or  AccType=12) ");
			$list='';
			while($row = mssql_fetch_assoc($out)){ 
			//	print_r($row);
				if($row['AccType']=='1'){ 
					if(is_numeric($row['Number'])){
						if(strlen($row['Number'])==6){
							$res[1][]='78452'.$row['Number'];
							//$list.='78452'.$row['Number'].',';
						}else{
							$res[1][]=$row['Number'];	
							//$list.=$row['Number'].',';
						}
				}
					
				}else{
					$res[12]='true';
				}
				//$res[]=$row;
			}if($return){
				return $res;
			}else{
				echo json_encode($res,TRUE);
			}
		}
		
		function getsaldo($accid='10080'){
			//$accid =str_replace("_",",",$accid);
			// подключаемся к базке 	
			$dbhandle = mssql_connect($this->config->item('mssql_host'), $this->config->item('mssql_username'), $this->config->item('mssql_password'));
			$selected = mssql_select_db($this->config->item('mssql_database'), $dbhandle);
			$ids=explode("_",$accid);
			foreach($ids as $id){
				//echo "SELECT TOP 1 s.Name, a.Saldo FROM Account a  JOIN Subscriber s ON a.SubscriberID=s.ID WHERE s.ID = '{$id}'  ";
				#$out= mssql_query("SELECT TOP 1 s.Name, a.Saldo FROM Account a  JOIN Subscriber s ON a.SubscriberID=s.ID WHERE s.ID = '{$id}' AND ISNUMERIC(a.Number)=1 ");
				$out= mssql_query("SELECT TOP 1 s.Name, a.Saldo FROM Account a  JOIN Subscriber s ON a.SubscriberID=s.ID WHERE s.ID = '{$id}' AND a.AccType in (1,12) ");
				$row = mssql_fetch_assoc($out);
				//var_dump($row);
				
				$saldo[]=array( iconv("CP1251", "UTF-8",$row['Name']),round($row['Saldo']),$id);
				
				//$saldo[$id]['saldo']=;
			}
			echo json_encode($saldo,TRUE);
			/*
			$out= mssql_query("SELECT DISTINCT s.Name, a.Saldo FROM Account a  JOIN Subscriber s ON a.SubscriberID=s.ID WHERE a.SubscriberID IN ({$accid}) AND ISNUMERIC(a.Number)=1 ");
			while($row = mssql_fetch_assoc($out)){
				
				$saldo[]= round($row['Saldo'],2);
			}
			echo json_encode($saldo,TRUE);
			*/
		}
}