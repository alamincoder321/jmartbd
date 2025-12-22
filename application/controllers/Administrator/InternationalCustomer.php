<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class InternationalCustomer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->cbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('SMS_model', 'sms', true);
    }

    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add International Customer";
        $data['customerCode'] = $this->mt->generateInternationalCustomerCode();
        $data['content'] = $this->load->view('Administrator/international/add_international_customer', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getCustomers()
    {
        $data = json_decode($this->input->raw_input_stream);

        $customerTypeClause = "";
        $limit = "";

        if (isset($data->customerType) && $data->customerType != null) {
            $customerTypeClause = " and c.Customer_Type = '$data->customerType'";
        }
        if (isset($data->areaId) && $data->areaId != null) {
            $customerTypeClause = " and c.area_ID = '$data->areaId'";
        }
        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }
        if (isset($data->name) && $data->name != '') {
            $customerTypeClause .= " and (c.Customer_Code like '%$data->name%' or c.Customer_Name like '%$data->name%' or c.Customer_Mobile like '%$data->name%')";
        }

        $customers = $this->db->query("
            select
                c.*,
                d.District_Name,
                concat_ws(' - ', c.Customer_Code, c.Customer_Name, c.owner_name, c.Customer_Mobile) as display_name
            from tbl_international_customer c
            left join tbl_district d on d.District_SlNo = c.area_ID
            where c.status = 'a'
            and c.Customer_Type != 'G'
            and (c.Customer_brunchid = ? or c.Customer_brunchid = 0)
            $customerTypeClause
            order by c.Customer_SlNo desc
            $limit
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($customers);
    }

    public function getCustomerPayments()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->paymentType) && $data->paymentType != '' && $data->paymentType == 'received') {
            $clauses .= " and cp.CPayment_TransactionType = 'CR'";
        }
        if (isset($data->paymentType) && $data->paymentType != '' && $data->paymentType == 'paid') {
            $clauses .= " and cp.CPayment_TransactionType = 'CP'";
        }

        if (isset($data->reportType) && $data->reportType != '' && $data->reportType == 'claim') {
            $clauses .= " and cp.gross_sale > 0";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and cp.CPayment_date between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->customerId) && $data->customerId != '' && $data->customerId != null) {
            $clauses .= " and cp.CPayment_customerID = '$data->customerId'";
        }

        $payments = $this->db->query("
            select
                cp.*,
                c.Customer_Code,
                ifnull(c.Customer_Name, 'General Customer') as Customer_Name,
                c.Customer_Mobile,
                c.Customer_Type,
                ba.account_name,
                ba.account_number,
                ba.bank_name,
                case cp.CPayment_TransactionType
                    when 'CR' then 'Received'
                    when 'CP' then 'Paid'
                end as transaction_type,
                case cp.CPayment_Paymentby
                    when 'bank' then concat('Bank - ',  ba.account_number)
                    when 'wallet' then concat('Wallet - ', ba.account_number)
                    when 'By Cheque' then 'Cheque'
                    else 'Cash'
                end as payment_by
            from tbl_international_customer_payment cp
            left join tbl_international_customer c on c.Customer_SlNo = cp.CPayment_customerID
            left join tbl_bank_accounts ba on ba.account_id = cp.account_id
            where cp.CPayment_status = 'a'
            and cp.CPayment_brunchid = ? $clauses
            order by cp.CPayment_id desc
        ", $this->session->userdata('BRANCHid'))->result();

        foreach ($payments as $key => $payment) {
            $payment->canEditDelete = checkEditDelete($this->session->userdata('accountType'), $payment->CPayment_AddDAte);
        }

        echo json_encode($payments);
    }

    public function addCustomerPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);

            // check bank txid uniqueness
            if ($paymentObj->CPayment_Paymentby == 'bank' && $paymentObj->bank_txid != null && $paymentObj->bank_txid != '') {
                $checkTxid = $this->db->query("select * from tbl_international_customer_payment where bank_txid = ? and CPayment_status = 'a'", $paymentObj->bank_txid)->num_rows();
                if ($checkTxid > 0) {
                    $res = ['success' => false, 'message' => "Bank TXID already exists"];
                    echo json_encode($res);
                    exit;
                } else {
                    $paymentObj->bank_txid = $paymentObj->bank_txid;
                }
            }

            $checkinv = $this->db->query("select * from tbl_international_customer_payment where CPayment_invoice = ?", $paymentObj->CPayment_invoice)->num_rows();
            if ($checkinv > 0) {
                $paymentObj->CPayment_invoice = $this->mt->generateInternationalCustomerPaymentCode();
            }

            $payment = (array)$paymentObj;
            $payment['CPayment_status'] = 'a';
            $payment['CPayment_Addby'] = $this->session->userdata("FullName");
            $payment['CPayment_AddDAte'] = date('Y-m-d H:i:s');
            $payment['CPayment_brunchid'] = $this->session->userdata("BRANCHid");

            $this->db->insert('tbl_international_customer_payment', $payment);
            $paymentId = $this->db->insert_id();

            $res = ['success' => true, 'message' => 'Payment added successfully', 'paymentId' => $paymentId, 'CPayment_invoice' => $this->mt->generateInternationalCustomerPaymentCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateCustomerPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $paymentObj = json_decode($this->input->raw_input_stream);
            $paymentId = $paymentObj->CPayment_id;

            // check bank txid uniqueness
            if ($paymentObj->CPayment_Paymentby == 'bank' && $paymentObj->bank_txid != null && $paymentObj->bank_txid != '') {
                $checkTxid = $this->db->query("select * from tbl_international_customer_payment where bank_txid = ? and CPayment_status = 'a' and CPayment_id != ?", [$paymentObj->bank_txid, $paymentObj->CPayment_id])->num_rows();
                if ($checkTxid > 0) {
                    $res = ['success' => false, 'message' => "Bank TXID already exists"];
                    echo json_encode($res);
                    exit;
                } else {
                    $paymentObj->bank_txid = $paymentObj->bank_txid;
                }
            }

            $payment = (array)$paymentObj;
            unset($payment['CPayment_id']);
            $payment['update_by'] = $this->session->userdata("FullName");
            $payment['CPayment_UpdateDAte'] = date('Y-m-d H:i:s');

            $this->db->where('CPayment_id', $paymentObj->CPayment_id)->update('tbl_international_customer_payment', $payment);

            $res = ['success' => true, 'message' => 'Payment updated successfully', 'paymentId' => $paymentId, 'CPayment_invoice' => $this->mt->generateInternationalCustomerPaymentCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteCustomerPayment()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $this->db->set(['CPayment_status' => 'd'])->where('CPayment_id', $data->paymentId)->update('tbl_international_customer_payment');

            $res = ['success' => true, 'message' => 'Payment deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function addCustomer()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $customerObj = json_decode($this->input->post('data'));

            $customerCodeCount = $this->db->query("select * from tbl_international_customer where Customer_Code = ?", $customerObj->Customer_Code)->num_rows();
            if ($customerCodeCount > 0) {
                $customerObj->Customer_Code = $this->mt->generateInternationalCustomerCode();
            }

            $customer = (array)$customerObj;
            unset($customer['Customer_SlNo']);
            unset($customer['confirmation']);
            $customer["Customer_brunchid"] = $this->session->userdata("BRANCHid");

            $customerId = null;
            $res_message = "";

            $duplicateMobileQuery = $this->db->query("select * from tbl_international_customer where Customer_Mobile = ? and Customer_brunchid = ?", [$customerObj->Customer_Mobile, $this->session->userdata("BRANCHid")]);

            if ($duplicateMobileQuery->num_rows() > 0 && !isset($customerObj->confirmation)) {
                $res = ['success' => false, 'message' => 'Mobile number already exist. Do you again use this number ?'];
                echo json_encode($res);
                exit;
            } else {
                $customer["AddBy"] = $this->session->userdata("FullName");
                $customer["AddTime"] = date("Y-m-d H:i:s");

                $this->db->insert('tbl_international_customer', $customer);
                $customerId = $this->db->insert_id();

                $res_message = 'Customer added successfully';
            }


            if (!empty($_FILES)) {
                $config['upload_path'] = './uploads/customers/';
                $config['allowed_types'] = 'gif|jpg|png';

                $imageName = $customerObj->Customer_Code;
                $config['file_name'] = $imageName;
                $this->load->library('upload', $config);
                $this->upload->do_upload('image');

                $config['image_library'] = 'gd2';
                $config['source_image'] = './uploads/customers/' . $imageName;
                $config['new_image'] = './uploads/customers/';
                $config['maintain_ratio'] = TRUE;
                $config['width']    = 640;
                $config['height']   = 480;

                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                $imageName = $customerObj->Customer_Code . $this->upload->data('file_ext');

                $this->db->query("update tbl_international_customer set image_name = ? where Customer_SlNo = ?", [$imageName, $customerId]);
            }

            $res = ['success' => true, 'message' => $res_message, 'customerCode' => $this->mt->generateInternationalCustomerCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateCustomer()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $customerObj = json_decode($this->input->post('data'));

            $customer = (array)$customerObj;
            $customerId = $customerObj->Customer_SlNo;

            unset($customer["Customer_SlNo"]);
            $customer["Customer_brunchid"] = $this->session->userdata("BRANCHid");
            $customer["UpdateBy"] = $this->session->userdata("FullName");
            $customer["UpdateTime"] = date("Y-m-d H:i:s");

            $this->db->where('Customer_SlNo', $customerId)->update('tbl_international_customer', $customer);

            if (!empty($_FILES)) {
                $config['upload_path'] = './uploads/customers/';
                $config['allowed_types'] = 'gif|jpg|png';

                $imageName = $customerObj->Customer_Code;
                $config['file_name'] = $imageName;
                $this->load->library('upload', $config);
                $this->upload->do_upload('image');

                $config['image_library'] = 'gd2';
                $config['source_image'] = './uploads/customers/' . $imageName;
                $config['new_image'] = './uploads/customers/';
                $config['maintain_ratio'] = TRUE;
                $config['width']    = 640;
                $config['height']   = 480;

                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                $imageName = $customerObj->Customer_Code . $this->upload->data('file_ext');

                $this->db->query("update tbl_international_customer set image_name = ? where Customer_SlNo = ?", [$imageName, $customerId]);
            }

            $res = ['success' => true, 'message' => 'Customer updated successfully', 'customerCode' => $this->mt->generateInternationalCustomerCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteCustomer()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $this->db->query("update tbl_international_customer set status = 'd' where Customer_SlNo = ?", $data->customerId);

            $res = ['success' => true, 'message' => 'Customer deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function customerPaymentPage()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Payment Received";
        $data['CPayment_invoice'] = $this->mt->generateInternationalCustomerPaymentCode();
        $data['content'] = $this->load->view('Administrator/international/internationalcustomerPaymentPage', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function customerPaymentHistory()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Customer Payment History";
        $data['content'] = $this->load->view('Administrator/international/international_customer_payment_history', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }


    /// cash balance

    public function getInternationalCashBalance()
    {
        $branchId = $this->session->userdata('BRANCHid');

        $result = $this->mt->internationalCashBalance($branchId);

        echo json_encode($result);
    }
}
