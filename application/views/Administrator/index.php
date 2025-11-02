<?php
$companyInfo = $this->db->query("select * from tbl_company c order by c.Company_SlNo desc limit 1")->row();

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta charset="utf-8" />
	<title><?php echo $companyInfo->Company_Name; ?> || <?php echo $title; ?></title>

	<meta name="description" content="Static &amp; Dynamic Tables" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- bootstrap & fontawesome -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/4.5.0/css/font-awesome.min.css" />

	<!-- page specific plugin styles -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-duallistbox.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-multiselect.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/select2.min.css" />

	<!-------------------  profile css start   --------------------->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery.gritter.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-editable.min.css" />
	<!-------------------  profile css end   --------------------->

	<!-- page specific plugin styles -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery-ui.custom.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/chosen.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datepicker3.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-timepicker.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/daterangepicker.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-colorpicker.min.css" />
	<!-- text fonts -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fonts.googleapis.com.css" />

	<!-- ace styles -->
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.min.css" class="ace-main-stylesheet" id="main-ace-style" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/responsive.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-rtl.min.css" />
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/fancyBox/css/jquery.fancybox.css?v=2.1.5" media="screen" />
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/css/prints.css" rel="stylesheet" />

	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-skins.min.css" />

	<!-- ace settings handler -->
	<script src="<?php echo base_url(); ?>assets/js/ace-extra.min.js"></script>

	<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>

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
						<?php echo $companyInfo->Company_Name; ?> <span style="color:#000;font-weight:700;letter-spacing:1px;font-size:16px;"> </span>
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
		</div>
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

						<li>
							<a href="#"><?php echo $title; ?></a>
						</li>

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
				</div>
				<div class="row" style="display:none;">
					<table id="dynamic-table" class="table table-striped table-bordered table-hover">
					</table>
				</div>
			</div>
		</div>

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
	</div><!-- /.main-container -->

	<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>

	<script type="text/javascript">
		if ('ontouchstart' in document.documentElement) document.write("<script src='<?php echo base_url(); ?>assets/js/jquery.mobile.custom.min.js'>" + "<" + "/script>");
	</script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>

	<!-- page specific plugin scripts -->
	<script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.bootstrap.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/buttons.flash.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/buttons.html5.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/buttons.print.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/buttons.colVis.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/dataTables.select.min.js"></script>
	<!-------------------  profile script end   --------------------->
	<script src="<?php echo base_url(); ?>assets/js/jquery-ui.custom.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/chosen.jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/daterangepicker.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap-datetimepicker.min.js"></script>

	<script src="<?php echo base_url(); ?>assets/js/jquery.bootstrap-duallistbox.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/jquery.raty.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap-multiselect.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/select2.min.js"></script>

	<script type="text/javascript" src="<?php echo base_url() ?>assets/fancyBox/js/jquery.fancybox.js?v=2.1.5"></script>
	<!-- ace scripts -->
	<script src="<?php echo base_url(); ?>assets/js/ace-elements.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/ace.min.js"></script>

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
		jQuery(function($) {
			//initiate dataTables plugin
			var myTable =
				$('#dynamic-table')
				.DataTable({
					bAutoWidth: false,
					"aoColumnDefs": [{
						"aTargets": ['_all'],
						"bSortable": true
					}],

					"aaSorting": [],
					"lengthMenu": [
						[10, 25, 50, 100, -1],
						[10, 25, 50, 100, "All"]
					],

					select: {
						style: 'multi'
					}
				});

			var myTable2 =
				$('#dynamic-table2')
				.DataTable({
					bAutoWidth: false,
					"aoColumnDefs": [{
						"aTargets": ['_all'],
						"bSortable": true
					}],

					"aaSorting": [],
					"lengthMenu": [
						[10, 25, 50, 100, -1],
						[10, 25, 50, 100, "All"]
					],

					select: {
						style: 'multi'
					}
				});

			$.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-overlap btn-group btn-overlap';

			new $.fn.dataTable.Buttons(myTable, {
				buttons: [{
						"extend": "colvis",
						"text": "<i class='fa fa-search bigger-110 blue'></i> <span class='hidden'>Show/hide columns</span>",
						"className": "btn btn-white btn-primary btn-bold",
						columns: ':not(:first):not(:last)'
					},
					{
						"extend": "copy",
						"text": "<i class='fa fa-copy bigger-110 pink'></i> <span class='hidden'>Copy to clipboard</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "csv",
						"text": "<i class='fa fa-database bigger-110 orange'></i> <span class='hidden'>Export to CSV</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "excel",
						"text": "<i class='fa fa-file-excel-o bigger-110 green'></i> <span class='hidden'>Export to Excel</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "pdf",
						"text": "<i class='fa fa-file-pdf-o bigger-110 red'></i> <span class='hidden'>Export to PDF</span>",
						"className": "btn btn-white btn-primary btn-bold"
					},
					{
						"extend": "print",
						"text": "<i class='fa fa-print bigger-110 grey'></i> <span class='hidden'>Print</span>",
						"className": "btn btn-white btn-primary btn-bold",
						autoPrint: false,
						message: 'This print was produced using the Print button for DataTables'
					}
				]
			});
			myTable.buttons().container().appendTo($('.tableTools-container'));

			//style the message box
			var defaultCopyAction = myTable.button(1).action();
			myTable.button(1).action(function(e, dt, button, config) {
				defaultCopyAction(e, dt, button, config);
				$('.dt-button-info').addClass('gritter-item-wrapper gritter-info gritter-center white');
			});


			var defaultColvisAction = myTable.button(0).action();
			myTable.button(0).action(function(e, dt, button, config) {

				defaultColvisAction(e, dt, button, config);


				if ($('.dt-button-collection > .dropdown-menu').length == 0) {
					$('.dt-button-collection')
						.wrapInner('<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />')
						.find('a').attr('href', '#').wrap("<li />")
				}
				$('.dt-button-collection').appendTo('.tableTools-container .dt-buttons')
			});

			////

			setTimeout(function() {
				$($('.tableTools-container')).find('a.dt-button').each(function() {
					var div = $(this).find(' > div').first();
					if (div.length == 1) div.tooltip({
						container: 'body',
						title: div.parent().text()
					});
					else $(this).tooltip({
						container: 'body',
						title: $(this).text()
					});
				});
			}, 500);





			myTable.on('select', function(e, dt, type, index) {
				if (type === 'row') {
					$(myTable.row(index).node()).find('input:checkbox').prop('checked', true);
				}
			});
			myTable.on('deselect', function(e, dt, type, index) {
				if (type === 'row') {
					$(myTable.row(index).node()).find('input:checkbox').prop('checked', false);
				}
			});




			/////////////////////////////////
			//table checkboxes
			$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

			//select/deselect all rows according to table header checkbox
			$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function() {
				var th_checked = this.checked; //checkbox inside "TH" table header

				$('#dynamic-table').find('tbody > tr').each(function() {
					var row = this;
					if (th_checked) myTable.row(row).select();
					else myTable.row(row).deselect();
				});
			});

			//select/deselect a row when the checkbox is checked/unchecked
			$('#dynamic-table').on('click', 'td input[type=checkbox]', function() {
				var row = $(this).closest('tr').get(0);
				if (this.checked) myTable.row(row).deselect();
				else myTable.row(row).select();
			});



			$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
				e.stopImmediatePropagation();
				e.stopPropagation();
				e.preventDefault();
			});



			//And for the first simple table, which doesn't have TableTools or dataTables
			//select/deselect all rows according to table header checkbox
			var active_class = 'active';
			$('#simple-table > thead > tr > th input[type=checkbox]').eq(0).on('click', function() {
				var th_checked = this.checked; //checkbox inside "TH" table header

				$(this).closest('table').find('tbody > tr').each(function() {
					var row = this;
					if (th_checked) $(row).addClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', true);
					else $(row).removeClass(active_class).find('input[type=checkbox]').eq(0).prop('checked', false);
				});
			});

			//select/deselect a row when the checkbox is checked/unchecked
			$('#simple-table').on('click', 'td input[type=checkbox]', function() {
				var $row = $(this).closest('tr');
				if ($row.is('.detail-row ')) return;
				if (this.checked) $row.addClass(active_class);
				else $row.removeClass(active_class);
			});



			/********************************/
			//add tooltip for small view action buttons in dropdown menu
			$('[data-rel="tooltip"]').tooltip({
				placement: tooltip_placement
			});

			//tooltip placement on right or left
			function tooltip_placement(context, source) {
				var $source = $(source);
				var $parent = $source.closest('table')
				var off1 = $parent.offset();
				var w1 = $parent.width();

				var off2 = $source.offset();
				//var w2 = $source.width();

				if (parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2)) return 'right';
				return 'left';
			}




			/***************/
			$('.show-details-btn').on('click', function(e) {
				e.preventDefault();
				$(this).closest('tr').next().toggleClass('open');
				$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
			});
			/***************/


		})
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