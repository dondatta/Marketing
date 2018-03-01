<section class="content-header">
   <section class="content">
     <div class="box box-info custom_box">
       <div class="box-header">
         <h3 class="box-title"><i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('Import Contact (CSV)'); ?> </h3>
       </div><!-- /.box-header -->
	   
	   
       <!-- form start -->
	   
	   
       <form class="form-horizontal" action="" method="POST" id="csv_import_form" enctype="multipart/form-data">
         <div class="box-body">		 
		 
          <!--  <div class="form-group">
             <label class="col-sm-3 control-label">Contact Group *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <?php 
                  $contact_info['']='Select Contact Type';
                  echo form_dropdown('contact_type',$contact_info,set_value('contact_type'),'class="form-control" id="contact_type"'); 
                ?>
               <span class="red contact_type_error"><?php echo form_error('contact_type'); ?></span>
             </div>
           </div> -->

           <div class="form-group">
             <label class="col-sm-3 control-label"><?php echo $this->lang->line('Browse CSV'); ?> *</label>
             <div class="col-sm-9 col-md-6 col-lg-6">
               <input type="file" name="csv_file" id="csv_file" class="form-control" value="<?php echo set_value('csv_file'); ?>">
               <span class="red csv_file_error"><?php echo form_error('csv_file'); ?></span>
               <br/><span><a target="_BLANK" class="btn btn-sm btn-primary" href="<?php echo base_url("assets/sample/contact_import_sample.csv"); ?>"><i class="fa fa-cloud-download"></i> <?php echo $this->lang->line('Download Sample CSV'); ?></a></span> 
                 <br/><?php echo $this->lang->line('(open with text editor to view original formatting)'); ?>
             </div>
           </div>

		   
           </div> <!-- /.box-body --> 
		   
		   
           <div class="box-footer">
            <div class="form-group">
             <div class="col-sm-12 text-center">              
               <input name="submit" type="button" id="import_submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line('Save'); ?>"/>       
               <input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('Cancel'); ?>" onclick='goBack("phonebook/contact_list",0)'/>			   
			   
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
	$("#import_submit").click(function(){ 

  var contact_type=$("#contact_type").val();
    
      
      var site_url="<?php echo site_url();?>";    
 
      var queryString = new FormData($("#csv_import_form")[0]);
      
      var fileval=$("#csv_file").val();
      if(fileval=="")  $(".csv_file_error").html("<?php echo $this->lang->line('You have not select any contact.');?>");
      else $(".csv_file_error").html("");

     /* var contact_type=$("#contact_type").val();
      if(contact_type=="") 
      {
      	$(".contact_type_error").html("<b>Contact Group</b> is required.</b>");
      	return;
      }
      else $(".contact_type_error").html("");*/

      $(this).html('<?php echo $this->lang->line("please wait"); ?>');
      $.ajax({
          url: site_url+'phonebook/import_contact_action_ajax',
          type: 'POST',
          data: queryString,
          dataType:'json',
          async: false,
          cache: false,
          contentType: false,
          processData: false,
          success: function (response)                
          {
          	 $("#import_submit").html('Import');         
          	 if(response.status=='ok')
          	 {          	 	
		        alert(response.count + " <?php echo $this->lang->line('contacts has been imported from csv was successfully'); ?> ");
		        var link="<?php echo site_url('phonebook/contact_list');?>";
		        window.location.assign(link);
          	 }
          	 else
          	 {
          	 	 var error=response.status.replace(/<\/?[^>]+(>|$)/g, "");
          	 	 alert(response.status);
          	 }
          }
            
        });
              
         
  });	
		
});
</script>
