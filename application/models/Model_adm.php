<?php if(!defined('BASEPATH')) exit('No direct script access allowed!');

class Model_adm extends CI_Model
{
	function __construct() {
		parent::__construct();
	}
	
	public function get($table, $order_by='', $sort='ASC') {
		$this->load->database();
		if($order_by) {
			$this->db->order_by($order_by, $sort);
		}
		$result = $this->db->get($table);
		$this->db->close();
		return $result;
	}
	
	public function select($condition='', $table, $order_by='', $sort='ASC'){
		$this->load->database();
		if($condition) {
			$this->db->where($condition);
		}
		if($order_by) {
			$this->db->order_by($order_by, $sort);
		}
		$result = $this->db->get($table);
		$this->db->close();
		return $result;
	}
	
	public function select_join($condition='', $table, $select, $joins, $order_by='', $sort='ASC'){
		$this->load->database();
		if($condition) {
			$this->db->where($condition);
		}
		if($order_by) {
			$this->db->order_by($order_by, $sort);
		}
		if(!empty($select)) {
			$this->db->select($select);
		}
		if(!empty($joins)) {
			foreach ($joins as $join) {
				$this->db->join($join[0], $join[1], $join[2]);
			}
		}
		$result = $this->db->get($table);
		$this->db->close();
		return $result;
	}
	
	public function insert($data, $table) {
		$this->load->database();
		$result = $this->db->insert($table, $data);
		$this->db->close();
		return $result;
	}
	
	public function insert_id($data, $table) {
		$this->load->database();
		$result = $this->db->insert($table, $data);
		$insert_id = $this->db->insert_id();
		$this->db->close();
		return $insert_id;
	}
	
	public function insert_batch($data, $table) {
		$this->load->database();
		$result = $this->db->insert_batch($table, $data);
		$this->db->close();
		return $result;
	}
	
	public function update($condition, $data, $table){
		$this->load->database();
		if($condition) {
			$this->db->where($condition);
		}
		$result = $this->db->update($table, $data);
		$this->db->close();
		return $result;
	}
	
	public function update_batch($data, $table, $field_title){
		$this->load->database();
		$result = $this->db->update_batch($table, $data, $field_title);
		$this->db->close();
		return $result;
	}
	
	public function delete($condition, $table) {
		$this->load->database();
		$this->db->where($condition);
		$result = $this->db->delete($table);
		$this->db->close();
		return $result;
	}
	
	public function delete_batch($condition, $table, $field) {
		$this->load->database();
		$this->db->where_in($field, $condition);
		$result = $this->db->delete($table);
		$this->db->close();
		return $result;
	}
	
	public function rawQuery($query) {
		$this->load->database();
		$result = $this->db->query($query);
		$this->db->close();
		return $result;
	}
	
	public function rawQueries($arr_query) {
		$this->load->database();
		// $this->db->trans_start();
		foreach ($arr_query as $query) {
			$result = $this->db->query($query);
		}
		// $result = $this->db->trans_complete();
		$this->db->close();
		return $result;
	}	
	
	public function have_in($condition, $data, $table, $field){
		$this->load->database();
		if($condition != null){
			$this->db->where($condition);
		}
		$this->db->where_in($field, $data);
		$result = $this->db->get($table);
		$this->db->close();
		return $result;
	}
	
	public function have_not_in($condition, $data, $table, $field){
		$this->load->database();
		if($condition != null){
			$this->db->where($condition);
		}
		$this->db->where_not_in($field, $data);
		$result = $this->db->get($table);
		$this->db->close();
		return $result;
	}
}

