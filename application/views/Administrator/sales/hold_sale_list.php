<style>
	.v-select {
		margin-top: -2.5px;
		float: right;
		min-width: 180px;
		margin-left: 5px;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}

	#searchForm select {
		padding: 0;
		border-radius: 4px;
	}

	#searchForm .form-group {
		margin-right: 5px;
	}

	#searchForm * {
		font-size: 13px;
	}

	.record-table {
		width: 100%;
		border-collapse: collapse;
	}

	.record-table thead {
		background-color: #0097df;
		color: white;
	}

	.record-table th,
	.record-table td {
		padding: 3px;
		border: 1px solid #454545;
	}

	.record-table th {
		text-align: center;
	}
</style>
<div id="salesRecord">

	<div class="row" style="margin-top:15px;display:none;" v-bind:style="{display: sales.length > 0 ? '' : 'none'}">
		<div class="col-md-6">
			<a href="" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a>
		</div>
		<div class="col-md-5 text-right">
			<strong>Total Hold Sale: </strong> <span v-text="sales.length"></span>
		</div>
		<div class="col-md-1 text-right">
			<a href="" v-on:click.prevent="excelExport">
				<i class="fa fa-file-excel-o"></i> Excel
			</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent">
				<table
					class="record-table"
					v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'without_details'"
					style="display:none"
					v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'without_details' ? '' : 'none'}">
					<thead>
						<tr>
							<th>Invoice No.</th>
							<th>Date</th>
							<th>Customer Name</th>
							<th>Employee Name</th>
							<th>Saved By</th>
							<th>SubTotal</th>
							<th>Total</th>
							<th>Note</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="sale in sales" :style="{background: (sale.Status == 'p' && sale.web_order == '1') ? 'rgb(252 179 179)' : ''}">
							<td>{{ sale.SaleMaster_InvoiceNo }}</td>
							<td>{{ sale.SaleMaster_SaleDate }}</td>
							<td>{{ sale.Customer_Name }}</td>
							<td>{{ sale.Employee_Name }}</td>
							<td>{{ sale.AddBy }}</td>
							<td style="text-align:right;">{{ sale.SaleMaster_SubTotalAmount }}</td>
							<td style="text-align:right;">{{ sale.SaleMaster_TotalSaleAmount }}</td>
							<td style="text-align:left;">{{ sale.SaleMaster_Description }}</td>
							<td style="text-align:center;">
								<a href="" title="Sale Invoice" v-bind:href="`/hold_sale/${sale.SaleMaster_InvoiceNo}`" target="_blank"><i class="fa fa-cart-plus"></i></a>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<a v-if="sale.Status != 'c' && sale.Status != 'd'" href="" title="Delete Sale" @click.prevent="deleteSale(sale.SaleMaster_SlNo)"><i class="fa fa-trash"></i></a>
								<?php } ?>
							</td>
						</tr>
						<tr style="font-weight:bold;">
							<td colspan="5" style="text-align:right;">Total</td>
							<td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_SubTotalAmount)}, 0).toFixed(2) }}</td>
							<td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.SaleMaster_TotalSaleAmount)}, 0).toFixed(2) }}</td>
							<td></td>
							<td></td>
						</tr>
						<tr style="font-weight:bold;" v-if="sales.length > 0">
							<td colspan="5"></td>
							<td style="text-align:right;">SubTotal</td>
							<td style="text-align:right;">Total</td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="row" style="margin-top:15px;display:none;text-align:center;font-weight: 700;" v-bind:style="{display: sales.length == 0 ? '' : 'none'}">
		<div class="col-xs-12">
			Not Found Data
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#salesRecord',
		data() {
			return {
				searchType: '',
				recordType: 'without_details',
				dateFrom: moment().format('YYYY-MM-DD'),
				dateTo: moment().format('YYYY-MM-DD'),
				customers: [],
				selectedCustomer: null,
				employees: [],
				selectedEmployee: null,
				products: [],
				selectedProduct: null,
				users: [],
				selectedUser: null,
				categories: [],
				selectedCategory: null,
				sales: [],
				searchTypesForRecord: ['', 'user', 'customer', 'employee'],
				searchTypesForDetails: ['quantity', 'category'],
				status: 'a'
			}
		},

		created() {
			<?php if (isset($_GET['type']) && $_GET['type'] == 'online') { ?>
				this.dateFrom = '';
				this.dateTo = '';
				this.status = 'p';

			<?php } ?>
			this.getSearchResult();
		},
		methods: {
			checkReturnAndEdit(sale) {
				axios.get('/check_sale_return/' + sale.SaleMaster_InvoiceNo).then(res => {
					if (res.data.found) {
						alert('Unable to edit. Sale return found!');
					} else {
						if (sale.is_service == 'true') {
							location.replace('/sales/service/' + sale.SaleMaster_SlNo);
						} else {
							location.replace('/sales/product/' + sale.SaleMaster_SlNo);
						}
					}
				})
			},
			statusData(status) {
				if (status === 'p') {
					return {
						class: 'label-warning',
						text: 'Pending'
					};
				} else if (status === 'a') {
					return {
						class: 'label-success',
						text: 'Confirm'
					};
				} else if (status === 'c') {
					return {
						class: 'label-danger',
						text: 'Cancel'
					};
				} else if (status === 'r') {
					return {
						class: 'label-danger',
						text: 'Return'
					};
				} else if (status === 'd') {
					return {
						class: 'label-danger',
						text: 'Delete'
					};
				}
			},
			onChangeSearchType() {
				this.sales = [];
				if (this.searchType == 'quantity') {
					this.getProducts();
				} else if (this.searchType == 'user') {
					this.getUsers();
				} else if (this.searchType == 'category') {
					this.getCategories();
				} else if (this.searchType == 'customer') {
					this.getCustomers();
				} else if (this.searchType == 'employee') {
					this.getEmployees();
				}
			},
			getProducts() {
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})
			},
			getUsers() {
				axios.get('/get_users').then(res => {
					this.users = res.data;
				})
			},
			getCategories() {
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			getSearchResult() {
				if (this.searchType != 'customer') {
					this.selectedCustomer = null;
				}

				if (this.searchType != 'employee') {
					this.selectedEmployee = null;
				}

				if (this.searchType != 'quantity') {
					this.selectedProduct = null;
				}

				if (this.searchType != 'category') {
					this.selectedCategory = null;
				}

				if (this.searchTypesForRecord.includes(this.searchType)) {
					this.getSalesRecord();
				} else {
					this.getSaleDetails();
				}
			},
			getSalesRecord() {
				let filter = {
					userFullName: this.selectedUser == null || this.selectedUser.FullName == '' ? '' : this.selectedUser.FullName,
					customerId: this.selectedCustomer == null || this.selectedCustomer.Customer_SlNo == '' ? '' : this.selectedCustomer.Customer_SlNo,
					employeeId: this.selectedEmployee == null || this.selectedEmployee.Employee_SlNo == '' ? '' : this.selectedEmployee.Employee_SlNo,
					dateFrom: this.dateFrom,
					dateTo: this.dateTo,
					status: this.status

				}

				let url = '/get_hold_sale';
				if (this.recordType == 'with_details') {
					url = '/get_hold_sale';
				}

				axios.post(url, filter)
					.then(res => {
						if (this.recordType == 'with_details') {
							this.sales = res.data;
						} else {
							this.sales = res.data.sales;
						}
					})
			},
			deleteSale(saleId) {
				let deleteConf = confirm('Are you sure?');
				if (deleteConf == false) {
					return;
				}
				axios.post('/delete_hold_sale', {
						holdSaleId: saleId
					})
					.then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getSalesRecord();
						}
					})
			},
			async print() {
				let dateText = '';
				if (this.dateFrom != '' && this.dateTo != '') {
					dateText = `Statement from <strong>${this.dateFrom}</strong> to <strong>${this.dateTo}</strong>`;
				}

				let userText = '';
				if (this.selectedUser != null && this.selectedUser.FullName != '' && this.searchType == 'user') {
					userText = `<strong>Sold by: </strong> ${this.selectedUser.FullName}`;
				}

				let customerText = '';
				if (this.selectedCustomer != null && this.selectedCustomer.Customer_SlNo != '' && this.searchType == 'customer') {
					customerText = `<strong>Customer: </strong> ${this.selectedCustomer.Customer_Name}<br>`;
				}

				let employeeText = '';
				if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != '' && this.searchType == 'employee') {
					employeeText = `<strong>Employee: </strong> ${this.selectedEmployee.Employee_Name}<br>`;
				}

				let productText = '';
				if (this.selectedProduct != null && this.selectedProduct.Product_SlNo != '' && this.searchType == 'quantity') {
					productText = `<strong>Product: </strong> ${this.selectedProduct.Product_Name}`;
				}

				let categoryText = '';
				if (this.selectedCategory != null && this.selectedCategory.ProductCategory_SlNo != '' && this.searchType == 'category') {
					categoryText = `<strong>Category: </strong> ${this.selectedCategory.ProductCategory_Name}`;
				}


				let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Sales Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								${userText} ${customerText} ${employeeText} ${productText} ${categoryText}
							</div>
							<div class="col-xs-6 text-right">
								${dateText}
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
				reportWindow.document.body.innerHTML += reportContent;

				if (this.searchType == '' || this.searchType == 'user') {
					let rows = reportWindow.document.querySelectorAll('.record-table tr');
					rows.forEach(row => {
						row.lastChild.remove();
					})
				}


				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			},

			excelExport() {
				let onlyData = this.sales.map(item => {
					return {
						'Invoice No.': item.SaleMaster_InvoiceNo,
						'Date': item.SaleMaster_SaleDate,
						'Customer Name': item.Customer_Name,
						'Employee Name': item.Employee_Name,
						'Saved By': item.AddBy,
						'SubTotal': item.SaleMaster_SubTotalAmount,
						'Discount': item.SaleMaster_TotalDiscountAmount,
						'Point': item.pointAmount,
						'Total': item.SaleMaster_TotalSaleAmount,
						'Paid': item.SaleMaster_PaidAmount,
						'Return': item.returnAmount,
						'Note': item.SaleMaster_Description
					}
				})

				const worksheet = XLSX.utils.json_to_sheet(onlyData);
				const workbook = XLSX.utils.book_new();
				XLSX.utils.book_append_sheet(workbook, worksheet, "Skipped Rows");
				// Excel download
				XLSX.writeFile(workbook, "SaleRecord.xlsx");
			}
		}
	})
</script>