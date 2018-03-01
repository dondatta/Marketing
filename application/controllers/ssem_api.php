<?php
require_once("home.php");

class ssem_api extends Home
{
    public $user_id;
    
    public function __construct()
    {
        parent::__construct();   
        $this->user_id=$this->session->userdata("user_id");
        
    }


    public function index()
    {
       $this->get_api();
    }

    public function _api_membership_validity($user_id="")
    {
        if($user_id=="") $user_id=$this->session->userdata("user_id");

        if($user_id=="") return false;

        $where['where'] = array('id'=>$user_id);
        $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date','user_type'));

        if(empty($user_expire_date)) return false;
       
        $user_type = $user_expire_date[0]['user_type'];
        if($user_type=="Admin") return true;

        $expire_date = strtotime($user_expire_date[0]['expired_date']);
        $current_date = strtotime(date("Y-m-d"));
        $payment_config=$this->basic->get_data("payment_config");
        $monthly_fee=$payment_config[0]["monthly_fee"];


        if ($expire_date < $current_date && $monthly_fee>0) return false;
        else return true;
    
    }

    public function _api_key_generator()
    {
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login', 'location');
        $val=$this->session->userdata("user_id")."-".substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') , 0 , 7 ).time()
        .substr(str_shuffle('abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789') , 0 , 7 );
        return $val;
    }

    public function get_api()
    {
        if ($this->session->userdata('logged_in') != 1)
        redirect('home/login', 'location');

        $this->_api_membership_validity();

        $data['body'] = "api/ssem_api";
        $data['page_title'] = 'API';
        $api_data=$this->basic->get_data("ssem_api",array("where"=>array("user_id"=>$this->session->userdata("user_id"))));
        $data["api_key"]="";
        if(count($api_data)>0) $data["api_key"]=$api_data[0]["api_key"];
        $this->_viewcontroller($data);
    }

    public function get_api_action()
    { 
         if ($this->session->userdata('logged_in') != 1)
        redirect('home/login', 'location');

        $api_key=$this->_api_key_generator(); 
        if($this->basic->is_exist("ssem_api",array("api_key"=>$api_key)))
        $this->get_api_action();

        $user_id=$this->session->userdata("user_id");        
        if($this->basic->is_exist("ssem_api",array("user_id"=>$user_id)))
        $this->basic->update_data("ssem_api",array("user_id"=>$user_id),array("api_key"=>$api_key));
        else $this->basic->insert_data("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id));
            
        redirect('ssem_api/get_api', 'location');
    }

    /*
    status => 0 = failed, 1 = success
    response_code => 1100  = valid-user+recieved but failed, 1101 = valid-user+recieved and updates, 1110 = valid-user+recieved and inserted , 1000 = valid-user+required data missing, 0000 = invalid user+required field missing
    [bit 1 = user status ,bit 2 = recieve status , bit 3 = update status, bit 4 = insert status]
    details => detailes of response
    */
    public function sync_contact()
    { 
        $return=array("status"=>"unknown","response_code"=>"unknown","details"=>"unknown");
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            echo json_encode($return);
            exit(); 
        }

        $api_key=$_POST["api_key"];
        $first_name=$_POST["first_name"];
        $last_name=$_POST["last_name"];
        $mobile=$_POST["mobile"];
        $email=$_POST["email"];
        $date_birth=$_POST["date_birth"];
        $contact_group_id=$_POST["contact_group_id"];

        $user_id="";        
        if (strpos($api_key, '-') !== false) 
        {
            $explde_api_key=explode('-',$api_key);
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];        
        }
        else $user_id=substr($api_key, 0, 1);

        if($api_key=="" || $user_id=="" || $first_name=="" || $email=="" || $contact_group_id=="")
        {
            $return["status"]="0";            
            $return["response_code"]="1000";
            $return["details"]="API Key, First Name, Email and Contact Group ID are required.";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";            
            $return["response_code"]="0000";
            $return["details"]="API Key does not match with any user.";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";            
            $return["response_code"]="0000";
            $return["details"]="API Key does not match with any user.";
            echo json_encode($return);
            exit();
        }  

