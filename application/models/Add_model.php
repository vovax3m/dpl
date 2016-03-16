<?php
class Add_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}
	
	public function add($sub,$ip,$name,$type,$ver) 	{ 
	
	
		$data['sub'] = $sub;
		$data['ip']  = $ip;
		$data['name']= $name;
		$data['ip']  = $ip;
		$data['type']= $type;
		$data['ver'] = $ver;
		return $query=$this->db->insert('list', $data);  
	}
	public function del($sub) 
	{
		
		$this->db->where('sub',$sub);  
		return $query=$this->db->delete('list');  
	}
	public function add_acc($array) 
	{
		return $query=$this->db->insert('accounts', $array);  
	}
	public function del_acc_user($id) 
	{
		$this->db->where('id',$id);  
		return $query=$this->db->delete('accounts');
	}
		public function del_acc($sub) 
	{
		$this->db->where('sub',$sub);  
		return $query=$this->db->delete('accounts');
	}
	public function upd_acc($acc_id,$array) 
	{
		$this->db->where('id',$acc_id);  
		return $query=$this->db->update('accounts', $array);  
	}
	public function get_ver()	{
		/*
		получает версию референсного кабинета
		*/
	
		$data['sub'] = 'ref';
		$this->db->where($data);
		$query = $this->db->select('ver');
		$query = $this->db->get('list');
		if ($query->num_rows() > 0){
			 $row=$query->row(); 
			 return $row->ver;
		}		
	}
	public function get_ip($sub)	{
		/*
		получает версию референсного кабинета
		*/
	
		$data['sub'] = $sub;
		$this->db->where($data);
		$query = $this->db->select('ip');
		$query = $this->db->get('list');
		if ($query->num_rows() > 0){
			 $row=$query->row(); 
			 return $row->ip;
		}		
	}
	
	public function maillog($array) 
	{
		return $query=$this->db->insert('maillog', $array);  
	}
	
}