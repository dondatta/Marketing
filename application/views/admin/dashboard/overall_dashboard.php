<section class="content-header">
   <section class="content">
   	<div class="row">
			<div class="col-xs-12">
				<?php 										
					if ($total_email_sent=="") $total_email_sent=0; 
					if ($total_sms_sent=="") $total_sms_sent=0; 
					if ($today_total_email_sent=="") $today_total_email_sent=0; 
					if ($today_total_sms_sent=="") $today_total_sms_sent=0; 
					if ($total_sent_email_this_month=="") $total_sent_email_this_month=0; 
					if ($total_sent_sms_this_month=="") $total_sent_sms_this_month=0; 
				?>

				<!-- <div class="row">
					<div class="text-center"><h2 style="font-weight:900;">TOTAL EMAIL & SMS SENT REPORT</h2></div>
					<div id="div_for_circle_chart"></div>
				</div> -->

<div class="row" style="padding : 10px;">
	<div class="col-xs-12 col-md-6">
		<div class="text-center"><h2 style="font-weight:900;"><?php echo $this->lang->line('TOTAL EMAIL SENT REPORT'); ?></h2></div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-8 col-xs-12">
					<div class="chart-responsive">
						<canvas id="pieChart" height="220"></canvas>
					</div><!-- ./chart-responsive -->
				</div><!-- /.col -->
				<div class="col-md-4 col-xs-12" style="padding-top:35px;">
					<ul class="chart-legend clearfix">
						<?php foreach($email_gateway_name as $value): ?>
							<li><i class="fa fa-circle-o" style="color:<?php echo $value['color']; ?>"></i> <?php echo $value['name']; ?></li>
						<?php endforeach; ?>
					</ul>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.box-body -->
	</div>
	<div class="col-xs-12 col-md-6">
		<div class="text-center"><h2 style="font-weight:900;"><?php echo $this->lang->line('TOTAL SMS SENT REPORT'); ?></h2></div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-8 col-xs-12">
					<div class="chart-responsive">
						<canvas id="pieChart_sms" height="220"></canvas>
					</div><!-- ./chart-responsive -->
				</div><!-- /.col -->
				<div class="col-md-4 col-xs-12" style="padding-top:35px;">
					<ul class="chart-legend clearfix">
						<?php foreach($gateway_name as $sms_gateway): ?>
							<li><i class="fa fa-circle-o" style="color:<?php echo $sms_gateway['color']; ?>"></i> <?php echo $sms_gateway['name']; ?></li>
						<?php endforeach; ?>
					</ul>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.box-body -->
	</div>
</div>

<br/><br/>
<!-- daily report section -->
<div class="row" style="padding:10px;">
	<!-- Info Boxes Style 2 -->
	<div class="text-center"><h2 style="font-weight:900;"><?php echo $this->lang->line("TODAY'S REPORT"); ?></h2></div>
	<div class="col-md-4 col-md-offset-2 col-sm-6 col-xs-12">
		<div class="info-box bg-aqua">
			<span class="info-box-icon"><i class="fa fa-send"></i></span>
			<div class="info-box-content">
				<!-- <span class="info-box-text">Inventory</span> -->
				<span class="info-box-number"><?php echo $today_total_email_sent; ?></span>
				<div class="progress">
					<div class="progress-bar" style="width: 70%"></div>
				</div>
				<span class="progress-description">
					<b><?php echo $this->lang->line("Today's Sent Email"); ?></b>
				</span>
			</div><!-- /.info-box-content -->
		</div><!-- /.info-box -->
	</div>
	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box bg-red">
			<span class="info-box-icon"><i class="fa fa-envelope"></i></span>
			<div class="info-box-content">				
				<span class="info-box-number"><?php echo $today_total_sms_sent; ?></span>
				<div class="progress">
					<div class="progress-bar" style="width: 70%"></div>
				</div>
				<span class="progress-description">
					<b><?php echo $this->lang->line("Today's Sent SMS"); ?></b>
				</span>
			</div><!-- /.info-box-content -->
		</div><!-- /.info-box -->
	</div>	
