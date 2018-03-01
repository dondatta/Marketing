<?php 
require_once("home.php");

class My_email extends Home
{
    public $user_id;
    
    public function __construct()
    {
        parent::__construct();

        if ($this->session->userdata('logged_in')!= 1) {
            redirect('home/login', 'location');
        }

        $this->important_feature();

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
       $this->db->from('message_template_email');
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


    public function email_template()
    {
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('emailTemplateLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('emailTemplateLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();
        
        $crud->where('message_template_email.deleted', '0');
        $crud->where('message_template_email.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();

        $images_url = base_url("plugins/grocery_crud/themes/flexigrid/css/images/magnifier.png");
        $crud->add_action('View Email Template', $images_url, 'my_email/view_template');

        $crud->set_theme('flexigrid');
        $crud->set_table('message_template_email');
        $crud->order_by('template_name');
        $crud->set_subject($this->lang->line('Email Template'));
        $crud->required_fields('message', 'template_name');
        $crud->fields('template_name', 'message');
        
        $state = $crud->getState();
        if ($state=='read') {
            $crud->columns('template_name', 'message');
        } else {
            $crud->columns('SL', 'template_name');
        }


        $crud->callback_after_insert(array($this, 'insert_user_id_email_template'));    /**insert the user_id***/

            
        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialEmailTemplate'));
    
        $crud->display_as('template_name', $this->lang->line('Template Name'));
        $crud->display_as('message', $this->lang->line('Message'));
        
        $output = $crud->render();
        $data['page_title'] = 'Email Template';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }


    public function view_template($id=0)
    {
        if ($id==0) {
            redirect('home/access_forbidden', 'location');
        }

        $table = 'message_template_email';
        $where['where'] = array('id'=>$id,"user_id"=>$this->user_id);

        $info = $this->basic->get_data($table, $where);

        $data['template_name'] = $info[0]['template_name'];
        $data['message'] = $info[0]['message'];
        $data['page_title'] = 'View Email Template';
        $this->load->view('my_email/template/view_template',$data);
    }


    public function view_message($id=0)
    {
        if ($id==0) {
            redirect('home/access_forbidden', 'location');
        }

        $table = 'email_history';
        $where['where'] = array('id'=>$id,"user_id"=>$this->user_id);

        $info = $this->basic->get_data($table, $where);

        $data['subject'] = $info[0]['subject'];
        $data['message'] = $info[0]['email_message'];
        $data['to'] = $info[0]['to_email'];
        $data['sent_time'] = $info[0]['sent_time'];
        $data['page_title'] = 'View Email';
        $this->load->view('my_email/email_history/view_message',$data);
    }





    public function view_birthday_message($id=0)
    {
        if ($id==0) {
            redirect('home/access_forbidden', 'location');
        }

        $table = 'birthday_reminder_email';
        $where['where'] = array('id'=>$id,"user_id"=>$this->user_id);

        $info = $this->basic->get_data($table, $where);

        $data['subject'] = $info[0]['subject'];
        $data['message'] = $info[0]['message'];
        $data['page_title'] = 'View Birthday Wish Email';
        $this->load->view('my_email/birthday_email/view_message',$data);
    } 


 




    public function email_smtp_settings()
    {
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('emailSMTPLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('emailSMTPLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('email_config.deleted', '0');
        $crud->where('email_config.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();

        $crud->set_theme('flexigrid');
        $crud->set_table('email_config');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line('SMTP API'));
        $crud->required_fields('email_address', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'status');
        $crud->columns('SL','id','email_address', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'status');
        $crud->fields('email_address', 'smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'status');
        $crud->set_rules('email_address', 'Email Address', 'required|valid_email');
        /**insert the user_id***/
        $crud->callback_after_insert(array($this, 'insert_user_id_smtp_settings'));
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));

        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialEmailSMTP'));

        $crud->display_as('email_address', $this->lang->line('Email Address'));
        $crud->display_as('id', $this->lang->line('Reference ID'));
        $crud->display_as('smtp_host', $this->lang->line('SMTP Host'));
        $crud->display_as('smtp_port', $this->lang->line('SMTP Port'));
        $crud->display_as('smtp_user', $this->lang->line('SMTP Username'));
        $crud->display_as('smtp_password', $this->lang->line('SMTP Password'));
        $crud->display_as('status', $this->lang->line('Status'));

        $output = $crud->render();
        $data['page_title'] = 'Email SMTP Settings';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }

    
    public function email_mandrill_settings()
    {
                    
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('emailMandrillLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('emailMandrillLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('email_mandrill_config.deleted', '0');
        $crud->where('email_mandrill_config.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();

        $crud->set_theme('flexigrid');
        $crud->set_table('email_mandrill_config');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line('Mandrill API'));
        $crud->required_fields('your_name', 'email_address', 'api_key', 'status');
        $crud->columns('SL','id', 'your_name', 'email_address', 'api_key', 'status');
        $crud->fields('your_name', 'email_address', 'api_key', 'status');
        $crud->set_rules('email_address', 'Email Address', 'required|valid_email');
        
        /**insert the user_id***/
        $crud->callback_after_insert(array($this, 'insert_user_id_mandrill_settings'));
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));

        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialEmailMandrill'));

        $crud->display_as('your_name', $this->lang->line('Your Name'));
        $crud->display_as('email_address', $this->lang->line('Email Address'));
        $crud->display_as('api_key', $this->lang->line('API Key'));
        $crud->display_as('id', $this->lang->line('Reference ID'));
        $crud->display_as('status', $this->lang->line('Status'));
    
        $output = $crud->render();
        $data['page_title'] = 'Email Mandrill Settings';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }

    public function email_sendgrid_settings()
    {
                    
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('emailSendgridLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('emailSendgridLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('email_sendgrid_config.deleted', '0');
        $crud->where('email_sendgrid_config.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();

        $crud->set_theme('flexigrid');
        $crud->set_table('email_sendgrid_config');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line('SendGrid API'));
        $crud->required_fields('email_address', 'username', 'password', 'status');
        $crud->columns('SL','id', 'email_address', 'username', 'password', 'status');
        $crud->fields('email_address', 'username', 'password', 'status');
        $crud->set_rules('email_address', 'Email Address', 'required|valid_email');
        
        /**insert the user_id***/
        $crud->callback_after_insert(array($this, 'insert_user_id_sendgrid_settings'));
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));
        
        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialEmailSendgrid'));

        $crud->display_as('email_address', $this->lang->line('Email Address'));
        $crud->display_as('username', $this->lang->line('Username'));
        $crud->display_as('password', $this->lang->line('Password'));
        $crud->display_as('id', $this->lang->line('Reference ID'));
        $crud->display_as('status', $this->lang->line('Status'));
    
        $output = $crud->render();
        $data['page_title'] = 'Email SendGrid Settings';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }


    public function email_mailgun_settings()
    {
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('emailMailgunLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('emailMailgunLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('email_mailgun_config.deleted', '0');
        $crud->where('email_mailgun_config.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();
        $crud->unset_read();

        $crud->set_theme('flexigrid');
        $crud->set_table('email_mailgun_config');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line('Mailgun API'));
        $crud->required_fields('email_address', 'domain_name', 'api_key', 'status');
        $crud->columns('SL', 'id','email_address', 'domain_name', 'api_key', 'status');
        $crud->fields('email_address', 'domain_name', 'api_key', 'status');
        $crud->set_rules('email_address', 'Email Address', 'required|valid_email');
        
        /**insert the user_id***/
        $crud->callback_after_insert(array($this, 'insert_user_id_mailgun_settings'));
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_column('status', array($this, 'status_display_crud'));
        
        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialEmailMailgun'));

        $crud->display_as('email_address', $this->lang->line('Email Address'));
        $crud->display_as('domain_name', $this->lang->line('Domain Name'));
        $crud->display_as('api_key', $this->lang->line('API Key'));
        $crud->display_as('id', $this->lang->line('Reference ID'));
        $crud->display_as('status', $this->lang->line('Status'));
    
        $output = $crud->render();
        $data['page_title'] = 'Email Mailgun Settings';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }
    
    public function email_history()
    {
        $data['body']="my_email/email_history/email_history";
        $data['page_title'] = 'My Email History';
        $this->_viewcontroller($data);
    }
    
    public function my_email_history_data()
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
        
        $where_simple['user_id']=$this->user_id;
        $where=array('where'=>$where_simple);
        $offset = ($page-1)*$rows;
        $result = array();
        $info=$this->basic->get_data('email_history', $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='', $num_rows=0);
        
        $total_rows_array=$this->basic->count_row($table="email_history", $where, $count="email_history.id", $join='');
        $total_result=$total_rows_array[0]['total_rows'];
        echo convert_to_grid_data($info, $total_result);
    }
    

    public function upload_attachment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $ret=array();
        $output_dir = FCPATH."upload/attachment";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename=$filename."_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("attachment_file_path_name", $output_dir.'/'.$filename);
            $this->session->set_userdata("attachment_filename", $filename);
            echo json_encode($filename);
        }
    }

    public function upload_attachment_scheduler()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $ret=array();
        $output_dir = FCPATH."upload/attachment";
        if (isset($_FILES["myfile"])) {
            $error =$_FILES["myfile"]["error"];
            $post_fileName =$_FILES["myfile"]["name"];
            $post_fileName_array=explode(".", $post_fileName);
            $ext=array_pop($post_fileName_array);
            $filename=implode('.', $post_fileName_array);
            $filename=$filename."_".$this->user_id."_".time().substr(uniqid(mt_rand(), true), 0, 6).".".$ext;
            move_uploaded_file($_FILES["myfile"]["tmp_name"], $output_dir.'/'.$filename);
            $ret[]= $filename;
            $this->session->set_userdata("attachment_file_path_name_scheduler", $output_dir.'/'.$filename);
            $this->session->set_userdata("attachment_filename_scheduler", $filename);
            echo json_encode($filename);
        }
    }

    public function delete_attachment()
    {
        unlink($this->session->userdata("attachment_file_path_name"));
        $this->session->unset_userdata("attachment_file_path_name");
        $this->session->unset_userdata("attachment_filename");
    }

    public function delete_attachment_scheduler()
    {
        unlink($this->session->userdata("attachment_file_path_name_scheduler"));
        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");
    }


    public function send_email()
    {
        $data['body']="my_email/send_email/send_email";

        $this->session->unset_userdata("attachment_file_path_name");
        $this->session->unset_userdata("attachment_filename");
          
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
        
        /*** get Email Template ***/
        $where=array("where"=>array('user_id'=>$this->user_id));
        $data['email_template']=$this->basic->get_data('message_template_email', $where, $select=array('id','template_name'), $join='', $limit='', $start='', $order_by='id DESC', $group_by='', $num_rows=0);
                                                        
                                                                
        // $data['contacts_info']=$contact_info;
        $data['groups_name']=$group_name;
        
        
        /***get smtp  option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        $smtp_option=array();
        foreach ($smtp_info as $info) {
            $id="smtp_".$info['id'];
            $smtp_option[$id]="SMTP: ".$info['email_address'];
        }
    
        /***get mandrill option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="mandrill_".$info['id'];
            $smtp_option[$id]="Mandrill: ".$info['email_address'];
        }

        /***get sendgrid option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="sendgrid_".$info['id'];
            $smtp_option[$id]="SendGrid: ".$info['email_address'];
        }

        /***get mailgun option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="mailgun_".$info['id'];
            $smtp_option[$id]="Mailgun: ".$info['email_address'];
        }
                                                        
        $data['smtp_option']=$smtp_option;
        $data['page_title'] = 'Send Email';
        $this->_viewcontroller($data);
    }


    public function email_send_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $contacts_email_group= $this->input->post('contacts_email', true);
        $to_emails= strip_tags(trim($this->input->post('to_emails', true)));
        $to_emails=explode(",", $to_emails);
        $message=$this->input->post('message');
        $from_email=  strip_tags(trim($this->input->post('from_email', true)));
        $subject= strip_tags(trim($this->input->post('subject', true)));

        $attachement=$this->session->userdata("attachment_file_path_name");
        $filename=$this->session->userdata("attachment_filename");

        $this->session->unset_userdata("attachment_file_path_name");
        $this->session->unset_userdata("attachment_filename");
        
        $all_contacts=array();

        if(!is_array($contacts_email_group))
            $contacts_email_group=array();   

        $contacts_email=array();
        foreach ($contacts_email_group as $key => $value) 
        {
            $where_simple=array('contacts.user_id'=>$this->user_id);
            $this->db->where("FIND_IN_SET('$value',contacts.contact_type_id) !=", 0);
            $where=array('where'=>$where_simple);    
            $contact_details=$this->basic->get_data('contacts', $where, $select='email');        
            foreach ($contact_details as $key2 => $value2) 
            {
                $contacts_email[] = $value2["email"];                
            }

        }

        $contacts_email=array_filter($contacts_email);
        $to_emails=array_filter($to_emails);

        if(!empty($contacts_email))
        $all_contacts=array_add($contacts_email, $to_emails);
        else
        $all_contacts=array_add($to_emails, $contacts_email);

        
        $all_contacts=array_unique($all_contacts);

            
        /**Get contact number and contact_type***/
        $contact_details=array();

        if(count($contacts_email)>0)
        {
            $where_in=array('email'=>$contacts_email);
            $where_simple=array('user_id'=>$this->user_id);
            $where=array('where_in'=>$where_in,'where'=>$where_simple);
            $contact_details=$this->basic->get_data('contacts', $where, $select='', $join='', $limit='', $start='', $group_by='', $num_rows=0);
        }
        // if message contains no veriable then send bulk
        if (strpos($message, '#firstname#')== false && strpos($message, '#lastname#')== false && strpos($message, '#mobile#')== false && strpos($message, '#email#')== false) {
            $sent_email=array();
            $result="";
            foreach ($contact_details as $details) 
            {
                $sent_email[]=$details['email'];
            }
            $sent_email=array_unique($sent_email);
            if(!empty($sent_email))
            $result.=implode(", ", $sent_email) ." - ". $this->_email_send_function($from_email, $message, $sent_email, $subject, $attachement, $filename)."<br/>";    /***** Send email of contact list (bulk) ******/
            $remaining_emails=array_diff($all_contacts, $sent_email); /***** Send email which numbers are not in the contact list (bulk) ******/
            $remaining_emails=array_filter($remaining_emails);
            $remaining_emails=array_unique($remaining_emails);
            if (!empty($remaining_emails)) {
                $result.=implode(", ", $remaining_emails) ." - ". $this->_email_send_function($from_email, $message, $remaining_emails, $subject, $attachement, $filename)."<br/>";
            }
        } 
        else 
        {
            if ($filename!="" || $filename!="0") 
            {
                echo "exta_validation_error";
                exit();
            }

            $sent_email=array();
            $result="";       

            foreach ($contact_details as $details) 
            {
                $first_name=$details['first_name'];
                $last_name=$details['last_name'];
                $email=$details['email'];
                $mobile=$details['phone_number'];

                $message_replaced=$message;
                $message_replaced=str_replace("#firstname#", $first_name, $message_replaced);
                $message_replaced=str_replace("#lastname#", $last_name, $message_replaced);
                $message_replaced=str_replace("#mobile#", $mobile, $message_replaced);
                $message_replaced=str_replace("#email#", $email, $message_replaced);

                if(in_array($email,$sent_email)) continue;                
                $sent_email[]=$email;

                $email_array=array($email); // making single email an array 				
                $result.="{$email} - " . $this->_email_send_function($from_email, $message_replaced, $email_array, $subject, $attachement, $filename)."<br/>"; /***** Send email of contact list (bulk) ******/
            }
            $remaining_emails=array_diff($all_contacts, $sent_email); /***** Send email which numbers are not in the contact list bulk()******/
            $remaining_emails=array_filter($remaining_emails);
            $remaining_emails=array_unique($remaining_emails);
            if (!empty($remaining_emails)) 
            {
                $result.=implode(", ", $remaining_emails) . " - ".$this->_email_send_function($from_email, $message, $remaining_emails, $subject, $attachement, $filename)."<br/>";
            }
        }
        
        echo $result;
    }
    
    public function scheduled_email()
    {
        $data['body']="my_email/schedule_email/scheduled_email";
        $data['page_title'] = 'Scheduled Email';
        $this->_viewcontroller($data);
    }
    
    public function upcoming_scheduled_email_data()
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
        
        $where_simple['user_id']=$this->user_id;
        $where=array('where'=>$where_simple);
        $offset = ($page-1)*$rows;
        $result = array();
        $info=$this->basic->get_data('schedule_email', $where, $select='', $join='', $limit=$rows, $start=$offset, $order_by=$order_by_str, $group_by='', $num_rows=0);
        $total_rows_array=$this->basic->count_row($table="schedule_email", $where, $count="schedule_email.id", $join='');
        $total_result=$total_rows_array[0]['total_rows'];
        echo convert_to_grid_data($info, $total_result);
    }

    public function schedule_contacts()
    {
        $schedule_info=$_POST['schedule_info'];
        $contacts=$schedule_info['contact_ids'];
        $contacts=explode(",", $contacts);

        $api_id=$schedule_info['api_id'];
        $configure_table_name=$schedule_info['configure_table_name'];
        $gateway_name="SMTP : ";
        if ($configure_table_name=="email_mandrill_config") {
            $gateway_name="Madrill : ";
        } elseif ($configure_table_name=="email_sendgrid_config") {
            $gateway_name="SendGrid : ";
        } elseif ($configure_table_name=="email_mailgun_config") {
            $gateway_name="Mailgun : ";
        }
        
        $data["gateway_name"]=$gateway_name;
        $data['send_as_info']=$this->basic->get_data($configure_table_name, $where=array("where"=>array("id"=>$api_id)), $select=array("email_address"));
                
        $where_in = array('id'=> $contacts);
        $where = array('where_in'=> $where_in);
        $data['contact_details']=$this->basic->get_data('contacts', $where, $select='', $join='', $limit='', $start='', $order_by='', $group_by='', $num_rows=0);
        
        $this->load->view('my_email/schedule_email/schedule_contact_details', $data);
    }
    
    /*** Delete Schedule ******/
    public function delete_schedule()
    {
        $schedule_id=$this->input->post('schedule_id', true);
        $where=array("id"=>$schedule_id);
        $this->basic->delete_data('schedule_email', $where);
    }
        
    public function add_schedule()
    {
        $data['body']="my_email/schedule_email/schedule_add";

        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");
        
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
        
        
        /*** get Email Template ***/
        $where=array("where"=>array('user_id'=>$this->user_id));
        $data['email_template']=$this->basic->get_data('message_template_email', $where, $select='', $join='', $limit='', $start='', $order_by='template_name ASC', $group_by='', $num_rows=0);
                                                        

        /***get smtp  option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        $smtp_option=array();
        foreach ($smtp_info as $info) {
            $id="smtp_".$info['id'];
            $smtp_option[$id]="SMTP: ".$info['email_address'];
        }
    
        /***get mandrill option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="mandrill_".$info['id'];
            $smtp_option[$id]="Mandrill: ".$info['email_address'];
        }

        /***get sendgrid option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="sendgrid_".$info['id'];
            $smtp_option[$id]="SendGrid: ".$info['email_address'];
        }

        /***get mailgun option***/
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $smtp_info=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        
        foreach ($smtp_info as $info) {
            $id="mailgun_".$info['id'];
            $smtp_option[$id]="Mailgun: ".$info['email_address'];
        }



        $data['email_option']=$smtp_option;
        // $data['contacts_info']=$contact_info;
        $data['groups_name']=$group_name;
        $data['time_zone_str']=$this->time_zone_drop_down();
        $data['page_title'] = 'Scheduled Email';
        $this->_viewcontroller($data);
    }

    public function add_schedule_action()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect('home/access_forbidden', 'location');
        }

        $schedule_name= strip_tags(trim($this->input->post('schedule_name', true)));
        $message=$this->input->post('message');
        $subject= strip_tags(trim($this->input->post('subject', true)));
        $day= strip_tags(trim($this->input->post('day', true)));
        $month= strip_tags(trim($this->input->post('month', true)));
        $year= strip_tags(trim($this->input->post('year', true)));
        $hour= strip_tags(trim($this->input->post('hour', true)));
        $minute= strip_tags(trim($this->input->post('minute', true)));
        $time_zone= strip_tags(trim($this->input->post('time_zone', true)));
        $from_email= strip_tags(trim($this->input->post('from_email', true)));
        $from_email_separate=explode('_', $from_email);
        $api_id=$from_email_separate[1];
        $configure_table_name="email_config";

        $attachement=$this->session->userdata("attachment_file_path_name_scheduler");
        $filename=$this->session->userdata("attachment_filename_scheduler");

        $this->session->unset_userdata("attachment_file_path_name_scheduler");
        $this->session->unset_userdata("attachment_filename_scheduler");

        if (strtolower($from_email_separate[0])=='mandrill') {
            $configure_table_name="email_mandrill_config";
        } elseif (strtolower($from_email_separate[0])=='sendgrid') {
            $configure_table_name="email_sendgrid_config";
        } elseif (strtolower($from_email_separate[0])=='mailgun') {
            $configure_table_name="email_mailgun_config";
        }
        
        // $contacts_id=$this->input->post('contacts_id', true);
        // $contacts_id=implode(',', $contacts_id);

        $contacts_email_group= $this->input->post('contacts_id', true);
        if(!is_array($contacts_email_group))
            $contacts_email_group=array();   

        $contacts_id=array();
        foreach ($contacts_email_group as $key => $value) 
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
        $insert_data=array("user_id"=>$this->user_id,"contact_ids"=>$contacts_id,"schedule_name"=>$schedule_name,"message"=>$message,"time_zone"=>$time_zone,"schedule_time"=>$schedule_time,"api_id"=>$api_id,"configure_table_name"=>$configure_table_name,"subject"=>$subject,"attachment"=>$filename);
        $this->basic->insert_data("schedule_email", $insert_data);
        $this->session->set_flashdata('success_message', 1);
    }
    
    public function birthday_email()
    {
        //This process is important to reset the last serial no...
        $page = $this->input->get_post('page');
        if ($page == '') {
            $this->session->set_userdata('birthdayEmailLastSerial', "");
        } else {
            $per_page = $this->input->get_post('per_page');
            $start = ($page-1) * $per_page;
            $this->session->set_userdata('birthdayEmailLastSerial', $start);
        }

        $this->load->database();
        $this->load->library('grocery_CRUD');
        $crud = new grocery_CRUD();

        $crud->where('birthday_reminder_email.deleted', '0');
        $crud->where('birthday_reminder_email.user_id', $this->user_id);

        $crud->unset_export();
        $crud->unset_print();
        
        $crud->set_theme('flexigrid');
        $crud->set_table('birthday_reminder_email');
        $crud->order_by('id');
        $crud->set_subject($this->lang->line('Scheduled Email (Birthday Wish)'));
        $crud->columns('SL', 'time_zone', 'api_id', 'status');

        $state = $crud->getState();
        if ($state == 'add' || $state=='edit') {
            $crud->fields('api_id', 'subject','email_template', 'message', 'time_zone', 'status');
        } else {
            $crud->fields('subject', 'message','api_id', 'time_zone');
        }

        $crud->change_field_type("subject", "string");
        $crud->required_fields('subject', 'message', "time_zone", "api_id", "status");
        $crud->callback_field('api_id', array($this, 'api_field_crud'));
        $crud->callback_field('status', array($this, 'status_field_crud'));
        $crud->callback_field('time_zone', array($this, 'time_zone_drop_down'));
        $crud->callback_field('email_template', array($this, 'email_template_field'));
        $crud->callback_column('status', array($this, 'status_display_crud'));
        $crud->callback_column('api_id', array($this, 'api_display_crud'));

        // for SL column				
        $crud->callback_column('SL', array($this, 'generateSerialBirthdayEmail'));

        // Only one schedule can be active at a time
        $crud->callback_after_insert(array($this, 'insert_user_id_birthday')); // insert id + check active functionalities as well
        $crud->callback_after_update(array($this, 'make_up_scheduler_setting_edit'));

        $crud->display_as('email_template', $this->lang->line('Email Template'));
        $crud->display_as('time_zone', $this->lang->line('Time Zone'));
        $crud->display_as('api_id', $this->lang->line('Send As'));
        $crud->display_as('status', $this->lang->line('Status'));
        $crud->display_as('subject', $this->lang->line('Subject'));
        $crud->display_as('message', $this->lang->line('Message'));

        
        
        $output = $crud->render();
        $data['page_title'] = 'Birthday Wish Email';
        $data['output']=$output;
        $data['crud']=1;
        $this->_viewcontroller($data);
    }


    //=================================================================================================================================
    // crud call back functions	
    public function insert_user_id_email_template($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        ;
        $this->basic->update_data("message_template_email", $where, $update_data);
    }

    public function insert_user_id_smtp_settings($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        ;
        $this->basic->update_data("email_config", $where, $update_data);
    }
    
    public function insert_user_id_mandrill_settings($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        ;
        $this->basic->update_data("email_mandrill_config", $where, $update_data);
    }

    public function insert_user_id_sendgrid_settings($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        ;
        $this->basic->update_data("email_sendgrid_config", $where, $update_data);
    }
        
    public function insert_user_id_mailgun_settings($post_array, $primary_key)
    {
        $user_id=$this->user_id;
        $update_data=array('user_id'=>$user_id);
        $where=array("id"=>$primary_key);
        ;
        $this->basic->update_data("email_mailgun_config", $where, $update_data);
    }
        
    public function status_field_crud($value = '', $primary_key = null)
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

    public function api_field_crud($value = '', $primary_key = null)
    {
        $selected_tab="";
        if ($primary_key!= null) {
            $xdata=$this->basic->get_data('birthday_reminder_email', $where=array("where"=>array("id"=>$primary_key)), $select=array("api_id", "configure_table_name"));
            $selected_tab=$xdata[0]['configure_table_name'];
            $selected_api_id=$xdata[0]['api_id'];
        }
        
        $str='';
        $str.='<select id="api_id" class="form-control" name="api_id">';
        $str.='<option value="">'.$this->lang->line('Email API').'</option>';
        
        $email_api_config=array();
        $where=array("where"=>array('user_id'=>$this->user_id,'status'=>'1'));
        $email_api_config=$this->basic->get_data('email_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        for ($i=0;$i<count($email_api_config);$i++) {
            if ($selected_tab=="email_config" && $email_api_config[$i]['id']==$selected_api_id) {
                $str.='<option selected="selected" value="email_config_'.$email_api_config[$i]['id'].'">'."SMTP : ".$email_api_config[$i]['email_address'].'</option>';
            } else {
                $str.='<option value="email_config_'.$email_api_config[$i]['id'].'">'."SMTP : ".$email_api_config[$i]['email_address'].'</option>';
            }
        }

        $email_api_config=array();
        $email_api_config=$this->basic->get_data('email_mandrill_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        for ($i=0;$i<count($email_api_config);$i++) {
            if ($selected_tab=="email_mandrill_config" && $email_api_config[$i]['id']==$selected_api_id) {
                $str.='<option selected="selected" value="email_mandrill_config_'.$email_api_config[$i]['id'].'">'."Mandrill : ".$email_api_config[$i]['email_address'].'</option>';
            } else {
                $str.='<option value="email_mandrill_config_'.$email_api_config[$i]['id'].'">'."Mandrill : ".$email_api_config[$i]['email_address'].'</option>';
            }
        }
        $email_api_config=array();
        $email_api_config=$this->basic->get_data('email_sendgrid_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        for ($i=0;$i<count($email_api_config);$i++) {
            if ($selected_tab=="email_sendgrid_config" && $email_api_config[$i]['id']==$selected_api_id) {
                $str.='<option selected="selected" value="email_sendgrid_config_'.$email_api_config[$i]['id'].'">'."SendGrid : ".$email_api_config[$i]['email_address'].'</option>';
            } else {
                $str.='<option value="email_sendgrid_config_'.$email_api_config[$i]['id'].'">'."SendGrid : ".$email_api_config[$i]['email_address'].'</option>';
            }
        }

        $email_api_config=array();
        $email_api_config=$this->basic->get_data('email_mailgun_config', $where, $select='', $join='', $limit='', $start='', $order_by='email_address ASC', $group_by='', $num_rows=0);
        for ($i=0;$i<count($email_api_config);$i++) {
            if ($selected_tab=="email_mailgun_config" && $email_api_config[$i]['id']==$selected_api_id) {
                $str.='<option selected="selected" value="email_mailgun_config_'.$email_api_config[$i]['id'].'">'."Mailgun : ".$email_api_config[$i]['email_address'].'</option>';
            } else {
                $str.='<option value="email_mailgun_config_'.$email_api_config[$i]['id'].'">'."Mailgun : ".$email_api_config[$i]['email_address'].'</option>';
            }
        }

        $str.='</select>';
        return $str;
    }

    public function api_display_crud($value, $row)
    {
        $config_table_name=$row->configure_table_name;
        $gateway="";
        if ($config_table_name=="email_config") {
            $gateway="SMTP : ";
        }
        if ($config_table_name=="email_mandrill_config") {
            $gateway="Mandrill : ";
        }
        if ($config_table_name=="email_sendgrid_config") {
            $gateway="SendGrid : ";
        }

        $where=array("where"=>array('birthday_reminder_email.user_id'=>$this->user_id,'birthday_reminder_email.id'=>$row->id));
        $join=array($config_table_name=>$config_table_name.".id=birthday_reminder_email.api_id,left");
        $sms_api_config=$this->basic->get_data('birthday_reminder_email', $where, $select=array($config_table_name.".email_address"), $join);
        return $gateway.$sms_api_config[0]['email_address'];
    }


    public function email_template_field($value = '', $primary_key = null)
    {
        $where=array("where"=>array('user_id'=>$this->user_id));
        $template=$this->basic->get_data('message_template_email', $where, $select=array("id","template_name"), $join='', $limit='', $start='', $order_by='template_name ASC', $group_by='', $num_rows=0);
                                                        
        $str= "<select id='message_template_birthday'>";
        
        $str.="<option value=''>".$this->lang->line("I want to write new messsage, don't want any template")."</option>";
        foreach ($template as $info) {
            $id=$info['id'];
            $template_name=$info['template_name'];
            // $message=$info['message'];
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
        $this->basic->update_data("birthday_reminder_email", $where, $update_data);

        if ($post_array['status']=='1') {
            $table="birthday_reminder_email";
            $where=array('id !='=> $primary_key);
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);
        }

        $api_val=$post_array['api_id'];
        $api_id_array=explode("_", $api_val);
        $api_id=array_pop($api_id_array);
        $configure_table_name=implode("_", $api_id_array);

        $table="birthday_reminder_email";
        $where=array('id'=> $primary_key);
        $data=array("api_id"=>$api_id,"configure_table_name"=>$configure_table_name);
        $this->basic->update_data($table, $where, $data);

        return true;
    }


    public function make_up_scheduler_setting_edit($post_array, $primary_key)
    {
        if ($post_array['status']=='1') {
            $table="birthday_reminder_email";
            $where=array();
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);

            $table="birthday_reminder_email";
            $where=array('id'=> $primary_key);
            $data=array("status"=>"1");
            $this->basic->update_data($table, $where, $data);
        } else {
            $table="birthday_reminder_email";
            $where=array('id'=> $primary_key);
            $data=array("status"=>"0");
            $this->basic->update_data($table, $where, $data);
        }

        $api_val=$post_array['api_id'];
        $api_id_array=explode("_", $api_val);
        $api_id=array_pop($api_id_array);
        $configure_table_name=implode("_", $api_id_array);

        $table="birthday_reminder_email";
        $where=array('id'=> $primary_key);
        $data=array("api_id"=>$api_id,"configure_table_name"=>$configure_table_name);
        $this->basic->update_data($table, $where, $data);

        return true;
    }


    public function generateSerialEmailTemplate()
    {
        if ($this->session->userdata('emailTemplateLastSerial') == '') {
            $this->session->set_userdata('emailTemplateLastSerial', 0);
            $this->session->set_userdata('emailTemplateLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('emailTemplateLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('emailTemplateLastPage', $page);
        } else {
            $this->session->set_userdata('emailTemplateLastPage', 1);
        }
        $this->session->set_userdata('emailTemplateLastSerial', $lastSerial);
        return $lastSerial;
    }

    public function generateSerialEmailSMTP()
    {
        if ($this->session->userdata('emailSMTPLastSerial') == '') {
            $this->session->set_userdata('emailSMTPLastSerial', 0);
            $this->session->set_userdata('emailSMTPLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('emailSMTPLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('emailSMTPLastPage', $page);
        } else {
            $this->session->set_userdata('emailSMTPLastPage', 1);
        }
        $this->session->set_userdata('emailSMTPLastSerial', $lastSerial);
        return $lastSerial;
    }
    
    public function generateSerialEmailMandrill()
    {
        if ($this->session->userdata('emailMandrillLastSerial') == '') {
            $this->session->set_userdata('emailMandrillLastSerial', 0);
            $this->session->set_userdata('emailMandrillLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('emailMandrillLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('emailMandrillLastPage', $page);
        } else {
            $this->session->set_userdata('emailMandrillLastPage', 1);
        }
        $this->session->set_userdata('emailMandrillLastSerial', $lastSerial);
        return $lastSerial;
    }

    public function generateSerialEmailSendgrid()
    {
        if ($this->session->userdata('emailSendgridLastSerial') == '') {
            $this->session->set_userdata('emailSendgridLastSerial', 0);
            $this->session->set_userdata('emailSendgridLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('emailSendgridLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('emailSendgridLastPage', $page);
        } else {
            $this->session->set_userdata('emailSendgridLastPage', 1);
        }
        $this->session->set_userdata('emailSendgridLastSerial', $lastSerial);
        return $lastSerial;
    }
    
    public function generateSerialEmailMailgun()
    {
        if ($this->session->userdata('emailMailgunLastSerial') == '') {
            $this->session->set_userdata('emailMailgunLastSerial', 0);
            $this->session->set_userdata('emailMailgunLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('emailMailgunLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('emailMailgunLastPage', $page);
        } else {
            $this->session->set_userdata('emailMailgunLastPage', 1);
        }
        $this->session->set_userdata('emailMailgunLastSerial', $lastSerial);
        return $lastSerial;
    }



    public function generateSerialBirthdayEmail()
    {
        if ($this->session->userdata('birthdayEmailLastSerial') == '') {
            $this->session->set_userdata('birthdayEmailLastSerial', 0);
            $this->session->set_userdata('birthdayEmailLastPage', 1);
            $lastSerial = 0;
        } else {
            $lastSerial = $this->session->userdata('birthdayEmailLastSerial');
        }
        
        $lastSerial++;
        $page = $this->input->post('page');
        if ($page != '') {
            $this->session->set_userdata('birthdayEmailLastPage', $page);
        } else {
            $this->session->set_userdata('birthdayEmailLastPage', 1);
        }
        $this->session->set_userdata('birthdayEmailLastSerial', $lastSerial);
        return $lastSerial;
    }
    // crud call back functions
    //=================================================================================================================================
}
