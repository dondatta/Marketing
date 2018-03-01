<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-send"></i><?php echo $this->lang->line('Send SMS');?> </h3>
       </div><!-- /.box-header -->
	   
	   
       <!-- form start -->
	   
	   
       <form class="form-horizontal" method="POST">
	   
         <div class="box-body">	  
		 
		 <div class="form-group">
			  <label class="col-sm-3 control-label" for="contacts_mobile"> <?php echo $this->lang->line('Select Contacts')?></label>
			  	<div class="col-sm-9 col-md-6 col-lg-6">
					<select multiple="multiple"  class="form-control" id="contacts_mobile" name="contacts_mobile">					
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
	             <label class="col-sm-3 control-label" for="to_numbers"> <?php echo $this->lang->line('or / and'); ?></label>
	             <div class="col-sm-9 col-md-6 col-lg-6"></div>
            </div>


		    <div class="form-group">
	             <label class="col-sm-3 control-label" for="to_numbers"> <?php echo $this->lang->line('Numbers To Send');?> *</label>
	             <div class="col-sm-9 col-md-6 col-lg-6">
	              <!-- <textarea placeholder="You can type comma seperated numbers with country code here. You can also import numbers from a CSV file and numbers will be mereged here." id="to_numbers" name="to_numbers" class="form-control"></textarea> -->
	              <textarea style='height:200px' placeholder="<?php echo $this->lang->line('placeholder_1_sms') ;?>" id="to_numbers" name="to_numbers" class="form-control"></textarea>
	             </div>
            </div>
		 	
		 	<div class="form-group">
	             <label class="col-sm-3 control-label"></label>
	             <div class="col-sm-9 col-md-6 col-lg-6">
	               <a id="import_from_csv" data-toggle="modal" href='#csv_import_modal' class="btn btn-primary btn-small"><i class="fa fa-file-text"></i> <?php echo $this->lang->line('I want to import numbers from CSV');?></a>
	             </div>
            </div>

            <div class="form-group">
             	<label class="col-sm-3 control-label" for="from_sms"> <?php echo $this->lang->line('Send As');?> *</label>
				<div class="col-sm-9 col-md-6 col-lg-6">
				
				   <select  id='from_sms' class='form-control'>
					<option value=''><?php $this->lang->line('SMS API');?></option>
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
             	<label class="col-sm-3 control-label" for="message_template_send"> <?php echo $this->lang->line('SMS Template');?> </label>
				<div class="col-sm-9 col-md-6 col-lg-6">
				
				   <select  id='message_template_send' class='form-control'>
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
             <label class="col-sm-3 control-label" for="message"> <?php echo $this->lang->line('Message');?> * </label>
             <div class="col-sm-9 col-md-6 col-lg-6">
              <b><?php echo $this->lang->line('You can use variables:'); ?></b> <br/><b><i>&nbsp;&nbsp; #firstname# &nbsp;&nbsp;  #lastname# &nbsp;&nbsp; #mobile# &nbsp;&nbsp; #email#</b></i>

              <!-- <textarea placeholder="If you don't want any template, type your custom message here" id="message" name="message" class="form-control"></textarea> -->
              <textarea style='height:200px' placeholder="<?php echo $this->lang->line('If you don\'t want any template, type your custom message here'); ?>" id="message" name="message" class="form-control"></textarea>

                <span class="red"><?php echo form_error('schedule_name'); ?></span>
             </div>
           </div>

           <div id="text_count"></div>

           
           </div> <!-- /.box-body --> 
		   
		   
           <div class="box-footer">
            <div class="form-group">
             <div class="col-sm-12 text-center">
              
               <input name="submit" type="button" id="send_sms" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line('Send'); ?>"/>       
               <input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('Cancel'); ?>" onclick='goBack("my_sms/scheduled_sms",0)'/>
			   
			   
             </div>
           </div>
         </div><!-- /.box-footer -->         
         </div><!-- /.box-info -->      
		  
       </form>     
     </div>
   </section>
</section>




<!--  Modal for contacts show of the tution -->

<div id="modal_sms_send_waiting" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
		
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						 <span aria-hidden="true">&times;</span>
				 </button>
                <h4 id="sms_send_waiting_title" class="modal-title"><?php echo $this->lang->line('Sending SMS....');?> </h4>
            </div>
			
            <div id="sms_send_waiting_body" class="modal-body">
					
			</div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('Close');?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="csv_import_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><i class="fa fa-file-text"></i> <?php echo $this->lang->line('Import numbers from CSV'); ?></h4>
			</div>
			<div class="modal-body">
				<form action="" id="csv_import_form" method="POST" enctype="multipart/form-data">
					<?php echo $this->lang->line('Browse CSV');?> <input type="file"  name="csv_file" id="csv_file"/>
					<input type="hidden" id="hidden_import_type" name="hidden_import_type" value="sms"/>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('Cancel');?></button>
				<button type="button" id="import_submit" class="btn btn-primary"><?php echo $this->lang->line('Import');?></button>
			</div>
		</div>
	</div>
</div>





<script>
$j("document").ready(function(){

	var base_url="<?php echo base_url(); ?>";
	
	 $j("#contacts_mobile").multipleSelect({
            filter: true,
            multiple: true
        });
			
	
		$("#message_template_send").change(function(){
			var template_id = $(this).val();
			$.ajax({
				url:base_url+'my_sms/load_template',
				type:'POST',
				dataType: 'JSON',
				data:{template_id:template_id},
				success:function(response){
					$("#message").val(response.message);
					$("#message").keyup();
				}
			});
			
		});
		
		
		/****Submit send sms button ****/
		
		$("#send_sms").click(function(){			
			var to_numbers=$("#to_numbers").val();
			var contacts_mobile=$("#contacts_mobile").val();
			var message=$("#message").val();
			var from_sms=$("#from_sms").val();
			
			if(to_numbers=='' && contacts_mobile==null){
				alert("<?php echo $this->lang->line('You have not mentioned any receipent.')?>");
				return false;
			}

			if(from_sms==''){
				alert("<?php echo $this->lang->line('You have not selected any SMS API.');?>");
				return false;
			}
			
			if(message==''){
				alert("<?php echo $this->lang->line('You have neither selected any template nor typed any custom message.');?>");
				return false;
			}
			
			$("#modal_sms_send_waiting").modal();
					
			var history_link=base_url+"my_sms/sms_history";
			$("#sms_send_waiting_title").html("<?php echo $this->lang->line('request has been submitted for processing');?>");
			$("#sms_send_waiting_body").html("<?php echo $this->lang->line('request has been submitted for processing');?> <a href='"+history_link+"'><?php echo $this->lang->line('view sms history');?></a>");
			
			$.ajax({
				url:base_url+'my_sms/sms_send_action',
				type:'POST',
				data:{to_numbers:to_numbers,contacts_mobile:contacts_mobile,message:message,from_sms:from_sms},
				success:function(response){					
				}
			});
		
		});


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
          url: site_url+'my_sms/csv_upload',
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
		         var to_numbers=$("#to_numbers").val().trim();
		         if(to_numbers!="") file_content=','+file_content; 	
		         file_content=to_numbers+file_content;
		         $("#to_numbers").val(file_content);
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