</div>
				<br/><br/>				
				<!--end of daily report section -->		

				<!-- monthly report section -->
				

<div class="row" style="padding:10px;">
	<div class="text-center"><h2 style="font-weight:900;"><?php echo $this->lang->line("CURRENT MONTH'S REPORT"); ?></h2></div>
	<div class="col-md-4 col-md-offset-2 col-sm-6 col-xs-12">
		<div class="info-box" style="border:1px solid #00A65A;border-bottom:3px solid #00A65A;">
			<span class="info-box-icon bg-green"><i class="fa fa-send"></i></span>
			<div class="info-box-content">
				<span class="info-box-text"><?php echo $this->lang->line("This Month's Sent Email"); ?></span>
				<span class="info-box-number"><?php echo $total_sent_email_this_month; ?></span>
			</div><!-- /.info-box-content -->
		</div><!-- /.info-box -->
	</div><!-- /.col -->
	<div class="col-md-4 col-sm-6 col-xs-12">
		<div class="info-box" style="border:1px solid #F39C12;border-bottom:3px solid #F39C12;">
			<span class="info-box-icon bg-yellow"><i class="fa fa-envelope"></i></span>
			<div class="info-box-content">
				<span class="info-box-text"><?php echo $this->lang->line("This Month's Sent SMS"); ?></span>
				<span class="info-box-number"><?php echo $total_sent_sms_this_month; ?></span>
			</div><!-- /.info-box-content -->
		</div><!-- /.info-box -->
	</div><!-- /.col -->
</div><!-- /.row -->
				
				<!--end of monthly report section -->

				
				<br/><br/>
				<div class="row">
					<div class="text-center"><h2 style="font-weight:900;"><?php echo $this->lang->line('EMAIL & SMS SENT REPORT FOR LAST 12 MONTHS'); ?></h2></div>
					<div id='div_for_bar'></div>
				</div>

				
				
				
				
				<?php
				
  						// $bar=array("0"=>array("y"=>2014,"a"=>100,"b"=>50),"1"=>array("y"=>2015,"a"=>100,"b"=>50));
				$bar = $chart_bar;
				$circle_bir = array(
					'0' => array(
						'label'=>"Total Email Sent",
						'value'=>$total_email_sent
						),
					'1' =>array(
						'label'=>"Total SMS Sent",
						'value'=>$total_sms_sent
						
						)
					
					);
				
				 ?>
				
				
				<input type="hidden" id="pichart_sms_data" value='<?php echo $piechart_sms; ?>' />
				<input type="hidden" id="pichart_email_data" value='<?php echo $piechart_email; ?>' />

			</div>
		</div>
   </section>
</section>


<script>
$j(document).ready(function(){


  var pieChartCanvas = $j("#pieChart").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
  var PieData = $("#pichart_email_data").val();
  PieData=JSON.parse(PieData); 

  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 20, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%>"
  };
  pieChart.Doughnut(PieData, pieOptions);



  var pieChartCanvas_sms = $j("#pieChart_sms").get(0).getContext("2d");
  var pieChart_sms = new Chart(pieChartCanvas_sms);

  var PieData_sms = $("#pichart_sms_data").val(); 
  PieData_sms=JSON.parse(PieData_sms); 


  var pieOptions_sms = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 20, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%>"
  };
  pieChart_sms.Doughnut(PieData_sms, pieOptions_sms);

	Morris.Bar({
	  element: 'div_for_bar',
	  data: <?php echo json_encode($bar); ?>,
	  xkey: 'year',
	  ykeys: ['sent_email', 'sent_sms'],
	  labels: ['Total Sent Email', 'Total Sent Sms']
	});
});

// Morris.Donut({
//   element: 'div_for_circle_chart',
//   data: <?php echo json_encode($circle_bir); ?>
// });
</script>






