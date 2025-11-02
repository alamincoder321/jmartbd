<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class DiscountFixed extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product Discount Entry";
        $data['content'] = $this->load->view('Administrator/products/discount_fixed', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function checkDiscountProduct()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";
        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and p.ProductCategory_ID = '$data->categoryId'";
        }

        $products = $this->db->query("select p.Product_SlNo, p.ProductCategory_ID, p.Product_Code, p.Product_Name, p.Product_Purchase_Rate, p.Product_SellingPrice, p.discount, p.discountAmount from tbl_product p where p.status = 'a' $clauses")->result();

        echo json_encode(array_values($products));
    }

    public function addProductDiscount()
    {
        $res = ['status' => false, 'message' => ''];
        $data = json_decode($this->input->raw_input_stream);

        foreach ($data->products as $item) {
            $product = array(
                "discount"         => $item->discount,
                "discountAmount"   => $item->discountAmount,
                "UpdateBy"         => $this->session->userdata('FullName'),
                "UpdateTime"       => date("Y-m-d H:i:s"),
                "Product_branchid" => $this->session->userdata("BRANCHid"),
            );
            $this->db->where('Product_SlNo', $item->Product_SlNo)->update('tbl_product', $product);
        }
        $res = ['status' => true, 'message' => 'Product discount added successfully'];


        echo json_encode($res);
    }
}
