<?php
class Un_model extends CI_Model {

	public function __construct()
	{
		 parent::__construct();
		 
		$this->db_lk=$this->load->database('lk',true);
	}
	function get_auth($id=false){
		if($id){
			$data['id'] = $id;
			$this->db_lk->where($data);
		}
		$query = $this->db_lk->get('auth');
		if ($query->num_rows() > 0){
			return $query->result_array(); 
		}		
	}
	function get_pass($id=false){
		if($id){
			$data['auth_id'] = $id;
			$this->db_lk->where($data);
		}
		
		$query = $this->db_lk->get('accounts');
		if ($query->num_rows() > 0){
			return $query->result_array(); 
		}		
	}
	function useredit($id,$username,$passwd,$accid){
		if($passwd) $data['passwd'] = sha1($passwd);
		$data['username'] = $username ;
        $data['accid']= $accid;
		$this->db_lk->where('id',$id);
		$this->db_lk->update('auth', $data);
		
		$dataacc['login'] = $username ;
		if($passwd) $dataacc['pass'] = base64_encode(str_rot13($passwd));
		$this->db_lk->where('auth_id',$id);
		$this->db_lk->update('accounts', $dataacc);
	}
	function userdel($id){
		
		$this->db_lk->where('auth_id',$id);
		$this->db_lk->delete('accounts');
		
		$this->db_lk->where('id',$id);
		$this->db_lk->delete('auth');
		
		$this->db_lk->where('authid',$id);
		$this->db_lk->delete('book');
		
		mysqli_multi_query($this->db_lk->conn_id,'DROP TABLE callevent_'.$id);
		//$this->dbforge->drop_table('callevent_'.$id);
		//$this->db_lk->where('auth_id',$id);
		//$this->db_lk->delete('accounts');
	}
	
	function addtobook($id,$names){
	
		foreach($names as $k=>$v){
			   $data['nomer'] = $k ;
               $data['name']  = $v;
               $data['authid']= $id;
			   $this->db_lk->insert('book', $data); 
			   //$this->db->insert_batch('book', $data);
		}
	}
	function useradd($username,$passwd,$accid){
		
               $data['username'] = $username ;
               $data['passwd'] = sha1($passwd);
               $data['accid']= $accid;
			   $this->db_lk->insert('auth', $data); 
			   $iid=$this->db_lk->insert_id();
			   
				$dataacc['login'] = $username ;
				$dataacc['pass'] = base64_encode(str_rot13($passwd));
				$dataacc['auth_id']= $iid;
				$this->db_lk->insert('accounts', $dataacc); 
			   
			  $sql='
				SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
				SET time_zone = "+04:00";
				/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
				/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
				/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
				/*!40101 SET NAMES utf8mb4 */;


				CREATE TABLE IF NOT EXISTS `callevent_'.$iid.'` (
				  `id` int(11) NOT NULL,
				  `CallDate` datetime NOT NULL,
				  `Called` text NOT NULL,
				  `Calling` text NOT NULL,
				  `DisconnectCause` varchar(6) NOT NULL,
				  `Duration` int(8) NOT NULL,
				  `Direct` text NOT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;

				ALTER TABLE `callevent_'.$iid.'` ADD PRIMARY KEY (`id`);

				ALTER TABLE `callevent_'.$iid.'`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
				/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
				/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
				/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
				';
				mysqli_multi_query($this->db_lk->conn_id, $sql);
				
				
				return $iid;
	}
	
//class end	
}