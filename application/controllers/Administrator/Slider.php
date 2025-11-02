<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Slider extends CI_Controller
{

    public function index()
    {
        $data['title'] = "Slider";
        $data['content'] = $this->load->view('Administrator/slider/index', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function upload_file($field_name, $dir, $name, $old = null)
    {
        $config['upload_path'] = './uploads/' . $dir;

        // Create folder if it doesn't exist
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }

        // Adjust allowed types as needed
        $config['allowed_types'] = '*'; // Add 'webp' or others if needed
        $config['file_name'] = $name;
        $config['overwrite'] = true;

        // Load & initialize upload library
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        // Delete old file if needed
        if ($old && file_exists($config['upload_path'] . '/' . $old)) {
            unlink($config['upload_path'] . '/' . $old);
        }


        // Attempt upload
        if (!$this->upload->do_upload($field_name)) {
            $error = $this->upload->display_errors();
            log_message('error', 'Upload failed: ' . $error);
            echo $error; // Optional for debugging
            return false;
        }


        $data = $this->upload->data();
        return $data['file_name'];
    }


    public function updateSlider()
    {
        $input_data = $this->input->post();
        $field_name = 'selectedFile';

        $data = [
            'title' => $input_data['title'],
        ];

        // Determine whether it's update or insert
        $is_update = !empty($input_data['id']);
        if ($is_update) {
            $existing = $this->db->get_where('sliders', ['id' => $input_data['id']])->row();
            $image_name = $existing ? $existing->image : rand(100000, 999999) . '.png';
            $data['updateBy'] = $this->session->userdata('userId');
        } else {
            $image_name = rand(100000, 999999) . '.png';
            $data['addBy'] = $this->session->userdata('userId');
            $data['updateBy'] = $this->session->userdata('userId');
        }

        if (!empty($_FILES[$field_name]['name'])) {
            $uploaded_file = $this->upload_file($field_name, 'slider', $image_name, $image_name);
            if ($uploaded_file) {
                $data['image'] = $uploaded_file;
            } else {
                // handle upload failure if needed
                $data['image'] = $image_name; // or skip image update
            }
        } else {
            $data['image'] = $image_name;
        }

       

        if ($input_data['id'] != '') {
            $this->db->where('id', $input_data['id']);
            $this->db->update('sliders', $data);
            $data['message'] = "Slider Updated";
        } else {
            $this->db->insert('sliders', $data);
            $data['message'] = "Slider Added";
        }

        $data['success'] = true;

        echo json_encode($data);
    }


    public function getSliders()
    {
        echo json_encode($this->db->get('sliders')->result());
    }

    public function deleteSlider()
    {
      $request_all = json_decode(file_get_contents('php://input'), true);
     
        $this->db->where('id', $request_all['id'])->delete('sliders');
        echo json_encode(['success' => true, 'message' => 'Slider Deleted']);
    }



}
