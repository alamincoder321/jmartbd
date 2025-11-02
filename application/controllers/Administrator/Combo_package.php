<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Combo_package extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->sbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
        $this->load->model('SMS_model', 'sms', true);
    }
    
    public function index()
    {
        // $access = $this->mt->userAccess();
        // if (!$access) {
        //     redirect(base_url());
        // }
        $data['title'] = "Combo Package";

        $invoice = $this->mt->generateSalesInvoice();

        $data['content'] = $this->load->view('Administrator/combo_package/index', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addComboPackage(){
        // $access = $this->mt->userAccess();
        // if (!$access) {
        //     redirect(base_url());
        // }

        


        $data['salesId'] = 0;
        $data['invoiceNo'] = $this->mt->generateSalesInvoice();
        $data['title'] = "Add Combo Package";
        $data['content'] = $this->load->view('Administrator/combo_package/add_combo_package', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function updateComboPackage(){
        // $access = $this->mt->userAccess();
        // if (!$access) {
        //     redirect(base_url());
        // }
        $data = $this->input->post();
        $data = json_decode($this->input->raw_input_stream);
        $data_input = [
            'comboInvoice' => $data->sales->invoiceNo,
            'total' => $data->sales->subTotal,
           
            'add_by' => $this->session->userdata('FullName'),
            'update_by' => $this->session->userdata('FullName'),
            'add_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
            'packageName' => $data->sales->packageName,
            'branch_id' => $this->session->userdata('BRANCHid')

        ];

        $this->db->insert('tbl_combomaster', $data_input);

        $comboId = $this->db->insert_id();

        foreach ($data->cart as $key => $item) {
            $cart_data = [
                'combo_master_id' => $comboId,
                'product_id' => $item->productId,
                'selling_price' => $item->salesRate,
                'add_time' => date('Y-m-d H:i:s'),
                'qty' => $item->quantity
            ];

            $this->db->insert('tbl_combom_details', $cart_data);
        }



        echo json_encode(['success' => true, 'message' => 'Combo Package added successfully', 'salesId' => $comboId]);


        exit();
    }


    public function getComboPackageDetails(){
        $data = json_decode($this->input->raw_input_stream);

        $query = "where tbl_combomaster.add_time between '$data->dateFrom' and '$data->dateTo'";

        $data_result = $this->db->query("select * from tbl_combomaster ".$query)->result();


        echo json_encode($data_result);

        exit();
    }


    public function combo_package($invoice_id = 0){
        $package_master = $this->db->query("select * from tbl_combomaster where ComboId = ? ", $invoice_id)->row();
        if(!$package_master){
            redirect('combo_package');
        }
        $package_data  = $this->db->query("select * from tbl_combom_details left join tbl_product on tbl_product.Product_SlNo = tbl_combom_details.product_id where combo_master_id = ? ", $invoice_id)->result();

        $data['title'] = "Combo Package";
        $data['package_data'] = $package_data;
        $data['package_master'] = $package_master;
        $data['content'] = $this->load->view('Administrator/combo_package/invoice_details', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    public function getPackages(){
        $data = $this->input->post();
        $data = json_decode($this->input->raw_input_stream);
        
        $data_result = $this->db->query("select * from tbl_combomaster ")->result();
        echo json_encode($data_result);
        exit();
    }

    public function update_combo_package_stock()  {
        $data = $this->input->post();
        $data = json_decode($this->input->raw_input_stream);
        $qty = $data->stockQty;
        if($data->addStock == true){
            $qty = $data->stockQty + $data->newstock;
        }else{
            $qty = $data->stockQty - $data->newstock;
        }
      
        $this->db->query("update tbl_combomaster set stockQty = ? where ComboId = ?", [$qty, $data->ComboId]);
        echo json_encode(['success' => true, 'message' => 'Combo Package quantity updated successfully']);
        
    }
}

