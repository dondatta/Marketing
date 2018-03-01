<!doctype html>
<html class="no-js" lang="">
<head>
	<meta charset="utf-8">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title><?php echo $this->config->item("product_name"); ?></title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" href="apple-touch-icon.png">
	<link rel="icon" href="<?php echo base_url(); ?>assets/images/favicon.png" type="image/x-icon"/>
	<!-- Place favicon.ico in the root directory -->
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic|Lato:400,700,400italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?php echo site_url(); ?>website/css/animate.css">
	<link rel="stylesheet" href="<?php echo site_url(); ?>website/css/font-awesome.css">
	<link rel="stylesheet" href="<?php echo site_url(); ?>website/css/bootstrap.css">
	<link rel="stylesheet" href="<?php echo site_url(); ?>website/css/normalize.css">
	<link rel="stylesheet" href="<?php echo site_url(); ?>website/css/main.css">
	<link rel="stylesheet" href="<?php echo site_url(); ?>website/style.css">
	<script src="<?php echo site_url(); ?>website/js/vendor/modernizr-2.8.3.min.js"></script>
</head>
<body id="mainbody">
	<!-- Preloader -->
	<div id="preloader">
		<div id="status">&nbsp;</div>
	</div>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<!-- Add your site or application content here -->
<nav class="navbar navbar-default custom-navbar">
	<div class="container-fluid custom-container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand custom-brand" href="<?php echo site_url(); ?>"><img src="<?php echo base_url(); ?>assets/images/logo.png"  style="margin-top:10px;width:220px !important;" alt="<?php echo $this->config->item('product_name'); ?>"></a>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav custom-nav navbar-right">
				<li><a data-scroll href="#mainbody" class="active"><?php echo $this->lang->line('Home'); ?></a></li>
				<li><a data-scroll href="#about"><?php echo $this->lang->line('About'); ?></a></li>
				<li><a data-scroll href="#features"><?php echo $this->lang->line('Key Feature'); ?></a></li>
				<!-- <li><a data-scroll href="#howitsworks">How Its Works</a></li>	 -->
				<li><a data-scroll href="#pricing"><?php echo $this->lang->line('Pricing'); ?></a></li>
				<li><a href="<?php echo site_url('home/sign_up'); ?>"><?php echo $this->lang->line('Sign Up'); ?></a></li>
				<li><a href="<?php echo site_url('home/login'); ?>"><?php echo $this->lang->line('Log In'); ?></a></li>		
				<li>
				    <?php
				    $select_lan="english";
				    if($this->session->userdata("selected_language")=="") $select_lan=$this->config->item("language");
				    else $select_lan=$this->session->userdata("selected_language");
				    echo form_dropdown('language',$language_info,$select_lan,'class="form-control pull-right" id="language_change" autocomplete="off" style="width:100px;height:40px;margin-top:5px;margin-left:15px;margin-right:5px;border-radius:0"');  ?>              

				</li>			
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container -->
</nav> <!-- end nav bar -->


