<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lk extends CI_Controller {
		public function __construct()	{
			parent::__construct();
			// подключаем помощники, библиотеки, модели
			$this->load->helper('url');
			$this->load->helper('cookie');
			$this->load->model('deploy_model');
			
     	}

	public function index()
	{ 
		/*
		check remote folders to vats
		*/
		
		// проверка пароля и куки
		
  
		$sub=$data['sub']=$_GET['sub'];
		
		$data['version']=$this->deploy_model->get_ver($sub);
		$data['type']=$this->deploy_model->get_type($sub);
		$db_host=$this->config->item('deploy_db_host');
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		//$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', $sub);
		if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
		if($result = $db->query("SELECT *  FROM auth_failed ORDER BY id; " )) {
			while ($row = $result->fetch_assoc()) {
				$data['failed'][]=$row;
			}
		}	
		
		$this->load->view('templates/header');  
		$this->load->view('lk',$data);
		$this->load->view('templates/footer');
	}
	public function open($sub)
	{ 
		$data['sub']=$sub;
		setcookie('su_deploy', '889a3a791b3875cfae413574b53da4bb8a90d53e', time()+86400,'/',"sip64.ru"); 
		setcookie('auth_username', 'deploy', time()+86400,'/',"sip64.ru"); 
		header("Location: http://$sub.sip64.ru");
		
	}
	public function settings($sub)
	{ 
		$data['type']=$this->deploy_model->get_type($sub);
		$data['ver']=$this->deploy_model->get_ver($sub);
		$data['sub']= $sub;
		$this->load->view('templates/header');  
		$this->load->view('settings',$data);
		$this->load->view('templates/footer');
	}
	public function confdel($sub)	{ 
		$data['sub']=$sub;
		$this->load->view('templates/header');  
		$this->load->view('confirm_delete',$data);
		$this->load->view('templates/footer');
	}
	public function del($sub)	{ 
	/*
		backuping settiongs and DB dump 
	*/
	$path=$_SERVER['DOCUMENT_ROOT'];
	$mess['OK'][]='Сохраняем дамп базы '.$sub;
	$date=date('Y-m-d');
	$res= exec($path.'/bash/get_dump '.$sub );
	if(strstr($res,$sub)){
		$mess['OK'][]='сформирован файл '.$res;
	}else{
		$mess['ERROR'][]='дамп не сохранен';
	}
	$mess['OK'][]='сохраняем конфиги кабинета';
	 exec($path.'/bash/get_confs '.$sub );
		
	if(file_exists('/dumps/'.$date.'/'.$sub.'/config.php')){
		$mess['OK'][]='файл  config.php сохранен';
	}else{
		$mess['ERROR'][]='config.php не сохранен';
	}
	
	if(file_exists('/dumps/'.$date.'/'.$sub.'/database.php')){
		$mess['OK'][]='файл  database.php сохранен';
	}else{
		$mess['ERROR'][]='database.php не сохранен';
	}
	/*
	DROP base and user
	*/
	$this->load->model('add_model');
	$db=$this->add_model->del_acc($sub);
	$db_host=$this->config->item('deploy_db_host');
	$db_user=$this->config->item('deploy_db_user');
	$db_pass=$this->config->item('deploy_db_pass');
	$db_db=$this->config->item('deploy_db_db');
	$db = new mysqli($db_host, $db_user, $db_pass , $db_db);
	
	
	if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
	if($result = $db->query("DROP user  '$sub'@'localhost';" )) {
		$mess['OK'][]='Пользователь базы удален';
	}else{
		$mess['ERROR'][]= ' Ошибка удаления пользователя базы '.$db->error;
	}
	if($result = $db->query("DROP database  $sub;" )){
		$mess['OK'][]='База удалена';
	}else{
		$mess['ERROR'][]= ' Ошибка удаления базы '.$db->error;
	}
	exec($path.'/bash/del_lk '.$sub );
	if (!file_exists('/var/www/html/'.$sub.'/index.php')){
		$mess['OK'][]='Папка удалена';
	}else{
		$mess['ERROR'][]= ' Ошибка удаления папки '.$db->error;
	}
	exec($path.'/bash/del_vhost '.$sub );
	if (!file_exists('/etc/httpd/vhost.d/'.$sub.'.conf')){
		$mess['OK'][]='vhost удален';
	}else{
		$mess['ERROR'][]= ' Ошибка удаления vhost '.$db->error;
	}
		/* try{
					$f=fopen('/var/www/html/deploy/cron/flag','w');
					fwrite($f,'true');
					fclose($f);
				}catch (Exception $e) {
					$mess['ERROR'][]='Не удалось запланировать перезагрузку сервера';
				} */
	$ip=$this->add_model->get_ip($sub);			
	$con = ssh2_connect($ip, '22');
				if(!$con) exit("не могу подключиться "); 
				if(!ssh2_auth_password($con, 'root', 'nhfypbcnjh315')) 	exit("логин/пароль некоректен"); 
				$line=ssh2_exec($con, 'rm -f /home/cab.tar.gz;rm -fr /var/www/html/cabinet'); 			
	/*
	del from LK list and menu
	*/
	$this->add_model->del($sub);
	
	$data['mess']=$mess; 
	
	$this->load->view('templates/header');  
	$this->load->view('add_result',$data);
	$this->load->view('templates/footer');
	}
	public function upgrade($sub)	{ 
	/*
		backuping settings 
	*/
		$subdom=$sub;
		$path=$_SERVER['DOCUMENT_ROOT'];
		
		$mess['OK'][]='сохраняем конфиги кабинета';
		 exec($path.'/bash/get_confs '.$sub );
			
		if(file_exists('/dumps/'.$sub.'/config.php')){
			$mess['OK'][]='файл  config.php сохранен';
		}else{
			$mess['ERROR'][]='config.php не сохранен';
		}
		
		if(file_exists('/dumps/'.$sub.'/database.php')){
			$mess['OK'][]='файл  database.php сохранен';
		}else{
			$mess['ERROR'][]='database.php не сохранен';
		}
		/*
			ALTER DB, CREATE NEW  tables and fields
		*/
		/*
		$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', 'deploy');
		if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
		if(!$result = $db->query("
				CREATE TABLE IF NOT EXISTS `$subdom`.`book` (
					`id` int(11) NOT NULL,
					`nomer` varchar(64) NOT NULL,
					`name` text NOT NULL,
					`type` varchar(32) NOT NULL
				) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;")) die(  $db->error); 
		if(!$result = $db->query("
				INSERT INTO `$subdom`.`book` (`id`, `nomer`, `name`, `type`) VALUES
					(1, '78452740740', 'Dialog', 'saratov' ),
					(2, '78453999740', 'Dialog', 'engels' ),
					(3, '78453470340', 'Dialog', 'balakovo' ),
					(4, '74996890191', 'Dialog', 'moskow' ),
					(5, '78412397740', 'Dialog', 'penza' ),
					(6, '78422700425', 'Dialog', 'ulyanovsk' ),
					(7, '78442515308', 'Dialog', 'volgograd' )
					
					;")) die(  $db->error); 
		if(!$result = $db->query("ALTER TABLE `$subdom`.`book`
						ADD PRIMARY KEY (`id`),
						ADD UNIQUE KEY `nomer` (`nomer`),
						ADD KEY `id` (`id`);")) die(  $db->error); 
					if(!$result = $db->query("ALTER TABLE `$subdom`.`book`
					MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;")) die(  $db->error); 
		*/	
			// new method
			/*
			$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', 'deploy');
			if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
			// создаем пользоватеоя
			if(!$result = $db->query("CREATE USER  '$subdom'@'localhost' IDENTIFIED BY '$dbpass';" )) die(  $db->error);
			// создаем базу
			if(!$result = $db->query("CREATE DATABASE IF NOT EXISTS `$subdom`;"))die(  $db->error);
			// даем права
			if(!$result = $db->query("GRANT ALL PRIVILEGES ON `$subdom`.* TO '$subdom'@'localhost';"))die(  $db->error);
			$db->close();
			*/
			// переподключаемся к базе
			 $db = new mysqli('localhost', 'deploy', 'deploygfhjkm', $sub);
			 $sql= file_get_contents('/var/www/html/ref/sql/upgrade.sql');
			// заливаем конфиг
			if($db->multi_query($sql)){
				do { 
					$db->use_result(); 
				}while( $db->more_results() && $db->next_result() );
				
				echo 'ok';
				$db->close();
			}		
		//some queries
		$mess['OK'][]='Обновляем структуру базы базы';
		
		/////////////////
		
		//$oldversion=file_get_contents('/var/www/html/'.$sub.'/version');
		//$mess['OK'][]='текущая версия кабинета '.$oldversion;
		//$newversion=file_get_contents('/var/www/html/ref/cab/version');
		//$mess['OK'][]='последняя доступная версия кабинета '.$newversion;
		//if($oldversion==$newversion) $mess['OK'][]='Версии кабинетов одинаковые ';
		exec($path.'/bash/del_lk '.$sub );
		if (!file_exists('/var/www/html/'.$sub.'/index.php')){
			$mess['OK'][]='Папка удалена';
		}else{
			$mess['ERROR'][]= ' Ошибка удаления папки '.$db->error;
		}
		$newdir=mkdir("/var/www/html/".$sub, 0755);  
		if($newdir){
			$mess['OK'][]='папка создана';
			/*
			копируем в папку кабинет, из референсной копии
			*/
			exec("cp -r /var/www/html/ref/cab/* /var/www/html/".$sub);
			exec("cp -T /var/www/html/ref/cab/.htaccess /var/www/html/".$sub.'/.htaccess');
			if(file_exists("/var/www/html/".$sub.'/index.php') and  file_exists("/var/www/html/".$sub.'/application/config/autoload.php')){
				$mess['OK'][]='кабинет скопирован';
				exec("cp -p /dumps/$sub/config.php /var/www/html/".$sub.'/application/config/config.php');
				exec("cp -p /dumps/$sub/database.php /var/www/html/".$sub.'/application/config/database.php');
				if(file_exists("/var/www/html/".$sub.'/application/config/config.php')){
					$mess['OK'][]='config.php скопирован из бэкапа';
				}else{
						$mess['ERROR'][]='config.php скопировать не удалось';
				}
				if(file_exists("/var/www/html/".$sub.'/application/config/database.php')){
					$mess['OK'][]='database.php скопирован из бэкапа';
				}else{
						$mess['ERROR'][]='database.php скопировать не удалось';
				}
				//exec("chown -R  ftpuser2:ftpuser2 /var/www/html/".$sub);
				/*
				копируем серверную часть на ватс
				*/
				if(strstr($sub,'a')){
					$ip= '91.196.5.'.substr($sub,1);
				}elseif(strstr($sub,'b')){
					$ip= '159.253.121.'.substr($sub,1);
				}
				
				$con = ssh2_connect($ip, '22');
				if(!$con) exit("не могу подключиться "); 
				if(!ssh2_auth_password($con, 'root', 'nhfypbcnjh315')) 	exit("логин/пароль некоректен"); 
				$line=ssh2_exec($con, 'rm -fr /var/www/html/cabinet;rm -f /home/cab.tar.gz ; cd /home; wget http://ref.sip64.ru/cab.tar.gz;cd /; tar -xf /home/cab.tar.gz;ls /var/www/html/cabinet/| grep  cdr.php'); 
				stream_set_blocking($line, true); 
				$peers= nl2br(stream_get_contents($line));
				if(strstr($peers,'cdr.php')){
					$mess['OK'][]= 'серверная часть перенесна';
				}else{
					$mess['ERROR'][]='серверную часть скопировать не удалось';
				}
			}else{
				$mess['ERROR'][]='кабинет не скопирован';
			}
		
		}else{
			$mess['ERROR'][]='папка не создана';
		}
		$data['mess']=$mess; 
		
		$this->load->view('templates/header');  
		$this->load->view('add_result',$data);
		$this->load->view('templates/footer');
	}
	public function users($sub)	{ 
		$db_host=$this->config->item('deploy_db_host');
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		//$db = new mysqli('localhost', 'deploy', 'deploygfhjkm', $sub);
		if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
		if($result = $db->query("SELECT id,username,exten,is_admin,ip  FROM auth ORDER BY id; " )) {
			while ($row = $result->fetch_assoc()) {
				$data['auth'][]=$row;
				 $data['pass'][$row['username']]=$this->deploy_model->get_pass($sub,$row['username']);
				 $data['acc_id'][$row['username']]=$this->deploy_model->get_id($sub,$row['username']);
				// echo str_rot13(base64_decode($this->deploy_model->get_pass($sub,$row['username'])));
			}
			
		}
		if(strstr($sub,'a')){
			$ip= '91.196.5.'.substr($sub,1);
		}elseif(strstr($sub,'b')){
			$ip= '159.253.121.'.substr($sub,1);
		}
		$f=file_get_contents('https://'.$ip.'/cabinet/extensions.php?type=get_w_reg');
		$data['ext']=json_decode($f);
		$data['sub']=$sub;
		
		$this->load->view('templates/header');  
		$this->load->view('userlist',$data);
		$this->load->view('templates/footer');
	}
	public function useradd($sub)	{ 
		$this->load->helper('url');
		$this->load->model('add_model');
		//print_r($_POST);
		$username=$_POST['username'];
		$ext=$_POST['ext'];
		$is_admin=($_POST['admin']=='on' ? 1 : 0);
		$pass=sha1($_POST['pass']);
		$acc_data=array('sub'=>$sub,'login'=>$username,'exten'=>$ext,'pass'=>base64_encode(str_rot13($_POST['pass'])));
		$db=$this->add_model->add_acc($acc_data);
		
		$db_host=$this->config->item('deploy_db_host'); 
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
		if($result = $db->query("INSERT INTO auth (`username`,`passwd`,`exten`,`is_admin`) VALUES ('$username','$pass','$ext','$is_admin');" )) {
			$this->input->set_cookie('STAT', 'Пользователь с именем '.$username.' добавлен', '86400');
			
		}else{
				$this->input->set_cookie('STAT', 'Не удалось добавить пользователя', '86400');
		}
		
		$bu= base_url();
		header('Location: '.$bu.'/lk/users/'.$sub); 
	}
	public function useredit($sub)	{ 
		$this->load->helper('url');
		$this->load->model('add_model');
		print_r($_POST);
		$username=$_POST['username'];
		$ext=$_POST['ext'];
		$uid=$_POST['uid'];
		$acc_id=$_POST['acc_id'];
		$is_admin=($_POST['admin']=='on' ? 1 : 0);
		$pass=sha1($_POST['pass']);
		
		
		$db_host=$this->config->item('deploy_db_host'); 
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
		// проверка на обновление пароля
		if($_POST['pass']){
			$q="UPDATE auth SET `is_admin`='$is_admin', `username`='$username',`exten`='$ext',`passwd`='$pass' WHERE `id`=$uid;" ;
			$acc_data=array('sub'=>$sub,'login'=>$username,'exten'=>$ext,'pass'=>base64_encode(str_rot13($_POST['pass'])));
		}else{
			$q="UPDATE auth SET `is_admin`='$is_admin', `username`='$username',`exten`='$ext' WHERE `id`=$uid;" ;
			$acc_data=array('sub'=>$sub,'login'=>$username,'exten'=>$ext);
		}
		
		
		if($result = $db->query($q)) {
			$db=$this->add_model->upd_acc($acc_id,$acc_data);
			$this->input->set_cookie('STAT', 'Пользователь с именем '.$username.' изменен', '86400');
			
		}else{
				$this->input->set_cookie('STAT', 'Не удалось изменить пользователя'.$db->error, '86400');
				echo $db->error;
		}
		
		$bu= base_url();
		header('Location: '.$bu.'/lk/users/'.$sub); 
	}
	/* ajax calling main.js  deluser(sub,uid,acc_id) */
	public function userdel($sub,$uid,$acc_id)	{
		$this->load->model('add_model');		
		$db_host=$this->config->item('deploy_db_host');
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		$db=$this->add_model->del_acc_user($acc_id);
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		if($result = $db->query("DELETE FROM auth WHERE id=$uid;" )) {
			echo  'Пользователь удален';
		}else{
			echo  'Не удалось удалить пользователя';
		}
		
	}
		/* ajax calling main.js   delbad(sub,id) */
	public function delbad($sub,$id)	{ 
		$db_host=$this->config->item('deploy_db_host');
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		if($result = $db->query("DELETE FROM auth_failed WHERE id=$id;" )) {
			echo  'Запись удалена';
		}else{
			echo  'Не удалось удалить запись';
		}
		
	}
	/* ajax calling main.js  setting_save() */
	public function sett_save($sub)	{ 
		$this->load->model('deploy_model');
		if(isset($_POST['type'])){
		$type= $_POST['type'];
		}else{
		$type= $_GET['type'];	
		}
		$db_host=$this->config->item('deploy_db_host');
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		if($result = $db->query("SHOW TABLES LIKE 'settings' ;" )) {
			if(mysqli_fetch_array($result)[0] =='settings'){
				$result = $db->query("UPDATE `settings` SET s_value='{$type}' WHERE s_key='type' ;" );
				
				echo 'изменено';
			}else{
				echo 'нет таблицы для изменения ';
				
			}
			$this->deploy_model->set_param($sub,'type',$type);
		}
		
	}
	public function user_save($sub)	{
	
		$db_host=$this->config->item('deploy_db_host'); 
		$db_user=$this->config->item('deploy_db_user');
		$db_pass=$this->config->item('deploy_db_pass');
		$db = new mysqli($db_host, $db_user, $db_pass , $sub);
		if($db->connect_errno > 0)	die('Unable to connect to database [' . $db->connect_error . ']');
		$q="UPDATE auth SET `is_admin`='{$_GET['type']}' WHERE `username`='{$_GET['username']}' AND  `exten`='{$_GET['exten']}';" ;
		echo ($result = $db->query($q)) ? 'изменен': 'не изменен';
		
	}	
}