        if(!$this->_api_membership_validity($user_id))
        {
            $return["status"]="0";            
            $return["response_code"]="0000";
            $return["details"]="Membership expired.";
            echo json_encode($return);
            exit();
        }
        
        $return["status"]="0";
        $return["response_code"]="1100";
        $return["details"]="Contact has been recieved but failed to sync.";

        $insert_update_data=array("first_name"=>$first_name,"email"=>$email,"contact_type_id"=>$contact_group_id, "user_id"=>$user_id);
        if($date_birth!="") $insert_update_data["date_birth"]=date("Y-m-d",strtotime($date_birth));
        if($mobile!="")     $insert_update_data["phone_number"]=$mobile;
        if($last_name!="")  $insert_update_data["last_name"]=$last_name;

        if($this->basic->is_exist("contacts",array("email"=>$email,"user_id"=>$user_id))) // if exist then update
        {
            if($this->basic->update_data("contacts",array("email"=>$email,"user_id"=>$user_id),$insert_update_data))
            {
                $return["status"]="1";
                $return["response_code"]="1101";
                $return["details"]="Contact has been recieved and updated successfully."; 
            }
        }
        else  // if does not exist insert
        {
            if($this->basic->insert_data("contacts",$insert_update_data))
            {
                $return["status"]="1";
                $return["response_code"]="1110";
                $return["details"]="Contact has been recieved and inserted successfully."; 
            }
        }  