<div class="container-fluid top-margin"> <!-- start slider image -->

	<div class="row">
		<div class="hidden-xs hidden-sm col-md-12 no-padding">
			<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
				<!-- Indicators -->
				<ol class="carousel-indicators">
					<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
					<li data-target="#carousel-example-generic" data-slide-to="1"></li>
					<li data-target="#carousel-example-generic" data-slide-to="2"></li>
					<!-- <li data-target="#carousel-example-generic" data-slide-to="3"></li> -->
				</ol>
				<!-- Wrapper for slides -->
				<div class="carousel-inner" role="listbox">
					<div class="item active">
						<img src="<?php echo site_url(); ?>website/img/slider/slider-1.png" class="carousel-img" alt="slider">
						<div class="carousel-caption text-center">
							<div class="carosel-box">
								<h3>

								<?php echo $this->lang->line('Send bulk SMS/Email with attachment & template');?>

								<!-- Send bulk SMS/Email with attachment &amp; template -->

								</h3>
								<br/><br/>
								<p>
								<?php echo $this->lang->line('slider_4');?>
									<!-- Send bulk SMS to your contact. You can send SMS with their name like “Hello, #firstname#......”. You can create SMS/Email templates. Send bulk Email to your contacts , attach file if needed. --></h3>
								</p>
							</div>
						</div>
					</div>
					<div class="item">
						<img src="<?php echo site_url(); ?>website/img/slider/slider-2.png" class="carousel-img" alt="slider">
						<div class="carousel-caption">
							<div class="carosel-box">
								<h3><?php echo $this->lang->line('Birthday wish SMS/Email'); ?></h3>
								<br/><br/>
								<p>
								<?php echo $this->lang->line('slider_1'); ?>

								<!-- Birthday wish will be send automatically to the contact’s birthday. Birthday wish setting is available with enable or disable option. Create your own wish Message and then give responsibility to the application to send each birthday. -->
								</p>
							</div>
						</div>
					</div>
					<div class="item">
						<img src="<?php echo site_url(); ?>website/img/slider/slider-3.png" class="carousel-img" alt="slider">
						<div class="carousel-caption">
							<div class="carosel-box">
								<h3><?php echo $this->lang->line('Schedule SMS/Email'); ?></h3>
								<br/><br/>
								<p>
								<?php echo $this->lang->line('slider_5');?>

								<!-- Create your campaign, fixed the time when you want to shoot. Email /SMS will be sent at scheduled time. It’s super easy to set. You can send party invitation or meeting reminder. -->

								</p>
							</div>
						</div>
					</div>
					<div class="item">
						<img src="<?php echo site_url(); ?>website/img/slider/slider-4.png" class="carousel-img" alt="slider">
						<div class="carousel-caption">
							<div class="carosel-box">
								<h3><?php echo $this->lang->line('Design your Email template using visual editor'); ?> </h3>
								<br/><br/>
								<p>

								<?php echo $this->lang->line('slider_3'); ?>

								<!-- Create email template easily with ckEditor. Html code writing option is also available. Attach image, html tag, variable easily. Let’s have a try and create beautiful email and send to it your customer.  -->

								</p>
							</div>
						</div>
					</div>
					<div class="item">
						<img src="<?php echo site_url(); ?>website/img/slider/slider-5.png" class="carousel-img" alt="slider">
						<div class="carousel-caption">
							<div class="carosel-box">
								<h3><?php echo $this->lang->line('Manage Contacts'); ?> </h3>
								<br/><br/>
								<p>

								<?php echo $this->lang->line('slider_2'); ?>

								<!-- Mangae your contacts with groups like friends, family, office etc. You can import contacts from csv file. You can also store same contact in multiple groups. -->

								</p>
							</div>
						</div>
					</div>
				</div>
				<!-- Controls -->
				<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
					<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
					<span class="sr-only"><?php echo $this->lang->line('Previous'); ?></span>
				</a>
				<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
					<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
					<span class="sr-only"><?php echo $this->lang->line('Next'); ?></span>
				</a>
			</div>
		</div> <!-- end col md 12 -->
	</div> <!-- end row -->
</div> <!-- end container fluid -->

<section id="about">
	<div class="discription-bg" >
		<div class="container">
			<div class="row about-padding">
				<div class="col-md-6 intro-img animated wow zoomInRight">
					<img src="<?php echo base_url();?>website/img/img-2.png" class="img-responsive" alt="intro">
				</div>
				<div class="col-md-6 intro-contain animated wow zoomInLeft" >
					<h2>
						<?php echo $this->config->item("product_name") ?>
					</h2>
					<hr/>
					<p>
					<?php echo $this->lang->line('welcome_description');?>
						<!-- You can manage your contacts, create SMS/Email template, send SMS/Email, schedule SMS/Email, wish your contacts’ birthday etc using <strong>Smart SMS &amp; Email Manager (SSEM)</strong> in a smarter way. 
						SSEM has built-in support for world’s most popular SMS &amp; Email gateways like <strong> Plivo, Twilio, Clickatell, Nexmo, Mandrill, Sendgrid, Mailgun etc.</strong> -->
					</p>
					<br/>
				</div>
			</div> <!-- end row -->
		</div> <!-- end container -->
	</div> <!-- end container bg -->
</section>

