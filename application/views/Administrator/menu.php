<?php
$CI = &get_instance();
$CI->load->model('Sale_model');
$sales_notify = $CI->Sale_model->uncheckSales($this->session->userdata('BRANCHid'), 1);




$userID =  $this->session->userdata('userId');
$CheckSuperAdmin = $this->db->where('UserType', 'm')->where('User_SlNo', $userID)->get('tbl_user')->row();

$CheckAdmin = $this->db->where('UserType', 'a')->where('User_SlNo', $userID)->get('tbl_user')->row();

$userAccessQuery = $this->db->where('user_id', $userID)->get('tbl_user_access');
$access = [];
if ($userAccessQuery->num_rows() != 0) {
	$userAccess = $userAccessQuery->row();
	$access = json_decode($userAccess->access);
}

$module = $this->session->userdata('module');
if ($module == 'dashboard' or $module == '') {
?>
	<ul class="nav nav-list">
		<li class="active">
			<!-- module/dashboard -->
			<a href="<?php echo base_url(); ?>">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>module/SalesModule">
				<i class="menu-icon fa fa-shopping-cart" style="font-size:23px;"></i>
				<span class="menu-text"> Manage Sales </span>
				<?= $sales_notify;  ?>
			</a>
			<b class="arrow"></b>
		</li>

		<?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>module/PurchaseModule">
					<i class="menu-icon fa fa-cart-plus" style="font-size:23px;"></i>
					<span class="menu-text"> Manage Purchase </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<li class="">
			<a href="<?php echo base_url(); ?>module/InternationalModule">
				<i class="menu-icon fa fa-credit-card" style="font-size: 16px;"></i>
				<span class="menu-text"> International Bill </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>module/AccountsModule">
				<i class="menu-icon fa fa fa-money" style="font-size: 16px;"></i>
				<span class="menu-text"> Manage Accounts </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>module/VehicleManagement">
				<i class="menu-icon fa fa fa-truck" style="font-size: 16px;"></i>
				<span class="menu-text"> Vehicle Management </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<!-- module/HRPayroll -->
			<a href="<?php echo base_url(); ?>module/HRPayroll">
				<i class="menu-icon fa fa-users"></i>
				<span class="menu-text"> Manage HR </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<!-- module/ReportsModule -->
			<a href="<?php echo base_url(); ?>module/ReportsModule">
				<i class="menu-icon fa fa-calendar-check-o"></i>
				<span class="menu-text"> Manage Reports </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>module/Administration">
				<i class="menu-icon fa fa-university"></i>
				<span class="menu-text"> Administration </span>
			</a>
			<b class="arrow"></b>
		</li>

		<li class="">
			<a href="<?php echo base_url(); ?>graph">
				<i class="menu-icon fa fa-bar-chart"></i>
				<span class="menu-text"> Business View </span>
			</a>
			<b class="arrow"></b>
		</li>
	</ul>
<?php } elseif ($module == 'Administration') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>
			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/Administration" style="background:gray !important;" class="module_title">
				<span>Administration</span>
			</a>
		</li>

		<?php if (array_search("sms", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>sms">
					<i class="menu-icon fa fa-mobile"></i>
					<span class="menu-text"> Send SMS </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<li class="">
			<a href="<?php echo base_url(); ?>slider">
				<i class="menu-icon fa fa-image"></i>
				<span class="menu-text"> Slider </span>
			</a>
			<b class="arrow"></b>
		</li>
		<li class="">
			<a href="<?php echo base_url(); ?>feedback">
				<i class="menu-icon fa fa-comments"></i>
				<span class="menu-text"> Feedback </span>
			</a>
			<b class="arrow"></b>
		</li>

		<?php if (
			array_search("product", $access) > -1
			|| array_search("productlist", $access) > -1
			|| array_search("product_ledger", $access) > -1
			|| isset($CheckSuperAdmin)
			|| isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-product-hunt"></i>
					<span class="menu-text"> Product Info </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("product", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>product">
								<i class="menu-icon fa fa-caret-right"></i>
								Product Entry
							</a>

							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("discount_entry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>discount_entry">
								<i class="menu-icon fa fa-caret-right"></i>
								Discount Entry
							</a>

							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("productlist", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>productlist" target="_blank">
								<i class="menu-icon fa fa-caret-right"></i>
								Product List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("product_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>product_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Product Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>
		<?php if (
			array_search("damageEntry", $access) > -1
			|| array_search("damageList", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Damage Info </span>
					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("damageEntry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>damageEntry">
								<i class="menu-icon fa fa-caret-right"></i>
								Damage Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("damageList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>damageList">
								<i class="menu-icon fa fa-caret-right"></i>
								Damage List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("product_transfer", $access) > -1
			|| array_search("transfer_list", $access) > -1
			|| array_search("received_list", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-exchange"></i>
					<span class="menu-text"> Product Transfer </span>
					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("product_transfer", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>product_transfer">
								<i class="menu-icon fa fa-caret-right"></i>
								Product Transfer
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
					<?php if (array_search("transfer_list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>transfer_list">
								<i class="menu-icon fa fa-caret-right"></i>
								Transfer List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("received_list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>received_list">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Received List</span>
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("customer", $access) > -1
			|| array_search("supplier", $access) > -1
			|| array_search("brunch", $access) > -1
			|| array_search("category", $access) > -1
			|| array_search("subcategory", $access) > -1
			|| array_search("unit", $access) > -1
			|| array_search("area", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-cog"></i>
					<span class="menu-text"> Settings </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("customer", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>customer">
								<i class="menu-icon fa fa-caret-right"></i>
								Customer Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("supplier", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>supplier">
								<i class="menu-icon fa fa-caret-right"></i>
								Supplier Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>


					<?php if ((array_search("category", $access) > -1) || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>category">
								<i class="menu-icon fa fa-caret-right"></i>
								Category entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
					<?php if ((array_search("subcategory", $access) > -1) || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>subcategory">
								<i class="menu-icon fa fa-caret-right"></i>
								sub Category entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("unit", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>unit">
								<i class="menu-icon fa fa-caret-right"></i>
								Unit Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("area", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>area">
								<i class="menu-icon fa fa-caret-right"></i>
								<span class="menu-text"> Add Area </span>
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>companyProfile">
					<i class="menu-icon fa fa-bank"></i>
					<span class="menu-text"> Company Profile </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>user">
					<i class="menu-icon fa fa-user-plus"></i>
					<span class="menu-text"> Create User </span>
				</a>
			</li>
		<?php endif; ?>

		<?php if (isset($CheckSuperAdmin) && $this->session->userdata('BRANCHid') == 1) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>user_activity">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> User Activity</span>
				</a>
			</li>
		<?php endif; ?>

		<?php if (array_search("database_backup", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>database_backup">
					<i class="menu-icon fa fa-database"></i>
					<span class="menu-text"> Database Backup </span>
				</a>
			</li>
		<?php endif; ?>

	</ul><!-- /.nav-list -->

<?php } elseif ($module == 'SalesModule') { ?>
	<ul class="nav nav-list">

		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/SalesModule" style="background:gray !important;" class="module_title">
				<span> Manage Sales </span>
			</a>
		</li>

		<?php if (array_search("sales/product", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>sales/product">
					<i class="menu-icon fa fa-shopping-bag"></i>
					<span class="menu-text"> Sales Add </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>


		<!-- <?php if (array_search("sales/service", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>sales/service">
					<i class="menu-icon fa fa-shopping-bag"></i>
					<span class="menu-text"> Service Add </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?> -->

		<?php if (array_search("salesrecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salesrecord?type=online">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text">Online Sales Record </span>
					<?= $sales_notify;  ?>
				</a>
				<b class="arrow"></b>
			</li>
			<li class="">
				<a href="<?php echo base_url(); ?>salesrecord">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Sales Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>



		<li class="">
			<a href="<?php echo base_url(); ?>combo_package">
				<i class="menu-icon fa fa-list"></i>
				<span class="menu-text"> Combo Record </span>
			</a>
			<b class="arrow"></b>
		</li>
		<li class="">
			<a href="<?php echo base_url(); ?>add_combo_package">
				<i class="menu-icon fa fa-list"></i>
				<span class="menu-text">Combo Add </span>
			</a>
			<b class="arrow"></b>
		</li>

		<?php if (array_search("exchange", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>exchange">
					<i class="menu-icon fa fa-exchange"></i>
					<span class="menu-text"> Sales Exchange </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("exchange_record", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>exchange_record">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Exchange Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("salesinvoice", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salesinvoice">
					<i class="menu-icon fa fa-file-text"></i>
					<span class="menu-text"> Sales Invoice </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("salesReturn", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salesReturn">
					<i class="menu-icon fa fa-rotate-left"></i>
					<span class="menu-text"> Sale Return </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("returnList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>returnList">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Sale Return Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<?php if (array_search("special_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>special_report">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Daily Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<!-- <?php if (array_search("quotation", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>quotation">
					<i class="menu-icon fa fa-shopping-bag"></i>
					<span class="menu-text"> Quotation Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("quotation_record", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>quotation_record">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Quotation Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("quotation_invoice_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>quotation_invoice_report">
					<i class="menu-icon fa fa-file-text"></i>
					<span class="menu-text"> Quotation Invoice </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?> -->

		<?php if (array_search("currentStock", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>currentStock">
					<i class="menu-icon fa fa-shopping-basket"></i>
					<span class="menu-text"> Stock Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

	</ul>

<?php } elseif ($module == 'PurchaseModule') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/PurchaseModule" style="background:gray !important;" class="module_title">
				<span>Manage Purchase</span>
			</a>
		</li>

		<?php if (array_search("purchase", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchase">
					<i class="menu-icon fa fa-cart-plus"></i>
					<span class="menu-text"> Purchase Add </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchaseRecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchaseRecord">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text">Purchase Record</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchaseInvoice", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchaseInvoice">
					<i class="menu-icon fa fa-file-text"></i>
					<span class="menu-text">Purchase Invoice</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("purchaseReturns", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>purchaseReturns">
					<i class="menu-icon fa fa-rotate-right"></i>
					<span class="menu-text"> Purchase Return </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("returnsList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>returnsList">
					<i class="menu-icon fa fa-list"></i>
					Pur. Return Record
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("AssetsEntry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>AssetsEntry">
					<i class="menu-icon fa fa-shopping-cart"></i>
					<span class="menu-text"> Assets Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("assets_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>assets_report">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Assets Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
	</ul>

<?php } elseif ($module == 'AccountsModule') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/AccountsModule" style="background:gray !important;" class="module_title">
				<span> Manage Accounts </span>
			</a>
		</li>

		<?php if (array_search("cashTransaction", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>cashTransaction">
					<i class="menu-icon fa fa-medkit"></i>
					<span class="menu-text"> Cash Transaction </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("cashTransaction", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>cash_transfer">
					<i class="menu-icon fa fa-exchange"></i>
					<span class="menu-text"> Cash Transfer </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("bank_transactions", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li>
				<a href="<?php echo base_url(); ?>bank_transactions">
					<i class="menu-icon fa fa-dollar"></i>
					<span class="menu-text"> Bank Transactions </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("customerPaymentPage", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>customerPaymentPage">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text"> Payment Received</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("supplierPayment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>supplierPayment">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text"> Supplier Payment </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("cash_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>cash_view">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text">Cash View</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("loan_transactions", $access) > -1
			|| array_search("loan_view", $access) > -1
			|| array_search("loan_transaction_report", $access) > -1
			|| array_search("loan_ledger", $access) > -1
			|| array_search("loan_accounts", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>

			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Loan </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("loan_transactions", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>loan_transactions">
								<i class="menu-icon fa fa-caret-right"></i>
								Loan Transection
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("loan_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>loan_view">
								<i class="menu-icon fa fa-caret-right"></i>
								Loan View
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("loan_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>loan_transaction_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Loan Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("loan_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>loan_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Loan Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("loan_accounts", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>loan_accounts">
								<i class="menu-icon fa fa-caret-right"></i>
								Loan Account
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("investment_transactions", $access) > -1
			|| array_search("investment_transaction_report", $access) > -1
			|| array_search("investment_view", $access) > -1
			|| array_search("investment_ledger", $access) > -1
			|| array_search("investment_account", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>

			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Investment </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("investment_transactions", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_transactions">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Transection
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_view">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment View
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_transaction_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("investment_account", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>investment_account">
								<i class="menu-icon fa fa-caret-right"></i>
								Investment Account
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("account", $access) > -1
			|| array_search("bank_accounts", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>

			<li>
				<a href="" class="dropdown-toggle">
					<i class="menu-icon fa fa-bank"></i>
					<span class="menu-text"> Account Head </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("account", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li>
							<a href="<?php echo base_url(); ?>account">
								<i class="menu-icon fa fa-caret-right"></i>
								Transaction Accounts
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
					<?php if (array_search("bank_accounts", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li>
							<a href="<?php echo base_url(); ?>bank_accounts">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Accounts
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("check/entry", $access) > -1
			|| array_search("check/list", $access) > -1
			|| array_search("check/reminder/list", $access) > -1
			|| array_search("check/pending/list", $access) > -1
			|| array_search("check/dis/list", $access) > -1
			|| array_search("check/paid/list", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>

			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Cheque </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("check/entry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>check/entry">
								<i class="menu-icon fa fa-caret-right"></i>
								New Cheque Entry
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("check/list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>check/list">
								<i class="menu-icon fa fa-caret-right"></i>
								Cheque list
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("check/reminder/list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>check/reminder/list">
								<i class="menu-icon fa fa-caret-right"></i>
								Reminder Cheque list
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("check/pending/list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>check/pending/list">
								<i class="menu-icon fa fa-caret-right"></i>
								Pending Cheque list
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("check/dis/list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>check/dis/list">
								<i class="menu-icon fa fa-caret-right"></i>
								Dishonoured Cheque list
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("check/paid/list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>check/paid/list">
								<i class="menu-icon fa fa-caret-right"></i>
								Paid Cheque list
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>
				</ul>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("TransactionReport", $access) > -1
			|| array_search("bank_transaction_report", $access) > -1
			|| array_search("cash_ledger", $access) > -1
			|| array_search("bank_ledger", $access) > -1
			|| array_search("cashStatment", $access) > -1
			|| array_search("BalanceSheet", $access) > -1
			|| array_search("balance_sheet", $access) > -1
			|| array_search("day_book", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Reports </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("TransactionReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>TransactionReport">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_transaction_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("cash_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cash_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("cashStatment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cashStatment">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Statement
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("balance_sheet", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>balance_sheet">
								<i class="menu-icon fa fa-caret-right"></i>
								Balance Sheet
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("BalanceSheet", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>BalanceSheet">
								<i class="menu-icon fa fa-caret-right"></i>
								Balance In Out
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("day_book", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>day_book">
								<i class="menu-icon fa fa-caret-right"></i>
								Day Book
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>


	</ul><!-- /.nav-list -->
<?php } elseif ($module == 'HRPayroll') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/HRPayroll" style="background:gray !important;" class="module_title">
				<span>HR & Payroll</span>
			</a>
		</li>

		<?php if (array_search("salary_payment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>salary_payment">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text"> Salary Payment </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("employee", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>employee">
					<i class="menu-icon fa fa-users"></i>
					<span class="menu-text"> Add Employee </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("designation", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>designation">
					<i class="menu-icon fa fa-binoculars"></i>
					<span class="menu-text"> Add Designation </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("depertment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>depertment">
					<i class="menu-icon fa fa-plus-square"></i>
					<span class="menu-text"> Add Department </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("month", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>month">
					<i class="menu-icon fa fa-calendar"></i>
					<span class="menu-text"> Add Month </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (
			array_search("emplists/all", $access) > -1
			|| array_search("emplists/active", $access) > -1
			|| array_search("emplists/deactive", $access) > -1
			|| array_search("salary_payment_report", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Report </span>
					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("emplists/all", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>emplists/all">
								<i class="menu-icon fa fa-caret-right"></i>
								All Employee List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("emplists/active", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>emplists/active">
								<i class="menu-icon fa fa-caret-right"></i>
								Active Employee List
							</a>
							<b class="arrow"></b>
						</li>
						<?php endif; ?><?php if (array_search("emplists/deactive", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>emplists/deactive">
								<i class="menu-icon fa fa-caret-right"></i>
								Deactive Employee List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("salary_payment_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>salary_payment_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Salary Payment Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>

	</ul><!-- /.nav-list -->
<?php } elseif ($module == 'ReportsModule') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/ReportsModule" style="background:gray !important;" class="module_title">
				<span>Reports Manage</span>
			</a>
		</li>

		<?php if (array_search("profitLoss", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>profitLoss">
					<i class="menu-icon fa fa-medkit"></i>
					<span class="menu-text"> Profit & Loss Report </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("cash_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>cash_view">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text">Cash View</span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>


		<?php if (array_search("currentStock", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>currentStock">
					<i class="menu-icon fa fa-th-list"></i>
					<span class="menu-text"> Stock </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>


		<?php if (
			array_search("TransactionReport", $access) > -1
			|| array_search("bank_transaction_report", $access) > -1
			|| array_search("cash_ledger", $access) > -1
			|| array_search("bank_ledger", $access) > -1
			|| array_search("cashStatment", $access) > -1
			|| array_search("BalanceSheet", $access) > -1
			|| array_search("day_book", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Accounts Report </span>

					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">
					<?php if (array_search("TransactionReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>TransactionReport">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_transaction_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Transaction Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("cash_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cash_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("bank_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>bank_ledger">
								<i class="menu-icon fa fa-caret-right"></i>
								Bank Ledger
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("cashStatment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>cashStatment">
								<i class="menu-icon fa fa-caret-right"></i>
								Cash Statement
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("BalanceSheet", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>BalanceSheet">
								<i class="menu-icon fa fa-caret-right"></i>
								Balance In Out
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("day_book", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>day_book">
								<i class="menu-icon fa fa-caret-right"></i>
								Day Book
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>


		<?php if (
			array_search("emplists/all", $access) > -1
			|| array_search("emplists/active", $access) > -1
			|| array_search("emplists/deactive", $access) > -1
			|| array_search("salary_payment_report", $access) > -1
			|| isset($CheckSuperAdmin) || isset($CheckAdmin)
		) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>" class="dropdown-toggle">
					<i class="menu-icon fa fa-file"></i>
					<span class="menu-text"> Employee Report </span>
					<b class="arrow fa fa-angle-down"></b>
				</a>

				<b class="arrow"></b>

				<ul class="submenu">

					<?php if (array_search("emplists/all", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>emplists/all">
								<i class="menu-icon fa fa-caret-right"></i>
								All Employee List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("emplists/active", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>emplists/active">
								<i class="menu-icon fa fa-caret-right"></i>
								Active Employee List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("emplists/deactive", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>emplists/deactive">
								<i class="menu-icon fa fa-caret-right"></i>
								Deactive Employee List
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

					<?php if (array_search("salary_payment_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
						<li class="">
							<a href="<?php echo base_url(); ?>salary_payment_report">
								<i class="menu-icon fa fa-caret-right"></i>
								Salary Payment Report
							</a>
							<b class="arrow"></b>
						</li>
					<?php endif; ?>

				</ul>
			</li>
		<?php endif; ?>
	</ul><!-- /.nav-list -->

<?php } elseif ($module == 'InternationalModule') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/InternationalModule" style="background:gray !important;" class="module_title">
				<span>International Bill</span>
			</a>
		</li>

		<?php if (array_search("international_purchase", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>international_purchase">
					<i class="menu-icon fa fa-shopping-cart"></i>
					<span class="menu-text"> Purchase Add </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<?php if (array_search("international_purchaseRecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>international_purchaseRecord">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Purchase Record </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<?php if (array_search("international_supplierPayment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>international_supplierPayment">
					<i class="menu-icon fa fa-money"></i>
					<span class="menu-text"> Supplier Payment </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<?php if (array_search("international_supplier_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>international_supplier_ledger">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Supplier Ledger </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<?php if (array_search("international_supplier_payment_history", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>international_supplier_payment_history">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Payment History </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

		<?php if (array_search("international_supplier", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>international_supplier">
					<i class="menu-icon fa fa-user"></i>
					<span class="menu-text"> Supplier Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>

	</ul><!-- /.nav-list -->

<?php } elseif ($module == 'VehicleManagement') { ?>
	<ul class="nav nav-list">
		<li class="active">
			<a href="<?php echo base_url(); ?>module/dashboard">
				<i class="menu-icon fa fa-th"></i>
				<span class="menu-text"> Dashboard </span>
			</a>

			<b class="arrow"></b>
		</li>
		<li>
			<a href="<?php echo base_url(); ?>module/VehicleManagement" style="background:gray !important;" class="module_title">
				<span>Vehicle Management</span>
			</a>
		</li>

		<?php if (array_search("vehicle", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>vehicle">
					<i class="menu-icon fa fa-truck"></i>
					<span class="menu-text"> Vehicle Entry </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
		<?php if (array_search("vehicleList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
			<li class="">
				<a href="<?php echo base_url(); ?>vehicleList">
					<i class="menu-icon fa fa-list"></i>
					<span class="menu-text"> Vehicle List </span>
				</a>
				<b class="arrow"></b>
			</li>
		<?php endif; ?>
	</ul>
<?php } ?>