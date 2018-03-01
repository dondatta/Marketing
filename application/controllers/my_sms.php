<?php 
require_once("home.php");

class My_sms extends Home
{

    public $user_id;
    public function __construct()
    {
        parent::__construct();
        
        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }

        $this->important_feature();

        $this->load->library('Sms_manager');

        $this->user_id=$this->session->userdata("user_id");

        if ($this->session->userdata('logged_in') == 1 && $this->session->userdata('user_type') != 'Admin') {
            $where['where'] = array('id'=>$this->user_id);
            $user_expire_date = $this->basic->get_data('users',$where,$select=array('expired_date'));
            $expire_date = strtotime($user_expire_date[0]['expired_date']);
            $current_date = strtotime(date("Y-m-d"));
            $payment_config=$this->basic->get_data("payment_config");
            $monthly_fee=$payment_config[0]["monthly_fee"];
            if ($expire_date < $current_date && $monthly_fee>0)
            redirect('payment/member_payment_history','Location');
        }
    }

    public function index()
    {
        $this->_viewcontroller();
    }

    public function csv_upload()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $type= strip_tags($this->input->post('hidden_import_type', true));

        $this->load->library('upload');
        $filename=$this->user_id."_".$type."_".time().substr(uniqid(mt_rand(), true), 0, 6);
        $config['file_name'] = $filename;
        $config['upload_path'] = './upload/csv/';
        $config['allowed_types'] = 'text/plain|text/anytext|csv|text/x-comma-separated-values|text/comma-separated-values|application/octet-stream|application/vnd.ms-excel|application/x-csv|text/x-csv|text/csv|application/csv|application/excel|application/vnd.msexcel';
        $this->upload->initialize($config);
        
        if ($this->upload->do_upload('csv_file') != true) {
            $upload_image = array('upload_data' => $this->upload->data());
            $temp= $upload_image['upload_data']['file_name'];
            $response['status']=$this->upload->display_errors();
        } else {
            $upload_image = array('upload_data' => $this->upload->data());
            $csv= base_url().'upload/csv/'.$upload_image['upload_data']['file_name'];
            $file=file_get_contents($csv);
            
            $file=str_replace(array("\'", "\"","\t","\r"," "), '', $file);
            $file=str_replace(array("\n"), ',', $file);
            $file=trim($file,",");

            $response['status']='ok';
            $response['file']=$file;
        }

        echo json_encode($response);
    }

    public function load_template()
    {
       $id= $this->input->post('template_id'); 
       if($id=='') 
       {
         $no_response["message"] = "";
         echo json_encode($no_response);
         exit();
       }
 
       $this->db->select('message');
       $this->db->from('message_template');
       $this->db->where(array("id"=>$id,"user_id"=>$this->user_id));
       $data=$this->db->get()->result_array();
       $response=array();
       $message = "";
       foreach ($data as $key => $value) 
       {
          $message=$value["message"];
       }
       $response["message"] = $message;
       echo json_encode($response);
    }


    public function sms_api()
    {
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('SMSAPILastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('SMSAPILastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('sms_api_config.deleted', '0');
        $crud->where('sms_api_config.user_id', $this->user_id);

        $crud->set_theme('flexigrid');
        $crud->set_table('sms_api_config');
        $crud->order_by('gateway_name');
        $crud->set_subject($this->lang->line('SMS API'));
        $crud->columns('SL','id','gateway_name', 'username_auth_id', 'password_auth_token', 'api_id', 'phone_number','credit', 'status');
        $crud->required_fields('gateway_name');
        $crud->fields('gateway_name', 'username_auth_id', 'password_auth_token', 'api_id', 'phone_number', 'status');
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->callback_column('credit', array($this, 'credit_display_crud'));
        $crud->callback_after_insert(array($this, 'insert_user_id_sms_api'));    /**insert the user_id***/

        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialSMSAPI'));

        $crud->display_as('gateway_name', $this->lang->line('Gateway'));
        $crud->display_as('username_auth_id', $this->lang->line('Auth ID/ Auth Key/ API Key/ MSISDN/ Account Sid/ Account ID/ Username/ Admin'));
        $crud->display_as('password_auth_token', $this->lang->line('Auth Token/ API Secret/ Password'));
        $crud->display_as('api_id', $this->lang->line("API ID (if clickatell)"));
        $crud->display_as('phone_number', $this->lang->line("Sender/ Sender ID/ Mask/ From"));
        $crud->display_as('id', $this->lang->line('Reference ID'));
        $crud->display_as('credit', $this->lang->line('Remaining Credit'));
        $crud->display_as('status', $this->lang->line('Status'));

        $crud->unset_read();
        $crud->unset_print();
        $crud->unset_export();
    
    
        $output = $crud->render();
        $data['page_title'] = 'SMS API';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }
    
    
    public function sms_template()
    {
    
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('SMSTemplateLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('SMSTemplateLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        
        $crud->where('message_template.deleted', '0');
        $crud->where('message_template.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();

        $crud->set_theme('flexigrid');
        $crud->set_table('message_template');
        $crud->order_by('template_name');
        $crud->set_subject($this->lang->line('SMS Template'));
        $crud->required_fields('message', 'template_name');
        $crud->columns('SL', 'template_name', 'message');
        $crud->fields('template_name', 'message');
        
        $crud->callback_after_insert(array($this, 'insert_user_id_sms_template'));    /**insert the user_id***/

        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialSMSTemplate'));
        $crud->callback_field('message', array($this, 'message_field_with_instruction'));

        $state = $crud->getState();
        if ($state == 'read') {
            $crud->columns('template_name', 'message');
        } else {
            $crud->columns('SL', 'template_name', 'message');
        }

        $crud->display_as('template_name', $this->lang->line('Template Name'));
        $crud->display_as('message', $this->lang->line('Message'));
        
        $output = $crud->render();
        $data['output']=$output;
        $data['page_title'] = 'SMS Template';
        $data['crud']=1;
        $this->_viewcontroller($data);
    }
    
    
    public function scheduled_sms()
    {
        $data['body']="my_sms/schedule_sms/scheduled_sms";
        $data['page_title'] = 'Scheduled SMS';
        $this->_viewcontroller($data);
    }
    
    public function upcoming_scheduled_sms_data()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'schedule_time';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'ASC';
        $order_by_str=$sort." ".$order;
        
        $to_date="";
        $from_date="";
    
        if (isset($_POST['is_searched'])) {
            $to_date= strip_tags(trim($this->input->post('schedule_to_date', true)));
            $from_date= strip_tags(trim($this->input->post('schedule_from_date', true)));
        }
        
        
        if ($to_date) {
            $to_date=date('Y-m-d', strtotime($to_date));
            $where_simple["date_format(schedule_time,'%Y-%m-%d') <= "] =    $to_date;
        }
        
        if ($from_date) {
            $from_date=date('Y-m-d', strtotime($from_date));
            $where_simple["date_format(schedule_time,'%Y-%m-%d') >= "] =    $from_date;
        }
        
        $where_simple['schedule_sms.user_id']=$this->user_id;
        $where=array('where'=>$where_simple);
        $join=array("sms_api_config"=>"schedule_sms.api_id=sms_api_config.id,left");
        $select=array("schedule_sms.*","CONCAT(sms_api_config.gateway_name,' : ',sms_api_config.phone_number) AS send_as");
        $offset = ($page-1)*$rows;
        $result = array();
        $info=$this->basic->get_data('schedule_sms', $where, $select, $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='', $num_rows=0);
        $total_rows_array=$this->basic->count_row($table="schedule_sms", $where, $count="schedule_sms.id", $join);
        $total_result=$total_rows_array[0]['total_rows'];
        echo convert_to_grid_data($info, $total_result);
    }
    
    
    public function schedule_contacts()
    {
        $schedule_info=$_POST['schedule_info'];
        $contacts=$schedule_info['contact_ids'];
        $contacts=explode(",", $contacts);
        
        $where_in = array('id'=> $contacts);
        $where = array('where_in'=> $where_in);
        $data['contact_details']=$this->basic->get_data('contacts', $where, $select='', $join='', $limit='', $start='', $order_by='', $group_by='', $num_rows=0);
        
        $this->load->view('my_sms/schedule_sms/schedule_contact_details', $data);
    }
    
    /*** Delete Schedule ******/
    public function delete_schedule()
    {
        $schedule_id=$this->input->post('schedule_id', true);
        $where=array("id"=>$schedule_id);
        $this->basic->delete_data('schedule_sms', $where);
    }
        
    public function add_schedule()
    {
        $data['body']="my_sms/schedule_sms/schedule_add";
        
        /**Get contact number and contact_type***/
        $user_id = $this->user_id;
        $table_type = 'contact_type';   
        $where_type['where'] = array('user_id'=>$user_id);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='type');  
        $result = array();

        foreach ($info_type as  $value) 
        {
            $search_key = $value['id'];
            $search_type = $value['type'];

            $where_simple=array('contacts.user_id'=>$this->user_id);
            $this->db->where("FIND_IN_SET('$search_key',contacts.contact_type_id) !=", 0);
            $where=array('where'=>$where_simple);
            $this->db->select("count(contacts.id) as number_count",false);    
            $contact_details=$this->basic->get_data('contacts', $where, $select='', $join='', $limit='', $start='', $order_by='contacts.first_name', $group_by='', $num_rows=0);
        
            foreach ($contact_details as $key2 => $value2) 
            {
                if($value2['number_count']>0)
                $group_name[$search_key] = $search_type." (".$value2['number_count'].")";
            }
                
        }     
        
        /*** get Sms Template ***/
        $where=array("where"=>array('user_id'=>$this->user_id));
        $data['sms_template']=$this->basic->get_data('message_template', $where, $select=array("id","template_name"), $join='', $limit='', $start='', $order_by='template_name ASC', $group_by='', $num_rows=0);
                                                        
        /***get sms config***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        
        $sms_api_config_option=array();
        foreach ($sms_api_config as $info) {
            $id=$info['id'];
            $sms_api_config_option[$id]=$info['gateway_name'].": ".$info['phone_number'];
        }

        $data['sms_option']=$sms_api_config_option;
        // $data['contacts_info']=$contact_info;
        $data['groups_name']=$group_name;
        $data['time_zone_str']=$this->time_zone_drop_down();
        $data['page_title'] = 'Scheduled SMS';
        $this->_viewcontroller($data);
    }
    


    public function add_schedule_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $schedule_name= strip_tags(trim($this->input->post('schedule_name', true)));
        $message=$this->input->post('message', true);
        $day= strip_tags(trim($this->input->post('day', true)));
        $month= strip_tags(trim($this->input->post('month', true)));
        $year= strip_tags(trim($this->input->post('year', true)));
        $hour= strip_tags(trim($this->input->post('hour', true)));
        $minute= strip_tags(trim($this->input->post('minute', true)));
        $time_zone= strip_tags(trim($this->input->post('time_zone', true)));
        $from_sms= strip_tags(trim($this->input->post('from_sms', true)));
        
        // $contacts_id=$this->input->post('contacts_id', true);
        // $contacts_id=implode(',', $contacts_id);

        $contacts_sms_group= $this->input->post('contacts_id', true);
        if(!is_array($contacts_sms_group))
            $contacts_sms_group=array();   

        $contacts_id=array();
        foreach ($contacts_sms_group as $key => $value) 
        {
            $where_simple=array('contacts.user_id'=>$this->user_id);
            $this->db->where("FIND_IN_SET('$value',contacts.contact_type_id) !=", 0);
            $where=array('where'=>$where_simple);    
            $contact_details=$this->basic->get_data('contacts', $where, $select='id');        
            foreach ($contact_details as $key2 => $value2) 
            {
                $contacts_id[] = $value2["id"];                
            }
        }

        $contacts_id = array_filter($contacts_id);
        $contacts_id = array_unique($contacts_id);
        $contacts_id=implode(',', $contacts_id);
        
        $schedule_time="{$year}-{$month}-{$day} {$hour}:{$minute}:00";
        $insert_data=array("user_id"=>$this->user_id,"contact_ids"=>$contacts_id,"schedule_name"=>$schedule_name,"message"=>$message,"time_zone"=>$time_zone,"schedule_time"=>$schedule_time,"api_id"=>$from_sms);
        $this->basic->insert_data("schedule_sms", $insert_data);
        $this->session->set_flashdata('success_message', 1);
    }
    
    
    
    public function birthday_sms()
    {
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('birthdaySMSLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('birthdaySMSLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('birthday_reminder.deleted', '0');
        $crud->where('birthday_reminder.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();

        $crud->set_theme('flexigrid');
        $crud->set_table('birthday_reminder');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line('Scheduled SMS (Birthday Wish)'));
        $crud->required_fields('message', "time_zone", "api_id");
        $crud->columns('SL', 'message', 'time_zone', 'api_id', 'status');
        $crud->callback_field('message', array($this, 'message_field_with_instruction'));
        $crud->unset_texteditor("message");

        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialBirhdaySMS'));
        
        
        
        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->callback_column('api_id', array($this, 'api_display_crud'));
        $state = $crud->getState();
        if ($state == 'add' || $state=='edit') {
            $crud->callback_field('time_zone', array($this, 'time_zone_drop_down'));
            $crud->callback_field('sms_template', array($this, 'sms_template_field'));
            $crud->fields('api_id', 'sms_template', 'message', 'time_zone', 'status');
        }
        else $crud->fields('message', 'api_id', 'time_zone','status');
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_field('api_id', array($this, 'api_field_crud'));

        // Only one schedule can be active at a time
        $crud->callback_after_insert(array($this, 'insert_user_id_birthday')); // insert id + check active functionalities as well
        $crud->callback_after_update(array($this, 'make_up_scheduler_setting_edit'));

        $crud->display_as('sms_template', $this->lang->line('SMS Template'));
        $crud->display_as('time_zone', $this->lang->line('Time Zone'));
        $crud->display_as('api_id', $this->lang->line('Send As'));
        $crud->display_as('message', $this->lang->line('Message'));
        $crud->display_as('status', $this->lang->line('Status'));
        
        
        $output = $crud->render();
        $data['page_title'] = 'Birthday Wish SMS';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }


    public function sms_history()
    {
        $data['body']="my_sms/sms_history/sms_history";
        $data['page_title'] = 'My SMS History';
        $this->_viewcontroller($data);
    }
    
    
    public function my_sms_history_data()
    {
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $rows = isset($_POST['rows']) ? intval($_POST['rows']) : 5;
        $sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'id';
        $order = isset($_POST['order']) ? strval($_POST['order']) : 'DESC';
        $order_by_str=$sort." ".$order;
        
        $to_date="";
        $from_date="";
    
        if (isset($_POST['is_searched'])) {
            $to_date= strip_tags(trim($this->input->post('schedule_to_date', true)));
            $from_date= strip_tags(trim($this->input->post('schedule_from_date', true)));
        }
        
        
        if ($to_date) {
            $to_date=date('Y-m-d', strtotime($to_date));
            $where_simple["date_format(sent_time,'%Y-%m-%d') <= "] =    $to_date;
        }
        
        if ($from_date) {
            $from_date=date('Y-m-d', strtotime($from_date));
            $where_simple["date_format(sent_time,'%Y-%m-%d') >= "] =    $from_date;
        }
        
        $where_simple['sms_history.user_id']=$this->user_id;
        $where=array('where'=>$where_simple);
        $select=array("sms_history.*","CONCAT(sms_api_config.gateway_name,' : ',sms_api_config.phone_number) AS send_as");
        $join=array("sms_api_config"=>"sms_history.gateway_id=sms_api_config.id,left");
        $offset = ($page-1)*$rows;
        $result = array();
        $info=$this->basic->get_data('sms_history', $where, $select, $join, $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='', $num_rows=0);
        $total_rows_array=$this->basic->count_row($table="sms_history", $where, $count="sms_history.id", $join);
        $total_result=$total_rows_array[0]['total_rows'];
        echo convert_to_grid_data($info, $total_result);
    }
    
    
    // public function send_sms()
    // {
    //     $data['body']="my_sms/send_sms/send_sms";
        
    //     /**Get contact number and contact_type***/
    //     $select=array("first_name","last_name","phone_number","email",'contacts.id as contact_id','contact_type_id','type');
    //     $where_simple=array('contacts.user_id'=>$this->user_id);
    //     $where=array('where'=>$where_simple);
    //     $join=array("contact_type"=>"contacts.contact_type_id=contact_type.id,left");
    //     $contact_details=$this->basic->get_data('contacts', $where, $select, $join, $limit='', $start='', $order_by='contact_type.type,contacts.first_name', $group_by='', $num_rows=0);
        
    //     $contact_info=array();
    //     $group_name=array();
    //     foreach ($contact_details as $details) {
    //         $contact_type_id=$details['contact_type_id'];
    //         $contact_type_name= $details['type'];
    //         $contct_first_name=$details['first_name'];
    //         $contact_last_name=$details['last_name'];
    //         $contact_mobile=$details['phone_number'];
    //         $contact_id=$details['contact_id'];
            
    //         $contact_info[$contact_type_id][]=array("contact_id"=>$contact_id,"first_name"=>$contct_first_name,"last_name"=>$contact_last_name,"mobile"=>$contact_mobile);
    //         $group_name[$contact_type_id]=$contact_type_name;
    //     }
        
    //     /*** get Sms Template ***/
    //     $where=array("where"=>array('user_id'=>$this->user_id));
    //     $data['sms_template']=$this->basic->get_data('message_template', $where, $select='', $join='', $limit='', $start='', $order_by='template_name ASC', $group_by='', $num_rows=0);
                                                                
    //     /***get sms config***/
    //     $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
    //     $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        
    //     $sms_api_config_option=array();
    //     foreach ($sms_api_config as $info) {
    //         $id=$info['id'];
    //         $sms_api_config_option[$id]=$info['gateway_name'].": ".$info['phone_number'];
    //     }


    //     $data['sms_option']=$sms_api_config_option;
    //     $data['contacts_info']=$contact_info;
    //     $data['groups_name']=$group_name;
    //     $data['page_title'] = 'Send SMS';
    //     $this->_viewcontroller($data);
    // }
    

    public function send_sms(){

        $data['body']="my_sms/send_sms/send_sms";
        
        $user_id = $this->user_id;
        $table_type = 'contact_type';   
        $where_type['where'] = array('user_id'=>$user_id);
        $info_type = $this->basic->get_data($table_type,$where_type,$select='', $join='', $limit='', $start='', $order_by='type');  
        $result = array();

        $group_name=array();
        foreach ($info_type as  $value) 
        {
            $search_key = $value['id'];
            $search_type = $value['type'];

            $where_simple=array('contacts.user_id'=>$this->user_id);
            $this->db->where("FIND_IN_SET('$search_key',contacts.contact_type_id) !=", 0);
            $where=array('where'=>$where_simple);
            $this->db->select("count(contacts.id) as number_count",false);    
            $contact_details=$this->basic->get_data('contacts', $where, $select='', $join='', $limit='', $start='', $order_by='contacts.first_name', $group_by='', $num_rows=0);
        
            foreach ($contact_details as $key2 => $value2) 
            {
                if($value2['number_count']>0)
                $group_name[$search_key] = $search_type." (".$value2['number_count'].")";
            }
                
        }      

        /*** get Sms Template ***/
        $where=array("where"=>array('user_id'=>$this->user_id));
        $data['sms_template']=$this->basic->get_data('message_template', $where, $select=array('id','template_name'), $join='', $limit='', $start='', $order_by='template_name ASC', $group_by='', $num_rows=0);
                                                                
        /***get sms config***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);
        
        $sms_api_config_option=array();
        foreach ($sms_api_config as $info) {
            $id=$info['id'];
            $sms_api_config_option[$id]=$info['gateway_name'].": ".$info['phone_number'];
        }


        $data['sms_option']=$sms_api_config_option;
        // $data['contacts_info']=$contact_info;
        $data['groups_name']=$group_name;


        $data['page_title'] = 'Send SMS';
        $this->_viewcontroller($data);
    }

    
    public function sms_send_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $contacts_mobile_group=$this->input->post('contacts_mobile', true);
        $to_numbers= strip_tags(trim($this->input->post('to_numbers', true)));
        $to_numbers=explode(",", $to_numbers);
        $message=strip_tags(trim($this->input->post('message', true)));
        
        $all_contacts=array();

        if(!is_array($contacts_mobile_group))
        $contacts_mobile_group=array();

        $contacts_mobile=array();
        foreach ($contacts_mobile_group as $key => $value) 
        {
            $where_simple=array('contacts.user_id'=>$this->user_id);
            $this->db->where("FIND_IN_SET('$value',contacts.contact_type_id) !=", 0);
            $where=array('where'=>$where_simple);    
            $contact_details=$this->basic->get_data('contacts', $where, $select='phone_number');        
            foreach ($contact_details as $key2 => $value2) 
            {
                $contacts_mobile[] = $value2["phone_number"];                
            }

        }


        $contacts_mobile=array_filter($contacts_mobile);
        $to_numbers=array_filter($to_numbers);

        if(!empty($contacts_mobile))
        $all_contacts=array_add($contacts_mobile, $to_numbers);
        else
        $all_contacts=array_add($to_numbers, $contacts_mobile);        

        $all_contacts = array_unique($all_contacts);

        $config_id= strip_tags(trim($this->input->post('from_sms', true)));
        
        
        /**Get contact number and contact_type***/
        $contact_details=array();
        if(count($contacts_mobile)>0)
        {
            $where_in=array('phone_number'=>$contacts_mobile);
            $where_simple=array('user_id'=>$this->user_id);
            $where=array('where_in'=>$where_in,'where'=>$where_simple);
            $contact_details=$this->basic->get_data('contacts', $where, $select='', $join='', $limit='', $start='', $group_by='', $num_rows=0);
        }
        /***Set Sms Credential ***/
        $this->sms_manager->set_credentioal($config_id);
        $sent_number=array();
    
        foreach ($contact_details as $details) 
        {
            $first_name=$details['first_name'];
            $last_name=$details['last_name'];
            $phone_number=$details['phone_number'];
            $email=$details['email'];

            $message_replaced=$message;
            $message_replaced=str_replace("#firstname#", $first_name, $message_replaced);
            $message_replaced=str_replace("#lastname#", $last_name, $message_replaced);
            $message_replaced=str_replace("#mobile#", $phone_number, $message_replaced);
            $message_replaced=str_replace("#email#", $email, $message_replaced);

            if(in_array($phone_number,$sent_number)) continue;  
            $sent_number[]=$phone_number;

            $this->sms_manager->send_sms($message_replaced, $phone_number);
        }
        
        /***** Send sms which numbers are not in the contact list ******/
        $remaining_numbers=array_diff($all_contacts, $sent_number);
        $remaining_numbers=array_filter($remaining_numbers);
        $remaining_numbers=array_unique($remaining_numbers);
        foreach ($remaining_numbers as $numbers) 
        {
            if ($numbers!='') 
            {
                $this->sms_manager->send_sms($message, $numbers);
            }
        }
    }


    //=================================================================================================================================
    // crud call back functions	
    public function status_field_crud($value, $row)
    {
        if ($value=='') {
            $value=1;
        }
        return form_dropdown('status', array(0=>$this->lang->line('Inactive'), 1=>$this->lang->line('Active')), $value, 'class="form-control" id="field-status"');
    }

    public function status_display_crud($value, $row)
    {
        if ($value==1) {
            return "<span class='label label-success'>".$this->lang->line('Active')."</span>";
        } else {
            return "<span class='label label-warning'>".$this->lang->line('Inactive')."</span>";
        }
    }

    public function credit_display_crud($value, $row)
    {
        $gateway_name=$row->gateway_name;
        $credit="-";
        $this->sms_manager->set_credentioal($row->id);
        if($gateway_name=="plivo") $credit=$this->sms_manager->get_plivo_balance();
        if($gateway_name=="clickatell") $credit=$this->sms_manager->get_clickatell_balance();
        if($gateway_name=="nexmo") $credit=$this->sms_manager->get_nexmo_balance();
		if($gateway_name=="africastalking.com") $credit=$this->sms_manager->africastalking_sms_balance();
        if($gateway_name=="infobip.com") $credit=$this->sms_manager->infobip_balance_check();
		if($gateway_name=="Shreeweb") $credit=$this->sms_manager->get_shreeweb_balance();
		
		

        return $credit;
    }

    public function api_field_crud($value, $row)
    {
        /***get sms config***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $sms_api_config=$this->basic->get_data('sms_api_config', $where, $select='', $join='', $limit='', $start='', $order_by='phone_number ASC', $group_by='', $num_rows=0);

        $str='';
        $str.='<select id="api_id" class="form-control" name="api_id">';
        $str.='<option value="">'.$this->lang->line('SMS API').'</option>';
        for ($i=0;$i<count($sms_api_config);$i++) {
            if ($sms_api_config[$i]['id']==$value) {
                $str.='<option selected="selected" value="'.$sms_api_config[$i]['id'].'">'.$sms_api_config[$i]['gateway_name'].": ".$sms_api_config[$i]['phone_number'].'</option>';
            } else {
                $str.='<option value="'.$sms_api_config[$i]['id'].'">'.$sms_api_config[$i]['gateway_name'].": ".$sms_api_config[$i]['phone_number'].'</option>';
            }
        }
        $str.='</select>';
        return $str;
    }

    public function api_display_crud($value, $row)
    {
        $where=array("where"=>array('birthday_reminder.user_id'=>$this->user_id,'birthday_reminder.id'=>$row->id));
        $join=array("sms_api_config"=>"sms_api_config.id=birthday_reminder.api_id,left");
        $sms_api_config=$this->basic->get_data('birthday_reminder', $where, $select=array("sms_api_config.gateway_name", "sms_api_config.phone_number"), $join);
        return $sms_api_config[0]['gateway_name']." : ".$sms_api_config[0]['phone_number'];
    }

    public function insert_user_id_sms_template($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        ;
        $this->basic->update_data("message_template", $where, $update_data);
    }

    public function insert_user_id_sms_api($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        $this->basic->update_data("sms_api_config", $where, $update_data);
    }
    
    public function message_field_with_instruction($value, $row)
    {
        return "<span class='hide_in_read'><b>".$this->lang->line('You can use variables:'). "</b><br/> <b><i>&nbsp;&nbsp;#firstname# &nbsp;&nbsp; #lastname# &nbsp;&nbsp; #mobile# &nbsp;&nbsp; #email#</i></b></span> <textarea name='message' id='message'>$value</textarea>";
    }

    public function sms_template_field($value, $row)
    {
        $where=array("where"=>array('user_id'=>$this->user_id));
        $template=$this->basic->get_data('message_template', $where, $select=array("id","template_name"), $join='', $limit='', $start='', $order_by='template_name ASC', $group_by='', $num_rows=0);
                                                        
        $str= "<select id='message_template_birthday'>";
        
        $str.="<option value=''>".$this->lang->line("I want to write new messsage, don't want any template")."</option>";
        foreach ($template as $info) {
            $template_name=$info['template_name'];
            $id=$info['id'];
            // $message=htmlentities($info['message'], ENT_QUOTES);
            $str.= "<option value='{$id}'>{$template_name}</option>";
        }
        $str.= "</select>";
        
        return $str;
    }
    
    public function insert_user_id_birthday($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        ;
        $this->basic->update_data("birthday_reminder", $where, $update_data);

        if ($post_array['status']=='1') {
            $table="birthday_reminder";
            $where=array('id !='=> $primary_key);
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);
        }

        return true;
    }


    public function make_up_scheduler_setting_edit($post_array, $primary_key)
    {
        if ($post_array['status']=='1') {
            $table="birthday_reminder";
            $where=array('id !='=> $primary_key);
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);
        }
        return true;
    }

    public function generateSerialSMSAPI()
    {
        if ($this->session->userdata('SMSAPILastSerial') == '') {
            $this->session->set_userdata('SMSAPILastSerial', 0);
            $this->session->set_userdata('SMSAPILastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('SMSAPILastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('SMSAPILastPage', $page);
        } else {
            $this->session->set_userdata('SMSAPILastPage', 1);
        }
        $this->session->set_userdata('SMSAPILastSerial', $lastSerial);
        return $lastSerial;
    }

    public function generateSerialSMSTemplate()
    {
        if ($this->session->userdata('SMSTemplateLastSerial') == '') {
            $this->session->set_userdata('SMSTemplateLastSerial', 0);
            $this->session->set_userdata('SMSTemplateLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('SMSTemplateLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('SMSTemplateLastPage', $page);
        } else {
            $this->session->set_userdata('SMSTemplateLastPage', 1);
        }
        $this->session->set_userdata('SMSTemplateLastSerial', $lastSerial);
        return $lastSerial;
    }


    public function generateSerialBirhdaySMS()
    {
        if ($this->session->userdata('birthdaySMSLastSerial') == '') {
            $this->session->set_userdata('birthdaySMSLastSerial', 0);
            $this->session->set_userdata('birthdaySMSLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('birthdaySMSLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('birthdaySMSLastPage', $page);
        } else {
            $this->session->set_userdata('birthdaySMSLastPage', 1);
        }
        $this->session->set_userdata('birthdaySMSLastSerial', $lastSerial);
        return $lastSerial;
    }

    

    
    
    //=================================================================================================================================
    // crud call back functions	
}