<section id="features" data-type="background" class="features-padding" data-speed="5">
	<div class="container Key-features">
		<div class="features-margin">
			<div class="row rowmargin">
				<div class="col-md-12">
					<h2>
						<?php echo $this->lang->line('Key Features'); ?>
					</h2><hr/>
				</div>
			</div> 
			<!-- end row -->
			<div class="row">
				<div class="col-md-6">
					<ul class="Key-features-ul">
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('Dashboard'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('Settings (system customization)'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('User management (User Types : admin, user)'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('Contact management'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('Most popular SMS/Email gateway support'); ?> </li>
					</ul>
				</div>
				<div class="col-md-6">
					<ul class="Key-features-ul">
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('SMS/Email template management'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('Bulk SMS/Email sending'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('Scheduled SMS/Email sending'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('Birthday wish SMS/Email'); ?> </li>
						<li><i class="fa fa-chevron-circle-right"></i> <?php echo $this->lang->line('SSEM native APIs: a)Contact Sync b)Send SMS c)Send Email'); ?> </li>
					</ul>
				</div>
			</div>
			<br/><br/>
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInLeft">
					<img src="<?php echo base_url();?>website/img/Manage-Contacts.png" alt="Manage Contacts">
					<h3>
						<?php echo $this->lang->line('Manage Contacts'); ?>
					</h3>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInLeft">
					<img src="<?php echo base_url();?>website/img/Worldwide-SMS.png" alt="Worldwide SMS/Email Via Most Popular Gateways">
					<h3>
						<?php echo $this->lang->line('Popular SMS/Email Gateways'); ?>
					</h3>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInLeft">
					<img src="<?php echo base_url();?>website/img/Send-Email-With-Template.png" alt="Send Email With Template &amp; Attachment">
					<h3>
						<?php echo $this->lang->line('Email With Template & Attachment'); ?><!-- Email With Template &amp; Attachment -->
					</h3>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInLeft">
					<img src="<?php echo base_url();?>website/img/Send-Bulk-SMS.png" alt="Send Bulk SMS">
					<h3>
						<?php echo $this->lang->line('Send Bulk SMS'); ?>
					</h3>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInRight">
					<img src="<?php echo base_url();?>website/img/Send-Bulk-Email.png" alt="Send Bulk Email">
					<h3>
						<?php echo $this->lang->line('Send Bulk Email'); ?>
					</h3>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInRight">
					<img src="<?php echo base_url();?>website/img/Send-Scheduled-SMS-Email.png" alt="Send Scheduled SMS/Email">
					<h3>
						<?php echo $this->lang->line('Send Scheduled SMS/Email'); ?>
					</h3>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInRight">
					<img src="<?php echo base_url();?>website/img/Send-Scheduled-SMSEmail.png" alt="Birthday Wish SMS/Email">
					<h3>
						<?php echo $this->lang->line('Birthday wish SMS/Email'); ?>
					</h3>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 text-Justify feature-margin animated wow slideInRight">
				</div>
			</div>
		</div>
	</div> <!-- end container -->
</section> <!-- end paralox bg section -->

<!-- <section id="howitsworks">
	<div class="container">
		<div class="row">
			<div class="col-md-6 how-its-works animated wow wow slideInLeft">
				<h2>
					How SSEM works
				</h2>
				<p>How SSEM works</p>
			</div>
			<div class="col-md-6 intro-img animated wow slideInRight">
				<img src="<?php echo base_url();?>website/img/img-2.png" class="img-responsive" alt="intro">
			</div>
		</div>
	</div>
</section> -->

<section id="pricing" data-type="background" data-speed="5">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-md-padding text-center">
			<h2 style="font-size:48px">
					<i class="fa fa-money"></i> <?php echo $this->lang->line('Pricing'); ?>
				</h2><hr>			
				<h3>
					<?php echo $this->lang->line('get early access to'); ?> <b><?php echo $this->config->item('product_name'); ?></b>
				</h3>
				<h2>$<?php echo $price; ?> / month  -  1 User </h2>
				<br/>
				<a href="<?php echo site_url('home/sign_up'); ?>" class="btn btn-default btn-lg" style="border-radius: 0px"><?php echo $this->lang->line('Sign Up Now');?></a>
			</div>
		</div> <!-- end row -->
	</div> <!-- end container -->
</section>
<div class="container" ><!-- start footer content div -->
	<div class="row">
		<div class="col-md-6 footer-text">
			<p class="copyright-text">
			&copy; <a style='color:#fff;' href="<?php echo site_url(); ?>"><?php echo $this->config->item("product_short_name")." ".$this->config->item("product_version"); ?></a>
			</p>
		</div>
		<div class="col-md-6">
			<div class="socials-list">
				<ul>
					<li><a href="#"><i class="fa fa-facebook-square fa-2x"></i></a></li>
					<li><a href="#"><i class="fa fa-youtube-square fa-2x"></i></a></li>
					<li><a href="#"><i class="fa fa-twitter-square fa-2x"></i></a></li>
					<li><a href="#"><i class="fa fa-google-plus-square fa-2x"></i></a></li>
					<li><a href="#"><i class="fa fa-rss-square fa-2x"></i></a></li>
				</ul>
			</div>
		</div>
	</div>
</div> <!-- end footer container -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo site_url(); ?>website/js/vendor/jquery-1.11.3.min.js"><\/script>')</script>
<script src="<?php echo site_url(); ?>website/js/plugins.js"></script>
<script src="<?php echo site_url(); ?>website/js/bootstrap.js"></script>
<script src="<?php echo site_url(); ?>website/js/main.js"></script>
<script src="<?php echo site_url(); ?>website/js/jquery.scrollUp.min.js"></script>
<script src="<?php echo site_url(); ?>website/js/wow.min.js"></script>
<script src="<?php echo site_url(); ?>website/js/smooth-scroll.js"></script>
<script>
	var offsetHeight = 51;
	$('.nav-collapse').scrollspy({
		offset: offsetHeight
	});
	$('.navbar li a').click(function (event) {
		var scrollPos = $('body > .container').find($(this).attr('href')).offset().top - offsetHeight;
		$('body,html').animate({
			scrollTop: scrollPos
		}, 500, function () {
			$(".btn-navbar").click();
		});
		return false;
	});
</script>

<script type="text/javascript">
	$(document).ready(function() {
		$("#language_change").change(function(){
			var language=$(this).val();
			$("#language_label").html("Loading Language...");
			$.ajax({
				url: '<?php echo site_url("home/language_changer");?>',
				type: 'POST',
				data: {language:language},
				success:function(response){
					$("#language_label").html("Language");
					location.reload(); 
				}
			})

		});
	});
</script>
</body>
</html>