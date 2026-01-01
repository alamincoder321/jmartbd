<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales extends CI_Controller
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

    public function index($serviceOrProduct = 'product')
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product Sales";

        $invoice = $this->mt->generateSalesInvoice();

        $data['isService'] = $serviceOrProduct == 'product' ? 'false' : 'true';
        $data['salesId'] = 0;
        $data['invoice'] = $invoice;
        $data['content'] = $this->load->view('Administrator/sales/product_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    
    public function holdSale($referenceNo)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Product Sales";

        $data['isService'] = 'false';
        $data['referenceNo'] = $referenceNo;
        $data['salesId'] = 0;
        $data['invoice'] = $this->mt->generateSalesInvoice();
        $data['content'] = $this->load->view('Administrator/sales/hold_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    
    public function addHoldSale()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $checkInvoice = $this->db->query("select * from tbl_hold_sale where SaleMaster_InvoiceNo = ?", $data->sales->referenceNo)->row();
            if ($checkInvoice) {
                echo json_encode(['success' => false, 'message' => 'This invoice already hold. Please change invoice number']);
                exit;
            }

            $customerId = $data->sales->customerId;
            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                unset($customer['Customer_Type']);
                $mobile_count = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and Customer_brunchid = ?", [$data->customer->Customer_Mobile, $this->session->userdata("BRANCHid")])->row();

                if ($data->customer->Customer_Type == 'N' && empty($mobile_count)) {
                    $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                    $customer['Customer_Type'] = $data->sales->salesType;
                    $customer['Customer_Credit_Limit'] = $data->sales->total;
                    $customer['status'] = 'a';
                    $customer['AddBy'] = $this->session->userdata("FullName");
                    $customer['AddTime'] = date("Y-m-d H:i:s");
                    $customer['Customer_brunchid'] = $this->session->userdata("BRANCHid");
                    $this->db->insert('tbl_customer', $customer);
                    $customerId = $this->db->insert_id();
                }
            }

            $sales = array(
                'SaleMaster_InvoiceNo'           => $data->sales->referenceNo,
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'pointAmount'                    => $data->sales->pointAmount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_cashPaid'            => $data->sales->cashPaid,
                'SaleMaster_bankPaid'            => $data->sales->bankPaid,
                'SaleMaster_PaidAmount'          => $data->sales->paid,
                'returnAmount'                   => $data->sales->returnAmount,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                'is_service'                     => $data->sales->isService,
                "AddBy"                          => $this->session->userdata("FullName"),
                'AddTime'                        => date("Y-m-d H:i:s"),
                'SaleMaster_branchid'            => $this->session->userdata("BRANCHid")
            );

            if ($data->customer->Customer_Type == 'G') {
                $sales['SalseCustomer_IDNo']    = Null;
                $sales['customerType']    = "G";
                $sales['customerName']    = $data->customer->Customer_Name;
                $sales['customerMobile']  = $data->customer->Customer_Mobile;
                $sales['customerAddress'] = $data->customer->Customer_Address;
            } else {
                $sales['customerType']       = $data->sales->salesType;
                $sales['SalseCustomer_IDNo'] = $customerId;
            }

            $this->db->insert('tbl_hold_sale', $sales);

            $salesId = $this->db->insert_id();

            foreach ($data->cart as $cartProduct) {
                if ($cartProduct->type == 'package') {
                    $saleDetails = array(
                        'SaleMaster_IDNo'           => $salesId,
                        'Product_IDNo'              => $cartProduct->productId,
                        'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                        'Purchase_Rate'             => $cartProduct->purchaseRate,
                        'SaleDetails_Rate'          => $cartProduct->salesRate,
                        'SaleDetails_Discount'      => $cartProduct->discount,
                        'Discount_amount'           => $cartProduct->discountAmount,
                        'SaleDetails_Tax'           => $cartProduct->vat,
                        'SaleDetails_TotalAmount'   => $cartProduct->total,
                        'isFree'                    => $cartProduct->isFree,
                        'Status'                    => 'a',
                        'AddBy'                     => $this->session->userdata("FullName"),
                        'AddTime'                   => date('Y-m-d H:i:s'),
                        'SaleDetails_BranchId'      => $this->session->userdata('BRANCHid'),
                        'is_package'                => 1
                    );
                    $this->db->insert('tbl_hold_sale_detail', $saleDetails);
                } else {
                    $saleDetails = array(
                        'SaleMaster_IDNo'           => $salesId,
                        'Product_IDNo'              => $cartProduct->productId,
                        'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                        'Purchase_Rate'             => $cartProduct->purchaseRate,
                        'SaleDetails_Rate'          => $cartProduct->salesRate,
                        'SaleDetails_Discount'      => $cartProduct->discount,
                        'Discount_amount'           => $cartProduct->discountAmount,
                        'SaleDetails_Tax'           => $cartProduct->vat,
                        'SaleDetails_TotalAmount'   => $cartProduct->total,
                        'isFree'                    => $cartProduct->isFree,
                        'Status'                    => 'a',
                        'AddBy'                     => $this->session->userdata("FullName"),
                        'AddTime'                   => date('Y-m-d H:i:s'),
                        'SaleDetails_BranchId'      => $this->session->userdata('BRANCHid')
                    );

                    $this->db->insert('tbl_hold_sale_detail', $saleDetails);
                }
            }

            $this->db->trans_commit();

            $res = ['success' => true, 'message' => 'Sales Hold On Successfully inserted'];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    
    function hold_sale_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Record";
        $data['content'] = $this->load->view('Administrator/sales/hold_sale_list', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    
    public function deleteHoldSale(){
        $data = json_decode($this->input->raw_input_stream);
        $this->db->where('SaleMaster_SlNo', $data->holdSaleId);
        $this->db->delete('tbl_hold_sale');

        $this->db->where('SaleMaster_IDNo', $data->holdSaleId);
        $this->db->delete('tbl_hold_sale_detail');
        echo json_encode(['success' => true, 'message' => 'Hold sale deleted successfully']);
    }

    public function addSales()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            
            foreach ($data->cart as $key => $item) {
                if ($item->type == 'package') {
                    $curentstock = $this->mt->productStock($item->productId, true);
                } else {
                    $curentstock = $this->mt->productStock($item->productId);
                }
                if ($item->quantity > $curentstock) {
                    $productName = $item->name . '-' . $item->productCode;
                    echo json_encode(['success' => false, 'message' => "Stock unavailable. Please check: {$productName}"]);
                    exit;
                }
            }

            $invoice = $data->sales->invoiceNo;
            $invoiceCount = $this->db->query("select * from tbl_salesmaster where SaleMaster_InvoiceNo = ?", $invoice)->num_rows();
            if ($invoiceCount != 0) {
                $invoice = $this->mt->generateSalesInvoice();
            }

            $customerId = $data->sales->customerId;
            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                unset($customer['Customer_Type']);
                $mobile_count = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and Customer_brunchid = ?", [$data->customer->Customer_Mobile, $this->session->userdata("BRANCHid")])->row();

                if ($data->customer->Customer_Type == 'N' && empty($mobile_count)) {
                    $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                    $customer['Customer_Type'] = $data->sales->salesType;
                    $customer['Customer_Credit_Limit'] = $data->sales->total;
                    $customer['status'] = 'a';
                    $customer['AddBy'] = $this->session->userdata("FullName");
                    $customer['AddTime'] = date("Y-m-d H:i:s");
                    $customer['Customer_brunchid'] = $this->session->userdata("BRANCHid");
                    $this->db->insert('tbl_customer', $customer);
                    $customerId = $this->db->insert_id();
                }
            }

            $sales = array(
                'SaleMaster_InvoiceNo'           => $invoice,
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'pointAmount'                    => $data->sales->pointAmount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_cashPaid'            => $data->sales->cashPaid,
                'SaleMaster_bankPaid'            => $data->sales->bankPaid,
                'bank_id'                        => $data->sales->bankPaid > 0 ? $data->sales->bank_id : NULL,
                'SaleMaster_PaidAmount'          => $data->sales->cashPaid + $data->sales->bankPaid,
                'returnAmount'                   => $data->sales->returnAmount,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                'Status'                         => 'a',
                'is_service'                     => $data->sales->isService,
                "AddBy"                          => $this->session->userdata("FullName"),
                'AddTime'                        => date("Y-m-d H:i:s"),
                'SaleMaster_branchid'            => $this->session->userdata("BRANCHid")
            );

            if ($data->customer->Customer_Type == 'G') {
                $sales['SalseCustomer_IDNo']    = Null;
                $sales['customerType']    = "G";
                $sales['customerName']    = $data->customer->Customer_Name;
                $sales['customerMobile']  = $data->customer->Customer_Mobile;
                $sales['customerAddress'] = $data->customer->Customer_Address;
            } else {
                $sales['customerType']       = $data->sales->salesType;
                $sales['SalseCustomer_IDNo'] = $customerId;
                if ($data->customer->is_member == 'yes') {
                    $excludedAmount = 0;
                    if (!empty($data->cart) && is_array($data->cart)) {
                        foreach ($data->cart as $cartItem) {
                            $prodRow = $this->db->query("select ProductsubCategory_ID from tbl_product where Product_SlNo = ?", $cartItem->productId)->row();
                            $subcatId = $prodRow ? $prodRow->ProductsubCategory_ID : null;
                            if ($subcatId == 49) {
                                $excludedAmount += isset($cartItem->total) ? $cartItem->total : ($cartItem->salesRate * $cartItem->quantity);
                            }
                        }
                    }

                    $baseAmountForPoint = max(0, $data->sales->total - $excludedAmount);
                    $new_points = ($data->customer->amount > 0) ? floor($baseAmountForPoint / $data->customer->amount) : 0;

                    $old_point = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $customerId)->row()->point;
                    $pointTotal = $old_point + $new_points;
                    $this->db
                        ->where('Customer_SlNo', $customerId)
                        ->update('tbl_customer', [
                            'is_member' => $data->customer->is_member,
                            'amount' => $data->customer->amount,
                            'point' => ($pointTotal - $data->sales->pointAmount)
                        ]);

                    $sales['point'] = $new_points;
                }
            }

            $this->db->insert('tbl_salesmaster', $sales);

            $salesId = $this->db->insert_id();

            foreach ($data->cart as $cartProduct) {
                if ($cartProduct->type == 'package') {
                    $package = $this->db->query("select * from tbl_combomaster 
                                                left join tbl_combom_details on tbl_combomaster.ComboId = tbl_combom_details.id
                                                left join tbl_product on tbl_product.Product_SlNo = tbl_combom_details.product_id
                                                where combo_master_id = ?", $cartProduct->productId)->result();

                    foreach ($package as $key => $value) {



                        $this->db->query("
                                    update tbl_currentinventory 
                                    set sales_quantity = sales_quantity + ? 
                                    where product_id = ?
                                    and branch_id = ?
                                ", [$cartProduct->quantity * $value->qty, $cartProduct->productId, $this->session->userdata('BRANCHid')]);
                    }

                    // update stock of package
                    $this->db->query("update tbl_combomaster set stockQty = stockQty - ? where ComboId = ?", [$cartProduct->quantity, $cartProduct->productId]);


                    $saleDetails = array(
                        'SaleMaster_IDNo'           => $salesId,
                        'Product_IDNo'              => $cartProduct->productId,
                        'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                        'Purchase_Rate'             => $cartProduct->purchaseRate,
                        'SaleDetails_Rate'          => $cartProduct->salesRate,
                        'SaleDetails_Discount'      => $cartProduct->discount,
                        'Discount_amount'           => $cartProduct->discountAmount,
                        'SaleDetails_Tax'           => $cartProduct->vat,
                        'SaleDetails_TotalAmount'   => $cartProduct->total,
                        'isFree'                    => $cartProduct->isFree,
                        'Status'                    => 'a',
                        'AddBy'                     => $this->session->userdata("FullName"),
                        'AddTime'                   => date('Y-m-d H:i:s'),
                        'SaleDetails_BranchId'      => $this->session->userdata('BRANCHid'),
                        'is_package'                => 1
                    );
                    $this->db->insert('tbl_saledetails', $saleDetails);
                } else {
                    $saleDetails = array(
                        'SaleMaster_IDNo'           => $salesId,
                        'Product_IDNo'              => $cartProduct->productId,
                        'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                        'Purchase_Rate'             => $cartProduct->purchaseRate,
                        'SaleDetails_Rate'          => $cartProduct->salesRate,
                        'SaleDetails_Discount'      => $cartProduct->discount,
                        'Discount_amount'           => $cartProduct->discountAmount,
                        'SaleDetails_Tax'           => $cartProduct->vat,
                        'SaleDetails_TotalAmount'   => $cartProduct->total,
                        'isFree'                    => $cartProduct->isFree,
                        'Status'                    => 'a',
                        'AddBy'                     => $this->session->userdata("FullName"),
                        'AddTime'                   => date('Y-m-d H:i:s'),
                        'SaleDetails_BranchId'      => $this->session->userdata('BRANCHid')
                    );

                    $this->db->insert('tbl_saledetails', $saleDetails);

                    //update stock
                    $this->db->query("
                        update tbl_currentinventory 
                        set sales_quantity = sales_quantity + ? 
                        where product_id = ?
                        and branch_id = ?
                    ", [$cartProduct->quantity, $cartProduct->productId, $this->session->userdata('BRANCHid')]);
                }
            }

            if (count($data->banks) > 0) {
                foreach ($data->banks as $key => $item) {
                    $bank = array(
                        'sale_id'    => $salesId,
                        'bank_id'    => $item->bank_id,
                        'last_digit' => $item->last_digit,
                        'amount'     => $item->amount,
                        'AddBy'      => $this->session->userdata("FullName"),
                        'AddTime'    => date('Y-m-d H:i:s'),
                        'branchId'   => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_sales_bank', $bank);
                }
            }
            
            
            if (!empty($data->sales->referenceNo)) {
                $holdSaleId = $this->db->query("select * from tbl_hold_sale where SaleMaster_InvoiceNo = ?", $data->sales->referenceNo)->row()->SaleMaster_SlNo;
                $this->db->where('SaleMaster_InvoiceNo', $data->sales->referenceNo);
                $this->db->delete('tbl_hold_sale');

                $this->db->where('SaleMaster_IDNo', $holdSaleId);
                $this->db->delete('tbl_hold_sale_detail');
            }

            $currentDue = $data->sales->previousDue + ($data->sales->total - $data->sales->paid);
            //Send sms
            if(isset($customerInfo)){
                $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $customerId)->row();
                $sendToName = $customerInfo->owner_name  ?  $customerInfo->owner_name : $customerInfo->Customer_Name;
                $currency = $this->session->userdata('Currency_Name');
    
                 $message = "Dear {$sendToName},\nYour bill is {$currency} {$data->sales->total}. Received {$currency} {$data->sales->paid} and current due is {$currency} {$currentDue} for invoice {$invoice} From https://jmart.org";
                 $recipient = [$customerInfo->Customer_Mobile];
                // $this->sms->sendSms($recipient, $message);
                $this->sms->sendBulkSms( $recipient,  $message, 'erspro');
                
            }
            
            $this->db->trans_commit();

            $res = ['success' => true, 'message' => 'Sales Success', 'salesId' => $salesId];

        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function salesEdit($productOrService, $salesId)
    {
        $data['title'] = "Sales update";
        $sales = $this->db->query("select * from tbl_salesmaster where SaleMaster_SlNo = ?", $salesId)->row();
        $data['isService'] = $productOrService == 'product' ? 'false' : 'true';
        $data['salesId'] = $salesId;
        $data['invoice'] = $sales->SaleMaster_InvoiceNo;
        $data['content'] = $this->load->view('Administrator/sales/product_sales', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getSaleDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and c.Customer_SlNo = '$data->customerId'";
        }
        
        if (isset($data->supplierId) && $data->supplierId != '') {
            $clauses .= " and p.Supplier_SlNo = '$data->supplierId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and p.Product_SlNo = '$data->productId'";
        }

        if (isset($data->categoryId) && $data->categoryId != '') {
            $clauses .= " and pc.ProductCategory_SlNo = '$data->categoryId'";
        }

        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        $saleDetails = $this->db->query("
            select 
                sd.*,
                p.Product_Code,
                p.Product_Name,
                p.ProductCategory_ID,
                pc.ProductCategory_Name,
                sm.SaleMaster_InvoiceNo,
                sm.SaleMaster_SaleDate,
                ifnull(c.Customer_Name, 'General Customer') as Customer_Code,
                ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
                ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile
            from tbl_saledetails sd
            left join tbl_product p on p.Product_SlNo = sd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            left join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sd.Status != 'd'
            and sm.SaleMaster_branchid = ?
            $clauses
        ", $this->sbrunch)->result();

        echo json_encode($saleDetails);
    }

    public function getSalesRecord()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");
        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and sm.AddBy = '$data->userFullName'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        if (isset($data->saleId) && $data->saleId != '') {
            $clauses .= " and sm.SaleMaster_SlNo = '$data->saleId'";
        }

       
        if(isset($data->type) && $data->type == 'online') {
            $status_clauses = " and sm.web_order =  '1'";
        }else{
             if (isset($data->saleId) && $data->saleId != '') {
                $status_clauses = "";
             }else{
                $status_clauses = " and sm.web_order =  0";
             }
        }
        

        if(isset($data->status) && $data->status != '') {
            $status_clauses .= " and sm.Status =  '$data->status'";
            $status_clausessd = " and sd.Status !=  'd'";
        }else{
            $status_clausessd = "";
        }


        $sales = $this->db->query("
            select 
                sm.*,
                ifnull(c.Customer_Name, 'General Customer') as Customer_Code,
                ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
                ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile,
                ifnull(c.Customer_Address, sm.customerAddress) as Customer_Address,
                ifnull(c.Customer_Type, sm.customerType) as Customer_Type,
                ifnull(c.is_member, 'no') as is_member,
                ifnull(c.amount, 0) as amount,
                ifnull(c.point, 0) as customerPoint,
                e.Employee_Name,
                br.Brunch_name
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
            where sm.SaleMaster_branchid = '$branchId'
            $status_clauses
            $clauses
            order by sm.SaleMaster_SlNo desc
        ")->result();
        
        

        foreach ($sales as $sale) {
            $sale->saleDetails = $this->db->query("
                select 
                    sd.*,
                    p.Product_Name,
                    p.Product_Code,
                    pc.ProductCategory_Name,
                    ifnull(ed.exchange_id, 'false') as is_exchange
                from tbl_saledetails sd
                join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                left join tbl_exchange_detail ed on ed.sale_detail_id = sd.SaleDetails_SlNo
                where sd.SaleMaster_IDNo = ?
               $status_clausessd
            ", $sale->SaleMaster_SlNo)->result();

            $sale->banks = $this->db
                ->query("select sb.id, sb.sale_id, sb.bank_id, ba.account_number,
                    ba.bank_name,
                    sb.last_digit,
                    sb.amount
                    from tbl_sales_bank sb
                    left join tbl_bank_accounts ba on ba.account_id = sb.bank_id
                    where sb.sale_id = ?", $sale->SaleMaster_SlNo)->result();
        }

        echo json_encode($sales);
    }

    
    public function getSales()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");

        $limit = "";
        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and sm.AddBy = '$data->userFullName'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        if (isset($data->customerType) && $data->customerType != '') {
            $clauses .= " and sm.customerType = '$data->customerType'";
        }

        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }
        if (isset($data->name) && $data->name != '') {
            $clauses .= " and c.Customer_Code like '%$data->name%'";
        }

        if (isset($data->salesId) && $data->salesId != 0 && $data->salesId != '') {
            $clauses .= " and SaleMaster_SlNo = '$data->salesId'";
            // $saleDetails = $this->db->query("
            //     select 
            //         sd.*,
            //         p.Product_Code,
            //         p.Product_Name,
            //         pc.ProductCategory_Name,
            //         u.Unit_Name,
            //         ifnull(ed.exchange_id, 'false') as is_exchange
            //     from tbl_saledetails sd

            //     join tbl_product p on p.Product_SlNo = sd.Product_IDNo
            //     join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID

            //     join tbl_unit u on u.Unit_SlNo = p.Unit_ID
            //     left join tbl_exchange_detail ed on ed.sale_detail_id = sd.SaleDetails_SlNo
            //     where sd.SaleMaster_IDNo = ?
            //     order by sd.SaleDetails_SlNo desc
            // ", $data->salesId)->result();
            $saleDetails = $this->db->query("
                SELECT 
                    sd.*,
                    p.Product_Code,
                    ifnull(p.Product_Name, cm.packageName) as Product_Name, 
                    pc.ProductCategory_Name,
                    p.ProductsubCategory_ID,
                    u.Unit_Name,
                    
                    IFNULL(ed.exchange_id, 'false') AS is_exchange
                FROM tbl_saledetails sd
                
                -- Join product if not package
                LEFT JOIN tbl_product p 
                    ON p.Product_SlNo = sd.Product_IDNo
                    AND sd.is_package = 0

                -- Join product category if not package
                LEFT JOIN tbl_productcategory pc 
                    ON pc.ProductCategory_SlNo = p.ProductCategory_ID
                    AND sd.is_package = 0

                -- Join combo if package
                LEFT JOIN tbl_combomaster cm 
                    ON cm.ComboId = sd.Product_IDNo
                    AND sd.is_package = 1

                -- Join unit only if not package
                LEFT JOIN tbl_unit u 
                    ON u.Unit_SlNo = p.Unit_ID
                    AND sd.is_package = 0

                LEFT JOIN tbl_exchange_detail ed 
                    ON ed.sale_detail_id = sd.SaleDetails_SlNo

                WHERE sd.SaleMaster_IDNo = ?
                ORDER BY sd.SaleDetails_SlNo DESC
            ", $data->salesId)->result();


            $res['saleDetails'] = $saleDetails;

            $res['banks'] = $this->db
                ->query("select sb.id, sb.sale_id, sb.bank_id, ba.account_number,
                    ba.bank_name,
                    sb.last_digit,
                    sb.amount
                    from tbl_sales_bank sb
                    left join tbl_bank_accounts ba on ba.account_id = sb.bank_id
                    where sb.sale_id = ?", $data->salesId)->result();
        }
        
               // Modify if online
        // if (isset($data->type) && $data->type == 'online') {
        //     $clauses .= "and sm.web_order = 1 and sm.Status != 'd'";
        // }else{
        //     $clauses .= "and sm.web_order = 0 and sm.Status != 'd'";
        // }

         if(isset($data->type) && $data->type == 'online') {
            $clauses .= " and sm.web_order =  '1'";
        }else{
              if (isset($data->salesId) && $data->salesId != '') {
             }else{
                
             $clauses .= " and sm.web_order =  '0'";
                 
             }
        }

        if(isset($data->status) && $data->status != '') {
            $clauses .= " and sm.Status =  '$data->status'";
        }else{
             $clauses .= " and sm.Status !=  'd'";
        }



        $sales = $this->db->query("
            select 
            concat(sm.SaleMaster_InvoiceNo, ' - ', ifnull(c.Customer_Name, sm.customerName)) as invoice_text,
            sm.*,
            ifnull(c.Customer_Code, 'General Customer') as Customer_Code,
            ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
            ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile,
            ifnull(c.Customer_Address, sm.customerAddress) as Customer_Address,
            ifnull(c.Customer_Type, sm.customerType) as Customer_Type,
            ifnull(c.is_member, 'no') as is_member,
            ifnull(c.amount, 0) as amount,
            ifnull(c.point, 0) as customerPoint,
            e.Employee_Name,
            br.Brunch_name,
            ba.account_name,
            ba.account_number,
            ba.bank_name
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
            left join tbl_bank_accounts ba on ba.account_id = sm.bank_id
            where sm.SaleMaster_branchid = '$branchId'
           
            $clauses
            order by sm.SaleMaster_SlNo desc
            $limit
        ")->result();

        $res['sales'] = $sales;

        echo json_encode($res);
    }
    
    
    public function getHoldSale()
    {
        $data = json_decode($this->input->raw_input_stream);
        $branchId = $this->session->userdata("BRANCHid");

        $limit = "";
        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and sm.AddBy = '$data->userFullName'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->employeeId) && $data->employeeId != '') {
            $clauses .= " and sm.employee_id = '$data->employeeId'";
        }

        if (isset($data->customerType) && $data->customerType != '') {
            $clauses .= " and sm.customerType = '$data->customerType'";
        }

        if (isset($data->forSearch) && $data->forSearch != '') {
            $limit .= "limit 20";
        }
        if (isset($data->name) && $data->name != '') {
            $clauses .= " and c.Customer_Code like '%$data->name%'";
        }

        if (isset($data->referenceNo) && $data->referenceNo != 0 && $data->referenceNo != '') {
            $clauses .= " and SaleMaster_InvoiceNo = '$data->referenceNo'";

            $saleDetails = $this->db->query("
                SELECT 
                    sd.*,
                    p.Product_Code,
                    ifnull(p.Product_Name, cm.packageName) as Product_Name, 
                    pc.ProductCategory_Name,
                    u.Unit_Name
                FROM tbl_hold_sale_detail sd
                
                -- Join product if not package
                LEFT JOIN tbl_product p 
                    ON p.Product_SlNo = sd.Product_IDNo
                    AND sd.is_package = 0

                -- Join product category if not package
                LEFT JOIN tbl_productcategory pc 
                    ON pc.ProductCategory_SlNo = p.ProductCategory_ID
                    AND sd.is_package = 0

                -- Join combo if package
                LEFT JOIN tbl_combomaster cm 
                    ON cm.ComboId = sd.Product_IDNo
                    AND sd.is_package = 1

                -- Join unit only if not package
                LEFT JOIN tbl_unit u 
                    ON u.Unit_SlNo = p.Unit_ID
                    AND sd.is_package = 0

                join tbl_hold_sale sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo

                WHERE sm.SaleMaster_InvoiceNo = ?
                ORDER BY sd.SaleDetails_SlNo DESC
            ", $data->referenceNo)->result();


            $res['saleDetails'] = $saleDetails;
        }

        // Modify if online
        if (isset($data->type) && $data->type == 'online') {
            $clauses = "and sm.web_order = 1 and sm.Status != 'd'";
        } else {
            $clauses = "and sm.Status != 'd'";
        }



        $sales = $this->db->query("
            select 
            concat(sm.SaleMaster_InvoiceNo, ' - ', ifnull(c.Customer_Name, sm.customerName)) as invoice_text,
            sm.*,
            ifnull(c.Customer_Code, 'General Customer') as Customer_Code,
            ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
            ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile,
            ifnull(c.Customer_Address, sm.customerAddress) as Customer_Address,
            ifnull(c.Customer_Type, sm.customerType) as Customer_Type,
            ifnull(c.is_member, 'no') as is_member,
            ifnull(c.amount, 0) as amount,
            ifnull(c.point, 0) as customerPoint,
            e.Employee_Name,
            br.Brunch_name
            from tbl_hold_sale sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            left join tbl_employee e on e.Employee_SlNo = sm.employee_id
            left join tbl_brunch br on br.brunch_id = sm.SaleMaster_branchid
            where sm.SaleMaster_branchid = '$branchId'
            $clauses
            order by sm.SaleMaster_SlNo desc
            $limit
        ")->result();

        $res['sales'] = $sales;

        echo json_encode($res);
    }
    
    
    

    public function orderStatus()
    {
        $res = ['success' => false, 'message' => ''];
        // try {
        $this->db->trans_begin();
        $data = json_decode($this->input->raw_input_stream);
        $salesId = $data->saleId;
        $status = $data->status;

        // $this->db->query("update tbl_salesmaster set Status = ? where SaleMaster_SlNo = ?", [$status, $salesId]);
        if ($status == 'a') {
            $sales_record = $this->db->query("select * from tbl_salesmaster where SaleMaster_SlNo = ?", [$salesId])->row();

            $this->db->query("update tbl_salesmaster set Status = ?, SaleMaster_cashPaid = ?, SaleMaster_DueAmount = ? where SaleMaster_SlNo = ?", [$status, $sales_record->SaleMaster_cashPaid + $sales_record->SaleMaster_DueAmount, 0, $salesId]);
            $sales_details = $this->db->query("select * from tbl_saledetails where SaleMaster_IDNo = ?", [$salesId])->result();
            foreach ($sales_details as $key => $value) {

                $this->db->query("update tbl_saledetails set status =  ? where SaleDetails_SlNo = ?", [$status, $value->SaleDetails_SlNo]);
                // update inventory
                $this->db->query("update tbl_currentinventory set sales_quantity = sales_quantity + ? where product_id = ?", [$value->SaleDetails_TotalQuantity, $value->Product_IDNo]);
            }
        } else {
            $this->db->query("update tbl_salesmaster set Status = ? where SaleMaster_SlNo = ?", [$status, $salesId]);
            $sales_details = $this->db->query("select * from tbl_saledetails where SaleMaster_IDNo = ?", [$salesId])->result();
            foreach ($sales_details as $key => $value) {
                $this->db->query("update tbl_saledetails set status =  ? where SaleDetails_SlNo = ?", [$status, $value->SaleDetails_SlNo]);
                // update inventory
                // $this->db->query("update tbl_currentinventory set sales_quantity = sales_quantity - ? where product_id = ?", [$value->SaleDetails_TotalQuantity, $value->Product_IDNo]);
            }
        }

        // } catch (Exception $ex) {
        //     $res = ['success' => false, 'message' => $ex->getMessage()];
        // }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => 'Something went wrong'];
        } else {
            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Status updated'];
        }

        echo json_encode($res);
    }
  public function orderDeliveryStatus()
    {
        // Decode JSON input
        $data = json_decode($this->input->raw_input_stream);
        if (empty($data) || !isset($data->saleId, $data->status)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            return;
        }

        $salesId = $data->saleId;
        $status = $data->status;

        // Start transaction
        $this->db->trans_begin();

        try {
            // Update delivery status
            $this->db->query(
                "UPDATE tbl_salesmaster 
                SET delivery_status = ? 
                WHERE SaleMaster_SlNo = ?",
                [$status, $salesId]
            );

            // Complete transaction
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
            } else {
                $this->db->trans_commit();
                echo json_encode(['success' => true, 'message' => 'Order status updated']);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function updateSales()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);
            $salesId = $data->sales->salesId;

            $customerId = $data->sales->customerId;
            if (isset($data->customer)) {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                unset($customer['Customer_Type']);
                $mobile_count = $this->db->query("select * from tbl_customer where Customer_Mobile = ? and Customer_brunchid = ?", [$data->customer->Customer_Mobile, $this->session->userdata("BRANCHid")])->row();

                if ($data->customer->Customer_Type == 'N' && empty($mobile_count)) {
                    $customer['Customer_Code'] = $this->mt->generateCustomerCode();
                    $customer['Customer_Type'] = $data->sales->salesType;
                    $customer['Customer_Credit_Limit'] = $data->sales->total;
                    $customer['status'] = 'a';
                    $customer['AddBy'] = $this->session->userdata("FullName");
                    $customer['AddTime'] = date("Y-m-d H:i:s");
                    $customer['Customer_brunchid'] = $this->session->userdata("BRANCHid");
                    $this->db->insert('tbl_customer', $customer);
                    $customerId = $this->db->insert_id();
                }
            }

            $sales = array(
                'employee_id'                    => $data->sales->employeeId,
                'SaleMaster_SaleDate'            => $data->sales->salesDate,
                'SaleMaster_SaleType'            => $data->sales->salesType,
                'SaleMaster_SubTotalAmount'      => $data->sales->subTotal,
                'SaleMaster_TotalDiscountAmount' => $data->sales->discount,
                'pointAmount'                    => $data->sales->pointAmount,
                'SaleMaster_TaxAmount'           => $data->sales->vat,
                'SaleMaster_Freight'             => $data->sales->transportCost,
                'SaleMaster_TotalSaleAmount'     => $data->sales->total,
                'SaleMaster_cashPaid'            => $data->sales->cashPaid,
                'SaleMaster_bankPaid'            => $data->sales->bankPaid,
                'bank_id'                        => $data->sales->bankPaid > 0 ? $data->sales->bank_id : NULL,
                'SaleMaster_PaidAmount'          => $data->sales->cashPaid + $data->sales->bankPaid,
                'returnAmount'                   => $data->sales->returnAmount,
                'SaleMaster_DueAmount'           => $data->sales->due,
                'SaleMaster_Previous_Due'        => $data->sales->previousDue,
                'SaleMaster_Description'         => $data->sales->note,
                "UpdateBy"                       => $this->session->userdata("FullName"),
                'UpdateTime'                     => date("Y-m-d H:i:s"),
                "SaleMaster_branchid"            => $this->session->userdata("BRANCHid")
            );

            if ($data->customer->Customer_Type == 'G') {
                $sales['SalseCustomer_IDNo'] = Null;
                $sales['customerType']       = "G";
                $sales['customerName']       = $data->customer->Customer_Name;
                $sales['customerMobile']     = $data->customer->Customer_Mobile;
                $sales['customerAddress']    = $data->customer->Customer_Address;
            } else {
                $sales['customerType']       = $data->sales->salesType;
                $sales['SalseCustomer_IDNo'] = $customerId;

                if ($data->customer->is_member == 'yes') {
                    $excludedAmount = 0;
                    if (!empty($data->cart) && is_array($data->cart)) {
                        foreach ($data->cart as $cartItem) {
                            $prodRow = $this->db->query("select ProductsubCategory_ID from tbl_product where Product_SlNo = ?", $cartItem->productId)->row();
                            $subcatId = $prodRow ? $prodRow->ProductsubCategory_ID : null;
                            if ($subcatId == 49) {
                                $excludedAmount += isset($cartItem->total) ? $cartItem->total : ($cartItem->salesRate * $cartItem->quantity);
                            }
                        }
                    }

                    $baseAmountForPoint = max(0, $data->sales->total - $excludedAmount);
                    $new_points = ($data->customer->amount > 0) ? floor($baseAmountForPoint / $data->customer->amount) : 0;
                    $old_sale_point = $this->db->query("select point from tbl_salesmaster where SaleMaster_SlNo = ?", $salesId)->row()->point;
                    $old_point = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $customerId)->row()->point - $old_sale_point;
                    $pointTotal = $old_point + $new_points;
                    $this->db
                        ->where('Customer_SlNo', $customerId)
                        ->update('tbl_customer', [
                            'is_member' => $data->customer->is_member,
                            'amount' => $data->customer->amount,
                            'point' => ($pointTotal - $data->sales->pointAmount)
                        ]);

                    $sales['point'] = $new_points;
                }
            }

            $this->db->where('SaleMaster_SlNo', $salesId);
            $this->db->update('tbl_salesmaster', $sales);

            $currentSaleDetails = $this->db
                ->select('sd.*, p.Product_Name')
                ->join('tbl_product p', 'p.Product_SlNo = sd.Product_IDNo', 'left')
                ->where('sd.SaleMaster_IDNo', $salesId)
                ->get('tbl_saledetails sd')
                ->result();
            $this->db->query("delete from tbl_saledetails where SaleMaster_IDNo = ?", $salesId);

            foreach ($currentSaleDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory 
                    set sales_quantity = sales_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->SaleDetails_TotalQuantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            foreach ($data->cart as $key => $item) {
                $curentstock = $this->mt->productStock($item->productId);
                if ($item->quantity > $curentstock) {
                    $productName = $item->Product_Name;
                    echo json_encode(['success' => false, 'message' => "Stock unavailable. Please check {$productName}"]);
                    exit;
                }
            }

            foreach ($data->cart as $cartProduct) {
                $saleDetails = array(
                    'SaleMaster_IDNo'           => $salesId,
                    'Product_IDNo'              => $cartProduct->productId,
                    'SaleDetails_TotalQuantity' => $cartProduct->quantity,
                    'Purchase_Rate'             => $cartProduct->purchaseRate,
                    'SaleDetails_Rate'          => $cartProduct->salesRate,
                    'SaleDetails_Tax'           => $cartProduct->vat,
                    'SaleDetails_TotalAmount'   => $cartProduct->total,
                    'isFree'                    => $cartProduct->isFree,
                    'Status'                    => 'a',
                    'AddBy'                     => $this->session->userdata("FullName"),
                    'AddTime'                   => date('Y-m-d H:i:s'),
                    'SaleDetails_BranchId'      => $this->session->userdata("BRANCHid")
                );

                $this->db->insert('tbl_saledetails', $saleDetails);

                $this->db->query("
                    update tbl_currentinventory 
                    set sales_quantity = sales_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$cartProduct->quantity, $cartProduct->productId, $this->session->userdata('BRANCHid')]);
            }

            //old bank list delete
            $this->db->where('sale_id', $salesId)->delete('tbl_sales_bank');
            if (count($data->banks) > 0) {
                foreach ($data->banks as $key => $item) {
                    $bank = array(
                        'sale_id'    => $salesId,
                        'bank_id'    => $item->bank_id,
                        'last_digit' => $item->last_digit,
                        'amount'     => $item->amount,
                        'AddBy'      => $this->session->userdata("FullName"),
                        'AddTime'    => date('Y-m-d H:i:s'),
                        'UpdateBy'   => $this->session->userdata("FullName"),
                        'UpdateTime' => date('Y-m-d H:i:s'),
                        'branchId'   => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_sales_bank', $bank);
                }
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'Sales Updated', 'salesId' => $salesId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getSaleDetailsForReturn()
    {
        $data = json_decode($this->input->raw_input_stream);
        $saleDetails = $this->db->query("
            select
                sd.*,
                sd.SaleDetails_Rate as return_rate,
                p.Product_Name,
                p.Product_Code,
                pc.ProductCategory_Name,
                (
                    select ifnull(sum(srd.SaleReturnDetails_ReturnQuantity), 0)
                    from tbl_salereturndetails srd
                    join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                    where sr.Status = 'a'
                    and srd.SaleReturnDetailsProduct_SlNo = sd.Product_IDNo
                    and sr.SaleMaster_InvoiceNo = sm.SaleMaster_InvoiceNo
                ) as returned_quantity,
                (
                    select ifnull(sum(srd.SaleReturnDetails_ReturnAmount), 0)
                    from tbl_salereturndetails srd
                    join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
                    where sr.Status = 'a'
                    and srd.SaleReturnDetailsProduct_SlNo = sd.Product_IDNo
                    and sr.SaleMaster_InvoiceNo = sm.SaleMaster_InvoiceNo
                ) as returned_amount
            from tbl_saledetails sd
            join tbl_salesmaster sm on sm.SaleMaster_SlNo = sd.SaleMaster_IDNo
            join tbl_product p on p.Product_SlNo = sd.Product_IDNo
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            where sm.SaleMaster_SlNo = ?
        ", $data->salesId)->result();

        echo json_encode($saleDetails);
    }

    public function addSalesReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $salesReturn = array(
                'SaleMaster_InvoiceNo' => $data->invoice->SaleMaster_InvoiceNo,
                'SaleReturn_ReturnDate' => $data->salesReturn->returnDate,
                'SaleReturn_ReturnAmount' => $data->salesReturn->total,
                'SaleReturn_Description' => $data->salesReturn->note,
                'Status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'SaleReturn_brunchId' => $this->session->userdata("BRANCHid")
            );

            $this->db->insert('tbl_salereturn', $salesReturn);
            $salesReturnId = $this->db->insert_id();

            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'SaleReturn_IdNo' => $salesReturnId,
                    'SaleReturnDetailsProduct_SlNo' => $product->Product_IDNo,
                    'SaleReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'SaleReturnDetails_ReturnAmount' => $product->return_amount,
                    'Status' => 'a',
                    'AddBy' => $this->session->userdata("FullName"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'SaleReturnDetails_brunchID' => $this->session->userdata("BRANCHid")
                );

                $this->db->insert('tbl_salereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set sales_return_quantity = sales_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $data->invoice->SalseCustomer_IDNo)->row();
            if (empty($customerInfo)) {
                $customerPayment = array(
                    'CPayment_date' => $data->salesReturn->returnDate,
                    'CPayment_invoice' => $data->invoice->SaleMaster_InvoiceNo,
                    'CPayment_customerID' => NULL,
                    'CPayment_TransactionType' => 'CP',
                    'CPayment_amount' => $totalReturnAmount,
                    'CPayment_Paymentby' => 'cash',
                    'CPayment_brunchid' => $this->session->userdata("BRANCHid"),
                    'CPayment_previous_due' => 0,
                    'CPayment_Addby' => $this->session->userdata("FullName"),
                    'CPayment_AddDAte' => date('Y-m-d H:i:s'),
                    'CPayment_status' => 'a'
                );

                $this->db->insert('tbl_customer_payment', $customerPayment);
            }

            $res = ['success' => true, 'message' => 'Return Success', 'id' => $salesReturnId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateSalesReturn()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $salesReturnId = $data->salesReturn->returnId;

            $oldReturn = $this->db->query("select * from tbl_salereturn where SaleReturn_SlNo = ?", $salesReturnId)->row();
            $oldDetails = $this->db->query("select * from tbl_salereturndetails sr where sr.SaleReturn_IdNo = ?", $salesReturnId)->result();

            $salesReturn = array(
                'SaleMaster_InvoiceNo' => $data->invoice->SaleMaster_InvoiceNo,
                'SaleReturn_ReturnDate' => $data->salesReturn->returnDate,
                'SaleReturn_ReturnAmount' => $data->salesReturn->total,
                'SaleReturn_Description' => $data->salesReturn->note,
                'Status' => 'a',
                'AddBy' => $this->session->userdata("FullName"),
                'AddTime' => date('Y-m-d H:i:s'),
                'SaleReturn_brunchId' => $this->session->userdata("BRANCHid")
            );

            $this->db->where('SaleReturn_SlNo', $salesReturnId)->update('tbl_salereturn', $salesReturn);

            foreach ($oldDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory 
                    set sales_return_quantity = sales_return_quantity - ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->SaleReturnDetails_ReturnQuantity, $product->SaleReturnDetailsProduct_SlNo, $this->session->userdata('BRANCHid')]);
            }

            $this->db->query("delete from tbl_salereturndetails where SaleReturn_IdNo = ?", $salesReturnId);

            $totalReturnAmount = 0;
            foreach ($data->cart as $product) {
                $returnDetails = array(
                    'SaleReturn_IdNo' => $salesReturnId,
                    'SaleReturnDetailsProduct_SlNo' => $product->Product_IDNo,
                    'SaleReturnDetails_ReturnQuantity' => $product->return_quantity,
                    'SaleReturnDetails_ReturnAmount' => $product->return_amount,
                    'Status' => 'a',
                    'AddBy' => $this->session->userdata("FullName"),
                    'AddTime' => date('Y-m-d H:i:s'),
                    'SaleReturnDetails_brunchID' => $this->session->userdata("BRANCHid")
                );

                $this->db->insert('tbl_salereturndetails', $returnDetails);

                $totalReturnAmount += $product->return_amount;

                $this->db->query("
                    update tbl_currentinventory 
                    set sales_return_quantity = sales_return_quantity + ? 
                    where product_id = ?
                    and branch_id = ?
                ", [$product->return_quantity, $product->Product_IDNo, $this->session->userdata('BRANCHid')]);
            }

            $customerInfo = $this->db->query("select * from tbl_customer where Customer_SlNo = ?", $data->invoice->SalseCustomer_IDNo)->row();
            if (empty($customerInfo)) {
                $this->db->query("
                    delete from tbl_customer_payment 
                    where CPayment_invoice = ? 
                    and CPayment_customerID is null
                    and CPayment_amount = ?
                    limit 1
                ", [
                    $data->invoice->SaleMaster_InvoiceNo,
                    $oldReturn->SaleReturn_ReturnAmount
                ]);

                $customerPayment = array(
                    'CPayment_date' => $data->salesReturn->returnDate,
                    'CPayment_invoice' => $data->invoice->SaleMaster_InvoiceNo,
                    'CPayment_customerID' => NULL,
                    'CPayment_TransactionType' => 'CP',
                    'CPayment_amount' => $totalReturnAmount,
                    'CPayment_Paymentby' => 'cash',
                    'CPayment_brunchid' => $this->session->userdata("BRANCHid"),
                    'CPayment_previous_due' => 0,
                    'CPayment_Addby' => $this->session->userdata("FullName"),
                    'CPayment_AddDAte' => date('Y-m-d H:i:s'),
                    'CPayment_status' => 'a'
                );

                $this->db->insert('tbl_customer_payment', $customerPayment);
            }

            $res = ['success' => true, 'message' => 'Return Updated', 'id' => $salesReturnId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteSaleReturn()
    {
        $data = json_decode($this->input->raw_input_stream);

        $res = ['success' => false, 'message' => ''];

        try {
            $data = json_decode($this->input->raw_input_stream);

            $oldReturn = $this->db->query("
                select 
                    sr.*,
                    c.Customer_SlNo,
                    c.Customer_Code,
                    c.Customer_Name,
                    c.Customer_Type
                from tbl_salereturn sr
                join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
                join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
                where sr.SaleReturn_SlNo = ?
            ", $data->id)->row();

            $this->db->query("delete from tbl_salereturn where SaleReturn_SlNo = ?", $data->id);

            $returnDetails = $this->db->query("select * from tbl_salereturndetails srd where srd.SaleReturn_IdNo = ?", $data->id)->result();

            if (empty($oldReturn)) {
                $this->db->query("
                    delete from tbl_customer_payment 
                    where CPayment_invoice = ? 
                    and CPayment_customerID is null
                    and CPayment_amount = ?
                    limit 1
                ", [
                    $oldReturn->SaleMaster_InvoiceNo,
                    $oldReturn->SaleReturn_ReturnAmount
                ]);
            }

            foreach ($returnDetails as $product) {
                $this->db->query("
                    update tbl_currentinventory set 
                    sales_return_quantity = sales_return_quantity - ? 
                    where product_id = ? 
                    and branch_id = ?
                ", [$product->SaleReturnDetails_ReturnQuantity, $product->SaleReturnDetailsProduct_SlNo, $this->sbrunch]);
            }

            $this->db->query("delete from tbl_salereturndetails where SaleReturn_IdNo = ?", $data->id);
            $res = ['success' => true, 'message' => 'Sale return deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getSaleReturns()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if ((isset($data->fromDate) && $data->fromDate != '') && (isset($data->toDate) && $data->toDate != '')) {
            $clauses .= " and sr.SaleReturn_ReturnDate between '$data->fromDate' and '$data->toDate'";
        }

        if (isset($data->userFullName) && $data->userFullName != '') {
            $clauses .= " and sr.AddBy = '$data->userFullName'";
        }

        if (isset($data->id) && $data->id != '') {
            $clauses .= " and sr.SaleReturn_SlNo = '$data->id'";

            $res['returnDetails'] = $this->db->query("
                SELECT
                    srd.*,
                    p.Product_Code,
                    p.Product_Name
                from tbl_salereturndetails srd
                join tbl_product p on p.Product_SlNo = srd.SaleReturnDetailsProduct_SlNo
                where srd.SaleReturn_IdNo = ?
            ", $data->id)->result();
        }

        $res['returns'] = $this->db->query("
            select  
                sr.*,
                c.Customer_SlNo,
                ifnull(c.Customer_Name, 'General Customer') as Customer_Code,
                ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
                ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile,
                ifnull(c.Customer_Address, sm.customerAddress) as Customer_Address,
                c.owner_name,
                sm.SaleMaster_TotalDiscountAmount,
                sm.SaleMaster_SlNo
            from tbl_salereturn sr
            join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sr.Status = 'a'
            and sr.SaleReturn_brunchId = '$this->sbrunch'
            $clauses
        ")->result();

        echo json_encode($res);
    }

    public function getSaleReturnDetails()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $clauses .= " and sr.SaleReturn_ReturnDate between '$data->dateFrom' and '$data->dateTo'";
        }

        if (isset($data->customerId) && $data->customerId != '') {
            $clauses .= " and sm.SalseCustomer_IDNo = '$data->customerId'";
        }

        if (isset($data->productId) && $data->productId != '') {
            $clauses .= " and srd.SaleReturnDetailsProduct_SlNo = '$data->productId'";
        }

        $returnDetails = $this->db->query("
            select
                srd.*,
                p.Product_Code,
                p.Product_Name,
                sr.SaleMaster_InvoiceNo,
                sr.SaleReturn_ReturnDate,
                sr.SaleReturn_Description,
                sm.SalseCustomer_IDNo,
                ifnull(c.Customer_Name, 'General Customer') as Customer_Code,
                ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
                ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile,
                ifnull(c.Customer_Address, sm.customerAddress) as Customer_Address
            from tbl_salereturndetails srd
            join tbl_product p on p.Product_SlNo = srd.SaleReturnDetailsProduct_SlNo
            join tbl_salereturn sr on sr.SaleReturn_SlNo = srd.SaleReturn_IdNo
            join tbl_salesmaster sm on sm.SaleMaster_InvoiceNo = sr.SaleMaster_InvoiceNo
            join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sr.SaleReturn_brunchId = ?
            $clauses
        ", $this->session->userdata('BRANCHid'))->result();

        echo json_encode($returnDetails);
    }

    public function saleReturnInvoice($id)
    {
        $data['title'] = "Sale return Invoice";
        $data['id'] = $id;
        $data['content'] = $this->load->view('Administrator/sales/sale_return_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function salesreturn()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }

        $data['returnId'] = 0;
        $data['title'] = " Sales Return";
        $data['content'] = $this->load->view('Administrator/sales/salseReturn', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function salesReturnEdit($id)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }

        $data['returnId'] = $id;
        $data['title'] = " Sales Return";
        $data['content'] = $this->load->view('Administrator/sales/salseReturn', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function sales_invoice()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Invoice";
        $data['content'] = $this->load->view('Administrator/sales/sales_invoice', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function sales_record()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Record";
        $data['content'] = $this->load->view('Administrator/sales/sales_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function saleInvoicePrint($saleId)
    {
        $data['title'] = "Sales Invoice";
        $data['salesId'] = $saleId;
        $data['content'] = $this->load->view('Administrator/sales/sellAndreport', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    function return_list()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Return List";
        $data['content'] = $this->load->view('Administrator/sales/sales_return_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    function saleReturnDetails()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Sales Return Details";
        $data['content'] = $this->load->view('Administrator/sales/sale_return_details', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    function sales_return_record()
    {
        $this->load->view('Administrator/sales/return_list');
    }



    /*Delete Sales Record*/
    public function  deleteSales()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $saleId = $data->saleId;

            $sale = $this->db->select('*')->where('SaleMaster_SlNo', $saleId)->get('tbl_salesmaster')->row();
            if ($sale->Status != 'a') {
                if($sale->web_order == 1){
                    $this->db->update('tbl_salesmaster', ['Status' => 'd'], ['SaleMaster_SlNo' => $saleId]);
                    $sale_details = $this->db->select('*')->where('SaleMaster_IDNo', $saleId)->get('tbl_saledetails')->result();
                    foreach ($sale_details as $detail) {
                        $this->db->update('tbl_saledetails', ['status' => 'd'], ['SaleDetails_SlNo' => $detail->SaleDetails_SlNo]);
                    }
                    $res = ['success' => true, 'message' => 'Sale deleted'];
                }else{
                    $res = ['success' => false, 'message' => 'Sale not found'];
                }
                echo json_encode($res);
                exit;
            }

            $returnCount = $this->db->query("select * from tbl_salereturn sr where sr.SaleMaster_InvoiceNo = ? and sr.Status = 'a'", $sale->SaleMaster_InvoiceNo)->num_rows();

            if ($returnCount != 0) {
                
                $this->db->update('tbl_salesmaster', ['Status' => 'd'], ['SaleMaster_SlNo' => $saleId]);
                $sale_details = $this->db->select('*')->where('SaleMaster_IDNo', $saleId)->get('tbl_saledetails')->result();
                foreach ($sale_details as $detail) {
                    $this->db->update('tbl_saledetails', ['status' => 'd'], ['SaleDetails_SlNo' => $detail->SaleDetails_SlNo]);
                }
                $res = ['success' => false, 'message' => 'Uncomplete Sale Deleted Successfully'];
                echo json_encode($res);
                exit;
            }

            /*Get Sale Details Data*/
            $saleDetails = $this->db->select('Product_IDNo, SaleDetails_TotalQuantity')->where('SaleMaster_IDNo', $saleId)->get('tbl_saledetails')->result();

            foreach ($saleDetails as $detail) {
                /*Get Product Current Quantity*/
                $totalQty = $this->db->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $sale->SaleMaster_branchid])->get('tbl_currentinventory')->row()->sales_quantity;

                /* Subtract Product Quantity form  Current Quantity  */
                $newQty = $totalQty - $detail->SaleDetails_TotalQuantity;

                /*Update Sales Inventory*/
                $this->db->set('sales_quantity', $newQty)->where(['product_id' => $detail->Product_IDNo, 'branch_id' => $sale->SaleMaster_branchid])->update('tbl_currentinventory');
            }

            if ($sale->customerType != 'G') {
                $this->db->query("update tbl_customer set point = (point + ?) - ? where Customer_SlNo = ?", [$sale->pointAmount, $sale->point, $sale->SalseCustomer_IDNo]);
            }
            /*Delete Sale Details*/
            $this->db->set('Status', 'd')->where('SaleMaster_IDNo', $saleId)->update('tbl_saledetails');

            /*Delete Sale Master Data*/
            $this->db->set('Status', 'd')->where('SaleMaster_SlNo', $saleId)->update('tbl_salesmaster');
            $res = ['success' => true, 'message' => 'Sale deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }







    //     public function product_delete($id = null){
    //       // $id = $this->input->post('deleted');
    //        // $id = $this->input->post('SaleDetails_SlNo');
    //        $Product_IDNo = $this->input->post('Product_IDNo');
    //        $SaleMaster_SlNo = $this->input->post('SaleMaster_SlNo');
    //        $SaleMaster_InvoiceNo = $this->input->post('SaleMaster_InvoiceNo');
    //        $SaleDetails_TotalQuantity = $this->input->post('SaleDetails_TotalQuantity');
    //        $SaleDetailsPrice = $this->input->post('SaleDetailsPrice');
    //        $SaleMaster_TotalSaleAmount = $this->input->post('SaleMaster_TotalSaleAmount');
    //        $SaleMaster_TaxAmount = $this->input->post('SaleMaster_TaxAmount');
    //
    //        $fld = 'SaleDetails_SlNo';
    //        $delete = $this->mt->delete_data("tbl_saledetails", $id, $fld);
    //        if(isset($delete))
    //        {
    //            $sirow = $this->db->where('sellProduct_IdNo',$Product_IDNo)->get('tbl_saleinventory')->row();
    //
    //
    //            $data1['SaleInventory_TotalQuantity'] = $sirow->SaleInventory_TotalQuantity-$SaleDetails_TotalQuantity;
    //            $this->Billing_model->update_saleinventory("tbl_saleinventory",$data1,$Product_IDNo);
    //
    //            $data2['SaleMaster_TotalSaleAmount'] = $SaleMaster_TotalSaleAmount-$SaleDetailsPrice;
    //            $total = $data2['SaleMaster_TotalSaleAmount']/100*$SaleMaster_TaxAmount+$data2['SaleMaster_TotalSaleAmount'];
    //            //$data2['SaleMaster_PaidAmount'] = $total;
    //            $data2['SaleMaster_SubTotalAmount'] = $total;
    //            $this->Billing_model->update_salesmaster("tbl_salesmaster",$data2,$SaleMaster_SlNo);
    //        }
    //        redirect('Administrator/Sales/sales_update_form/'.$SaleMaster_SlNo,'refresh');
    //    }

    function profitLoss()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Profit & Loss ";
        $data['products'] = $this->Product_model->products_by_brunch();
        $data['content'] = $this->load->view('Administrator/sales/profit_loss', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    function profitLossSearch()
    {
        $data['searchtype'] = $this->input->post('searchtype');
        $data['ProductID'] = $this->input->post('ProductID');
        $data['startdate'] = $this->input->post('startdate');
        $data['enddate'] = $this->input->post('enddate');
        $this->session->set_userdata($data);
        $this->load->view('Administrator/sales/profit_loss_search', $data);
    }

    public function getProfitLoss()
    {
        $data = json_decode($this->input->raw_input_stream);

        $customerClause = "";
        if ($data->customer != null && $data->customer != '') {
            $customerClause = " and sm.SalseCustomer_IDNo = '$data->customer'";
        }

        $dateClause = "";
        if (($data->dateFrom != null && $data->dateFrom != '') && ($data->dateTo != null && $data->dateTo != '')) {
            $dateClause = " and sm.SaleMaster_SaleDate between '$data->dateFrom' and '$data->dateTo'";
        }


        $sales = $this->db->query("
            select 
                sm.*,
                ifnull(c.Customer_Code, 'Cash Customer') as Customer_Code,
                ifnull(c.Customer_Name, sm.customerName) as Customer_Name,
                ifnull(c.Customer_Mobile, sm.customerMobile) as Customer_Mobile
            from tbl_salesmaster sm
            left join tbl_customer c on c.Customer_SlNo = sm.SalseCustomer_IDNo
            where sm.SaleMaster_branchid = ? 
            and sm.Status = 'a'
            $customerClause $dateClause
        ", $this->session->userdata('BRANCHid'))->result();

        foreach ($sales as $sale) {
            $sale->saleDetails = $this->db->query("
                select
                    sd.*,
                    p.Product_Code,
                    p.Product_Name,
                    (sd.Purchase_Rate * sd.SaleDetails_TotalQuantity) as purchased_amount,
                    (select sd.SaleDetails_TotalAmount - purchased_amount) as profit_loss
                from tbl_saledetails sd 
                left join tbl_product p on p.Product_SlNo = sd.Product_IDNo
                where sd.SaleMaster_IDNo = ?
            ", $sale->SaleMaster_SlNo)->result();
        }

        echo json_encode($sales);
    }

    public function chalan($saleId)
    {
        $data['title'] = "Chalan Invoice";
        $data['saleId'] = $saleId;
        $data['content'] = $this->load->view('Administrator/sales/chalan', $data, true);
        $this->load->view('Administrator/index', $data);
    }

    public function checkSaleReturn($invoice)
    {
        $res = ['found' => false];

        $returnCount = $this->db->query("select * from tbl_salereturn where SaleMaster_InvoiceNo = ? and Status = 'a'", $invoice)->num_rows();

        if ($returnCount != 0) {
            $res = ['found' => true];
        }

        echo json_encode($res);
    }
}