<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_management extends CI_Controller
{

    private  $access;

    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $this->access = $this->session->userdata('userId');
        $this->accountType = $this->session->userdata('accountType');
        if ($this->access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->model('Billing_model');
    }

    public function index()
    {
        if ($this->accountType == 'u') {
            redirect("Administrator/Page");
        }
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }

        $data['title'] = "Create User";
        $data['content'] = $this->load->view('Administrator/user', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getUsers()
    {
        $users = $this->db->query("
            select 
            u.*,
            b.Brunch_name
            from tbl_user u 
            left join tbl_brunch b on b.brunch_id = u.Brunch_ID
            where u.status != 'd'
            order by User_SlNo desc
        ")->result();

        echo json_encode($users);
    }

    public function getAllUsers()
    {
        $users = $this->db->query("select * from tbl_user order by User_SlNo desc")->result();

        echo json_encode($users);
    }

    public function add_user()
    {
        if ($this->accountType == 'u') {
            redirect("Administrator/Page");
        }
        $data['title'] = "Add User";
        $data['content'] = $this->load->view('Administrator/add_user', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function user_Insert()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $checkUsername = $this->db->query("select * from tbl_user where User_Name = ? and status != 'd'", $data->User_Name)->num_rows();
            if ($checkUsername > 0) {
                $res = ['success' => false, 'message' => 'Username already exists'];
                echo json_encode($res);
                exit;
            }

            $check = $this->db->query("select * from tbl_user where User_Name = ? and status = 'd'", $data->User_Name)->row();
            $userInfo = array(
                "User_Name"     => $data->User_Name,
                "FullName"      => $data->FullName,
                "UserEmail"     => $data->UserEmail,
                "Brunch_ID"     => $data->branch_id,
                "userBrunch_id" => $data->branch_id,
                "User_Password" => md5($data->Password),
                "UserType"      => $data->UserType,
                "status"        => $data->status,
                "AddBy"         => $this->session->userdata("userId"),
                "AddTime"       => date('Y-m-d H:i:s'),
            );
            if (!empty($check)) {
                $userInfo['DeletedBy'] = NULL;
                $userInfo['DeletedTime'] = NULL;
                $this->db->set($userInfo)->where('User_SlNo', $check->User_SlNo)->update('tbl_user');
            } else {
                $userInfo['User_ID'] = $this->mt->generateUserCode();
                $this->db->insert("tbl_user", $userInfo);
            }


            $res = ['success' => true, 'message' => 'User create successfully'];
        } catch (\Throwable $th) {
            $res = ['success' => false, 'message' => $th->getMessage()];
        }

        echo json_encode($res);
    }

    public function userupdate()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $checkUsername = $this->db->query("select * from tbl_user where User_Name = ? and User_SlNo != ?", [$data->User_Name, $data->User_SlNo])->num_rows();
            if ($checkUsername > 0) {
                $res = ['success' => false, 'message' => 'Username already exists'];
                echo json_encode($res);
                exit;
            }
            $user = array(
                "User_Name"     => $data->User_Name,
                "FullName"      => $data->FullName,
                "UserEmail"     => $data->UserEmail,
                "Brunch_ID"     => $data->branch_id,
                "userBrunch_id" => $data->branch_id,
                "UserType"      => $data->UserType,
                "status"         => $data->status,
                "UpdateBy"      => $this->session->userdata("userId"),
                "UpdateTime"    => date('Y-m-d H:i:s')
            );
            if (!empty($data->Password)) {
                $user['User_Password'] = md5($data->Password);
            }
            $this->db->where('User_SlNo', $data->User_SlNo);
            $this->db->update('tbl_user', $user);
            $res = ['success' => true, 'message' => 'User update successfully'];
        } catch (\Throwable $th) {
            $res = ['success' => false, 'message' => $th->getMessage()];
        }

        echo json_encode($res);
    }

    public function userstatusChange()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $userInfo = array(
                "status"     => $data->status,
                "UpdateBy"   => $this->session->userdata("userId"),
                "UpdateTime" => date('Y-m-d H:i:s')
            );

            $this->db->where('User_SlNo', $data->userId);
            $this->db->update('tbl_user', $userInfo);
            $msg = $data->status == 'p' ? 'User Deactive successfully' : 'User Active successfully';
            $res = ['success' => true, 'message' => $msg];
        } catch (\Throwable $th) {
            $res = ['success' => true, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    public function userDelete()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $userInfo = array(
                "status"     => "d",
                "DeletedBy"   => $this->session->userdata("userId"),
                "DeletedTime" => date('Y-m-d H:i:s')
            );

            $this->db->where('User_SlNo', $data->userId);
            $this->db->update('tbl_user', $userInfo);
            $res = ['success' => true, 'message' => 'User delete successfully'];
        } catch (\Throwable $th) {
            $res = ['success' => true, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }
    public function check_username_availablity()
    {
        $get_result = $this->mt->check_username_availablity();
        if (!$get_result)
            echo '<span style="color:#f00">Username already in use. </span>';
        else
            echo '<span style="color:#00c">Username Available</span>';
    }

    public function check_user_name()
    {
        $data = json_decode($this->input->raw_input_stream);
        $query = $this->db->query("select User_Name from tbl_user where User_Name = ?", $data->User_Name)->row();
        if (!empty($query)) {
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false]);
        }
    }

    public function check_email()
    {
        $user_email = $this->input->post('user_email', TRUE);
        $this->db->SELECT("UserEmail");
        $this->db->from('tbl_user');
        $this->db->where('UserEmail', $user_email);
        $query = $this->db->get();
        $result = $query->row();
        if (count($result) > 0) {
            echo '<span style="color:red;">This email ID already exist</span>';
        }
    }


    public function user_access($id = null)
    {
        if ($this->accountType == 'u') {
            redirect("Administrator/Page");
        }
        $data['title'] = "User Access Priority";
        $data['userId'] = $id;
        $data['content'] = $this->load->view('Administrator/menu_access_priority', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getUserAccess()
    {
        $data = json_decode($this->input->raw_input_stream);
        $accessQuery = $this->db->query("select * from tbl_user_access where user_id = ?", $data->userId);
        if ($accessQuery->num_rows() == 0) {
            echo '';
            exit;
        }

        echo json_encode($accessQuery->row()->access);
    }

    public function addUserAccess()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $count = $this->db->query("select * from tbl_user_access where user_id = ?", $data->userId)->num_rows();
            $access = array(
                'user_id' => $data->userId,
                'access' => json_encode($data->access),
                'saved_by' => $this->session->userdata('userId'),
                'saved_datetime' => date('Y-m-d H:i:s')
            );
            if ($count == 0) {
                $this->db->insert('tbl_user_access', $access);
            } else {
                $this->db->set($access)->where('user_id', $data->userId)->update('tbl_user_access');
            }

            $res = ['success' => true, 'message' => 'Success'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }


    public function profile()
    {
        $data['title'] = "User Profile";

        $user = $this->db->where('User_SlNo', $this->access)->get('tbl_user')->row();
        $data['branch_info'] = $this->db->where('brunch_id', $user->userBrunch_id)->get('tbl_brunch')->row();
        $data['user'] = $user;
        $data['content'] = $this->load->view('Administrator/profile', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function password_change(){

		$this->form_validation->set_rules('current_password', 'Current Password', 'required|trim');
		$this->form_validation->set_rules('password', 'New Password', 'required|trim');
		$this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|trim|matches[password]');

		if($this->form_validation->run() == FALSE)
		{
			$data['title'] = "user profile";
			$user= $this->db->where('User_SlNo', $this->access)->get('tbl_user')->row();
			$data['branch_info'] = $this->db->where('brunch_id', $user->userBrunch_id)->get('tbl_brunch')->row();
			$data['user'] = $user;
			$data['content'] = $this->load->view('Administrator/profile', $data, TRUE);
			$this->load->view('Administrator/index', $data);
		}
		else{

			$user_name = $this->session->userdata('User_Name');
			$check = $this->db->where('User_Name', $user_name)->where('User_Password', md5($this->input->post('current_password')))->get('tbl_user')->row();

			if($check){
				$attr = array(
					'User_Password'=> md5($this->input->post('password'))
				);
				$this->db->where('User_SlNo', $this->access);
				$res = $this->db->update('tbl_user', $attr);

				if($this->db->affected_rows()){
					$data['msg'] = 'Password Update Successful..!';
					$this->session->set_flashdata($data);
					return redirect('profile');
				}else{
					$data['msg'] = 'Password Update Un-Successful..!';
					$this->session->set_flashdata($data);
					return redirect('profile');
				}

			}else{
				$data['msg'] = 'Current Password not match..!';
				$this->session->set_flashdata($data);
				return redirect('profile');
			}
		}
	}

    public function profileUpdate()
    {
        $res = ['success' => false, 'message' => ""];
        try {
            $password = $this->input->post('password');
            $current_password = $this->input->post('current_password');

            $data = array();
            if (!empty($password)) {
                $hashpass = $this->db->query("select User_Password from tbl_user where User_SlNo = ?", $this->access)->row()->User_Password;
                if ($hashpass != md5($current_password)) {
                    $res = ['success' => false, 'message' => "Current password does not match"];
                    echo json_encode($res);
                    exit;
                }
                $data['User_Password'] = md5($password);
            }
            if (!empty($_FILES) && $_FILES['image']['name'] != '') {
                $oldImgFile = $this->session->userdata('user_image');
                if (file_exists($oldImgFile)) {
                    unlink($oldImgFile);
                }
                $imagePath = $this->mt->uploadImage($_FILES, 'image', 'uploads/users', $this->session->userdata('FullName'));
                $this->session->userdata['user_image'] = $imagePath;
                $data['image_name'] = $imagePath;
            }
            if (!empty($_FILES) || !empty($password)) {
                $this->db->where('User_SlNo', $this->access);
                $this->db->update('tbl_user', $data);
            }
            $res = ['success' => true, 'message' => "Profile update successfully"];
        } catch (\Throwable $th) {
            $res = ['success' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }

    public function userActivity()
    {
        if ($this->accountType != 'm' || $this->brunch != 1) {
            redirect("Administrator/Page");
        }
        $data['title'] = "User Activity";
        $data['content'] = $this->load->view('Administrator/user_activity', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getUserActivity()
    {
        $data = json_decode($this->input->raw_input_stream);

        $clauses = "";
        if (isset($data->dateFrom) && $data->dateFrom != '' && isset($data->dateTo) && $data->dateTo != '') {
            $dateFrom  = $data->dateFrom;
            $dateTo    = $data->dateTo;
            $clauses  .= " and DATE_FORMAT(ua.login_time, '%Y-%m-%d') between '$dateFrom' and '$dateTo'";
        }

        if (isset($data->user_id) && $data->user_id != '') {
            $clauses .= " and ua.user_id = '$data->user_id'";
        }

        $result = $this->db->query("
            SELECT ua.*,
            u.User_Name,
            CASE u.UserType
                WHEN 'm' then 'Super Admin'
                WHEN 'a' then 'Admin'
                WHEN 'u' then 'User'
                WHEN 'e' then 'Entry User'
                ELSE 'Unknown'
            END as UserType,
            b.Branch_name

            from tbl_user_activity ua
            left join tbl_user u on u.User_SlNo = ua.user_id
            left join tbl_branch b on b.brunch_id = ua.branch_id
            where ua.status = 'a'
            $clauses
            order by ua.id desc
        ")->result();

        echo json_encode($result);
    }

    public function deleteUserActivity()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            if (isset($data->id) && $data->id != '') {
                $this->db->query("DELETE FROM tbl_user_activity where id = '$data->id'");

                $res = ['success' => true, 'message' => 'Data deleted'];
            } elseif (isset($data->mark_arr) && $data->mark_arr != []) {
                $ids = join("','", $data->mark_arr);
                $this->db->query("DELETE FROM tbl_user_activity where id in ('$ids')");

                $res = ['success' => true, 'message' => 'Data deleted'];
            } else {
                $res = ['success' => false, 'message' => 'Something went wrong!'];
            }
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
}
