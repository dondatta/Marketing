<?php $this->load->view("include/upload_js"); ?>
<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-send"></i> <?php echo $this->lang->line('Send Email');?> </h3>
       </div><!-- /.box-header -->
	   
	   
       <!-- form start -->
	   
	   
       <form class="form-horizontal" method="POST">
	   
         <div class="box-body">	  
		 

		 
		   <div class="form-group">
			  <label class="col-sm-2 control-label" for="contacts_mobile"><?php echo $this->lang->line('Select Contacts');?> </label>
			  	<div class="col-sm-9 col-md-9 col-lg-9">
					<select multiple="multiple"  class="form-control" id="contacts_email" name="contacts_email">
					<?php
						foreach($groups_name as $key=>$value)
						{
							echo "<option value='{$key}'>{$value}</option>";
						}
					 ?>
					
					</select>
				
            	 </div>
         	</div>

         	<div class="form-group">
	             <label class="col-sm-2 control-label" for="to_emails"> <?php echo $this->lang->line('or / and'); ?></label>
	             <div class="col-sm-9 col-md-9 col-lg-9"></div>
            </div>
		   
		 
		   <div class="form-group">
             <label class="col-sm-2 control-label" for="to_emails"> <?php echo $this->lang->line('Email Addresses To Send'); ?> * </label>
             <div class="col-sm-9 col-md-9 col-lg-9">

              <!-- <textarea placeholder="You can type comma seperated email addresses here.Y ou can also import Emails from a CSV file and Emails will be mereged here." id="to_emails" name="to_emails" class="form-control"></textarea> -->

              <textarea style="height:200px !important;" placeholder="<?php echo $this->lang->line('placeholder_1_email'); ?>"  id="to_emails" name="to_emails" class="form-control"></textarea>
                                                
			  </select>

             


              <span class="red" id="to_emails_error"></span>
             </div>
           </div>  


           <div class="form-group">
	             <label class="col-sm-2 control-label"></label>
	             <div class="col-sm-9 col-md-9 col-lg-9">
	               <a id="import_from_csv" data-toggle="modal" href='#csv_import_modal' class="btn btn-primary btn-small"><i class="fa fa-file-text"></i> <?php echo $this->lang->line('I want to import Emails from CSV'); ?></a>
	             </div>
            </div>		  
		 
		  		   
		    <div class="form-group">
             	<label class="col-sm-2 control-label" for="from_email"> <?php echo $this->lang->line('Send As'); ?> *</label>
				<div class="col-sm-9 col-md-9 col-lg-9">
				
				   <select  id='from_email' class='form-control'>
					<option value=''><?php echo $this->lang->line('Email API'); ?></option>
					<?php 
						foreach($smtp_option as $id=>$option)
						{
							echo "<option value='{$id}'>{$option}</option>";
						}
					?>
					</select>
					<span class="red" id="from_email_error"></span>
					
				</div>
			</div>
		 
		   <div class="form-group">
             <label class="col-sm-2 control-label" for="message"> <?php echo $this->lang->line('Subject'); ?> *</label>
             <div class="col-sm-9 col-md-9 col-lg-9">
              <input type="text" name="subject" id="subject" placeholder="<?php echo $this->lang->line('Email Subject');?>" class="form-control"/>
              <span class="red" id="subject_error"></span>
             </div>
           </div>
		  		   
		    <div class="form-group">
             	<label class="col-sm-2 control-label" for="message_template_send"> <?php echo $this->lang->line('Email Template'); ?></label>
				<div class="col-sm-9 col-md-9 col-lg-9">
				
				   <select  id='message_template_send' class='form-control'>
					<option value=''><?php echo $this->lang->line("I want to write new messsage, don't want any template"); ?></option>
					<?php 
						foreach($email_template as $info){
							$id=$info['id'];
							$template_name=$info['template_name'];
							$template_name = htmlspecialchars($template_name);
							echo "<option value='{$id}'>{$template_name}</option>";
						}
					?>
					</select>
					
				</div>
			</div>
			
		 
		  <div class="form-group">
             <label class="col-sm-2 control-label" for="message"><?php echo $this->lang->line('Message'); ?>  *</label>
             <div class="col-sm-9 col-md-9 col-lg-9">
             <div class="alert alert-info"><?php echo $this->lang->line('If you want to embed image into html template use');?> <img alt="Image" src="<?php echo base_url(); ?>documentation/assets/images/imageicon.png"> <?php echo $this->lang->line("icon and paste image url, do not paste image in to the editor directly. You can also use varibales clicking editor's");?> <b>"Varibale"</b> <?php echo $this->lang->line('icon  but remeber that email with variables are quiet slower than simple emails.')?></div>
              <textarea placeholder="If you don't want any template, type your custom message here" id="message" name="message" class="form-control"></textarea>
               <span class="red" id="message_error"></span>
             </div>
           </div>

			
		  <div class="form-group">
             <label class="col-sm-2 control-label" for="message"><?php echo $this->lang->line('Attachment'); ?>   <br/><?php echo $this->lang->line('(Max 20MB)'); ?></label>
             <div class="col-sm-5 col-md-5 col-lg-5">

              <!-- <div class="alert alert-info">You can attach an attachment up to 20MB size. If you need multiple files to send, compress them in to a zip/rar file. Please remember that, you can not have both email with variables & attachment together.</div> -->

              <div class="alert alert-info"><?php echo $this->lang->line('placeholder_3'); ?></div>

              	<!-- <input type="file" name="attachment" id="attachment" class="form-control"/> -->
               <div id="fileuploader"><?php echo $this->lang->line('Upload');?></div>
             </div>
           </div>
		   
            
           </div> <!-- /.box-body --> 
		   
		   
           <div class="box-footer">
            <div class="form-group">
             <div class="col-sm-12 text-center">
              
               <input name="submit" type="button" id="send_email" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line('Send'); ?>"/>       
               <input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('Cancel'); ?>" onclick='goBack("my_email/send_email",0)'/>
			   
			   
             </div>
           </div>
         </div><!-- /.box-footer -->         
         </div><!-- /.box-info -->      
		  
       </form>     
     </div>
   </section>
