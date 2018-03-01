<?php $this->load->view("include/upload_js"); ?>

<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-clock-o"></i> <?php echo $this->lang->line('Scheduled Email'); ?> </h3>
       </div><!-- /.box-header -->
	   
	   
       <!-- form start -->
	   
	   
       <form class="form-horizontal" action="" method="POST">
         <div class="box-body">
		 
		 
           <div class="form-group">
             <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Scheduler Name'); ?> *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input id="schedule_name" name="schedule_name" value="<?php echo set_value('schedule_name');?>"  class="form-control" type="text">
               <span class="red" id="schedule_name_error"></span>
             </div>
           </div>

            <div class="form-group">
			  <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Select Contacts'); ?>  *</label>
			  	<div class="col-sm-9 col-md-6 col-lg-6">
					<select multiple="multiple"  class="form-control" id="contacts_id" name="contacts_id">
					<?php
						foreach($groups_name as $key=>$value)
						{
							echo "<option value='{$key}'>{$value}</option>";
						}
					 ?>
					
					</select>
					<span class="red" id="to_emails_error"></span>
				
            	 </div>
         	</div>

         	<div class="form-group">
             	<label class="col-sm-3 control-label" for="from_email"><?php echo $this->lang->line('Send As'); ?>  *</label>
				<div class="col-sm-9 col-md-6 col-lg-6">
				
				   <select  id='from_email' class='form-control'>
					<option value=''><?php echo $this->lang->line('Email API'); ?></option>
					<?php 
						foreach($email_option as $id=>$option)
						{
							echo "<option value='{$id}'>{$option}</option>";
						}
					?>
					</select>
					<span class="red" id="from_email_error"></span>
					
				</div>
			</div>
		   
		    
			<div class="form-group">
             <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Subject'); ?>  *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
              <input placeholder="<?php echo $this->lang->line('Email Subject'); ?>" id="subject" name="subject" type="text" class="form-control"/>
               <span class="red" id="subject_error"></span>
             </div>
           </div>

           <div class="form-group">
             	<label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Email Template'); ?>  </label>
				<div class="col-sm-9 col-md-6 col-lg-6">
				
				   <select  id='message_template_schedule' class='form-control'>
					<option value=''><?php echo $this->lang->line("I want to write new messsage, don't want any template");?></option>
					
					<?php 
						foreach($email_template as $info){
							$template_name=$info['template_name'];
							$id=$info['id'];
							echo "<option value='{$id}'>{$template_name}</option>";
						}
					?>
					</select>
					
				</div>
			</div>


           <div class="form-group">
             <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Message'); ?>  *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
              <textarea placeholder="If you don't want any template, type your custom message here" id="message" name="message" class="form-control"></textarea>
                 <span class="red" id="message_error"></span>
             </div>
           </div>


           <div class="form-group">
             <label class="col-sm-3 control-label" for="message"> <?php echo $this->lang->line('Attachment'); ?>  <br/><?php echo $this->lang->line('(Max 20MB)');?></label>
             <div class="col-sm-9 col-md-6 col-lg-6">
             	<!-- <input type="file" name="attachment" id="attachment" class="form-control"/> -->
               <div id="fileuploader">Upload</div>
               <span class="red" id="message_error"></span>
             </div>
           </div>	   		   
		    
		   		   
		   <div class="form-group">
             <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Schedule'); ?>  *</label>
             <div class="col-sm-9 col-md-3 col-md-2 col-lg-2">			 
			 	<select id="day" name="day" class="form-control">
					<option value=""><?php echo $this->lang->line('Day'); ?> </option>
					
					<?php 
						for($i=1;$i<=31;$i++){
						$day=str_pad($i,2,'0',STR_PAD_LEFT);
						echo "<option value='{$day}'>{$day}</option>";
					} 
					?>
				</select>

			</div>

			<div class="col-sm-9 col-sm-offset-3 col-md-2 col-md-offset-0 col-lg-2 col-lg-offset-0">				
				<select id="month" name="month" class="form-control">
					<option value=""><?php echo $this->lang->line('Month'); ?>  </option>
					
					<?php 
						for($i=1;$i<=12;$i++){
						$month=str_pad($i,2,'0',STR_PAD_LEFT);
						echo "<option value='{$month}'>{$month}</option>";
					} 
					?>
				</select>
			</div>

			<div class="col-sm-9 col-sm-offset-3 col-md-2 col-md-offset-0 col-lg-2 col-lg-offset-0">
				<select id="year" name="year" class="form-control">
					<option value=""><?php echo $this->lang->line('Year'); ?>  </option>
					
					<?php 
						$this_year=date('Y');
						for($i=0;$i<=15;$i++){
						$year=$this_year+$i;
						echo "<option value='{$year}'>{$year}</option>";
					} 
					?>
				</select>
			</div>

			<div class="col-sm-9 col-sm-offset-3 col-md-2 col-md-offset-3 col-lg-2 col-lg-offset-3">		
				<select id="hour" name="hour" class="form-control">
					<option value=""><?php echo $this->lang->line('Hour'); ?>  </option>
					
					<?php 
						for($i=1;$i<=24;$i++){
						$hour=str_pad($i,2,'0',STR_PAD_LEFT);
						echo "<option value='{$hour}'>{$hour}</option>";
					} 
					?>
				</select>
			</div>

			<div class="col-sm-9 col-sm-offset-3 col-md-2 col-md-offset-0 col-lg-2 col-lg-offset-0">
				<select id="minute" name="minute" class="form-control">
					<option value=""><?php echo $this->lang->line('Minute'); ?>  </option>
					
					<?php 
						for($i=0;$i<=59;$i++){
						$minute=str_pad($i,2,'0',STR_PAD_LEFT);
						echo "<option value='{$minute}'>{$minute}</option>";
					} 
					?>
				</select>	
			
             </div>
           </div>

			
			<div class="form-group">				
             <label class="col-sm-3 control-label" for="name"></label>
             <div class="col-sm-9 col-md-6 col-lg-6">
				<span class="red" id="schedule_time_error"></span>
             </div>
           </div>
		   
		   <div class="form-group">				
             <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Time Zone'); ?>  *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
				<?php echo $time_zone_str; ?>
				<span class="red" id="timezone_error"></span>
             </div>
           </div>
		   
		   
           </div> <!-- /.box-body --> 
		   
		   
           <div class="box-footer">
            <div class="form-group">
             <div class="col-sm-12 text-center">
              
               <input name="submit" type="button" id="submit_schedule" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line('Save'); ?>"/>       
               <input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('Cancel'); ?>" onclick='goBack("my_email/scheduled_email",0)'/>
			   
			   
             </div>
           </div>
         </div><!-- /.box-footer -->         
         </div><!-- /.box-info -->      
		  
       </form>     
     </div>
   </section>
