<?php
class Deploy_model extends CI_Model {

	public function __construct()
	{
		$this->load->database();
	}
	
	public function get_lk($id=false,$sub=false) 
	{
		
		if($id){
			$data['id'] = $id;
			$this->db->where($data);
		}elseif($sub){
				$data['sub'] = $sub;
			$this->db->where($data);
		}
		
		$query = $this->db->get('list');
		if ($query->num_rows() > 0){
			return $query->result_array(); 
		}		
	}
	public function get_ver($sub=false) 
	{
		$data['sub'] = $sub;
		$this->db->where($data);
		$query = $this->db->select('ver');
		$query = $this->db->get('list');
		if ($query->num_rows() > 0){
			$row=$query->row(); 
			 return $row->ver;
		}		
	}
	public function get_type($sub=false) 
	{
		$data['sub'] = $sub;
		$this->db->where($data);
		$query = $this->db->select('type');
		$query = $this->db->get('list');
		if ($query->num_rows() > 0){
			 $row=$query->row(); 
			 return $row->type;
		}		
	}
	public function set_param($sub,$k,$v) 
	{
		$data[$k] = $v;
		$this->db->where('sub',$sub);  
		return $this->db->update('list', $data);  		
	}
	public function get_pass($sub,$un) 
	{
		$data['sub'] = $sub;
		$data['login'] = $un;
		$this->db->where($data);
		$query = $this->db->select('pass');
		
		$query = $this->db->get('accounts');
		if ($query->num_rows() > 0){
			 $row=$query->row(); 
			 return $row->pass;
		}		
	}
	public function get_id($sub,$un) 
	{
		$data['sub'] = $sub;
		$data['login'] = $un;
		$this->db->where($data);
		$query = $this->db->select('id');
		
		$query = $this->db->get('accounts');
		if ($query->num_rows() > 0){
			 $row=$query->row(); 
			 return $row->id;
		}		
	}
}