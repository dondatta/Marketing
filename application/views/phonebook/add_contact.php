<section class="content-header">
	<section class="content">
		<div class="box box-info custom_box">
			<div class="box-header">
				<h3 class="box-title"><i class="fa fa-plus-circle"></i> <?php echo $this->lang->line('Add Contact'); ?></h3>
			</div><!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" action="<?php echo site_url().'phonebook/add_contact_action';?>" enctype="multipart/form-data" method="POST">
				<div class="box-body">					

					<div class="form-group">
						<label class="col-sm-3 control-label" for="name"><?php echo $this->lang->line('First Name'); ?> *
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="first_name" value="<?php echo set_value('first_name');?>"  class="form-control" type="text">		          
							<span class="red"><?php echo form_error('first_name'); ?></span>
						</div>
					</div>


					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Last Name'); ?>  
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="last_name" value="<?php echo set_value('last_name');?>"  class="form-control" type="text">		          
							<span class="red"><?php echo form_error('last_name'); ?></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Mobile Number'); ?> *
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="phone_number" value="<?php echo set_value('phone_number');?>"  class="form-control" type="text">		          
							<span class="red"><?php echo form_error('phone_number'); ?></span>
						</div>
					</div> 

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Email'); ?>  *
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">
							<input name="email" value="<?php echo set_value('email');?>"  class="form-control" type="text">		               
							<span class="red"><?php echo form_error('email'); ?></span>
						</div>
					</div>	

					

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Date of Birth'); ?>
						</label>

						<div class="col-sm-9 col-md-6 col-lg-6">
		                    <input id="from_date"  value="<?php echo set_value('from_date');?>" name="from_date" class="form-control datepicker" size="22" placeholder="<?php echo $this->lang->line('mm/dd/yyyy');?>">
		                    <span class="red"><?php echo form_error('from_date'); ?></span>
		                </div>    
                	</div>

					<div class="form-group">
						<label class="col-sm-3 control-label" ><?php echo $this->lang->line('Contact Group'); ?> *
						</label>
						<div class="col-sm-9 col-md-6 col-lg-6">							
							<?php if(isset($group_checkbox)) echo $group_checkbox; ?>
							<span class="red">
							<?php 
								if($this->session->userdata('group_type_error')==1){
									echo '<b>'.$this->lang->line('Contact Group').'</b>'.str_replace("%s","", $this->lang->line("required"));
								$this->session->unset_userdata('group_type_error');
								}
							?>
							</span>
						</div>
					</div>	
					

				</div> <!-- /.box-body --> 
				<div class="box-footer">
					<div class="form-group">
						<div class="col-sm-12 text-center">
							<input name="submit" type="submit" class="btn btn-warning btn-lg" value="<?php echo $this->lang->line('Save');?>"/>  
							<input type="button" class="btn btn-default btn-lg" value="<?php echo $this->lang->line('Cancel');?>" onclick='goBack("phonebook/contact_list")'/>  
						</div>
					</div>
				</div><!-- /.box-footer -->         
			</div><!-- /.box-info -->       
		</form>     
	</div>
</section>
</section>

<script type="text/javascript">
	
	 $j(function() {
    $( ".datepicker" ).datepicker();
  }); 
</script>