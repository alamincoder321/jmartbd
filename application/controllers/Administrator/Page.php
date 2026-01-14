<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        date_default_timezone_set('Asia/Dhaka');
    }
    public function index()
    {
        $data['title'] = "Dashboard";
        $data['international_cash_balance'] = $this->mt->internationalCashBalance()->balance ?? 0;
        $data['content'] = $this->load->view('Administrator/dashboard', $data, TRUE);
        $this->load->view('Administrator/master_dashboard', $data);
    }
    public function module($value)
    {
        $data['title'] = "Dashboard";

        $sdata['module'] = $value;
        $this->session->set_userdata($sdata);
        $data['international_cash_balance'] = $this->mt->internationalCashBalance()->balance ?? 0;
        $data['content'] = $this->load->view('Administrator/dashboard', $data, TRUE);
        $this->load->view('Administrator/master_dashboard', $data);
    }


    // Product Category 
    public function getCategories()
    {
        $categories = $this->db->query("select * from tbl_productcategory where status = 'a' and category_branchid = ?", $this->session->userdata('BRANCHid'))->result();
        echo json_encode($categories);
    }

    public function add_category()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Category";
        $data['content'] = $this->load->view('Administrator/add_prodcategory', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_category()
    {
        $catname = $this->input->post('catname');
        $brunch = $this->brunch;
        $query = $this->db->query("SELECT * from tbl_productcategory where category_branchid = '$brunch' AND ProductCategory_Name = '$catname'");
        if ($query->num_rows() > 0) {
            $this->db->query("update tbl_productcategory set status = 'a' where ProductCategory_SlNo = ?", $query->row()->ProductCategory_SlNo);
        } else {
            $data = array(
                "ProductCategory_Name"              => $this->input->post('catname', TRUE),
                "ProductCategory_Description"       => $this->input->post('catdescrip', TRUE),
                "status"                            => 'a',
                "AddBy"                              => $this->session->userdata("FullName"),
                "AddTime"                           => date("Y-m-d H:i:s"),
                "category_branchid"                 => $this->brunch
            );
            $this->mt->save_data('tbl_productcategory', $data);
            $success = 'Save Success';
            echo json_encode($success);
        }
    }
    public function catedit($id)
    {
        $data['title'] = "Edit Category";
        //$fld = 'ProductCategory_SlNo';
        $data['selected'] = $this->Billing_model->select_by_id('tbl_productcategory', $id, 'ProductCategory_SlNo');
        $data['content'] = $this->load->view('Administrator/edit/category_edit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function catupdate()
    {
        $id = $this->input->post('id');
        $catname = $this->input->post('catname');
        $brunch = $this->brunch;
        $query = $this->db->query("SELECT * from tbl_productcategory where category_branchid = '$brunch' AND ProductCategory_Name = '$catname' and ProductCategory_SlNo != '$id'");
        if ($query->num_rows() > 0) {
            $this->db->query("update tbl_productcategory set status = 'a' where ProductCategory_SlNo = ?", $query->row()->ProductCategory_SlNo);
        } else {

            $fld = 'ProductCategory_SlNo';
            $data = array(
                "ProductCategory_Name"              => $this->input->post('catname', TRUE),
                "ProductCategory_Description"       => $this->input->post('catdescrip', TRUE),
                "UpdateBy"                          => $this->session->userdata("FullName"),
                "UpdateTime"                        => date("Y-m-d H:i:s")
            );
            if ($this->mt->update_data("tbl_productcategory", $data, $id, $fld)) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }
    public function catdelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'ProductCategory_SlNo';
        $this->mt->delete_data("tbl_productcategory", $id, $fld);
        $success = 'Delete Success';
        echo json_encode($success);
    }


    // Product subCategory 

    public function getsubCategories()
    {

        $query = '';
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $id = $_GET['id'];
            $query = " and ProductCategory_ID = '$id'";
        }
        $categories = $this->db->query("select * from tbl_produsubctcategory where status = 'a' and category_branchid = ?" . $query, $this->session->userdata('BRANCHid'))->result();
        echo json_encode($categories);
    }

    public function add_subcategory()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Sub Category";
        $data['content'] = $this->load->view('Administrator/add_prodsubcategory', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function insert_subcategory()
    {
        $catid      = $this->input->post('catname', TRUE);
        $subcatname = $this->input->post('subcatname', TRUE);
        $catdescrip = $this->input->post('catdescrip', TRUE);
        $brunch     = $this->brunch;

        // Check if subcategory already exists
        $this->db->where('category_branchid', $brunch);
        $this->db->where('ProductCategory_SlNo', $catid);
        $this->db->where('ProductsubCategory_Name', $subcatname);
        $query = $this->db->get('tbl_produsubctcategory');

        if ($query->num_rows() > 0) {
            // Reactivate existing record
            $this->db->where('ProductCategory_SlNo', $query->row()->ProductCategory_SlNo);
            $this->db->where('ProductsubCategory_Name', $subcatname);
            $this->db->update('tbl_produsubctcategory', [
                'status'                  => 'a',
                'ProductsubCategory_Name' => $subcatname
            ]);
        } else {
            // Insert new record
            $data = [
                "ProductCategory_ID"          => $catid,
                "ProductsubCategory_Name"     => $subcatname,
                "ProductCategory_Description" => $catdescrip,
                "status"                      => 'a',
                "AddBy"                       => $this->session->userdata("FullName"),
                "AddTime"                     => date("Y-m-d H:i:s"),
                "category_branchid"           => $brunch
            ];
            $this->mt->save_data('tbl_produsubctcategory', $data);
            echo json_encode('Save Success');
        }
    }

    public function subcatedit($id)
    {
        $data['title'] = "Edit Category";
        //$fld = 'ProductCategory_SlNo';
        $data['selected'] = $this->Billing_model->select_by_id('tbl_produsubctcategory', $id, 'ProductCategory_SlNo');
        $data['content'] = $this->load->view('Administrator/edit/subcategory_edit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function subcatupdate()
    {
        $id        = $this->input->post('id');
        $catid     = $this->input->post('catname'); // category id
        $subcatname = addslashes($this->input->post('subcatname')); // subcategory name
        $catdescrip = $this->input->post('catdescrip');
        $brunch    = $this->brunch;

        // Check if same subcategory already exists (except current one)
        $query = $this->db->query("
            SELECT * 
            FROM tbl_produsubctcategory 
            WHERE category_branchid = ? 
              AND ProductsubCategory_Name = ? 
              AND ProductCategory_SlNo != ?
        ", [$brunch, $subcatname, $id]);

        if ($query->num_rows() > 0) {
            // If duplicate, maybe reactivate it
            $this->db->query(
                "UPDATE tbl_produsubctcategory 
                 SET status = 'a' 
                 WHERE ProductCategory_SlNo = ? 
                   AND ProductsubCategory_Name = ?",
                [$query->row()->ProductCategory_SlNo, $subcatname]
            );
        } else {
            // Otherwise update record
            $fld = 'ProductCategory_SlNo';
            $data = [
                "ProductCategory_ID"          => $catid,
                "ProductsubCategory_Name"     => $subcatname,
                "ProductCategory_Description" => $catdescrip,
                "UpdateBy"                    => $this->session->userdata("FullName"),
                "UpdateTime"                  => date("Y-m-d H:i:s")
            ];

            if ($this->mt->update_data("tbl_produsubctcategory", $data, $id, $fld)) {
                echo json_encode(true);
            }
        }
    }

    public function subcatdelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'ProductCategory_SlNo';
        $this->mt->delete_data("tbl_produsubctcategory", $id, $fld);
        $success = 'Delete Success';
        echo json_encode($success);
    }

    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    // unit 
    public function unit()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Unit";
        $data['content'] = $this->load->view('Administrator/unit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_unit()
    {
        $mail = $this->input->post('unitname');
        $query = $this->db->query("SELECT Unit_Name from tbl_unit where Unit_Name = '$mail'");

        if ($query->num_rows() > 0) {
            $exists = false;
            echo json_encode($exists);
        } else {
            $data = array(
                "Unit_Name"              => $this->input->post('unitname', TRUE),
                "status"              => 'a',
                "AddBy"                  => $this->session->userdata("FullName"),
                "AddTime"                => date("Y-m-d H:i:s")
            );
            $succ =  $this->mt->save_data('tbl_unit', $data);
            if ($succ) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }
    public function unitedit($id)
    {
        $data['title'] = "Edit Unit";
        $fld = 'Unit_SlNo';
        $data['selected'] = $this->Billing_model->select_by_id('tbl_unit', $id, $fld);
        $data['content'] = $this->load->view('Administrator/edit/unit_edit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function unitupdate()
    {
        $id = $this->input->post('id');
        $fld = 'Unit_SlNo';
        $data = array(
            "Unit_Name"                         => $this->input->post('unitname', TRUE),
            "UpdateBy"                          => $this->session->userdata("FullName"),
            "UpdateTime"                        => date("Y-m-d H:i:s")
        );
        if ($this->mt->update_data("tbl_unit", $data, $id, $fld)) {
            $msg = true;
            echo json_encode($msg);
        }
    }
    public function unitdelete()
    {
        $fld = 'Unit_SlNo';
        $id = $this->input->post('deleted');
        $this->mt->delete_data("tbl_unit", $id, $fld);
    }

    public function getUnits()
    {
        $units = $this->db->query("select * from tbl_unit where status = 'a'")->result();
        echo json_encode($units);
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    //Area 
    public function area()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Area";
        $data['categories'] = $this->db->query("select * from tbl_productcategory where status = 'a'")->result();
        $data['content'] = $this->load->view('Administrator/add_area', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_area()
    {
        $district = $this->input->post('district');
        $query = $this->db->query("SELECT District_Name from tbl_district where District_Name = '$district'");

        if ($query->num_rows() > 0) {
            $exist = false;
            echo json_encode($exist);
        } else {
            $data = array(
                "District_Name"          => $this->input->post('district', TRUE),
                "AddBy"                  => $this->session->userdata("FullName"),
                "AddTime"                => date("Y-m-d H:i:s"),
                'delivery_charge'        => $this->input->post('delivery_charge', TRUE),
                'forbidden_categories'   => $this->input->post('forbidden_categories', TRUE),
                'special_discount'              => $this->input->post('special_discount', TRUE)
            );

            if ($this->mt->save_data('tbl_district', $data)) {
                $msg = true;
                echo json_encode($msg);
            }
            // $this->load->view('Administrator/ajax/district');
        }
    }
    public function areaedit($id)
    {
        $data['title'] = "Edit Unit";
        $fld = 'District_SlNo';
        $data['selected'] = $this->Billing_model->select_by_id('tbl_district', $id, $fld);
        $data['categories'] = $this->db->query("select * from tbl_productcategory where status = 'a'")->result();
        $data['content'] = $this->load->view('Administrator/edit/district_edit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function areaupdate()
    {

        // echo $this->input->post('forbidden_categories', TRUE);exit;
        $id = $this->input->post('id');
        $fld = 'District_SlNo';
        $data = array(
            "District_Name"                     => $this->input->post('district', TRUE),
            "UpdateBy"                          => $this->session->userdata("FullName"),
            "UpdateTime"                        => date("Y-m-d H:i:s"),
            'delivery_charge'                   => $this->input->post('delivery_charge', TRUE),
            'forbidden_categories'              => $this->input->post('forbidden_categories', TRUE),
            'special_discount'              => $this->input->post('special_discount', TRUE)
        );
        if ($this->mt->update_data("tbl_district", $data, $id, $fld)) {
            $msg = true;
            echo json_encode($msg);
        }
        /* else {
                $sdata['district'] = 'Update is Faild';
            }
            $this->session->set_userdata($sdata);
            redirect("Administrator/Page/district"); */
    }
    public function areadelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'District_SlNo';
        $this->mt->delete_data("tbl_district", $id, $fld);
        //$this->load->view('Administrator/ajax/district');
    }

    public function getDistricts()
    {
        $districts = $this->db->query("select * from tbl_district d where d.status = 'a'")->result();
        echo json_encode($districts);
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    // Country 
    public function add_country()
    {
        $data['title'] = "Add Country";
        $data['content'] = $this->load->view('Administrator/add_country', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function insert_country()
    {
        $mail = $this->input->post('Country');
        $query = $this->db->query("SELECT CountryName from tbl_country where CountryName = '$mail'");

        if ($query->num_rows() > 0) {
            echo "F";
            //$this->load->view('Administrator/ajax/Country');
        } else {
            $data = array(
                "CountryName"          => $this->input->post('Country', TRUE),
                "AddBy"                  => $this->session->userdata("FullName"),
                "AddTime"                => date("Y-m-d H:i:s")
            );
            $this->mt->save_data('tbl_country', $data);
            $this->load->view('Administrator/ajax/Country');
        }
    }
    public function fancybox_add_country()
    {
        $this->load->view('Administrator/products/fancybox_add_country');
    }
    public function fancybox_insert_country()
    {
        $mail = $this->input->post('Country');
        $query = $this->db->query("SELECT CountryName from tbl_country where CountryName = '$mail'");

        if ($query->num_rows() > 0) {
            echo "F";
        } else {
            $data = array(
                "CountryName"          => $this->input->post('Country', TRUE),
                "AddBy"                  => $this->session->userdata("FullName"),
                "AddTime"                => date("Y-m-d H:i:s")
            );
            $this->mt->save_data('tbl_country', $data);
            $this->load->view('Administrator/products/ajax_Country');
        }
    }
    public function countryedit($id)
    {
        $data['title'] = "Edit Country";
        $fld = 'Country_SlNo';
        $data['selected'] = $this->mt->select_by_id('tbl_country', $id, $fld);
        $data['content'] = $this->load->view('Administrator/edit/country_edit', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function countryupdate()
    {
        $id = $this->input->post('id');
        $fld = 'Country_SlNo';
        $data = array(
            "CountryName"                     => $this->input->post('Country', TRUE),
            "UpdateBy"                          => $this->session->userdata("FullName"),
            "UpdateTime"                        => date("Y-m-d H:i:s")
        );
        $this->mt->update_data("tbl_country", $data, $id, $fld);
        $this->load->view('Administrator/ajax/Country');
    }
    public function countrydelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'Country_SlNo';
        $this->mt->delete_data("tbl_country", $id, $fld);
        $this->load->view('Administrator/ajax/Country');
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
    //Company Profile

    public function getCompanyProfile()
    {
        $companyProfile = $this->db->query("select * from tbl_company order by Company_SlNo desc limit 1")->row();
        echo json_encode($companyProfile);
    }

    public function company_profile()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Company Profile";
        $data['selected'] = $this->db->query("
            select * from tbl_company order by Company_SlNo desc limit 1
        ")->row();
        $data['content'] = $this->load->view('Administrator/company_profile', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function company_profile_insert()
    {
        $id = $this->brunch;
        $inpt = $this->input->post('inpt', true);
        $fld = 'company_BrunchId';
        $this->load->library('upload');
        $config['upload_path'] = './uploads/company_profile_org/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size'] = '10000';
        $config['image_width'] = '4000';
        $config['image_height'] = '4000';
        $this->upload->initialize($config);

        $data['Company_Name'] =  $this->input->post('Company_name', true);
        $data['Repot_Heading'] =  $this->input->post('Description', true);

        $xx = $this->mt->select_by_id("tbl_company", $id, $fld);

        $image = $this->upload->do_upload('companyLogo');
        $images = $this->upload->data();

        if ($image != "") {
            if ($xx['Company_Logo_thum'] && $xx['Company_Logo_org']) {
                unlink("./uploads/company_profile_thum/" . $xx['Company_Logo_thum']);
                unlink("./uploads/company_profile_org/" . $xx['Company_Logo_org']);
            }
            $data['Company_Logo_org'] = $images['file_name'];

            $config['image_library'] = 'gd2';
            $config['source_image'] = $this->upload->upload_path . $this->upload->file_name;
            $config['new_image'] = 'uploads/' . 'company_profile_thum/' . $this->upload->file_name;
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 165;
            $config['height'] = 175;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
            $data['Company_Logo_thum'] = $this->upload->file_name;
        } else {

            $data['Company_Logo_org'] = $xx['Company_Logo_org'];
            $data['Company_Logo_thum'] = $xx['Company_Logo_thum'];
        }
        $data['print_type'] = $inpt;
        $data['company_BrunchId'] = $this->brunch;
        $this->mt->save_data("tbl_company", $data, $id, $fld);
        $id = '1';
        redirect('Administrator/Page/company_profile');
        //$this->load->view('Administrator/company_profile');
    }

    public function company_profile_Update()
    {
        $data['Company_Name'] =  $this->input->post('Company_name', true);
        $data['Repot_Heading'] =  $this->input->post('Description', true);
        $data['invoice_footer'] = $this->input->post('invoice_footer', true);

        $this->db->set('print_type', $this->input->post('inpt', true))->where('brunch_id', $this->brunch)->update('tbl_brunch');

        if (isset($_FILES['companyLogo']) && $_FILES['companyLogo']['error'] != UPLOAD_ERR_NO_FILE) {

            $files = glob('./uploads/company_profile_org/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }

            $files = glob('./uploads/company_profile_thum/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }

            $this->load->library('upload');
            $config = array();
            $config['upload_path']      = './uploads/company_profile_org/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif';
            $config['max_size']         = '0';
            $config['file_name']        = 'company_logo';
            $config['overwrite']        = FALSE;

            $this->upload->initialize($config);
            $this->upload->do_upload('companyLogo');
            $image = $this->upload->data();

            $data['Company_Logo_org'] = $image['file_name'];

            $config['image_library'] = 'gd2';
            $config['source_image'] = $this->upload->upload_path . $this->upload->file_name;
            $config['new_image'] = 'uploads/company_profile_thum/' . $this->upload->file_name;
            $config['maintain_ratio'] = FALSE;
            $config['width'] = 165;
            $config['height'] = 175;
            $this->load->library('image_lib', $config);
            $this->image_lib->resize();
            $data['Company_Logo_thum'] = $this->upload->file_name;
        }

        if (isset($_FILES['login_img']) && $_FILES['login_img']['error'] != UPLOAD_ERR_NO_FILE) {
            $this->load->library('upload');
            $config = array();
            $config['upload_path']      = './uploads/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif';
            $config['max_size']         = '0';
            $config['file_name']        = 'login_img';
            $config['overwrite']        = FALSE;

            $this->upload->initialize($config);
            $this->upload->do_upload('login_img');
            $image = $this->upload->data();
            $data['login_img'] = $image['file_name'];
        }


        if (isset($_FILES['banner_img']) && $_FILES['banner_img']['error'] != UPLOAD_ERR_NO_FILE) {
            $this->load->library('upload');
            $config = array();
            $config['upload_path']      = './uploads/';
            $config['allowed_types']    = 'jpg|jpeg|png|gif';
            $config['max_size']         = '0';
            $config['file_name']        = 'banner_img';
            $config['overwrite']        = FALSE;

            $this->upload->initialize($config);
            $this->upload->do_upload('banner_img');
            $image = $this->upload->data();
            $data['banner_img'] = $image['file_name'];
        }




        $this->db->update('tbl_company', $data);
        redirect('Administrator/Page/company_profile');
    }

    //^^^^^^^^^^^^^^^^^^^^^
    // Brunch Name

    public function getBranches()
    {
        $branches = $this->db->query("
            select 
            b.*,
            case b.status
                when 'a' then 'Active'
                else 'Inactive'
            end as active_status,
            d.District_Name
            from tbl_brunch b
            left join tbl_district d on d.District_SlNo = b.area_id
            order by b.brunch_id desc
        ")->result();
        echo json_encode($branches);
    }

    public function getCurrentBranch()
    {
        $branch = $this->Billing_model->company_branch_profile($this->brunch);
        echo json_encode($branch);
    }

    public function changeBranchStatus()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $status = $this->db->query("select * from tbl_brunch where brunch_id = ?", $data->branchId)->row()->status;
            $status = $status == 'a' ? 'd' : 'a';
            $this->db->set('status', $status)->where('brunch_id', $data->branchId)->update('tbl_brunch');
            $res = ['success' => true, 'message' => 'Status changed'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function branch()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Brunch";
        $data['content'] = $this->load->view('Administrator/add_branch', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addBranch()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $branch = json_decode($this->input->raw_input_stream);

            $nameCount = $this->db->query("select * from tbl_brunch where Brunch_name = ?", $branch->Brunch_name)->num_rows();
            if ($nameCount > 0) {
                $res = ['success' => false, 'message' => $branch->Brunch_name . ' already exists'];
                echo json_encode($res);
                exit;
            }

            $newBranch = array(
                'Brunch_name'    => $branch->Brunch_name,
                'Brunch_title'   => $branch->Brunch_title,
                'Brunch_address' => $branch->Brunch_address,
                'area_id'        => $branch->area_id,
                'print_type'     => $branch->print_type,
                'Brunch_sales'   => '2',
                'add_by'         => $this->session->userdata("FullName"),
                'add_time'       => date('Y-m-d H:i:s'),
                'status'         => 'a'
            );

            $this->db->insert('tbl_brunch', $newBranch);
            $res = ['success' => true, 'message' => 'Branch added'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateBranch()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $branch = json_decode($this->input->raw_input_stream);

            $nameCount = $this->db->query("select * from tbl_brunch where Brunch_name = ? and brunch_id != ?", [$branch->Brunch_name, $branch->brunch_id])->num_rows();
            if ($nameCount > 0) {
                $res = ['success' => false, 'message' => $branch->Brunch_name . ' already exists'];
                echo json_encode($res);
                exit;
            }

            $newBranch = array(
                'Brunch_name' => $branch->Brunch_name,
                'Brunch_title' => $branch->Brunch_title,
                'Brunch_address' => $branch->Brunch_address,
                'area_id' => $branch->area_id,
                'print_type'     => $branch->print_type,
                'update_by' => $this->session->userdata("FullName")
            );

            $this->db->set($newBranch)->where('brunch_id', $branch->brunch_id)->update('tbl_brunch');
            $res = ['success' => true, 'message' => 'Branch updated'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    
    //^^^^^^^^^^^^^^^^^^^^^^^^
    public function add_color()
    {
        $data['title'] = "Add color";
        $data['content'] = $this->load->view('Administrator/add_color', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function Fancybox_add_color()
    {
        $data['title'] = "Add color";
        $this->load->view('Administrator/products/fancybox_color', $data);
    }
    public function insert_color()
    {
        $colorname = $this->input->post('colorname');
        $query = $this->db->query("SELECT color_name from tbl_color where color_name = '$colorname'");

        if ($query->num_rows() > 0) {
            $exits = false;
            echo json_encode($exits);
        } else {
            $data = array(
                "color_name"      => $this->input->post('colorname', TRUE),
                "status"          => 'a'

            );
            if ($this->mt->save_data('tbl_color', $data)) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }
    public function fancybox_insert_color()
    {
        $mail = $this->input->post('Country');
        $query = $this->db->query("SELECT color_name from tbl_color where color_name = '$mail'");

        if ($query->num_rows() > 0) {
            echo "F";
        } else {
            $data = array(
                "color_name"          => $this->input->post('Country', TRUE)
            );
            $this->mt->save_data('tbl_color', $data);
            $this->load->view('Administrator/products/ajax_color');
        }
    }
    public function colordelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'color_SiNo';
        $this->mt->delete_data("tbl_color", $id, $fld);
        echo "Success";
    }
    public function coloredit($id)
    {
        $data['title'] = "Edit Color";
        $fld = 'color_SiNo';
        $data['selected'] = $this->Billing_model->select_by_id('tbl_color', $id, $fld);
        $data['content'] = $this->load->view('Administrator/edit/edit_color', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function colorupdate()
    {
        $id = $this->input->post('id');
        $colorname = $this->input->post('colorname');
        $query = $this->db->query("SELECT color_name from tbl_color where color_name = '$colorname'");

        if ($query->num_rows() > 1) {
            $exits = false;
            echo json_encode($exits);
        } else {
            $fld = 'color_SiNo';
            $data = array(
                "color_name" => $this->input->post('colorname', TRUE)
            );
            if ($this->mt->update_data("tbl_color", $data, $id, $fld)) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }
    //^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

    public function getBrands()
    {
        $brands = $this->db->query("select * from tbl_brand where status = 'a'")->result();
        echo json_encode($brands);
    }

    public function add_brand()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Brand";
        $data['brand'] =  $this->Billing_model->select_brand($this->brunch);
        $data['content'] = $this->load->view('Administrator/add_brand', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function insert_brand()
    {
        $brandname = $this->input->post('brandname');
        $branch = $this->brunch;
        $query = $this->db->query("SELECT brand_name from tbl_brand where brand_branchid = '$branch' AND brand_name = '$brandname'");

        if ($query->num_rows() > 0) {
            $exist = false;
            echo json_encode($exist);
        } else {
            $data = array(
                "brand_name"          => $this->input->post('brandname', TRUE),
                "status"      => 'a',
                "brand_branchid"      => $this->brunch
            );
            $succ = $this->mt->save_data('tbl_brand', $data);
            if ($succ) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }
    public function branddelete()
    {
        $id = $this->input->post('deleted');
        $fld = 'brand_SiNo';
        $this->mt->delete_data("tbl_brand", $id, $fld);
        echo "Success";
    }
    public function brandedit($id)
    {
        $data['title'] = "Edit Brand";
        $fld = 'brand_SiNo';
        $data['selected'] = $this->Billing_model->select_by_id('tbl_brand', $id, 'brand_SiNo');
        $data['content'] = $this->load->view('Administrator/edit/edit_brand', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function Update_brand()
    {
        $id = $this->input->post('id');
        $brandname = $this->input->post('brandname');
        $branch = $this->brunch;
        $query = $this->db->query("SELECT brand_name from tbl_brand where brand_branchid = '$branch' AND brand_name = '$brandname'");
        if ($query->num_rows() > 0) {
            $exist = false;
            echo json_encode($exist);
        } else {
            $fld = 'brand_SiNo';
            $data = array(
                "brand_name" => $this->input->post('brandname', TRUE)
            );
            $succ = $this->mt->update_data("tbl_brand", $data, $id, $fld);
            if ($succ) {
                $msg = true;
                echo json_encode($msg);
            }
        }
    }

    public function databaseBackup()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Database Backup";
        $data['content'] = $this->load->view('Administrator/database_backup', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
}
