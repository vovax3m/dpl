<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends CI_Controller {

	
	public function index(){ 
		
		
		$this->load->view('templates/header');  
		$this->load->view('add');
		$this->load->view('templates/footer');
	}
	public function handler()	{ 
		$this->load->model('add_model');
		
		//$_POST['ip_3oct'];
		//$_POST['ip_4oct'];
		$klient_name=$_POST['nazv'];
		$domen=$_POST['domen'];
		$name=$db_db=$db_login=$subdom=$_POST['subdomen'];
		$dbpass=$db_pass=$subdom.'gfhjkm';
		$un=$_POST['login'];
		$uext=$_POST['exten'];
		$pass=sha1($_POST['pass']);
		$cdr_pagin=$_POST['pagin'];
		$vats_ver=$_POST['ver'];
		$ver=$this->add_model->get_ver();
		$type=$_POST['type'];
		if($_POST['ip_3oct']=='a'){
			$frst3oct='91.196.5.';
		}elseif($_POST['ip_3oct']=='b'){
			$frst3oct='159.253.121.';
		}
		$ip=$frst3oct.$_POST['ip_4oct'];
		//создаем папку в которую поместим ЛК
		$newdir=mkdir("/var/www/html/".$subdom, 0755);  
		if($newdir){
		
			 $mess['OK'][]='папка создана';
				/*
					копируем в папку кабинет, из референсной копии
				*/
				exec("cp -r /var/www/html/ref/cab/* /var/www/html/".$subdom);
				exec("cp -T /var/www/html/ref/cab/.htaccess /var/www/html/".$subdom.'/.htaccess');
				if(file_exists("/var/www/html/".$subdom.'/index.php') and  file_exists("/var/www/html/".$subdom.'/application/config/autoload.php')){
				
					$mess['OK'][]='кабинет скопирован';
					/*
					 собираем файл config.php 
					 берем шаблон из референсной папки и подменяем на реальные значения
					 кладем в рабочую папку создаваемого ЛК, проверям что файл существует
					*/
					$f=file_get_contents('/var/www/html/ref/confs/config.php');  
					$f=str_replace("#!vats#", "'$ip'", $f);
					$f=str_replace("#!klient_name#", " '$klient_name'", $f);
					$f=str_replace("#!base_url#", " 'http://$subdom.sip64.ru'", $f);
					$f=str_replace("#!cdr_pagin#", "$cdr_pagin", $f); 
					$new_f=str_replace(" #!vats_ver#", "'$vats_ver'", $f);
					$w=fopen('/var/www/html/'.$subdom.'/application/config/config.php','a'); 
					fwrite($w,$new_f);
					fclose($w);
					if(file_exists('/var/www/html/'.$subdom.'/application/config/config.php')){
					
						$mess['OK'][]='файл config.php сформирован';
						/*
						 собираем файл database.php 
						 берем шаблон из референсной папки и подменяем на реальные значения
						 кладем в рабочую папку создаваемого ЛК, проверям что файл существует
						*/
						$f=file_get_contents('/var/www/html/ref/confs/database.php');  
						$f=str_replace("#!db_username#", "'$db_login'", $f);
						$f=str_replace("#!db_password#", " '$db_pass'", $f);
						$new_f=str_replace("#!db_db#", " '$db_db'", $f);
						$w=fopen('/var/www/html/'.$subdom.'/application/config/database.php','a'); 
						fwrite($w,$new_f);
						fclose($w);
						if(file_exists('/var/www/html/'.$subdom.'/application/config/database.php')){ 
						
							$mess['OK'][]='файл database.php сформирован';
							/*
							 собираем файл конфигурации виртуального хоста
							 берем шаблон из референсной папки и подменяем на реальные значения
							 кладем в  папку vhosts.d, проверям что файл существует
							*/
							$f=file_get_contents('/var/www/html/ref/vhost/blank.conf');  
							if($f){
								$new_f=str_replace("#!subdom#", "$subdom", $f);
							}
							$fname='/var/www/html/ref/'.$subdom.'.conf'; 
							$w=fopen($fname,'a'); 
							if($w){
								fwrite($w,$new_f); 
								fclose($w);
								exec(' cp  /var/www/html/ref/'.$subdom.'.conf /etc/httpd/vhost.d/'.$subdom.'.conf' );
							}
								if(file_exists('/etc/httpd/vhost.d/'.$subdom.'.conf')){ 
									unlink('/var/www/html/ref/'.$subdom.'.conf');
									
									$mess['OK'][]='файл vhost сформирован';
									
								}else{
									$mess['ERROR'][]='Не удалось  сформировать vhost ';
								}
							
							}else{
							$mess['ERROR'][]='Не удалось  сформировать database.php ';
							}
					
						}else{
							$mess['ERROR'][]='Не удалось  сформировать config.php ';
						}
					
					}else{
						$mess['ERROR'][]='Не удалось скопировать кабинет';
					}
				
			}else{
					$mess['ERROR'][]='Не удалось создать папку';
			}
			
			// new method
			$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', 'deploy');
			if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
			// создаем пользоватеоя
			if(!$result = $db->query("CREATE USER  '$subdom'@'localhost' IDENTIFIED BY '$dbpass';" )) die(  $db->error);
			// создаем базу
			if(!$result = $db->query("CREATE DATABASE IF NOT EXISTS `$subdom`;"))die(  $db->error);
			// даем права
			if(!$result = $db->query("GRANT ALL PRIVILEGES ON `$subdom`.* TO '$subdom'@'localhost';"))die(  $db->error);
			$db->close();
			// переподключаемся к базе
			$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', $subdom);
			$sql= file_get_contents('/var/www/html/ref/sql/add.sql');
			// заливаем конфиг
			if($db->multi_query($sql)){
				do { 
					$db->use_result(); 
				}while( $db->more_results() && $db->next_result() );
				
				echo 'ok';
			}
			// заполняем пользователя
			if(!$result = $db->query("INSERT INTO `auth` (`id`, `username`, `passwd`, `exten`, `attempt`, `ip`, `state`, `sessid`,  			`is_admin`) VALUES (1, '$un', '$pass', '$uext', 0, '', '', '', 1);")) die(  $db->error);
			// заполняем тип кабинета
			if(!$result = $db->query("UPDATE settings  SET s_value='$type' WHERE s_key='type' ;")) die(  $db->error); 			
			$result = $db->query("SELECT id  FROM`$subdom`.`auth`WHERE id=1 ;");
			$db=$this->add_model->add($subdom,$ip,$klient_name,$type,$ver);
			$check=mysqli_fetch_assoc($result);
			if($check['id']==1){
				$mess['OK'][]='База данных создана';
				$mess['OK'][]= 'Перезапустите httpd сервис, или попробуйте зайти на '.$domen.' через 5 минут';
				$mess['OK'][]='Учетные данные:';
				$mess['OK'][]='Логин:'.$un;
				$mess['OK'][]='Вн. Номер:'.$uext;
				$mess['OK'][]='Пароль: '.$_POST['pass'];
				$acc_data=array('sub'=>$subdom,'login'=>$un,'exten'=>$uext,'pass'=>base64_encode(str_rot13($_POST['pass'])));
				$db=$this->add_model->add_acc($acc_data);
				/* try{
					$f=fopen('/var/www/html/deploy/cron/flag','w');
					fwrite($f,'true');
					fclose($f);
				}catch (Exception $e) {
					$mess['ERROR'][]='Не удалось запланировать перезагрузку сервера';
				} */
				
			}else{
				$mess['ERROR'][]='Не удалось создать базу';
			}
			/*
			 getting server side part of LK
			*/
			$user_agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ip);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
			$page = curl_exec($ch);
			if(strlen($page)>0){
				$mess['OK'][]= 'доступ  к серверу '.$ip.' есть, копируем файлы';
				$con = ssh2_connect($ip, '22');
				if(!$con) exit("не могу подключиться "); 
				if(!ssh2_auth_password($con, 'root', 'nhfypbcnjh315')) 	exit("логин/пароль некоректен");  
				shell_exec('cd /var/www/html/ref/serverside && rm -f ../cab.tar.gz && tar zcf ../cab.tar.gz ./');
				$line=ssh2_exec($con, 'mkdir -p /var/www/html/cabinet && cd /var/www/html/cabinet && wget http://ref.sip64.ru/cab.tar.gz && tar -xf cab.tar.gz && rm -f cab.tar.gz; ls /var/www/html/cabinet/| grep cdr.php'); 
				stream_set_blocking($line, true); 
				$peers= nl2br(stream_get_contents($line));
				if(strstr($peers,'cdr.php')){
				$mess['OK'][]= 'Работы по развертыванию завершены, перезапустите httpd сервис, или попробуйте зайти на '.$domen.' через 5 минут';
				}
			}else{
				$mess['ERROR'][]= 'Серверная часть кабинета не скопирована. Так как доступа к серверу '.$ip.' нет. Зайдите консолью и пропишите в firewall следующие строки <pre>iptables -t filter -A OUTPUT -p all -d 91.196.6.51/32 -j ACCEPT
iptables -t filter -A INPUT  -p all -s 91.196.6.51/32 -j ACCEPT</pre>';
				$mess['ERROR'][]='Для продолжения развертывания, нажмите <a  class="minititle" href="/add/cabtargz/'.$_POST['ip_3oct'].'/'.$_POST['ip_4oct'].'">тут</a>, либо возьмите архив http://ref.sip64.ru/cab.tar.gz и разверните его в /var/www/html/cabinet/ на '.$ip; 
			}
			$data['mess']=$mess; 
		//	print_r($line);
		$this->load->view('templates/header');  
		$this->load->view('add_result',$data);
		$this->load->view('templates/footer');
		
	}
	public function cabtargz($oct3=false,$oct4=false) {
			/*
			 getting server side part of LK
			*/
			if($oct3=='a'){
			$frst3oct='91.196.5.';
			$ip=$frst3oct.$oct4;
		}elseif($oct3=='b'){
			$frst3oct='159.253.121.';
			
		}else{
			// считаем что в 1 параметре поличили sub
			if(strstr($oct3,'a')){
				$ip= '91.196.5.'.substr($oct3,1);
				$frst3oct='91.196.5.';
				$oct4=substr($oct3,1);
				$oct3='a';
			}elseif(strstr($oct3,'b')){
				$ip= '159.253.121.'.substr($oct3,1);
				$frst3oct='159.253.121.';
				$oct4=substr($oct3,1);
				$oct3='b';
			}
		}
			$ip=$frst3oct.$oct4;
			$mess['OK']='';
			$mess['ERROR']='';
			$user_agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $ip);
			curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
			$page = curl_exec($ch);
			
			if(strlen($page)>0){
				$mess['OK'][]= 'доступ  к серверу '.$ip.' есть, копируем файлы';
				$con = ssh2_connect($ip, '22'); 
				if(!$con) exit("не могу подключиться "); 
				if(!ssh2_auth_password($con, 'root', 'nhfypbcnjh315')) 	exit("логин/пароль некоректен"); 
				shell_exec('cd /var/www/html/ref/serverside && rm -f ../cab.tar.gz && tar zcf ../cab.tar.gz ./');
				$line=ssh2_exec($con, 'mkdir -p /var/www/html/cabinet && cd /var/www/html/cabinet && wget http://ref.sip64.ru/cab.tar.gz && tar -xf cab.tar.gz && rm -f cab.tar.gz; ls /var/www/html/cabinet/| grep cdr.php'); 
				stream_set_blocking($line, true); 
				 $peers= nl2br(stream_get_contents($line)); 
				if(strstr($peers,'cdr.php')){
					$mess['OK'][]= 'Работы по развертыванию завершены, перезапустите httpd сервис, или попробуйте зайти на http://'.$oct3.$oct4.'.sip64.ru через 5 минут';
				}else{
					$mess['ERROR'][]='что-то пошло не так, доступ к серверу есть, файлы не распоковались или лежат не  на своем месте';
				}
			}else{
				$mess['ERROR'][]= 'Серверная часть кабинета не скопирована. Так как доступа к серверу '.$ip.' нет. Зайдите консолью и пропишите в firewall следующие строки <pre>iptables -t filter -A OUTPUT -p all -d 91.196.6.51/32 -j ACCEPT
iptables -t filter -A INPUT  -p all -s 91.196.6.51/32 -j ACCEPT</pre>';
				$mess['ERROR'][]='Для продолжения развертывания, нажмите <a  class="minititle" href="/add/cabtargz/'.$oct3.'/'.$oct4.'">тут</a>, либо возьмите архив http://ref.sip64.ru/cab.tar.gz и разверните его в /var/www/html/cabinet/ на '.$ip; 
			}
			$data['mess']=$mess; 
		//	print_r($line);
		$this->load->view('templates/header');  
		$this->load->view('add_result',$data);
		$this->load->view('templates/footer');
		
	}
	
	
	public function pass($pass){
		echo $pass." =  ".sha1($pass);
		echo '<br>';
		
	}

	public function aprest(){
		$ip='159.253.121.75';
		//echo $exec= exec('/var/www/html/deploy/scripts/restart' );
//		$con = ssh2_connect($ip, '22');
//			if(!$con) exit("не могу подключиться к ".$ip."по порту  22"); 
//			if(!ssh2_auth_password($con, 'root', 'nhfypbcnjh315')) 	exit("логин/пароль некоректен"); 
//				$line=ssh2_exec($con, 'ls /');  
			//$line=ssh2_exec($con, 'cd /home; wget http://ref.sip64.ru/cab.tar.gz'); 
//			print_r($line);
		//print_r($mess);
		$user_agent = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0)';
					  $ch = curl_init();
					  curl_setopt($ch, CURLOPT_URL, 'https://'.$ip.'/libs/js/jquery/jquery-1.8.3.min.js');
					  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
					//  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					  curl_setopt($ch, CURLOPT_TIMEOUT, 3);
					  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					  curl_setopt($ch, CURLOPT_SSLVERSION, 1);
					  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					//  echo exec('ping 159.253.121.75 -c 4 ');
					  $page = curl_exec($ch);
					  if(strlen($page)>0){
						echo 'доступ есть';
					//	$con = ssh2_connect($ip, '22');
					//	if(!$con) exit("не могу подключиться "); 
					//	if(!ssh2_auth_password($con, 'root', 'nhfypbcnjh315')) 	exit("логин/пароль некоректен"); 
					//	$line=ssh2_exec($con, 'cd /home; wget http://ref.sip64.ru/cab.tar.gz;ls /home | grep cab.tar.gz; cd /; tar -zxf /home/cab.tar.gz;'); 
					//	stream_set_blocking($line, true); 
					//	$peers= nl2br(stream_get_contents($line));
					//	print_r($peers);
					  }else{
						echo 'доступа нет';
					  }
		
	}
	public function copydb($subdom){
		//it's works
		// add database from reference dump
		$this->load->model('add_model');
		$dbpass='123';
		$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', 'deploy');
		if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
		if(!$result = $db->query("CREATE USER  '$subdom'@'localhost' IDENTIFIED BY '$dbpass';" )) die(  $db->error);
		if(!$result = $db->query("CREATE DATABASE IF NOT EXISTS `$subdom`;"))die(  $db->error);
		if(!$result = $db->query("GRANT ALL PRIVILEGES ON `$subdom`.* TO '$subdom'@'localhost';"))die(  $db->error);
		$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', $subdom);
		$sql= file_get_contents('/var/www/html/ref/sql/add.sql');
		if($result=$db->multi_query($sql)){
			echo 'ok';
		}
		/*
		$result = $db->query("SHOW TABLES;");
		while($r=$result->fetch_assoc()){
			 $rr[]=$r;
		}
		foreach($rr as $k => $v){
			foreach($v as $k => $v){
				echo $v.' ';
			}
		};
		//echo $result;
		*/
		
	}
}

