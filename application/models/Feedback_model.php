<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feedback_model extends CI_Model
{

	public function get_feedbacks()
	{
		$this->db->select('*');
		$this->db->from('tbl_review');
		$this->db->order_by('id', 'desc');
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_feedback_by_id($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('tbl_review');
		return $query->row_array();
	}

	public function add_feedback($data)
	{
		return $this->db->insert('tbl_review', $data);
	}

	public function update_feedback($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('tbl_review', $data);
	}

	public function delete_feedback($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete('tbl_review');
	}

	public function updateStatus($id, $status){
		
		$this->db->set('status', $status);
		$this->db->where('id', $id);
		return $this->db->update('tbl_review');
	}
}
