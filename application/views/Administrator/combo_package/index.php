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
	<div class="row" style="border-bottom: 1px solid #ccc;padding: 3px 0;">
		<div class="col-md-12">
			<form class="form-inline" id="searchForm" @submit.prevent="getSearchResult">
				<div class="form-group">
					<label>Search Type</label>
					<select class="form-control" v-model="searchType">
						<option value="">All</option>
					</select>
				</div>

		

				<div class="form-group" >
					<label>Record Type</label>
					<select class="form-control" v-model="recordType" @change="sales = []">
						<option value="without_details">Without Details</option>
						<option value="with_details">With Details</option>
					</select>
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="dateFrom">
				</div>

				<div class="form-group">
					<input type="date" class="form-control" v-model="dateTo">
				</div>

				<div class="form-group" style="margin-top: -5px;">
					<input type="submit" value="Search">
				</div>
			</form>
		</div>
	</div>

 <div class="row" style="margin-top:15px;">
		<div class="col-md-12" style="margin-bottom: 10px;">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent">
				<table
					class="record-table"
				
					
					>
					<thead>
						<tr>
							<th>Package No.</th>
							<th>Date</th>
							<th>Package Name</th>
							<th>Stock</th>
							<th>Total</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<template v-for="sale in sales">
							<tr>
								<td>{{ sale.comboInvoice }}</td>
								<td>{{ sale.add_time }}</td>
								<td>{{ sale.packageName }}</td>
								<td>{{ sale.stockQty }}</td>
								<td>{{ sale.total }}</td>
								
								<td style="text-align:center;">
									<a href="" title="Sale Invoice" v-bind:href="`/combo_package/${sale.ComboId}`" target="_blank"><i class="fa fa-file"></i></a>
							
                                    <a href="javascript:" title="Update Sale" @click="selectedForStock(sale)">Update Stock</a>
                                    <a href="javascript:" title="Edit Sale" @click="checkReturnAndEdit(sale)"><i class="fa fa-edit"></i></a>
                                    <a href="" title="Delete Sale" @click.prevent="deleteSale(sale.ComboId)"><i class="fa fa-trash"></i></a>
									
								</td>
							</tr>
							
							
						</template>
					</tbody>
				</table>


				
			</div>
		</div>
	</div> 
	<!-- modal for multiple bank account -->
	<div class="modal fade PackageStock" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content" style="border-radius: 10px;">
				<div class="modal-header" style="display: flex; align-items: center; justify-content: space-between;background: #66853e;border-top-left-radius: 10px;border-top-right-radius: 10px;">
					<h5 class="modal-title" style="width:90%;margin: 0;color: #ffffff;">Package Stock</h5>
					<button type="button" style="width: 10%; margin: 0px; display: flex; align-items: center; justify-content: end;" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true" style="margin: 0;font-size: 20px;">X</span>
					</button>
				</div>
				<div class="modal-body">
					<form @submit.prevent="packageStockUpdate" v-if="selected_sale">
							<div>
								Current Stock
								<input type="text" class="form-control" id="stockQty" v-model="selected_sale.stockQty" placeholder="Stock" readonly />
							</div>
							<div>
								New Stock
								<input type="number" class="form-control" id="stockQty" v-model="selected_sale.newstock" placeholder="Stock" />
							</div>
							<div>
								<input type="checkbox" value="1" checked v-model="selected_sale.addStock" id="type"> <label for="type">Do you want add stock ? else deduct.</label>
							</div>
							<div style="margin-top: 10px;">
								<input type="submit" class="btn btn-primary" value="Update Stock">

							</div>
					</form>
					
				</div>
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
				selected_sale: null,

		
			}
		},
		methods: {
			packageStockUpdate() {
				
				axios.post('/update_combo_package_stock', this.selected_sale)
					.then(res => {
						let r = res.data;
						alert(r.message);
						if(r.success){
							this.getSalesRecord();
						}
						$('.PackageStock').modal('hide');
					})
					.catch(error => {
						if (error.response) {
							alert(`${error.response.status}, ${error.response.statusText}`);
						}
					})
			},
			selectedForStock(sales) {
				this.selected_sale = sales;
				this.selected_sale.addStock = true;
				this.selected_sale.newstock = 0;
				$('.PackageStock').modal('show');
			},
	
			getSearchResult() {
			
                this.getSalesRecord();
			},
			getSalesRecord() {
				let filter = {
					dateFrom: this.dateFrom,
					dateTo: this.dateTo
				}

				let url = '/get_combo_packages';
				
				axios.post(url, filter)
					.then(res => {
					
							this.sales = res.data;
						
					})
					.catch(error => {
						if (error.response) {
							alert(`${error.response.status}, ${error.response.statusText}`);
						}
					})
			},
			
			deleteSale(saleId) {
				let deleteConf = confirm('Are you sure?');
				if (deleteConf == false) {
					return;
				}
				axios.post('/delete_sales', {
						saleId: saleId
					})
					.then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getSalesRecord();
						}
					})
					.catch(error => {
						if (error.response) {
							alert(`${error.response.status}, ${error.response.statusText}`);
						}
					})
			},
			
		}
	})
</script>