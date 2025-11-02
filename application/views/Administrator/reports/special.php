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
<div id="cashStatement">
	<div class="row" style="border-bottom: 1px solid #ccc;">
		<div class="col-md-12">
			<form action="" class="form-inline" @submit.prevent="getStatements">
				<div class="form-group">
					<label for="">User</label>
					<v-select v-model="selectedUser" :options="users" label="FullName"  placeholder="Select User"></v-select>
				</div>
				
				<div class="form-group">
					<label for="">Date from</label>
					<input type="date" class="form-control" v-model="filter.dateFrom">
				</div>

				<div class="form-group">
					<label for="">to</label>
					<input type="date" class="form-control" v-model="filter.dateTo">
				</div>

				<div class="form-group buttons">
					<input type="submit" value="Search">
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12" style="padding-top:15px;">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
	</div>

	<div id="printContent">
		<div class="row" style="padding-top:10px;">
			<div class="col-md-6 col-md-offset-3">
				<!-- Sales -->
				<table class="table table-bordered table-condensed">
					<tr>
						<td style="text-align: left;width: 50%;">Total Sale Amount</td>
						<td style="width: 5%;">:</td>
						<td style="width: 45%;text-align:right;" v-text="totalSaleAmount"></td>
					</tr>
					<tr>
						<td style="text-align: left;width: 50%;">Cash Amount</td>
						<td style="width: 5%;">:</td>
						<td style="width: 45%;text-align:right;" v-text="cashAmount"></td>
					</tr>
					<template v-for="(bank, sl) in bankPaids">
						<tr :style="{display: bankPaids.length > 0 ? '' : 'none'}" v-if="bankPaids.length > 0">
							<td style="text-align: left;width: 50%;">{{bank.bank_name}} - {{bank.account_number}}</td>
							<td style="width: 5%;">:</td>
							<td style="width: 45%;text-align:right;">{{parseFloat(bank.amount).toFixed(2)}}</td>
						</tr>
					</template>
					<tr>
						<td style="text-align: left;width: 50%;">Change Amount <span style="color:red;">(optional)</span></td>
						<td style="width: 5%;">:</td>
						<td style="width: 45%;text-align:right;" v-text="changeAmount"></td>
					</tr>
					<tr>
						<td style="text-align: left;width: 50%;">Return Amount</td>
						<td style="width: 5%;">:</td>
						<td style="width: 45%;text-align:right;" v-text="returnAmount"></td>
					</tr>
					<tr>
						<td style="text-align: left;width: 50%;">Exchange Amount</td>
						<td style="width: 5%;">:</td>
						<td style="width: 45%;text-align:right;" v-text="exchangeAmount"></td>
					</tr>
					<tr>
						<td style="text-align: left;font-weight:700;width: 50%;">Close Balance</td>
						<td style="width: 5%;">:</td>
						<td style="width: 45%;font-weight:700;text-align:right;" v-text="closeBalance"></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#cashStatement',
		data() {
			return {
				filter: {
					dateFrom: moment().format('YYYY-MM-DD'),
					dateTo: moment().format('YYYY-MM-DD'),
					userFullName: "",
				},
				users: [],
				selectedUser: {
					User_SlNo: "<?php echo $this->session->userdata('userId'); ?>",
					FullName: "<?php echo $this->session->userdata('FullName'); ?>"
				},
				sales: [],
				bankPaids: [],
				saleReturns: [],
				exchanges: [],
			}
		},
		computed: {
			// Total Sale Amount
			totalSaleAmount() {
				return parseFloat(this.sales.reduce((acc, cur) => acc + parseFloat(cur.SaleMaster_TotalSaleAmount), 0)).toFixed(2);
			},
			// Cash Amount
			cashAmount() {
				return parseFloat(this.sales.reduce((acc, cur) => acc + parseFloat(cur.SaleMaster_cashPaid), 0)).toFixed(2);
			},
			// Change Amount
			changeAmount() {
				return parseFloat(this.sales.reduce((acc, cur) => acc + parseFloat(cur.returnAmount), 0)).toFixed(2);
			},
			// Bank Amount
			bankAmount() {
				return parseFloat(this.bankPaids.reduce((acc, cur) => acc + parseFloat(cur.amount), 0)).toFixed(2);
			},
			// Return Amount
			returnAmount() {
				return parseFloat(this.saleReturns.reduce((acc, cur) => acc + parseFloat(cur.SaleReturn_ReturnAmount), 0)).toFixed(2);
			},
			// Exchange Amount
			exchangeAmount() {
				return parseFloat(this.exchanges.reduce((acc, cur) => acc + parseFloat(cur.total), 0)).toFixed(2);
			},

			// total amount
			closeBalance() {
				return parseFloat((+this.totalSaleAmount + +this.exchangeAmount) - this.returnAmount).toFixed(2);
			}
		},

		created() {
			this.getUser();
			this.getStatements();
		},
		methods: {
			getUser() {
				axios.get('/get_users')
					.then(res => {
						this.users = res.data;
					})
			},
			getStatements() {
				this.filter.userFullName = this.selectedUser ? this.selectedUser.FullName : "<?php echo $this->session->userdata('FullName'); ?>";
				this.getSales();
				this.getBankPaid();
				this.getSaleReturn();
				this.getExchange();
			},
			getSales() {
				axios.post('/get_sales', this.filter)
					.then(res => {
						this.sales = res.data.sales;
					})
			},

			getBankPaid() {
				axios.post('/get_sales_record', this.filter)
					.then(res => {
						this.bankPaids = Object.values(res.data.reduce((acc, sale) => {
							sale.banks.forEach(bank => {
								if (!acc[bank.bank_id]) {
									acc[bank.bank_id] = {
										bank_id: bank.bank_id,
										bank_name: bank.bank_name,
										account_number: bank.account_number,
										amount: 0
									};
								}
								acc[bank.bank_id].amount += parseFloat(bank.amount);
							});
							return acc;
						}, {}));

					})
			},
			getSaleReturn() {
				let data = {
					fromDate: this.filter.dateFrom,
					toDate: this.filter.dateTo,
					userFullName: this.filter.userFullName
				}
				axios.post('/get_sale_returns', data)
					.then(res => {
						this.saleReturns = res.data.returns;
					})
			},
			getExchange() {
				axios.post('/get_exchanges', this.filter)
					.then(res => {
						this.exchanges = res.data;
					})
			},

			async print() {
				let printContent = `
					<div class="container">
						<h4 style="text-align:center">Special Report</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12 text-center">
								<strong>Statement from</strong> ${this.filter.dateFrom} <strong>to</strong> ${this.filter.dateTo}
							</div>
						</div>
					</div>
					<div class="container">
						${document.querySelector('#printContent').innerHTML}
					</div>
				`;

				var printWindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				printWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				printWindow.document.body.innerHTML += printContent;
				printWindow.document.head.innerHTML += `
					<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
					<style>
						th{
							text-align: center;
						}
						.account-section{
							display: flex;
							border: none;
							border-radius: 5px;
							overflow:hidden;
							margin-bottom: 20px;
						}

						.account-section h3{
							margin: 10px 0;
							padding: 0;
						}

						.account-section h4{
							margin: 0;
							margin-top: 3px;
						}

						.account-section .col1{
							background-color: #82a253;
							color: white;
							flex: 1;
							text-align: center;
							padding: 10px;
						}
						.account-section .col2{
							background-color: #edf3e2;
							flex: 2;
							padding: 10px;
							align-items: center; 
							text-align:center;
						}
					</style>
				`;

				printWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				printWindow.print();
				printWindow.close();
			}
		}
	})
</script>