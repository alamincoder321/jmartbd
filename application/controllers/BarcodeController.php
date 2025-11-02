<?php defined('BASEPATH') or exit('No direct script access allowed');

class BarcodeController extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->sbrunch = $this->session->userdata('BRANCHid');
		$access = $this->session->userdata('userId');
		if ($access == '') {
			redirect("Login");
		}
		$this->load->library('cart');
		$this->load->model('Model_table', "mt", TRUE);
	}

	public function barcode_create($id)
	{
		$access = $this->mt->userAccess();
		if (!$access) {
			redirect(base_url());
		}
		$data['title'] = "Product Barcode";
		$data['product'] = $this->db->where('Product_SlNo', $id)->get('tbl_product')->row();
		$data['content'] = $this->load->view('Administrator/products/barcode/barcode', $data, TRUE);
		$this->load->view('Administrator/index', $data);
	}
}
