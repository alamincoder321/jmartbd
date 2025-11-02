<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Exchange extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->cbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('SMS_model', 'sms', true);
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Exchange";
        $data['content'] = $this->load->view('Administrator/exchange/add_exchange', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addExchange()
    {
        $res = array('success' => 'false', 'message' => '');
        try {
            $this->db->trans_begin();

            $exchangeObj = json_decode($this->input->post('exchange'));
            $carts = json_decode($this->input->post('carts'));
            $sales = json_decode($this->input->post('sales'));

            $exchangeData = array(
                'invoice'        => $sales->SaleMaster_InvoiceNo,
                'date'           => $exchangeObj->date,
                'sale_id'        => $sales->SaleMaster_SlNo,
                'sale_id'        => $sales->SaleMaster_SlNo,
                'saletotal'      => $exchangeObj->saletotal,
                'exchangeAmount' => $exchangeObj->exchangeAmount,
                'total'          => $exchangeObj->total,
                'cashPaid'       => $exchangeObj->cashPaid,
                'bankPaid'       => $exchangeObj->bankPaid,
                'bank_id'        => $exchangeObj->bank_id ?? NULL,
                'AddBy'          => $this->session->userdata('FullName'),
                'AddTime'        => date('Y-m-d H:i:s'),
                'branchId'       => $this->cbrunch,
            );

            $this->db->insert('tbl_exchange', $exchangeData);
            $exchangeId = $this->db->insert_id();

            foreach ($carts as $key => $cartProduct) {
                $saledetail = $this->db->where('SaleDetails_SlNo', $cartProduct->sale_detail_id)->get('tbl_saledetails')->row();
                $cartData = array(
                    'exchange_id'       => $exchangeId,
                    'sale_detail_id'    => $cartProduct->sale_detail_id,
                    'detail_product_id' => $saledetail->Product_IDNo,
                    'product_id'        => $cartProduct->product_id,
                    'quantity'          => $cartProduct->quantity,
                    'purchase_rate'     => $cartProduct->purchase_rate,
                    'sale_rate'         => $cartProduct->sale_rate,
                    'total'             => $cartProduct->total,
                    'AddBy'             => $this->session->userdata('FullName'),
                    'AddTime'           => date('Y-m-d H:i:s'),
                    'branchId'          => $this->cbrunch,
                );
                $this->db->insert('tbl_exchange_detail', $cartData);

                $this->db->query("
                        update tbl_currentinventory 
                        set sales_quantity = sales_quantity - ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$saledetail->SaleDetails_TotalQuantity, $saledetail->Product_IDNo, $this->session->userdata('BRANCHid')]);
                $this->db->query("
                        update tbl_currentinventory 
                        set sales_quantity = sales_quantity + ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$cartProduct->quantity, $cartProduct->product_id, $this->session->userdata('BRANCHid')]);
            }
            $this->db->trans_commit();

            $res = array('success' => 'true', 'message' => 'Exchange added successfully!');
        } catch (\Throwable $e) {
            $this->db->trans_rollback();
            $res = array('success' => 'false', 'message' => $e->getMessage());
        }

        echo json_encode($res);
    }

    public function exchange_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Exchange Record";
        $data['content'] = $this->load->view('Administrator/exchange/exchange_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getExchanges()
    {
        $data = json_decode($this->input->raw_input_stream);
        $clauses = "";
        if ((isset($data->dateFrom) && $data->dateFrom != "") && (isset($data->dateTo) && $data->dateTo != "")) {
            $clauses .= " and ex.date between '$data->dateFrom' and '$data->dateTo'";
        }
        if (isset($data->customerId) && $data->customerId != "") {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and ex.AddBy = '$data->userFullName'";
        }

        $exchanges = $this->db
            ->query("select ex.*, 
                c.Customer_Name, 
                c.Customer_Code, 
                c.Customer_Mobile
                from tbl_exchange ex 
                left join tbl_salesmaster sm on sm.SaleMaster_SlNo = ex.sale_id
                left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                where ex.Status = 'a'
                and ex.branchId = '$this->cbrunch'
                $clauses")
            ->result();

        $exchanges = array_map(function ($item) {
            $item->exchangeDetails = $this->db->query("select exd.*, p.Product_Name, p.Product_Code from tbl_exchange_detail exd 
                left join tbl_product p on p.Product_SlNo = exd.product_id
                where exd.Status = 'a'
                and exd.exchange_id = ?", [$item->id])->result();
            return $item;
        }, $exchanges);

        echo json_encode(array_values($exchanges));
    }

    public function deleteExchange()
    {
        $res = array('success' => false, 'message' => '');
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $this->db->where('id', $data->exchangeId)->update('tbl_exchange', array('Status' => 'd', 'UpdateBy' => $this->session->userdata('FullName'), 'UpdateTime' => date('Y-m-d H:i:s')));

            $oldDetails = $this->db->query("select * from tbl_exchange_detail where exchange_id = ?", [$data->exchangeId])->result();
            foreach ($oldDetails as $key => $oldDetail) {
                $detail_sale = $this->db->where('SaleDetails_SlNo', $oldDetail->sale_detail_id)->get('tbl_saledetails')->row();
                $this->db->query("
                    update tbl_currentinventory 
                    set sales_quantity = sales_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$detail_sale->SaleDetails_TotalQuantity, $detail_sale->Product_IDNo, $this->session->userdata('BRANCHid')]);

                $this->db->query("
                    update tbl_currentinventory 
                    set sales_quantity = sales_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$oldDetail->quantity, $oldDetail->product_id, $this->session->userdata('BRANCHid')]);
            }
            $this->db->where('exchange_id', $data->exchangeId)->update('tbl_exchange_detail', array('Status' => 'd', 'UpdateBy' => $this->session->userdata('FullName'), 'UpdateTime' => date('Y-m-d H:i:s')));

            $this->db->trans_commit();
            $res = array('success' => true, 'message' => 'Exchange deleted successfully!');
        } catch (\Throwable $th) {
            $this->db->trans_rollback();
            $res = array('success' => false, 'message' => $th->getMessage());
        }

        echo json_encode($res);
    }
}
