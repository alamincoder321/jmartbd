<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Vehicle extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $access = $this->session->userdata('userId');
        $this->brunch = $this->session->userdata('BRANCHid');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model("Model_myclass", "mmc", TRUE);
        $this->load->model('Model_table', "mt", TRUE);
    }
    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Vehicle Entry";
        $data['content'] = $this->load->view('Administrator/vehicle/add_vehicle', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function vehicleList()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Vehicle List";
        $data['content'] = $this->load->view('Administrator/vehicle/vehicleList', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getVehicles()
    {
        $vehicles = $this->db
            ->where('branch_id', $this->brunch)
            ->where('status !=', 'd')
            ->get('tbl_vehicles')
            ->result();
        
        foreach ($vehicles as $vehicle) {
            $vehicle->display_name = $vehicle->name;
        }

        echo json_encode($vehicles);
    }

    public function addVehicle()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->post('data'));

            $vehicle = (array)$data;
            unset($vehicle['id']);
            $vehicle['AddBy']     = $this->session->userdata('FullName');
            $vehicle['AddTime']   = date('Y-m-d H:i:s');
            $vehicle['branch_id'] = $this->brunch;
            $this->db->insert('tbl_vehicles', $vehicle);
            $vehicleId = $this->db->insert_id();

            if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                $file_name = $this->mt->uploadImage($_FILES, 'image', 'uploads/vehicles');
                $this->db->where('id', $vehicleId)->update('tbl_vehicles', ['image' => $file_name]);
            }

            $res = ['success' => true, 'message' => 'Vehicle added successfully!'];
        } catch (Exception $e) {
            $res = ['success' => false, 'message' => 'Something went wrong!'];
        }

        echo json_encode($res);
    }

    public function updateVehicle()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->post('data'));

            $vehicle = (array)$data;
            unset($vehicle['id']);
            $vehicle['UpdateBy']     = $this->session->userdata('FullName');
            $vehicle['UpdateTime']   = date('Y-m-d H:i:s');
            $this->db->where('id', $data->id)->update('tbl_vehicles', $vehicle);

            if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                $oldImage = $this->db->where('id', $data->id)->get('tbl_vehicles')->row()->image_name;
                if ($oldImage && file_exists($oldImage)) {
                    unlink('uploads/vehicles/' . $oldImage);
                }
                $file_name = $this->mt->uploadImage($_FILES, 'image', 'uploads/vehicles');
                $this->db->where('id', $data->id)->update('tbl_vehicles', ['image_name' => $file_name]);
            }

            $res = ['success' => true, 'message' => 'Vehicle updated successfully!'];
        } catch (Exception $e) {
            $res = ['success' => false, 'message' => 'Something went wrong!'];
        }

        echo json_encode($res);
    }

    public function deleteVehicle()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $this->db->where('id', $data->id);
            $this->db->update('tbl_vehicles', ['UpdateBy' => $this->session->userdata('FullName'), 'UpdateTime' => date('Y-m-d H:i:s'), 'status' => 'd']);

            $res = ['success' => true, 'message' => 'Vehicle deleted successfully!'];
        } catch (Exception $e) {
            $res = ['success' => false, 'message' => 'Something went wrong!'];
        }

        echo json_encode($res);
    }
}
