<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-clock-o"></i><?php echo $this->lang->line('Scheduled SMS');?></h3>
       </div><!-- /.box-header -->
	   
	   
       <!-- form start -->
	   
	   
       <form class="form-horizontal" action="" method="POST">
         <div class="box-body">
		 
		 
           <div class="form-group">
             <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Scheduler Name'); ?> *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input id="schedule_name" name="schedule_name" value="<?php echo set_value('schedule_name');?>"  class="form-control" type="text">
               <span class="red"><?php echo form_error('schedule_name'); ?></span>
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
				
            	 </div>
         	</div>

         	<div class="form-group">
             	<label class="col-sm-3 control-label" for="from_sms"> <?php echo $this->lang->line('Send As'); ?> *</label>
				<div class="col-sm-9 col-md-6 col-lg-6">
				
				   <select  id='from_sms' class='form-control'>
					<option value=''><?php echo $this->lang->line('SMS API');?></option>
					<?php 
						foreach($sms_option as $id=>$option)
						{
							echo "<option value='{$id}'>{$option}</option>";
						}
					?>
					</select>
					
				</div>
			</div>
		   
		    <div class="form-group">
             	<label class="col-sm-3 control-label" for="name"> <?php echo $this->lang->line('SMS Template'); ?> </label>
				<div class="col-sm-9 col-md-6 col-lg-6">
				
				   <select  id='message_template_schedule' class='form-control'>
					<option value=''><?php echo $this->lang->line("I want to write new messsage, don't want any template");?></option>
					<?php 
						foreach($sms_template as $info){
							$template_name=$info['template_name'];
							$id=$info['id'];
							// $message=htmlentities($info['message'], ENT_QUOTES);
							echo "<option value='{$id}'>{$template_name}</option>";
						}

					?>
					</select>
					
				</div>
			</div>

			<div class="form-group">
             <label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('Message'); ?>  *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
             <b><?php echo $this->lang->line('You can variables :');?> </b> <br/><b><i>&nbsp;&nbsp;#firstname# &nbsp;&nbsp; #lastname# &nbsp;&nbsp; #mobile# &nbsp;&nbsp; #email#</i></b>
              <textarea style='height:200px' placeholder="<?php echo $this->lang->line("If you don't want any template, type your custom message here");?>" id="message" name="message" class="form-control"></textarea>
               <span class="red"><?php echo form_error('schedule_name'); ?></span>
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
			
             <label class="col-sm-3 control-label" for="name"> <?php echo $this->lang->line('Time Zone'); ?> *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
				<?php echo $time_zone_str; ?>
             </div>
           </div>

           <div id="text_count"></div>
		   
		   
           </div> <!-- /.box-body --> 
		   
		   
           <div class="box-footer">
            <div class="form-group">
             <div class="col-sm-12 text-center">
              
               <input name="submit" type="button" id="submit_schedule" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line('Save')?>"/>       
               <input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('Cancel')?>" onclick='goBack("my_sms/scheduled_sms",0)'/>
			   
			   
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

	var base_url="<?php echo base_url(); ?>";
	
	 $j("#contacts_id").multipleSelect({
            filter: true,
            multiple: true
        });
		
		
		$("#submit_schedule").click(function(){
			var schedule_name=$("#schedule_name").val();
			var message=$("#message").val();
			var day=$("#day").val();
			var month=$("#month").val();
			var year=$("#year").val();
			var hour=$("#hour").val();
			var minute=$("#minute").val();
			var time_zone=$("#time_zone").val();
			var contacts_id=$("#contacts_id").val();
			var from_sms=$("#from_sms").val();
			
			if(schedule_name=='' || message=='' || from_sms=='' || day=='' || month=='' || year=='' || year=='' || hour=='' || minute=='' || time_zone=='' || contacts_id==null){
				alert("All * marked fields are mandatory to send scheduled SMS.");	
				return false;
			}
			
			$.ajax({
				url:base_url+'my_sms/add_schedule_action',
				type:'POST',
				data:{
					from_sms:from_sms,schedule_name:schedule_name,message:message,day:day,month:month,year:year,hour:hour,minute:minute,time_zone:time_zone,contacts_id:contacts_id
				},
				
				success:function(respose){
					window.location=base_url+"my_sms/scheduled_sms";
				}
			});
		
		});
			

		$("#message_template_schedule").change(function(){
			var template_id = $(this).val();
			$.ajax({
				url:base_url+'my_sms/load_template',
				type:'POST',
				dataType: 'JSON',
				data:{template_id:template_id},
				success:function(response){
					$("#message").val(response.message);
				}
			});
		});
		
		
});
</script>
