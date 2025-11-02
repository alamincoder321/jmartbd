<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Feedback extends CI_Controller
{

    public function index()
    {
   
        $data['title'] = "Feedback";
        $data['content'] = $this->load->view('Administrator/feedback/index', $data, TRUE);
        $this->load->view('Administrator/index', $data);

  
    }

    public function getFeedback(){
        $this->load->model('Feedback_model');
        $feedbacks = $this->Feedback_model->get_feedbacks();
        echo json_encode($feedbacks);
    }


    public function updateStatus(){
        $this->load->model('Feedback_model');
        $input = json_decode($this->input->raw_input_stream)->data;
        
        $update = $this->Feedback_model->updateStatus($input->id,$input->status );
    
        echo json_encode(['status' => $update , 'message' => 'Status Updated']);
    }



}