        echo json_encode($return);    
    }


    public function send_sms_api()
    {        
        $return=array("status"=>"unknown","details"=>"unknown");
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            echo json_encode($return);
            exit(); 
        }   
       
        $api_key=$_POST["api_key"];           
        $mobile=$_POST["mobile"]; // comma seperated string
        $config_id=$_POST["reference_id"];
        $message=$_POST["message"];
        
        $user_id="";        
        if (strpos($api_key, '-') !== false) 
        {
            $explde_api_key=explode('-',$api_key);
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];        
        }
        else $user_id=substr($api_key, 0, 1);

        if($api_key=="" || $user_id=="" || $mobile=="" || $config_id=="" || $message=="")
        {      
            $return["status"]="0";
            $return["details"]="API Key, Mobile No., Message and Reference ID are required.";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";
            $return["details"]="API Key does not match with any user.";
            echo json_encode($return);
            exit();
        }   

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";          
            $return["details"]="API Key does not match with any user.";
            echo json_encode($return);
            exit();
        }  

        if(!$this->_api_membership_validity($user_id))
        {
            $return["status"]="0";            
            $return["details"]="Membership expired.";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("sms_api_config",array("user_id"=>$user_id,"id"=>$config_id)))
        {
            $return["status"]="0";
            $return["details"]="This Reference ID is not associated with you.";
            echo json_encode($return);
            exit();
        } 

        $to_numbers=explode(',',$mobile);         
        
        $this->session->set_userdata("user_id",$user_id);
        $this->user_id=$user_id;

        $this->load->library('Sms_manager');
        $this->sms_manager->set_credentioal($config_id);
        foreach ($to_numbers as $phone_number) 
        {            
            $this->sms_manager->send_sms($message, $phone_number);
        }
        $return["status"]="1";
        $return["details"]="Submitted successfully.";
        echo json_encode($return);
    }


    // gateways smtp,mandrill,sendgrid,mailgun
    public function send_email_api()
    { 
        $return=array("status"=>"unknown","details"=>"unknown");
        if ($_SERVER['REQUEST_METHOD'] === 'GET')
        {
            echo json_encode($return);
            exit(); 
        }              
      
        $api_key=$_POST["api_key"];           
        $email=$_POST["email"]; // comma seperated string
        $config_id=$_POST["reference_id"];
        $subject=$_POST["subject"]; 
        $message=$_POST["message"];
        $gateway_name=$_POST["gateway_name"];

        $user_id="";        
        if (strpos($api_key, '-') !== false) 
        {
            $explde_api_key=explode('-',$api_key);
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];        
        }
        else $user_id=substr($api_key, 0, 1);

        if($api_key=="" || $user_id=="" || $email=="" || $config_id=="" || $message=="" || $subject=="" || $gateway_name=="")
        {      
            $return["status"]="0";
            $return["details"]="API Key, Email, Subject, Message and Gateway Name Reference ID are required.";
            echo json_encode($return);
            exit();
        }

        if(!$this->basic->is_exist("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            $return["status"]="0";
            $return["details"]="API Key does not match with any user.";
            echo json_encode($return);
            exit();
        }  

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0")))
        {
            $return["status"]="0";          
            $return["details"]="API Key does not match with any user.";
            echo json_encode($return);
            exit();
        }  

        if(!$this->_api_membership_validity($user_id))
        {
            $return["status"]="0";            
            $return["details"]="Membership expired.";
            echo json_encode($return);
            exit();
        }

        $to_emails=explode(',',$email);         
        
        $this->session->set_userdata("user_id",$user_id);
        $this->user_id=$user_id;

        $from_email="";
        if ($gateway_name=="smtp")
        $from_email="smtp_".$config_id;
        else if ($gateway_name=="mandrill") 
        $from_email="mandrill_".$config_id;
        elseif ($gateway_name=="sendgrid")
        $from_email="sendgrid_".$config_id;
        elseif ($gateway_name=="mailgun") 
        $from_email="mailgun_".$config_id;

        if ($gateway_name=="mandrill") 
        $config_table="email_mandrill_config";
        else if ($gateway_name=="sendgrid")
        $config_table="email_sendgrid_config";
        else if ($gateway_name=="mailgun") 
        $config_table="email_mailgun_config";
        else $config_table="email_config";

        if(!$this->basic->is_exist($config_table,array("user_id"=>$user_id,"id"=>$config_id)))
        {
            $return["status"]="0";
            $return["details"]="This Reference ID is not associated with you.";
            echo json_encode($return);
            exit();
        }  
        
        $this->_email_send_function($from_email, $message, $to_emails, $subject);

        $return["status"]="1";
        $return["details"]="Submitted successfully.";
        echo json_encode($return);
    }


    public function send_notification($api_key="")
    {
        if ($api_key=="") exit();
        $user_id=substr($api_key, 0, 1);

        if(!$this->basic->is_exist("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            echo "API Key does not match with any user.";
            exit();
        }   

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "API Key does not match with any admin user.";
            exit();
        }     

        $current_date = date("Y-m-d");
        $tenth_day_before_expire = date("Y-m-d", strtotime("$current_date + 10 days"));
        $one_day_before_expire = date("Y-m-d", strtotime("$current_date + 1 days"));
        $one_day_after_expire = date("Y-m-d", strtotime("$current_date - 1 days"));

        // echo $tenth_day_before_expire."<br/>".$one_day_before_expire."<br/>".$one_day_after_expire;

        $monthly_fee = $this->basic->get_data('payment_config');

        if($monthly_fee[0]['monthly_fee'] > 0)
        {

            //send notification to members before 10 days of expire date
            $where = array();
            $where['where'] = array(
                'user_type !=' => 'Admin',
                'expired_date' => $tenth_day_before_expire
                );
            $info = array();
            $value = array();
            $info = $this->basic->get_data('users',$where,$select='');
            $from = "";
            $mask = $this->config->item('product_name');
            $subject = "Payment Notification";
            foreach ($info as $value) 
            {
                $message = "Dear {$value['first_name']} {$value['last_name']},<br/> your account will expire after 10 days, Please pay your fees.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);
            }

            //send notificatio to members before 1 day of expire date
            $where = array();
            $where['where'] = array(
                'user_type !=' => 'Admin',
                'expired_date' => $one_day_before_expire
                );
            $info = array();
            $value = array();
            $info = $this->basic->get_data('users',$where,$select='');
            $from = $this->config->item('institute_email');
            $mask = $this->config->item('product_name');
            $subject = "Payment Notification";
            foreach ($info as $value) {
                $message = "Dear {$value['first_name']} {$value['last_name']},<br/> your account will expire tomorrow, Please pay your fees.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);
            }

            //send notificatio to members after 1 day of expire date
            $where = array();
            $where['where'] = array(
                'user_type !=' => 'Admin',
                'expired_date' => $one_day_after_expire
                );
            $info = array();
            $value = array();
            $info = $this->basic->get_data('users',$where,$select='');
            $from = $this->config->item('institute_email');
            $mask = $this->config->item('product_name');
            $subject = "Payment Notification";
            foreach ($info as $value) {
                $message = "Dear {$value['first_name']} {$value['last_name']},<br/> your account has been expired, Please pay your fees for continuity.<br/><br/>Thank you,<br/><a href='".base_url()."'>{$mask}</a> team";
                $to = $value['email'];
                $this->_mail_sender($from, $to, $subject, $message, $mask, $html=0);
            }
        }

    }


    public function scheduler_email($api_key="")
    {
        $number_of_day = 0;

        if ($api_key=="") exit();

        $user_id="";        
        if (strpos($api_key, '-') !== false) 
        {
            $explde_api_key=explode('-',$api_key);
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];        
        }
        else $user_id=substr($api_key, 0, 1);
        
        $this->session->set_userdata("user_id",$user_id);
        $this->user_id=$user_id;

        if(!$this->basic->is_exist("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            echo "API Key does not match with any user.";
            exit();
        }   

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "API Key does not match with any admin user.";
            exit();
        }  

        $this->load->library('Sms_manager');
        $output_dir = FCPATH."upload/attachment";

        //=================================================================================
        // scheduled email start
        //=================================================================================
        $scheduled_email_info=$this->basic->get_data($table="schedule_email", $where=array("where"=>array("is_sent"=>"0")),$select='',$join='',$limit=1,$start=0,$order_by='schedule_time asc');
        foreach ($scheduled_email_info as $row) 
        {
            $scheduled_time=$row["schedule_time"];
            $user_id=$row["user_id"];
            $this->user_id=$user_id;
            $time_zone=$row["time_zone"];
            if ($time_zone== '') {
                $time_zone=$this->config->item("time_zone");
                if ($time_zone== '') {
                    $time_zone="Europe/Dublin";
                }
            }
            date_default_timezone_set($time_zone);
            $cur_datetime=date("Y-m-d H:i:s");            

            if (strtotime($scheduled_time) > strtotime($cur_datetime)) 
            {
                continue;
            }

            $scheduler_id=$row["id"];
            $message=html_entity_decode($row["message"]);
            $subject=$row["subject"];
            $filename=$row["attachment"];
            if($filename=="0") $filename="";
            if($filename!="")
            $attachement=$output_dir.'/'.$filename;
            else $attachement="";

            // make is pending
            $this->basic->update_data("schedule_email", array("id"=>$scheduler_id), array("is_sent"=>"2"));


            $configure_table_name=$row["configure_table_name"];
            $from_email="";
            if ($configure_table_name=="email_config") 
            {
                $from_email="smtp_".$row["api_id"];
            } elseif ($configure_table_name=="email_mandrill_config") 
            {
                $from_email="mandrill_".$row["api_id"];
            } elseif ($configure_table_name=="email_sendgrid_config") 
            {
                $from_email="sendgrid_".$row["api_id"];
            } elseif ($configure_table_name=="email_mailgun_config") 
            {
                $from_email="mailgun_".$row["api_id"];
            }
            
            $contact_id_str=$row["contact_ids"];
            $contact_ids=explode(",", $contact_id_str);

            // if message contains no veriable then send bulk
            if (strpos($message, '#firstname#')== false && strpos($message, '#lastname#')== false && strpos($message, '#mobile#')== false && strpos($message, '#email#')== false) 
            {
                $sent_email=array();
                foreach ($contact_ids as $contact_id) 
                {
                    $contact_info=array();
                    $contact_info=$this->basic->get_data($table="contacts", $where=array("where"=>array("user_id"=>$user_id, "id"=>$contact_id)));
                    if(empty($contact_info)) continue;
                    $sent_email[]=$contact_info[0]['email'];
                }
                if (!empty($sent_email)) 
                {
                    /***** Send email of contact list (bulk) ******/
                    $sent_email=array_unique($sent_email);
                    $this->_email_send_function($from_email, $message, $sent_email, $subject, $attachement, $filename)."<br/>";
                }    
            } 
            else 
            {
                 $sent_email=array();      
                 foreach ($contact_ids as $contact_id) 
                 {
                    $contact_info=array();
                    $contact_info=$this->basic->get_data($table="contacts", $where=array("where"=>array("user_id"=>$user_id, "id"=>$contact_id)));
                    if(empty($contact_info)) continue;

                    $first_name=$contact_info[0]['first_name'];
                    $last_name=$contact_info[0]['last_name'];
                    $email=$contact_info[0]['email'];
                    $mobile=$contact_info[0]['phone_number'];

                    $message_replaced=$message;
                    $message_replaced=str_replace("#firstname#", $first_name, $message_replaced);
                    $message_replaced=str_replace("#lastname#", $last_name, $message_replaced);
                    $message_replaced=str_replace("#mobile#", $mobile, $message_replaced);
                    $message_replaced=str_replace("#email#", $email, $message_replaced);

                    if(in_array($email,$sent_email)) continue;
                    $sent_email[]=$email;

                    $email_array=array($email); // making single email an array     
                    $this->_email_send_function($from_email, $message_replaced, $email_array, $subject, $attachement, $filename)."<br/>"; /***** Send email of contact list (bulk) ******/
                }
            }

           $this->basic->update_data("schedule_email", array("id"=>$scheduler_id), array("is_sent"=>"1", "sent_time"=>date("Y-m-d H:i:s")));
        }
        //=================================================================================
        // scheduled email end
        //=================================================================================            
       
    }

    public function scheduler_sms($api_key="")
    {
        $number_of_day = 0;

        if ($api_key=="") exit();

        $user_id="";        
        if (strpos($api_key, '-') !== false) 
        {
            $explde_api_key=explode('-',$api_key);
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];        
        }
        else $user_id=substr($api_key, 0, 1);
        
        $this->session->set_userdata("user_id",$user_id);
        $this->user_id=$user_id;

        if(!$this->basic->is_exist("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            echo "API Key does not match with any user.";
            exit();
        }   

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "API Key does not match with any admin user.";
            exit();
        }  

        $this->load->library('Sms_manager');
        $output_dir = FCPATH."upload/attachment";
       
        //=================================================================================
        // scheduled SMS start
        //=================================================================================
        $scheduled_sms_info=$this->basic->get_data($table="schedule_sms", $where=array("where"=>array("is_sent"=>"0")),$select='',$join='',$limit=1,$start=0,$order_by='schedule_time asc');
        
        foreach ($scheduled_sms_info as $row) 
        {
            $scheduled_time=$row["schedule_time"];
            $user_id=$row["user_id"];
            $this->user_id=$user_id;
            $time_zone=$row["time_zone"];
            if ($time_zone== '') {
                $time_zone=$this->config->item("time_zone");
                if ($time_zone== '') {
                    $time_zone="Europe/Dublin";
                }
            }
            date_default_timezone_set($time_zone);
            $cur_datetime=date("Y-m-d H:i:s");


            if (strtotime($scheduled_time) > strtotime($cur_datetime)) {
                continue;
            }

            $scheduler_id=$row["id"];
            $message=urldecode($row["message"]);
            $config_id=$row["api_id"];
            $this->sms_manager->set_credentioal($config_id);
            $contact_id_str=$row["contact_ids"];
            $contact_ids=explode(",", $contact_id_str);

            // make is pending
            $this->basic->update_data("schedule_sms", array("id"=>$scheduler_id), array("is_sent"=>"2"));
                                        
            $sent_number=array();            

            foreach ($contact_ids as $contact_id) 
            {
                $contact_info=array();
                $contact_info=$this->basic->get_data($table="contacts", $where=array("where"=>array("user_id"=>$user_id, "id"=>$contact_id)));
                
                $first_name=$contact_info[0]['first_name'];
                $last_name=$contact_info[0]['last_name'];
                $phone_number=$contact_info[0]['phone_number'];
                $email=$contact_info[0]['email'];

                $message_replaced=$message;
                $message_replaced=str_replace("#firstname#", $first_name, $message_replaced);
                $message_replaced=str_replace("#lastname#", $last_name, $message_replaced);
                $message_replaced=str_replace("#mobile#", $phone_number, $message_replaced);
                $message_replaced=str_replace("#email#", $email, $message_replaced);

                if(in_array($phone_number,$sent_number)) continue;              
                $sent_number[]=$phone_number;

                $this->sms_manager->send_sms($message_replaced, $phone_number);                
            }

            $this->basic->update_data("schedule_sms", array("id"=>$scheduler_id), array("is_sent"=>"1", "sent_time"=>date("Y-m-d H:i:s")));
        }
        //=================================================================================
        // scheduled SMS end
        //=================================================================================
       
    }

    public function birthday_scheduler($api_key="")
    {
        $number_of_day = 0;

        if ($api_key=="") exit();

        $user_id="";        
        if (strpos($api_key, '-') !== false) 
        {
            $explde_api_key=explode('-',$api_key);
            if(array_key_exists(0, $explde_api_key))
            $user_id=$explde_api_key[0];        
        }
        else $user_id=substr($api_key, 0, 1);
        
        $this->session->set_userdata("user_id",$user_id);
        $this->user_id=$user_id;

        if(!$this->basic->is_exist("ssem_api",array("api_key"=>$api_key,"user_id"=>$user_id)))
        {
            echo "API Key does not match with any user.";
            exit();
        }   

        if(!$this->basic->is_exist("users",array("id"=>$user_id,"status"=>"1","deleted"=>"0","user_type"=>"Admin")))
        {
            echo "API Key does not match with any admin user.";
            exit();
        }  

        $this->load->library('Sms_manager');
        $output_dir = FCPATH."upload/attachment";


        //=================================================================================
        // Birthday Wish email start
        //=================================================================================
        $birthday_email_info=$this->basic->get_data($table="birthday_reminder_email", $where=array("where"=>array("status"=>"1")));
    
        foreach ($birthday_email_info as $row) 
        {
            $user_id=$row["user_id"];
            $this->user_id=$user_id;
            $scheduler_id=$row["id"];
            $message=html_entity_decode($row["message"]);
            $subject=$row["subject"];
            $filename="";
            $attachement="";

            $configure_table_name=$row["configure_table_name"];
            $from_email="";
            if ($configure_table_name=="email_config") {
                $from_email="smtp_".$row["api_id"];
            } elseif ($configure_table_name=="email_mandrill_config") {
                $from_email="mandrill_".$row["api_id"];
            } elseif ($configure_table_name=="email_sendgrid_config") {
                $from_email="sendgrid_".$row["api_id"];
            } elseif ($configure_table_name=="email_mailgun_config") {
                $from_email="mailgun_".$row["api_id"];
            }
                        
            $time_zone=$row["time_zone"];
            if ($time_zone== '') {
                $time_zone=$this->config->item("time_zone");
                if ($time_zone== '') {
                    $time_zone="Europe/Dublin";
                }
            }
            date_default_timezone_set($time_zone);
            $cur_date=date("Y-m-d");
            $cur_year=date("Y");

            //mostofa 6/15/16
            $new_date_variable = date("m-d", strtotime("$cur_date + $number_of_day days"));


            $contact_info_2d=array();
            $where_date_birth=array("where"=>array("user_id"=>$user_id,"email_last_wished_year !="=>$cur_year,"DATE_FORMAT(date_birth,'%m-%d')"=>$new_date_variable));
            $contact_info_2d=$this->basic->get_data($table="contacts", $where_date_birth);
             
            // if message contains no veriable then send bulk
            if (strpos($message, '#firstname#')== false && strpos($message, '#lastname#')== false && strpos($message, '#mobile#')== false && strpos($message, '#email#')== false) {
                $sent_email=array();
                foreach ($contact_info_2d as $contact_info) 
                {
                    $sent_email[]=$contact_info['email'];
                }
                if (!empty($sent_email)) 
                {
                    $sent_email=array_unique($sent_email);
                    /***** Send email of contact list (bulk) ******/
                    $this->_email_send_function($from_email, $message, $sent_email, $subject, $attachement, $filename)."<br/>";
                }    
            } 
            else 
            {
                $sent_email=array();                
                foreach ($contact_info_2d as $contact_info) 
                {
                    $first_name=$contact_info['first_name'];
                    $last_name=$contact_info['last_name'];
                    $email=$contact_info['email'];                    
                    $mobile=$contact_info['phone_number'];

                    $message_replaced=$message;
                    $message_replaced=str_replace("#firstname#", $first_name, $message_replaced);
                    $message_replaced=str_replace("#lastname#", $last_name, $message_replaced);
                    $message_replaced=str_replace("#mobile#", $mobile, $message_replaced);
                    $message_replaced=str_replace("#email#", $email, $message_replaced);

                    if(in_array($email,$sent_email)) continue;
                    $sent_email[]=$email;

                    $email_array=array($email); // making single email an array     
                    $this->_email_send_function($from_email, $message_replaced, $email_array, $subject, $attachement, $filename)."<br/>"; /***** Send email of contact list (bulk) ******/
                }
            }

            if(!empty($sent_email))
            {
                $this->db->where("user_id",$user_id);
                $this->db->where_in("email",$sent_email);
                $this->db->update("contacts",array("email_last_wished_year"=>$cur_year));
            }
            
        }
        //=================================================================================
        // Birthday Wish email end
        //=================================================================================


        //=================================================================================
        // Birthday Wish SMS start
        //=================================================================================
        $birthday_sms_info=$this->basic->get_data($table="birthday_reminder", $where=array("where"=>array("status"=>"1")));
    
        foreach ($birthday_sms_info as $row) 
        {
            $scheduler_id=$row["id"];
            $user_id=$row["user_id"];
            $this->user_id=$user_id;
            $message=html_entity_decode($row["message"]);
            $config_id=$row["api_id"];
            $this->sms_manager->set_credentioal($config_id);
                        
            $time_zone=$row["time_zone"];
            if ($time_zone== '') {
                $time_zone=$this->config->item("time_zone");
                if ($time_zone== '') {
                    $time_zone="Europe/Dublin";
                }
            }
            date_default_timezone_set($time_zone);
            $cur_date=date("Y-m-d");
            $cur_year=date("Y");

            //mostofa 6/15/16
            $new_date_variable = date("m-d", strtotime("$cur_date + $number_of_day days"));

            $contact_info_2d=array();
            $where_date_birth=array("where"=>array("user_id"=>$user_id,"sms_last_wished_year !="=>$cur_year,"DATE_FORMAT(date_birth,'%m-%d')"=>$new_date_variable));
            $contact_info_2d=$this->basic->get_data($table="contacts", $where_date_birth);
            
            $sent_number=array();       
            foreach ($contact_info_2d as $contact_info) 
            {
                $first_name=$contact_info['first_name'];
                $last_name=$contact_info['last_name'];
                $email=$contact_info['email'];
                $mobile=$contact_info['phone_number'];

                $message_replaced=$message;
                $message_replaced=str_replace("#firstname#", $first_name, $message_replaced);
                $message_replaced=str_replace("#lastname#", $last_name, $message_replaced);
                $message_replaced=str_replace("#mobile#", $mobile, $message_replaced);
                $message_replaced=str_replace("#email#", $email, $message_replaced);

                if(in_array($mobile,$sent_number)) continue;                
                $sent_number[]=$mobile;

                $this->sms_manager->send_sms($message_replaced, $mobile);
            }

            if(!empty($sent_number))
            {
                $this->db->where("user_id",$user_id);
                $this->db->where_in("phone_number",$sent_number);
                $this->db->update("contacts",array("sms_last_wished_year"=>$cur_year));
            }

            
        }
        //=================================================================================
        // Birthday Wish SMS end
        //=================================================================================
    }




    // public function call_sync_contact()
    // {  
    //     $url = 'http://konok-pc/xeroneit/sms_reseller/ssem_api/sync_contact';
    //     $data=array
    //     (
    //         "api_key"           => "1n2U51455446947iBlwn",
    //         "first_name"        => "alamin",
    //         "last_name"         => "Jwel",
    //         "mobile"            => "01723309003",
    //         "email"             => "jwel.cse@gmail.com",
    //         "contact_group_id"  => "1",
    //         "date_birth"        => "1989-12-10"
    //     );
         
    //     $ch=curl_init($url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data) ;
    //     curl_setopt($ch, CURLOPT_HEADER, 0);  
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    //     curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
    //     curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");  
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    //     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
    //     $response = curl_exec( $ch );
    //     curl_close($ch);        
    //     $response=json_decode($response,TRUE);
    //     print_r($response);
    // }


    // public function call_send_email_api()
    // {  
    //     $url = 'http://konok-pc/xeroneit/sms_reseller/ssem_api/send_email_api';
        
    //     $data=array
    //     (
    //         "api_key"       => "1n2U51455446947iBlwn",
    //         "email"         => "jwel.cse@gmail.com",
    //         "reference_id"  => "1","gateway_name"=>"smtp",
    //         "subject"       => "test subject",
    //         "message"       => "this is a test email"
    //     ); 
         
    //     $ch=curl_init($url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data) ;
    //     curl_setopt($ch, CURLOPT_HEADER, 0);  
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    //     curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
    //     curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");  
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    //     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
    //     $response = curl_exec( $ch );
    //     curl_close($ch);        
    //     $response=json_decode($response,TRUE);
    //     print_r($response);
    // }

    // public function call_send_sms_api()
    // {  
    //     $url = 'http://konok-pc/xeroneit/sms_reseller/ssem_api/send_sms_api';
        
    //     $data=array
    //     (
    //         "api_key"       => "1UZXH1455455545NZDQ9",
    //         "mobile"        => "8801722977459,8801723309003",
    //         "reference_id"  => "5",
    //         "message"       => "this is a test sms"
    //     );

    //     $ch=curl_init($url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data) ;
    //     curl_setopt($ch, CURLOPT_HEADER, 0);  
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    //     curl_setopt($ch, CURLOPT_COOKIEJAR, "my_cookies.txt");  
    //     curl_setopt($ch, CURLOPT_COOKIEFILE, "my_cookies.txt");  
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
    //     curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");  
    //     $response = curl_exec( $ch );
    //     curl_close($ch);        
    //     $response=json_decode($response,TRUE);
    //     print_r($response);
    // }
    


    
}
