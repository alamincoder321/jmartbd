<?php
$companyInfo = $this->db->query("select * from tbl_company c order by c.Company_SlNo desc limit 1")->row();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title><?php echo $companyInfo->Company_Name; ?> || <?php echo $title; ?></title>

	<meta name="description" content="overview &amp; stats" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/4.5.0/css/font-awesome.min.css" />

	<!-- page specific plugin styles -->

	<!-- text fonts -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fonts.googleapis.com.css" />
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/fancyBox/css/jquery.fancybox.css?v=2.1.5" media="screen" />
	<!-- ace styles -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-skins.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-rtl.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/responsive.css" />

	<!-- ace settings handler -->
	<script src="<?php echo base_url(); ?>assets/js/ace-extra.min.js"></script>

	<link rel="icon" type="image/x-icon" href="/uploads/company_profile_org/<?= $companyInfo->Company_Logo_org ?>">
</head>

<body class="skin-2">

	<div id="navbar" class="navbar navbar-default ace-save-state navbar-fixed-top" style="background:#007ebb;">
		<div class="navbar-container ace-save-state" id="navbar-container">
			<button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
				<span class="sr-only">Toggle sidebar</span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>

				<span class="icon-bar"></span>
			</button>

			<div class="navbar-header pull-left">
				<a href="<?php echo base_url(); ?>" class="navbar-brand">
					<small style="display: flex;align-items: center;gap: 4px;">
						<img src="/uploads/company_profile_org/<?= $companyInfo->Company_Logo_org ?>" style="border-radius:3px;" width="30" height="30" alt="">
						<?php echo $companyInfo->Company_Name; ?> <span style="color:#000;font-weight:700;letter-spacing:1px;font-size:16px;"></span>
					</small>
				</a>
			</div>

			<div class="navbar-buttons navbar-header pull-right" role="navigation">
				<ul class="nav ace-nav">
					<li class="light-blue dropdown-modal">
						<a data-toggle="dropdown" href="#" class="dropdown-toggle">
							<big>Hold List</big>
							<i class="ace-icon fa fa-caret-down"></i>
						</a>

						<ul style="min-width: 400px !important;" class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
							<li>
								<table class="table table-bordered">
									<tr>
										<th>Sl.</th>
										<th>Date</th>
										<th>Ref.</th>
										<th>Action</th>
									</tr>
									<?php
									$hold_sales = $this->db->query("SELECT * FROM tbl_hold_sale where Status = 'a' order by SaleMaster_InvoiceNo asc ")->result();
									foreach ($hold_sales as $key => $row) { ?>
										<tr>
											<td><?php echo $key + 1; ?></td>
											<td><?php echo $row->SaleMaster_SaleDate; ?></td>
											<td><?php echo $row->SaleMaster_InvoiceNo; ?></td>
											<td>
												<a class="btn-add" href="<?php echo base_url(); ?>hold_sale/<?php echo $row->SaleMaster_InvoiceNo; ?>">
													<i class="ace-icon fa fa-cart-plus"></i>
												</a>
											</td>
										</tr>
									<?php } ?>
								</table>
							</li>
						</ul>
					</li>

					<?php
					$userID =  $this->session->userdata('userId');
					$CheckSuperAdmin = $this->db
						->where('User_SlNo', $userID)
						// ->group_start()
						// ->where('UserType', 'm')
						// ->or_where('UserType', 'a')
						// ->group_end()
						->get('tbl_user')->row();
					if (isset($CheckSuperAdmin) && $userID == 1) :
					?>
						<li class="light-blue dropdown-modal">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<big>Branch Acess</big>
								<i class="ace-icon fa fa-caret-down"></i>
							</a>

							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">

								<?php
								$sql = $this->db->query("SELECT * FROM tbl_brunch where status = 'a' order by Brunch_name asc ");
								$row = $sql->result();
								foreach ($row as $row) { ?>
									<li>
										<a class="btn-add fancybox fancybox.ajax" href="<?php echo base_url(); ?>brachAccess/<?php echo $row->brunch_id; ?>">
											<i class="ace-icon fa fa-bank"></i>
											<?php echo $row->Brunch_name; ?>
										</a>
									</li>
								<?php } ?>
							</ul>
						</li>
					<?php endif; ?>

					<li class="clock_li">
						<a class="clock" style="background:transparent;">
							<span style="font-size:20px;"><i class="ace-icon fa fa-clock-o"></i></span> <span style="font-size:15px;"><?php date_default_timezone_set('Asia/Dhaka');
																																		echo date("l, d F Y"); ?>,&nbsp;<span id="timer" style="font-size:15px;"></span></span>
						</a>
					</li>

					<li class="light-blue dropdown-modal">
						<a data-toggle="dropdown" href="#" class="dropdown-toggle">
							<?php if ($this->session->userdata('user_image')) { ?>

								<img class="nav-user-photo" src="<?php echo base_url(); ?><?php echo $this->session->userdata('user_image'); ?>" alt="<?php echo $this->session->userdata('FullName'); ?>" />
							<?php } else { ?>

								<img class="nav-user-photo" src="<?php echo base_url(); ?>uploads/no_image.jpg ?>" alt="<?php echo $this->session->userdata('FullName'); ?>" />
							<?php } ?>
							<span class="user-info">
								<small>Welcome,</small>
								<?php echo $this->session->userdata('FullName'); ?>
							</span>

							<i class="ace-icon fa fa-caret-down"></i>
						</a>

						<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
							<li>
								<a href="<?php echo base_url(); ?>profile">
									<i class="ace-icon fa fa-user"></i>
									Profile
								</a>
							</li>

							<li class="divider"></li>

							<li>
								<a href="<?php echo base_url(); ?>Login/logout">
									<i class="ace-icon fa fa-power-off"></i>
									Logout
								</a>
							</li>
						</ul>
					</li>

				</ul>
			</div>
		</div><!-- /.navbar-container -->
	</div>

	<div class="main-container ace-save-state" id="main-container">
		<script type="text/javascript">
			try {
				ace.settings.loadState('main-container')
			} catch (e) {}
		</script>

		<div id="sidebar" class="sidebar responsive ace-save-state sidebar-fixed sidebar-scroll">
			<script type="text/javascript">
				try {
					ace.settings.loadState('sidebar')
				} catch (e) {}
			</script>

			<?php include('menu.php'); ?>

			<div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
				<i id="sidebar-toggle-icon" class="ace-icon fa fa-angle-double-left ace-save-state" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
			</div>
		</div>

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="#">Home</a>
						</li>
						<li class="active">Dashboard</li>
					</ul>

					<div class="nav-search" id="nav-search">
						<span style="font-weight: bold; color: #972366; font-size: 16px;">
							<?php echo $this->session->userdata('Brunch_name');  ?>
						</span>
					</div>
				</div>

				<div class="page-content">

					<div id="loader" hidden style="position: fixed; z-index: 1000; margin: auto; height: 100%; width: 100%; background:rgba(255, 255, 255, 0.72);;">
						<img src="<?php echo base_url(); ?>assets/loader.gif" style="top: 30%; left: 50%; opacity: 1; position: fixed;">
					</div>
					<?php echo $content; ?>
				</div><!-- /.page-content -->
			</div>
		</div><!-- /.main-content -->

		<div class="footer">
			<div class="footer-inner">
				<div class="footer-content">
					<div class="row">
						<div class="col-md-9" style="padding-right: 0;">
							<marquee scrollamount="2" onmouseover="this.stop();" onmouseout="this.start();" direction="left" height="30" style="padding-top: 3px;color: red;margin-bottom: -10px;font-size: 15px;"></marquee>
						</div>
						<div class="col-md-3" style="padding: 4px 0;background-color: #007ebb;color:white; margin-bottom: -1px;">
							<span style="font-size: 12px;">
								Developed by <span class="blue bolder"><a href="" target="_blank" style="color: white;text-decoration: underline;font-weight: normal;">BD Soft Technology</a></span>
							</span>
						</div>
					</div>

				</div>
			</div>
		</div>

		<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
			<i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
		</a>
	</div>
	<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>
	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo base_url(); ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery-ui.custom.min.js"></script>

	<!-- ace scripts -->
	<script src="<?php echo base_url(); ?>assets/js/ace-elements.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/ace.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>assets/fancyBox/js/jquery.fancybox.js?v=2.1.5"></script>
	<!-- inline scripts related to this page -->

	<script type="text/javascript">
		setInterval(function() {

			var currentTime = new Date();

			var currentHours = currentTime.getHours();

			var currentMinutes = currentTime.getMinutes();

			var currentSeconds = currentTime.getSeconds();

			currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;

			currentSeconds = (currentSeconds < 10 ? "0" : "") + currentSeconds;

			var timeOfDay = (currentHours < 12) ? "AM" : "PM";

			currentHours = (currentHours > 12) ? currentHours - 12 : currentHours;

			currentHours = (currentHours == 0) ? 12 : currentHours;

			var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;

			document.getElementById("timer").innerHTML = currentTimeString;

		}, 1000);
	</script>


	<script type="text/javascript">
		$(document).ready(function() {

			$('.fancybox').fancybox({

				padding: 0,

				openEffect: 'elastic',

				openSpeed: 150,

				closeEffect: 'elastic',

				closeSpeed: 150,

				maxWidth: "60%",

				autoSize: true,

				autoScale: true,

				fitToView: true,

				helpers: {

					title: {

						type: 'inside'

					},

					overlay: {

						css: {

							'background': 'rgba(0,0,0,0.3)'

						}

					}

				}

			});

			$('.fancyboxview').fancybox({

				padding: 0,

				openEffect: 'elastic',

				openSpeed: 150,



				closeEffect: 'elastic',

				closeSpeed: 150,

				maxWidth: "95%",

				autoSize: true,

				autoScale: true,

				fitToView: true,



				helpers: {

					title: {

						type: 'inside'

					},

					overlay: {

						css: {

							'background': 'rgba(0,0,0,0.3)'

						}

					}

				}

			});

		});
	</script>

</body>

</html>