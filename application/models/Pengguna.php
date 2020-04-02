<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends CI_Model {
	function __construct() {
		$this->tableName = 'pengguna';
		$this->primaryKey = 'id';
	}
	public function checkUser($data = array()){
		$this->db->select($this->primaryKey);
		$this->db->from($this->tableName);
		$con = array(
		'oauth_provider' => $data['oauth_provider'],
		'oauth_uid' => $data['oauth_uid']
		);
		$this->db->where($con);
		$query = $this->db->get();
		$check = $query->num_rows();
		if($check > 0){
			// Ambil data sebelumnya
			$result = $query->row_array();
			// Update data pengguna
			$data['diupdate'] = date("Y-m-d H:i:s");
			$update = $this->db->update($this->tableName, $data, array('id'=>$result['id']));
			// id pengguna
			$userID = $result['id'];
			}else{
			// Insert data pengguna
			$data['dibuat'] = date("Y-m-d H:i:s");
			$data['diupdate'] = date("Y-m-d H:i:s");
			$insert = $this->db->insert($this->tableName,$data);
			// id pengguna
			$userID = $this->db->insert_id();
		}
		// Return id pengguna
		return $userID?$userID:false;
	}
}