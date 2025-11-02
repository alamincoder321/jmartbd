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
</style>

<div id="customerDueList">
	<div class="row">
		<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
			<form class="form-inline">
				<div class="form-group">
					<label>Search Type</label>
					<select class="form-control" style="width:150px;" v-model="searchType" v-on:change="onChangeSearchType" style="padding:0px;">
						<option value="all">All</option>
						<option value="customer">By Customer</option>
						<option value="employee">By Employee</option>
						<option value="area">By Area</option>
					</select>
				</div>
				<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'customer' ? '' : 'none'}">
					<label>Select Customer</label>
					<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" placeholder="Select customer"></v-select>
				</div>
				<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'employee' ? '' : 'none'}">
					<label>Select Employee</label>
					<v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select employee"></v-select>
				</div>
				<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'area' ? '' : 'none'}">
					<label>Select Area</label>
					<v-select v-bind:options="areas" v-model="selectedArea" label="District_Name" placeholder="Select area"></v-select>
				</div>

				<div class="form-group">
					<input type="button" class="btn btn-primary" value="Show Report" v-on:click="getDues" style="margin-top: -4px; border: 0px; padding: 3px 6px;">
				</div>
			</form>
		</div>
	</div>

	<div class="row" style="display: none" v-bind:style="{display: dues.length > 0 ? '' : 'none'}">
		<div class="col-md-6">
			<a href="" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a>
		</div>
		<div class="col-md-6 text-right">
			<a href="" v-on:click.prevent="excelExport">
				<i class="fa fa-file-excel-o"></i> Excel
			</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportTable">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Customer Id</th>
							<th>Customer Name</th>
							<th>Owner Name</th>
							<th>Address</th>
							<th>Customer Mobile</th>
							<th>Due Amount</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="data in dues">
							<td>{{ data.Customer_Code }}</td>
							<td>{{ data.Customer_Name }}</td>
							<td>{{ data.owner_name }}</td>
							<td>{{ data.Customer_Address }}</td>
							<td>{{ data.Customer_Mobile }}</td>
							<td style="text-align:right">{{ parseFloat(data.dueAmount).toFixed(2) }}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr style="font-weight:bold;">
							<td colspan="5" style="text-align:right">Total Due</td>
							<td style="text-align:right">{{ parseFloat(totalDue).toFixed(2) }}</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customerDueList',
		data() {
			return {
				searchType: 'all',
				customers: [],
				selectedCustomer: null,
				employees: [],
				selectedEmployee: null,
				areas: [],
				selectedArea: null,
				dues: [],
				totalDue: 0.00
			}
		},
		created() {

		},
		methods: {
			onChangeSearchType() {
				this.selectedCustomer = null;
				this.selectedEmployee = null;
				this.selectedArea = null;
				if (this.searchType == 'customer') {
					this.getCustomers();
				} else if (this.searchType == 'area') {
					this.getAreas();
				} else if (this.searchType == 'employee') {
					this.getEmployees();
				}
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data.map(item => {
						item.display_name = `${item.Employee_Name} - ${item.Employee_ID}`;
						return item;
					});
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getAreas() {
				axios.get('/get_districts').then(res => {
					this.areas = res.data;
				})
			},
			getDues() {
				if (this.searchType == 'customer' && this.selectedCustomer == null) {
					alert('Select customer');
					console.log(this.selectedCustomer);
					return;
				}

				let customerId = this.selectedCustomer == null ? null : this.selectedCustomer.Customer_SlNo;
				let districtId = this.selectedArea == null ? null : this.selectedArea.District_SlNo;
				let employeeId = this.selectedEmployee == null ? null : this.selectedEmployee.Employee_SlNo;
				axios.post('/get_customer_due', {
					customerId: customerId,
					districtId: districtId,
					employeeId: employeeId
				}).then(res => {
					if (this.searchType == 'customer') {
						this.dues = res.data;
					} else {
						this.dues = res.data.filter(d => parseFloat(d.dueAmount) != 0);
					}
					this.totalDue = this.dues.reduce((prev, cur) => {
						return prev + parseFloat(cur.dueAmount)
					}, 0);
				})
			},
			async print() {
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Customer due report</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				mywindow.document.body.innerHTML += reportContent;

				mywindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				mywindow.print();
				mywindow.close();
			},

			excelExport() {
				let onlyData = this.dues.map(item => {
					return {
						'Customer Id': item.Customer_Code,
						'Customer Name': item.Customer_Name,
						'Owner Name': item.owner_name,
						'Address': item.Customer_Address,
						'Customer Mobile': item.Customer_Mobile,
						'Due Amount': parseFloat(item.dueAmount).toFixed(2)
					}
				})

				const worksheet = XLSX.utils.json_to_sheet(onlyData);
				const workbook = XLSX.utils.book_new();
				XLSX.utils.book_append_sheet(workbook, worksheet, "Skipped Rows");
				// Excel download
				XLSX.writeFile(workbook, "CustomerDue.xlsx");
			}
		}
	})
</script>