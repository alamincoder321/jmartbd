<?php
class SMS_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        //$this->getSettings();
    }

    private $smsEnabled = "false";
    private $apiKey = "";
    private $url = "";
    private $bulkUrl = "";
    private $url2 = "";
    private $bulkUrl2 = "";
    private $senderId = "";
    private $senderId2 = "";
    private $userId = "";
    private $password = "";
    private $countryCode = "";
    private $smsType = "";
    private $senderName = "";
    private $senderPhone = "";

    private function getSmsFooter(){
        return "\n\nThank you,\n{$this->senderName}\nPhone: {$this->senderPhone}";
    }

    public function getSettings($gateway = null){
        $query = '';
        if($gateway){
            $cluse =" where sms_enabled = '$gateway'";
            $query = $this->db->query("select * from tbl_sms_settings $cluse");
        }else{
            $query = $this->db->query("select * from tbl_sms_settings");
        }
        
       
        
        if($query->num_rows() == 0){
            $this->smsEnabled = 'false';
            return;
        }

        $settings           = $query->row();
        $this->smsEnabled   = $settings->sms_enabled;
        $this->apiKey       = $settings->api_key;
        $this->url          = $settings->url;
        $this->bulkUrl      = $settings->bulk_url;
        $this->url2         = $settings->url_2;
        $this->bulkUrl2     = $settings->bulk_url_2;
        $this->smsType      = $settings->sms_type;
        $this->senderId     = $settings->sender_id;
        $this->senderId2    = $settings->sender_id_2;
        $this->userId       = $settings->user_id;
        $this->password     = $settings->password;
        $this->countryCode  = $settings->country_code;
        $this->senderName   = $settings->sender_name;
        $this->senderPhone  = $settings->sender_phone;
    }

    public function sendSms($recipient, $message, $gateway = null) {
        
        $this->getSettings($gateway);
        
        
        if($this->smsEnabled == 'false'){
            return false;
        }
        $recipient = trim($recipient);
        $smsText = urldecode($message) . $this->getSmsFooter();

        if($this->smsEnabled == 'gateway1'){
            $url = $this->url;
            $postData = array(
                "api_key" => $this->apiKey,
                "type" => $this->smsType,
                "senderid" => $this->senderId,
                "msg" => $smsText,
                "contacts" => "88{$recipient}"
            );
        }else{
            $url = $this->url2;
            $postData = array(
                "user" => $this->userId,
                "sender" => $this->senderId2,
                "pwd" => $this->password,
                "CountryCode" => $this->countryCode,
                "mobileno" => $recipient,
                "msgtext" => $smsText
            );
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return $result;
    }

   public function sendBulkSms($recipients, $message, $gateway = null) {
         
         $this->getSettings($gateway);
         $flow_outer_control =  true;
        if($this->smsEnabled == 'false'){
            return false;
        }

        $smsText = urldecode($message);
        

        
        if($this->smsEnabled == 'gateway1'){
            $url = $this->bulkUrl;

            $messages = array_map(function($recipient) use ($smsText){
                $recipient = trim($recipient);
                return array(
                    'to' => "88{$recipient}",
                    'message' => $smsText
                );
            }, $recipients);
    
            $postData = array(
                "api_key" => $this->apiKey,
                "type" => $this->smsType,
                "senderid" => $this->senderId,
                "messages" => json_encode($messages)
            );

        }elseif($this->smsEnabled == 'mram'){
              $url = $this->url;
            $recipient = implode("+88",array_map('trim', $recipients));
              $postData = [
                "api_key" => $this->apiKey,
                "type" =>  $this->smsType,
                "contacts" =>  $recipient,
                "senderid" =>  $this->senderId,
                "msg" =>  $smsText,
              ];
              
      
        }elseif($this->smsEnabled == 'erspro'){
             $url = $this->url;
             $phone_no = $recipients;
             $time = time();
             
             $data_l = [];
             $smsText = str_replace(' ', '+', $smsText);
          
             
             foreach ($phone_no as $key => $phone){
                $phone_no = preg_replace('/\D/', '', $phone);
                if (strlen($phone) == 11 && preg_match('/^01[3-9]\d{8}$/', $phone)) {
                    $phone_no = "88" . $phone_no;
                } elseif (strlen($phone_no) == 13 && preg_match('/^8801[3-9]\d{8}$/', $phone)) {
                   
                }else{
                    continue;
                } 
                

                    
                $url = $this->url
                     . "?req_id={$this->senderId}"
                     . "&number={$phone_no}"
                     . "&message={$smsText}"
                     . "&user={$this->userId}"
                     . "&key={$this->apiKey }"
                     . "&time={$time}";
            
                $ch = curl_init();
                
                // cURL options
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return response instead of printing
                
                $response = curl_exec($ch);
                
                $response_status = true;
                if(curl_errno($ch)){
                    $response_status = false;
                   
                } else {
                    $response_status = true;
                }
                
                $data_l[] = $response;
                
                curl_close($ch);
                
                //$response = json_decode($response);
                
                
             }
            //  var_dump($data_l);
            //  exit();
             
             return true;
             
             $flow_outer_control = false; 
        }
        
        else{
            $url = $this->bulkUrl2;
            $recipient = implode(",",array_map('trim', $recipients));

            $postData = array(
                "user"          => $this->userId,
                "senderid"      => $this->senderId2,
                "pwd"           => $this->password,
                "CountryCode"   => $this->countryCode,
                "mobileno"      => $recipient,
                "msgtext"       => $smsText,
                "priority"      => 'High'
            );
        }

        if($flow_outer_control){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
             curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
                return $result;
        }
        
   
    }
}