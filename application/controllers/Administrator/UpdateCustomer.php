<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class UpdateCustomer extends CI_Controller
{


    public function updateCustomerProfile()
    {
        $data = $this->input->post();
        $customerId = $data['customer_id'];
        $customer = $this->db->get_where('tbl_customer', ['Customer_SlNo' => $customerId])->row();

        if (empty($customer)) {
            echo json_encode([
                'status' => false,
                'message' => 'Customer not found'
            ]);
            return;
        }

        if (!empty($_FILES['media']['name'])) {
            $uploadPath = './uploads/customers/';
            $imageName = $customer->Customer_Code . '_' . time(); // Unique name

            // Upload config
            $uploadConfig = [
                'upload_path'   => $uploadPath,
                'allowed_types' => 'jpg|jpeg|png|gif',
                'file_name'     => $imageName,
                'overwrite'     => true,
            ];

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $this->load->library('upload', $uploadConfig);

            if (!$this->upload->do_upload('media')) {
                echo json_encode([
                    'status' => false,
                    'message' => $this->upload->display_errors()
                ]);
                return;
            }

            $uploadData = $this->upload->data();
            $uploadedFileName = $uploadData['file_name'];

            // Resize
            $resizeConfig = [
                'image_library'  => 'gd2',
                'source_image'   => $uploadPath . $uploadedFileName,
                'maintain_ratio' => true,
                'width'          => 640,
                'height'         => 480,
            ];

            $this->load->library('image_lib', $resizeConfig);
            $this->image_lib->resize();

            // Update DB
            $this->db->query("UPDATE tbl_customer SET image_name = ? WHERE Customer_SlNo = ?", [
                $uploadedFileName,
                $customerId
            ]);
        }

        echo json_encode([
            'status' => true,
            'message' => 'Customer profile updated successfully'
        ]);
    }
}