</section>




<div id="modal_email_send_waiting" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
		
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						 <span aria-hidden="true">&times;</span>
				 </button>
                <h4 id="email_send_waiting_title" class="modal-title"><?php echo $this->lang->line('Sending Email....'); ?> </h4>
            </div>
			
            <div id="email_send_waiting_body" class="modal-body">
					
			</div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="csv_import_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-file-text"></i> <?php echo $this->lang->line('Import Emails from CSV'); ?></h4>
			</div>
			<div class="modal-body">
				<form action="" id="csv_import_form" method="POST" enctype="multipart/form-data">
					<?php echo $this->lang->line('Browse CSV'); ?> <input type="file"  name="csv_file" id="csv_file"/>
					<input type="hidden" id="hidden_import_type" name="hidden_import_type" value="email"/>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('Cancel'); ?></button>
				<button type="button" id="import_submit" class="btn btn-primary"><?php echo $this->lang->line('Import'); ?></button>
			</div>
		</div>
	</div>
</div>



<script>
$j("document").ready(function(){

	var base_url="<?php echo site_url(); ?>";
	
	 	$j("#contacts_email").multipleSelect({
            filter: true,
            multiple: true
        });
	
		$("#fileuploader").uploadFile({
		url:base_url+"my_email/upload_attachment",
		fileName:"myfile",
		maxFileSize:20*1024*1024,
		showPreview:true,
		returnType: "json",
		dragDrop: true,
		showDelete: true,
		multiple:false,
		maxFileCount:1,	
		deleteCallback: function (data, pd) {
			var delete_url="<?php echo site_url('my_email/delete_attachment');?>";
		    for (var i = 0; i < data.length; i++) {
		        $.post(delete_url, {op: "delete",name: data[i]},
		            function (resp,textStatus, jqXHR) {		                		                
		            });
		    }
		}

		});

		CKEDITOR.replace('message');
		$("#message_template_send").change(function(){
			var template_id = $(this).val();
			$.ajax({
				url:base_url+'my_email/load_template',
				type:'POST',
				dataType: 'JSON',
				data:{template_id:template_id},
				success:function(response){
					CKEDITOR.instances['message'].setData(response.message);
				}
			});
			
		});
		
		
		/****Submit send sms button ****/
		
		$("#send_email").click(function(){			
			var to_emails=$("#to_emails").val();
			var contacts_email=$("#contacts_email").val();
			var message=CKEDITOR.instances.message.getData();		
			var from_email=$("#from_email").val();
			var subject=$("#subject").val();

			$("#email_send_waiting_title").html("<?php echo $this->lang->line('please wait'); ?>");
		
			if(to_emails=='' && contacts_email==null)
			$("#to_emails_error").html("<?php echo $this->lang->line('You have not mentioned any receipent.');?>");
			else $("#to_emails_error").html("");
							
			
			if(message=='')
			$("#message_error").html("<?php echo $this->lang->line('You have neither selected any template nor typed any custom message.');?>");
			else $("#message_error").html("");

			if(subject=='')
			$("#subject_error").html("<b><?php echo $this->lang->line('Subject'); ?></b> <?php echo $this->lang->line('is required'); ?>");
			else $("#subject_error").html("");

			if(from_email=='')
			$("#from_email_error").html("<b><?php echo $this->lang->line('Send As'); ?></b> <?php echo $this->lang->line('is required'); ?>");
			else $("#from_email_error").html("");

			if((to_emails=='' && contacts_email==null) || message=="" || subject=="" || from_email=="")
			return;
			
			
			$("#modal_email_send_waiting").modal();
			$("#email_send_waiting_body").html("<img alt='Please wait...' src='<?php echo base_url();?>assets/pre-loader/Fading squares.gif'/> "+" <?php echo $this->lang->line('please wait'); ?>");
			
			var history_link=base_url+"my_email/email_history";
			$("#email_send_waiting_title").html("<?php echo $this->lang->line('request has been submitted for processing');?>");
			$("#email_send_waiting_body").html("<?php echo $this->lang->line('request has been submitted for processing');?> <a class='' href='"+history_link+"'><?php echo $this->lang->line('view email history');?></a>");
			
			$.ajax({
				url:base_url+'my_email/email_send_action',
				type:'POST',
				data:{to_emails:to_emails,contacts_email:contacts_email,message:message,from_email:from_email,subject:subject},
				success:function(response){
				
					// if(response=='error'){
					// 	$("#email_send_waiting_body").html("Sorry! Error occured in establishing connection.");
					// 	return false;
					// }

					// else if(response=="exta_validation_error") 
					// {
					// 	$("#email_send_waiting_title").html("Attachment and variable message can not be sent togather.");
					// 	$("#email_send_waiting_body").html("Attachment and variable message can not be sent togather.");
					// }
					// else
					// {
					// 	$("#email_send_waiting_title").html("Email sending is completed");
					// 	$("#email_send_waiting_body").html("<a class='btn btn-info' target='_BLANK' href='"+history_link+"'>View Email Sending History</a><br/><br/>"+response);
					// }
					
				}
			});
		
		});


		$('#modal_email_send_waiting').on('hidden.bs.modal', function () {
			var link="<?php echo site_url('my_email/send_email'); ?>";
			window.location.assign(link);
	    })


		$("#import_submit").click(function(){    
	      
	      var site_url="<?php echo site_url();?>";    
	 
	      var queryString = new FormData($("#csv_import_form")[0]);
	      var fileval=$("#csv_file").val();
	      if(fileval=="")
	      alert("<?php echo $this->lang->line('no file selected');?>");
	      else
	      {        
	        
	      $(this).html('<?php echo $this->lang->line("please wait");?>');
	      $.ajax({
	          url: site_url+'my_email/csv_upload',
	          type: 'POST',
	          data: queryString,
	          dataType:'json',
	          async: false,
	          cache: false,
	          contentType: false,
	          processData: false,
	          success: function (response)                
	          {
	          	 $("#import_submit").html('<?php echo $this->lang->line("import");?>');         
	          	 if(response.status=='ok')
	          	 {          	 	
			         var file_content=response.file;
			         var to_emails=$("#to_emails").val().trim();
			         if(to_emails!="") file_content=','+file_content; 
			         file_content=to_emails+file_content;
			         $("#to_emails").val(file_content);
			         $("#csv_import_modal").modal('hide');
			         alert("<?php echo $this->lang->line('import from csv was successful');?>");
	          	 }
	          	 else
	          	 {
	          	 	 var error=response.status.replace(/<\/?[^>]+(>|$)/g, "");
	          	 	 alert(error);
	          	 }
	          }
	            
	        });
	      }         
	         
	  });
			
});
</script>

<style type="text/css" media="screen">
	.tokenize-sample{width: 100% !important;}
	.Placeholder{width: 100% !important;white-space:normal !important;}
</style>