</section>

<script>
$j("document").ready(function(){

	var base_url="<?php echo site_url(); ?>";
	
	 $j("#contacts_id").multipleSelect({
            filter: true,
            multiple: true
        });

	   $("#fileuploader").uploadFile({
		url:base_url+"my_email/upload_attachment_scheduler",
		fileName:"myfile",
		maxFileSize:20*1024*1024,
		showPreview:true,
		returnType: "json",
		dragDrop: true,
		showDelete: true,
		multiple:false,
		maxFileCount:1,
		deleteCallback: function (data, pd) {
			var delete_url="<?php echo site_url('my_email/delete_attachment_scheduler');?>";
		    for (var i = 0; i < data.length; i++) {
		        $.post(delete_url, {op: "delete",name: data[i]},
		            function (resp,textStatus, jqXHR) {                
		            });
		    }
		  }
		});

		
		
		$("#submit_schedule").click(function(){
			var schedule_name=$("#schedule_name").val();
			var subject=$("#subject").val();			
			var message=CKEDITOR.instances.message.getData();
			var day=$("#day").val();
			var month=$("#month").val();
			var year=$("#year").val();
			var hour=$("#hour").val();
			var minute=$("#minute").val();
			var time_zone=$("#time_zone").val();
			var contacts_id=$("#contacts_id").val();
			var from_email=$("#from_email").val();

			if(schedule_name=='')
			$("#schedule_name_error").html("<b>Scheduler Name</b> is required.");
			else $("#schedule_name_error").html("");

			if(contacts_id==null)
			$("#to_emails_error").html("You have not mentioned any receipent.");
			else $("#to_emails_error").html("");							
			
			if(message=='')
			$("#message_error").html("You have neither selected any template nor typed any custom message.");
			else $("#message_error").html("");

			if(subject=='')
			$("#subject_error").html("<b>Subject</b> is required.");
			else $("#subject_error").html("");

			if(from_email=='')
			$("#from_email_error").html("<b>Send As</b> is required.");
			else $("#from_email_error").html("");	

			if(day=='' || month=='' || year=='' || year=='' || hour=='' || minute=='')
			$("#schedule_time_error").html("<b>Schedule</b> is required.");
			else $("#schedule_time_error").html("");

			if(time_zone=='')
			$("#timezone_error").html("<b>Time Zone</b> is required.");
			else $("#timezone_error").html("");
			
			if(schedule_name=='' || subject=="" || message=='' || from_email=='' || day=='' || month=='' || year=='' || year=='' || hour=='' || minute=='' || time_zone=='' || contacts_id==null)
			return false;
			
			
			$.ajax({
				url:base_url+'my_email/add_schedule_action',
				type:'POST',
				data:{
					subject:subject,from_email:from_email,schedule_name:schedule_name,message:message,day:day,month:month,year:year,hour:hour,minute:minute,time_zone:time_zone,contacts_id:contacts_id
				},
				
				success:function(respose){
					window.location=base_url+"my_email/scheduled_email";
				}
			});
		
		});
			
		CKEDITOR.replace('message');
		$("#message_template_schedule").change(function(){
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
		
		
		
});
</script>
