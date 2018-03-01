<?php $this->load->view('admin/theme/message'); ?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><?php echo $this->lang->line('Scheduled Email'); ?>  </h1>  
</section>

<!-- Main content -->
<section class="content">  
  <div class="row">
    <div class="col-xs-12">
        <div class="grid_container">
            <table 
            id="upcoming_schedule_table"  
            class="easyui-datagrid" 
            url="<?php echo base_url()."my_email/upcoming_scheduled_email_data"; ?>" 
            pagination="true" 
            rownumbers="true" 
            toolbar="#tb" 
            pageSize="10" 
            pageList="[5,10,20,50,100]"  
            fit= "true" 
            fitColumns= "true" 
            nowrap= "true" 
            view= "detailview"
            idField="id"
            >
            
                <thead>
                    <tr>      
                       <th field="schedule_name" sortable="true" ><?php echo $this->lang->line('Scheduler Name'); ?></th>
                        <th field="subject" sortable="true" ><?php echo $this->lang->line('Subject'); ?></th>
                        <th field="attachment" sortable="true" formatter="attachment_download"><?php echo $this->lang->line('Attachment'); ?></th>
                        <th field="schedule_time" sortable="true"><?php echo $this->lang->line('Schedule'); ?></th>
                        <th field="time_zone" sortable="true" ><?php echo $this->lang->line('Time Zone'); ?></th>
                        <th field="is_sent" formatter='yes_no' sortable="true" ><?php echo $this->lang->line('Sent'); ?></th>
                        <th field="view"  formatter='action_column'><?php echo $this->lang->line('Actions'); ?></th>
                    </tr>
                </thead>
            </table>                        
         </div>
  
       <div id="tb" style="padding:3px">
            <?php $this->load->view('my_email/schedule_email/schedule_submenu'); ?>           
            <form class="form-inline" style="margin-top:20px">
			
			
                <div class="form-group">
                    <input  id="schedule_from_date" name="schedule_from_date" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Schedule From Date');?> ">
                </div> 
				
				<div class="form-group">
                     <input  id="schedule_to_date" name="schedule_to_date" class="form-control" size="20" placeholder="<?php echo $this->lang->line('Schedule To Date');?> ">
                </div> 				
				
                <button class='btn btn-info'  onclick="doSearch(event)"><?php echo $this->lang->line('Search'); ?></button>
            </form>         
        </div>
    </div>
  </div>   
</section>



<!--  Modal for contacts show of the tution -->

<div id="modal_contact_detail" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">		
            <div class="modal-header">
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
						 <span aria-hidden="true">&times;</span>
				 </button>
                <h4 id="contact_details_title" class="modal-title"><?php echo $this->lang->line('Scheduler Details'); ?></h4>
            </div>
			
            <div class="modal-body">

            <div class="bs-callout bs-callout-info"> 
            	<h4><i class="fa fa-info-circle"></i> <?php echo $this->lang->line('Subject'); ?> </h4> <br/>
            	<p id="scheduled_subject"></p> 
            </div>

            <div class="bs-callout bs-callout-info"> 
            	<h4><i class="fa fa-paperclip"></i> <?php echo $this->lang->line('Attachment'); ?> </h4> <br/>
            	<p id="scheduled_attachment"></p> 
            </div>

            <div class="bs-callout bs-callout-info"> 
            	<h4><i class="fa fa-envelope"></i> <?php echo $this->lang->line('Message'); ?> </h4> <br/>
            	<p id="scheduled_message"></p> 
            </div>

            <div id="contacts_view_body"></div>				
			</div>
			
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->lang->line('Close'); ?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
	
	var base_url="<?php echo site_url(); ?>";
	
	function action_column(value,row,index)
	{
	
	    var schedule_id=row.id;
		var schedule_info=JSON.stringify(row);
			schedule_info=HTMLEncode(schedule_info);
				
        var edit_url=base_url+'my_email/update_schedule_email/'+row.id;
        var str="";
		
		  str="<a title='View Contacts' style='cursor:pointer' onclick='view_contacts(event,"+schedule_info+")' >" +' <img src="<?php echo base_url("plugins/grocery_crud/themes/flexigrid/css/images/magnifier.png");?>" alt="View">'+"</a>";
       
        //str=str+"&nbsp;&nbsp;&nbsp;&nbsp;<a style='cursor:pointer' title='Update' href='"+edit_url+"'>"+' <img src="<?php echo base_url("plugins/grocery_crud/themes/flexigrid/css/images/edit.png");?>" alt="Edit">'+"</a>";
		
		str=str+"&nbsp;&nbsp;&nbsp;&nbsp;<a style='cursor:pointer' title='Update' onclick='delete_contact(event,"+schedule_id+")'>"+' <img src="<?php echo base_url("plugins/grocery_crud/themes/flexigrid/css/images/close.png");?>" alt="Delete">'+"</a>";
			
   		return str;
	}



	
	function view_contacts(e,schedule_info)
	{
			$("#modal_contact_detail").modal();
			var schedule_name=schedule_info.schedule_name;
			var schedule_time=schedule_info.schedule_time;
			$("#contact_details_title").html(schedule_name+ " @ " + schedule_time);		
			$("#scheduled_subject").html(schedule_info.subject);
			$("#scheduled_message").html(schedule_info.message);

			var attachment=schedule_info.attachment;
			attachment=attachment_download(attachment);
			$("#scheduled_attachment").html(attachment);

			var img_src="<?php echo base_url();?>assets/pre-loader/Snakes chasing.gif";
			var img="<center><img src='"+img_src+"' alt='Loading...'/></center>";
			$("#contacts_view_body").html(img);
			
			$.ajax({
				url:base_url+"my_email/schedule_contacts",
				type:'POST',
				data:{schedule_info:schedule_info},
				success:function(response){
					$("#contacts_view_body").html(response);
				}
			});
	}
	
	 function doSearch(event)
    {
        event.preventDefault(); 
        $j('#upcoming_schedule_table').datagrid('load',{
          schedule_from_date:             $j('#schedule_from_date').val(),
          schedule_to_date:               $j('#schedule_to_date').val(),
          is_searched:      1
        });
    }  
	
	function delete_contact(e,schedule_id){
		var ans=confirm("Are you sure that you want to delete this record?");
		if(!ans){
			return false;
		}
		
		$.ajax({
			url:base_url+'my_email/delete_schedule',
			type:'POST',
			data:{
				schedule_id:schedule_id
			},
			success:function(response){
				$j('#upcoming_schedule_table').datagrid('reload');
			}
		});
		
	}
	
	
	$j("document").ready(function(){
		$('#schedule_from_date').datepicker({format: "dd-mm-yyyy"});    	
		$('#schedule_to_date').datepicker({format: "dd-mm-yyyy"});    
		
	});

</script>