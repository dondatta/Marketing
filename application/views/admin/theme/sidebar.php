<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->  
  
    <ul class="sidebar-menu">
        <li class="header text-right"><?php echo $this->config->item("product_short_name")." ".$this->config->item("product_version") ?></li>

        <?php if ($this->session->userdata('user_type')== "Admin")  { ?>
        <li> <a href="<?php echo site_url()."dashboard/dashboard"; ?>"> <i class="fa fa-dashboard"></i> <span><b><?php echo $this->lang->line("Admin Dashboard"); ?></b></span> </a></li>
        <?php } ?>
        
        <li> <a href="<?php echo site_url()."dashboard/my_dashboard"; ?>"> <i class="fa fa-dashboard"></i> <span><b><?php echo $this->lang->line('My Dashboard'); ?></b></span> </a></li>
    
        <?php if ($this->session->userdata('user_type')== "Admin")  { ?>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-gears"></i> <span><b><?php echo $this->lang->line('Settings'); ?></b></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
      
          <ul class="treeview-menu">
            <li><a href="<?php echo site_url(); ?>setting/configuration"><i class="fa fa-cog"></i><span><?php echo $this->lang->line('General Settings'); ?></span></a></li>  
            <li><a href="<?php echo site_url()."admin_config_email/index"; ?>"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('System Email Settings') ?> </a></li>    
          </ul>
        </li> <!-- end settings -->
        <!-- <li> <a href="<?php echo site_url()."setting/configuration"; ?>"> <i class="fa fa-cogs"></i> <span><b><?php echo $this->lang->line('General Settings'); ?></b></span> </a></li>  -->
        
        <li> <a href="<?php echo site_url()."user"; ?>"> <i class="fa fa-group"></i> <span><b> <?php echo $this->lang->line('User Management'); ?></b></span> </a></li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-paypal"></i> <span><b><?php echo $this->lang->line('Payment'); ?></b></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
      
          <ul class="treeview-menu">
            <li> <a href="<?php echo site_url()."payment/payment_dashboard_admin"; ?>"> <i class="fa fa-dashboard"></i> <?php echo $this->lang->line('Dashboard'); ?> </a></li>   
            <li><a href="<?php echo site_url()."payment/payment_setting_admin"; ?>"><i class="fa fa-gears"></i> <?php echo $this->lang->line('Payment Settings'); ?></a></li>    
            <li><a href="<?php echo site_url()."payment/admin_payment_history"; ?>"><i class="fa fa-history"></i> <?php echo $this->lang->line('Payment History'); ?> </a></li>     
          </ul>
        </li> <!-- end my sms --> 

        <?php } else { ?>
        <li><a href="<?php echo site_url()."payment/member_payment_history"; ?>"><i class="fa fa-paypal"></i> <span><?php echo $this->lang->line('Payment'); ?></span></a></li> 
        <?php } ?>
        
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span><b> <?php echo $this->lang->line('My Phonebook');  ?></b></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo site_url()."phonebook/contact_group"; ?>"><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('Contact Group');  ?> </a></li> 
            <li> <a href="<?php echo site_url()."phonebook/contact_list"; ?>"> <i class="fa fa-phone"></i> <?php echo $this->lang->line('Contact');  ?>  </a></li>   
          </ul>
        </li> <!-- end my sms --> 

        <li class="treeview">
          <a href="#">
            <i class="fa fa-envelope"></i> <span><b><?php echo $this->lang->line('My SMS'); ?></b></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo site_url()."my_sms/sms_api"; ?>"><i class="fa fa-plug"></i> <?php echo $this->lang->line('SMS API'); ?></a></li>    
            <li> <a href="<?php echo site_url()."my_sms/sms_template"; ?>"> <i class="fa fa-instagram"></i> <?php echo $this->lang->line('SMS Template'); ?></a></li>   
            <li><a href="<?php echo site_url()."my_sms/send_sms"; ?>"><i class="fa fa-send"></i> <?php echo $this->lang->line('Send SMS'); ?></a></li>   
            <li><a href="<?php echo site_url()."my_sms/scheduled_sms"; ?>"><i class="fa fa-clock-o"></i> <?php echo $this->lang->line('Scheduled SMS'); ?></a></li>   
            <li><a href="<?php echo site_url()."my_sms/birthday_sms"; ?>"><i class="fa fa-birthday-cake"></i> <?php echo $this->lang->line('Birthday Wish SMS'); ?></a></li>   
            <li><a href="<?php echo site_url()."my_sms/sms_history"; ?>"><i class="fa fa-list-ol "></i> <?php echo $this->lang->line('SMS History'); ?></a></li>   
          </ul>
        </li> <!-- end my sms -->  

        <li class="treeview">
          <a href="#">
            <i class="fa fa-envelope-o"></i> <span><b><?php echo $this->lang->line('My Email'); ?></b></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li>
              <a href="#"><i class="fa fa-plug"></i><?php echo $this->lang->line('Email API'); ?> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="<?php echo site_url()."my_email/email_smtp_settings"; ?>"><i class="fa fa-plug"></i><?php echo $this->lang->line('SMTP API'); ?>  </a></li>
                <li><a href="<?php echo site_url()."my_email/email_mandrill_settings"; ?>"><i class="fa fa-plug"></i><?php echo $this->lang->line('Mandrill API'); ?> </a></li>     
                <li><a href="<?php echo site_url()."my_email/email_sendgrid_settings"; ?>"><i class="fa fa-plug"></i><?php echo $this->lang->line('SendGrid API'); ?>  </a></li>     
                <li><a href="<?php echo site_url()."my_email/email_mailgun_settings"; ?>"><i class="fa fa-plug"></i><?php echo $this->lang->line('Mailgun API'); ?>  </a></li>     
              </ul>
            </li>               
            <li> <a href="<?php echo site_url()."my_email/email_template"; ?>"> <i class="fa fa-instagram"></i><?php echo $this->lang->line('Email Template'); ?>  </a></li>  
            <li><a href="<?php echo site_url()."my_email/send_email"; ?>"><i class="fa fa-send"></i><?php echo $this->lang->line('Send Email'); ?> </a></li>     
            <li><a href="<?php echo site_url()."my_email/scheduled_email"; ?>"><i class="fa fa-clock-o"></i><?php echo $this->lang->line('Scheduled Email'); ?> </a></li>   
            <li><a href="<?php echo site_url()."my_email/birthday_email"; ?>"><i class="fa fa-birthday-cake"></i><?php echo $this->lang->line('Birthday Wish Email'); ?> </a></li>   
            <li><a href="<?php echo site_url()."my_email/email_history"; ?>"><i class="fa fa-list-ol"></i><?php echo $this->lang->line('Email History'); ?> </a></li>   
          </ul>
        </li> <!-- end administrator --> 

        <li class="treeview">
          <a href="#">
            <i class="fa fa-list"></i> <span><b><?php echo $this->lang->line('Report'); ?></b></span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>

          <?php if ($this->session->userdata('user_type')== "Admin")  { ?>
          <ul class="treeview-menu">
            <li>
                <a href="#"><i class="fa fa-user"></i><?php echo $this->lang->line('My Report'); ?> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="<?php echo site_url()."report/sms_report_contactwise"; ?>"><i class="fa fa-envelope"></i><?php echo $this->lang->line('My SMS Report'); ?> </a></li>
                  <li><a href="<?php echo site_url()."report/email_report_contactwise"; ?>"><i class="fa fa-envelope-o"></i><?php echo $this->lang->line('My Email Report'); ?></a></li>                    
                </ul>
            </li>
            <li>
                <a href="#"><i class="fa fa-group"></i> <?php echo $this->lang->line("Users' Report"); ?><i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="<?php echo site_url()."report/sms_report_userwise"; ?>"><i class="fa fa-envelope"></i><?php echo $this->lang->line("Users' SMS Report"); ?> </a></li>
                  <li><a href="<?php echo site_url()."report/email_report_userwise"; ?>"><i class="fa fa-envelope-o"></i><?php echo $this->lang->line("Users' Email Report"); ?> </a></li>                    
                </ul>
            </li>                 
          </ul>
          <?php } 
          else { ?>
          <ul class="treeview-menu">            
            <li><a href="<?php echo site_url()."report/sms_report_contactwise"; ?>"><i class="fa fa-envelope"></i><?php echo $this->lang->line('My SMS Report'); ?> </a></li>
            <li><a href="<?php echo site_url()."report/email_report_contactwise"; ?>"><i class="fa fa-envelope-o"></i><?php echo $this->lang->line('My Email Report'); ?> </a></li>                    
          </ul>
          <?php } ?>

        </li> <!-- end administrator --> 

 
        <li> <a href="<?php echo site_url()."ssem_api/get_api"; ?>"> <i class="fa fa-plug"></i> <span><b><?php echo $this->lang->line('My'). ' '; ?> <?php echo $this->config->item('product_short_name') ?> <?php echo $this->lang->line('API'). ' '; ?></b></span> </a></li> 
      

    </ul>  <!-- end menu -->
  </section> <!-- /.sidebar -->
</aside>


