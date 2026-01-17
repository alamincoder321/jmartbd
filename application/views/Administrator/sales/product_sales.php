<style>
	.v-select {
		margin-bottom: 5px;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
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

	#branchDropdown .vs__actions button {
		display: none;
	}

	#branchDropdown .vs__actions .open-indicator {
		height: 15px;
		margin-top: 7px;
	}

	.add-button {
		padding: 2.8px;
		width: 100%;
		background-color: #0087bb;
		display: block;
		text-align: center;
		color: white;
		cursor: pointer;
		border-radius: 3px;
	}

	.add-button:hover {
		color: white;
	}

	.add-button:focus {
		color: white;
	}

	tr th,
	tr td {
		vertical-align: middle !important;
	}
</style>

<div id="sales" class="row">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
		<div class="row">
			<div class="form-group">
				<label class="col-md-1 control-label no-padding-right"> Invoice no </label>
				<div class="col-md-2">
					<input type="text" id="invoiceNo" class="form-control" v-model="sales.invoiceNo" readonly />
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-1 control-label no-padding-right"> Sales By </label>
				<div class="col-md-2">
					<v-select v-bind:options="employees" v-model="selectedEmployee" label="Employee_Name" @input="onChangeEmployee" placeholder="Select Employee"></v-select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-1 control-label no-padding-right"> Added By </label>
				<div class="col-md-2">
					<input type="text" class="form-control" v-model="sales.salesBy" readonly />
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-3">
					<input class="form-control" id="salesDate" type="date" v-model="sales.salesDate" v-bind:disabled="userType == 'u' ? true : false" />
				</div>
			</div>
		</div>
	</div>


	<div class="col-xs-12 col-md-9 col-lg-9">
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">Sales Information</h4>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
						<i class="ace-icon fa fa-chevron-up"></i>
					</a>

					<a href="#" data-action="close">
						<i class="ace-icon fa fa-times"></i>
					</a>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main">

					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Customer </label>
								<div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
									<div style="width: 86%;">
										<v-select v-bind:options="customers" style="margin: 0;" label="display_name" v-model="selectedCustomer" v-on:input="customerOnChange" @search="onSearchCustomer"></v-select>
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a href="<?= base_url('customer') ?>" class="add-button" target="_blank" title="Add New Customer"><i class="fa fa-plus" aria-hidden="true"></i></a>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Name </label>
								<div class="col-xs-8">
									<input type="text" id="customerName" placeholder="Customer Name" class="form-control" v-model="selectedCustomer.Customer_Name" v-bind:disabled="selectedCustomer.Customer_Type == 'G' || selectedCustomer.Customer_Type == 'N' ? false : true" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Mobile No </label>
								<div class="col-xs-8">
									<input type="text" id="mobileNo" placeholder="Mobile No" class="form-control" v-model="selectedCustomer.Customer_Mobile" v-bind:disabled="selectedCustomer.Customer_Type == 'G' || selectedCustomer.Customer_Type == 'N' ? false : true" />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-4 control-label no-padding-right"> Address </label>
								<div class="col-xs-8">
									<input type="text" id="address" placeholder="Address" class="form-control" v-model="selectedCustomer.Customer_Address" v-bind:disabled="selectedCustomer.Customer_Type == 'G' || selectedCustomer.Customer_Type == 'N' ? false : true">
								</div>
							</div>
							<div class="form-group">
								<label class="col-xs-12 control-label no-padding-right" for="is_member">
									<input type="checkbox" id="is_member" v-model="selectedCustomer.is_member" :true-value="`yes`" :false-value="`no`">
									Is Member
								</label>

							</div>
							<div class="form-group" v-show="selectedCustomer.is_member == 'yes'" style="display: none;" :style="{display: selectedCustomer.is_member == 'yes' ? '' : 'none'}">
								<label class="col-xs-2 control-label no-padding-right"> Point </label>
								<div class="col-xs-3">
									<input type="text" class="form-control" v-model="selectedCustomer.point" readonly>
								</div>
								<div class="col-xs-7">
									<div style="display: flex;align-items: center;gap: 8px;" title="১ পয়েন্ট কত টাকায় হবে?">
										<label class="control-label" for="amount">Amount:</label>
										<input type="number" min="0" step="any" class="form-control" id="amount" v-model="selectedCustomer.amount">
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-5">
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Category </label>
								<div class="col-xs-9" style="display: flex;align-items:center;margin-bottom:5px;">
									<div style="width: 86%;">
										<v-select v-bind:options="categories" id="category" style="margin: 0;" v-model="selectedCategory" label="ProductCategory_Name" @input="onChangeCategory" placeholder="Select Category"></v-select>
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a href="<?= base_url('category') ?>" class="add-button" target="_blank" title="Add New Category"><i class="fa fa-plus" aria-hidden="true"></i></a>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Product </label>
								<div class="col-xs-9" style="display: flex;align-items:center;margin-bottom:5px;">
									<div style="width: 86%;">
										<v-select v-show="!barcode" v-bind:options="products" ref="mySelect" id="product" style="margin: 0;" v-model="selectedProduct" label="display_text" @input="productOnChange" @search="onSearchProduct"></v-select>

										<input v-show="barcode" placeholder="barcode here" ref="barcode" v-model="barcodeVal" type="text" id="Productbarcode" class="form-control" @change="barcodeProduct($event)" />
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a class="add-button" @click="barcode = !barcode"><i :class="barcode ? 'fa fa-barcode' : 'fa fa-product-hunt'" aria-hidden="true"></i></a>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Sale Rate </label>
								<div class="col-xs-4">
									<input type="number" id="salesRate" placeholder="Rate" step="0.01" class="form-control" v-model="selectedProduct.Product_SellingPrice" v-on:input="productTotal" />
								</div>
								<label class="col-xs-1 control-label no-padding-right"> Qty </label>
								<div class="col-xs-4">
									<input type="number" step="any" id="quantity" placeholder="Qty" class="form-control" @change="cartCall($event)" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" autocomplete="off" required />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Amount </label>
								<div class="col-xs-9">
									<input type="text" id="productTotal" placeholder="Amount" class="form-control" v-model="selectedProduct.total" readonly />
								</div>
							</div>

							<div class="form-group">
								<label class="col-xs-7 control-label" for="isFree" style="display: flex;align-items:center;cursor:pointer;">
									<input type="checkbox" @change="onChangeFreeProduct" style="margin: 0px;width: 16px;height: 16px;cursor:pointer;" id="isFree" :true-value="`yes`" :false-value="`no`" v-model="isFree">
									<span style="margin: 0px;margin-left: 5px;margin-top: 1px;">Is Free Product</span>
								</label>
								<div class="col-xs-5">
									<button type="button" @click="addToCart" style="padding: 3px 6px; background: rgb(0, 126, 187) !important; border-color: rgb(0, 126, 187) !important; outline: none; border-radius: 6px;" class="btn pull-right">Add to Cart</button>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div style="display:none;" v-bind:style="{display:sales.isService == 'true' ? 'none' : ''}">
								<div class="text-center" style="display:none;" v-bind:style="{color: productStock > 0 ? 'green' : 'red', display: selectedProduct.Product_SlNo == '' ? 'none' : ''}">{{ productStockText }}</div class="text-center">

								<input type="text" id="productStock" v-model="productStock" readonly style="border:none;font-size:20px;width:100%;text-align:center;color:green"><br>
								<input type="text" id="stockUnit" v-model="selectedProduct.Unit_Name" readonly style="border:none;font-size:12px;width:100%;text-align: center;"><br><br>
							</div>
							<input type="password" ref="productPurchaseRate" v-model="selectedProduct.Product_Purchase_Rate" v-on:mousedown="toggleProductPurchaseRate" v-on:mouseup="toggleProductPurchaseRate" readonly title="Purchase rate (click & hold)" style="font-size:12px;width:100%;text-align: center;">
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="col-xs-12 col-md-12 col-lg-12" style="padding-left: 0px;padding-right: 0px;">
			<div class="table-responsive">
				<table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
					<thead>
						<tr class="">
							<th style="width:10%;color:#000;">Sl</th>
							<th style="width:25%;color:#000;">Description</th>
							<th style="width:15%;color:#000;">Category</th>
							<th style="width:7%;color:#000;">Qty</th>
							<th style="width:8%;color:#000;">Rate</th>
							<th style="width:8%;color:#000;">Discount</th>
							<th style="width:8%;color:#000;">Dis.Amount</th>
							<th style="width:15%;color:#000;">Total</th>
							<th style="width:10%;color:#000;">Action</th>
						</tr>
					</thead>
					<tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
						<tr v-for="(product, sl) in cart" :style="{background: product.isFree == 'yes' ? '#ffd150b3' : ''}" :title="product.isFree == 'yes' ? 'Free Product' : ''">
							<td>{{ sl + 1 }}</td>
							<td>{{ product.name }} - {{ product.productCode }}</td>
							<td>{{ product.categoryName }}</td>
							<td>{{ product.quantity }}</td>
							<td>{{ product.salesRate }}</td>
							<td>{{ product.discount }}</td>
							<td>{{ product.discountAmount }}</td>
							<td>{{ product.total }}</td>
							<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
						</tr>

						<tr>
							<td colspan="9"></td>
						</tr>

						<tr style="font-weight: bold;">
							<td colspan="6">Note</td>
							<td colspan="3">Total</td>
						</tr>

						<tr>
							<td colspan="6"><textarea style="width: 100%;font-size:13px;" placeholder="Note" v-model="sales.note"></textarea></td>
							<td colspan="3" style="padding-top: 15px;font-size:18px;">{{ sales.total }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>


	<div class="col-xs-12 col-md-3 col-lg-3">
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">Amount Details</h4>
				<div class="widget-toolbar">
					<a href="#" data-action="collapse">
						<i class="ace-icon fa fa-chevron-up"></i>
					</a>

					<a href="#" data-action="close">
						<i class="ace-icon fa fa-times"></i>
					</a>
				</div>
			</div>

			<div class="widget-body">
				<div class="widget-main">
					<div class="row">
						<div class="col-xs-12">
							<div class="table-responsive">
								<table style="color:#000;margin-bottom: 0px;border-collapse: collapse;">
									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right">Sub Total</label>
												<div class="col-xs-12">
													<input type="number" id="subTotal" class="form-control" v-model="sales.subTotal" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right">Discount</label>

												<div class="col-xs-4">
													<input type="number" id="discountPercent" class="form-control" v-model="discountPercent" v-on:input="calculateTotal" />
												</div>

												<label class="col-xs-1 control-label no-padding-right">%</label>

												<div class="col-xs-7">
													<input type="number" id="discount" class="form-control" v-model="sales.discount" v-on:input="calculateTotal" />
												</div>

											</div>
										</td>
									</tr>

									<tr v-show="selectedCustomer.is_member == 'yes'" style="display: none;" :style="{display: selectedCustomer.is_member == 'yes' ? '' : 'none'}">
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right">Use Point</label>
												<div class="col-xs-12">
													<input type="number" id="total" class="form-control" v-model="sales.pointAmount" v-on:input="calculateTotal" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right">Total</label>
												<div class="col-xs-12">
													<input type="number" id="total" class="form-control" v-model="sales.total" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-6 control-label">Cash</label>
											</div>
											<div class="col-xs-6">
												<input type="number" min="0" step="any" id="cashPaid" class="form-control" v-model="sales.cashPaid" v-on:input="calculateTotal" />
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="form-group">
												<div class="col-xs-6">
													<button type="button" @click="showModal" style="width: 100%;">Multiple Bank</button>
												</div>
												<div class="col-xs-6">
													<input type="number" min="0" step="any" id="bankPaid" class="form-control" v-model="sales.bankPaid" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label no-padding-right">Change Amount</label>
												<div class="col-xs-12">
													<input type="number" id="returnAmount" class="form-control" v-model="sales.returnAmount" readonly />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group">
												<label class="col-xs-12 control-label">Due</label>
												<div class="col-xs-6">
													<input type="number" id="due" class="form-control" v-model="sales.due" readonly />
												</div>
												<div class="col-xs-6">
													<input type="number" id="previousDue" class="form-control" v-model="sales.previousDue" readonly style="color:red;" />
												</div>
											</div>
										</td>
									</tr>

									<tr>
										<td>
											<div class="form-group" style="margin-top: 6px;">
												<div class="col-xs-6">
													<input type="button" class="btn btn-default btn-sm" value="Sale" v-on:click="saveSales" v-bind:disabled="saleOnProgress ? true : false" style="background: rgb(0, 126, 187) !important; outline: none; border: 0px !important; color: rgb(255, 255, 255) !important; margin-top: 0px; width: 100%; padding: 7px 5px; font-weight: bold; border-radius: 5px;">
												</div>
												<div class="col-xs-6">
													<a href="" @click.prevent="holdSale" class="btn btn-info btn-sm" style="outline:none; background: rgb(209, 91, 71) !important; border: 0px !important; margin-top: 0px; width: 100%; padding: 7px 5px; font-weight: bold; outline: none; border-radius: 5px;"><i style="font-size: 17px;" class="fa fa-hand-paper-o"></i> Hold</a>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- modal for multiple bank account -->
	<div class="modal fade bankAccount" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content" style="border-radius: 10px;">
				<div class="modal-header" style="display: flex; align-items: center; justify-content: space-between;background: #66853e;border-top-left-radius: 10px;border-top-right-radius: 10px;">
					<h5 class="modal-title" style="width:90%;margin: 0;color: #ffffff;">Multiple Bank Account List</h5>
					<button type="button" style="width: 10%; margin: 0px; display: flex; align-items: center; justify-content: end;" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true" style="margin: 0;font-size: 20px;">X</span>
					</button>
				</div>
				<div class="modal-body">
					<form @submit.prevent="bankAddToCart">
						<div class="row">
							<div class="col-xs-6">
								<v-select :options="banks" v-model="selectedBank" label="display_name" @input="onChangeBankAccount"></v-select>
							</div>
							<div class="col-xs-2">
								<input type="text" class="form-control" id="last_digit" v-model="selectedBank.last_digit" @input="goToAmount" placeholder="Last 4 digit" />
							</div>
							<div class="col-xs-3">
								<input type="number" step="any" min="0" class="form-control" id="bankAmount" v-model="selectedBank.amount" placeholder="Amount" />
							</div>
							<div class="col-xs-1 no-padding-left">
								<button type="submit">Add</button>
							</div>
						</div>
					</form>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Sl</th>
								<th>Bank Name</th>
								<th>Account Number</th>
								<th>Last Digit</th>
								<th>Amount</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(item, index) in bankCart" :key="index">
								<td v-text="index + 1"></td>
								<td v-text="item.bank_name"></td>
								<td v-text="item.account_number"></td>
								<td v-text="item.last_digit"></td>
								<td v-text="item.amount"></td>
								<td>
									<span style="cursor: pointer;color: red;" @click="removeBankCart(index)">X</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#sales',
		data() {
			return {
				barcode: true,
				barcodeVal: "",
				sales: {
					salesId: parseInt('<?php echo $salesId; ?>'),
					invoiceNo: '<?php echo $invoice; ?>',
					referenceNo: '',
					salesBy: '<?php echo $this->session->userdata("FullName"); ?>',
					salesType: 'retail',
					salesFrom: '',
					salesDate: moment().format('YYYY-MM-DD'),
					customerId: '',
					employeeId: null,
					subTotal: 0,
					discount: 0,
					pointAmount: 0,
					vat: 0,
					transportCost: 0,
					total: 0,
					bank_id: '',
					bankPaid: 0,
					cashPaid: 0,
					paid: 0,
					previousDue: 0,
					due: 0,
					returnAmount: 0,
					isService: '<?php echo $isService; ?>',
					note: ''
				},
				vatPercent: 0,
				discountPercent: 0,
				discountAmount: 0,
				isFree: 'no',
				cart: [],
				bankCart: [],
				categories: [],
				selectedCategory: null,
				employees: [],
				selectedEmployee: null,
				banks: [],
				selectedBank: {
					account_id: '',
					display_name: 'select bank',
					last_digit: '',
					amount: ''
				},
				branches: [],
				selectedBranch: {
					brunch_id: "<?php echo $this->session->userdata('BRANCHid'); ?>",
					Brunch_name: "<?php echo $this->session->userdata('Brunch_name'); ?>"
				},
				customers: [],
				selectedCustomer: {
					Customer_SlNo: '',
					Customer_Code: '',
					Customer_Name: 'General Customer',
					display_name: 'General Customer',
					Customer_Mobile: '',
					Customer_Address: '',
					Customer_Type: 'G',
					amount: 0,
					point: 0,
				},
				oldCustomerId: null,
				oldPreviousDue: 0,
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0,
					vat: 0,
					total: 0,
					warranty: '',
				},
				productPurchaseRate: '',
				productStockText: '',
				productStock: '',
				saleOnProgress: false,
				sales_due_on_update: 0,
				userType: '<?php echo $this->session->userdata("accountType"); ?>'
			}
		},
		async created() {
			await this.getBank();
			await this.getCategory();
			await this.getEmployees();
			await this.getBranches();
			await this.getCustomers();
			this.getProducts();


			if (this.sales.salesId != 0) {
				await this.getSales();
			}

			document.addEventListener('keydown', (e) => {
				if (event.shiftKey && (event.key === 'Enter' || event.keyCode === 13)) {
					this.saveSales();

				}
			})
			setTimeout(() => {
				$('#Productbarcode').focus();
			}, 200)
		},
		methods: {
			getBank() {
				axios.get('/get_bank_accounts').then(res => {
					this.banks = res.data.map(item => {
						item.display_name = `${item.bank_name} - ${item.account_number} - ${item.account_name}`;
						return item;
					});
				})
			},
			getCategory() {
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			onChangeCategory() {
				if (this.selectedCategory == null) {
					return
				}
				this.clearProduct();

			},

			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})
			},
			onChangeEmployee() {
				if (this.selectedEmployee == null) {
					return;
				}
				this.getCustomers();
			},
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},
			async getCustomers() {
				await axios.post('/get_customers', {
					customerType: this.sales.salesType,
					employeeId: this.selectedEmployee == null ? '' : this.selectedEmployee.Employee_SlNo
				}).then(res => {
					this.customers = res.data;
					this.customers.unshift({
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: 'General Customer',
						display_name: 'General Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G',
						amount: 0,
						point: 0,
					}, {
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: '',
						display_name: 'New Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'N',
						amount: 0,
						point: 0,
					})
				})
			},
			async onSearchCustomer(val, loading) {
				if (val.length > 2) {
					loading(true);
					await axios.post("/get_customers", {
							name: val,
						})
						.then(res => {
							let r = res.data;
							this.customers = r.filter(item => item.status == 'a')
							loading(false)
						})
				} else {
					loading(false)
					await this.getCustomers();
				}
			},
			async customerOnChange() {
				if (this.selectedCustomer == null) {
					this.selectedCustomer = {
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: 'General Customer',
						display_name: 'General Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G',
						amount: 0,
						point: 0
					}
					return;
				}

				if (this.selectedCustomer.Customer_SlNo != '') {
					await this.getCustomerDue();
					this.calculateTotal();
				}
			},
			async getCustomerDue() {
				if (this.selectedCustomer == null) {
					this.selectedCustomer = {
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: 'General Customer',
						display_name: 'General Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G',
						amount: 0,
						point: 0,
					}
					return;
				}
				if (this.selectedCustomer.Customer_SlNo != '') {
					await axios.post('/get_customer_due', {
						customerId: this.selectedCustomer.Customer_SlNo
					}).then(res => {
						if (res.data.length > 0) {
							this.sales.previousDue = res.data[0].dueAmount;
						} else {
							this.sales.previousDue = 0;
						}
					})
				}
			},
			getProducts() {
				axios.post('/get_products', {
					isService: this.sales.isService,
					categoryId: this.selectedCategory == null ? "" : this.selectedCategory.ProductCategory_SlNo
				}).then(res => {
					if (this.sales.salesType == 'wholesale') {
						this.products = res.data;
						this.products.map((product) => {
							return product.Product_SellingPrice = product.Product_WholesaleRate;
						})
					} else {
						this.products = res.data;
					}
					this.getPackages();
				})
			},
			getPackages(data1 = null) {
				let data = {
					name: data1 == null ? '' : data1.name
				}
				axios.post('/get_packages', data).then(res => {
					let data = res.data.map(item => {

						item.display_text = 'Package: ' + item.packageName + ' (' + item.comboInvoice + ')';
						item.Product_SellingPrice = item.total;
						item.Product_Name = item.packageName;
						item.Product_Code = item.comboInvoice;
						item.Product_Purchase_Rate = parseFloat(item.purchase_price).toFixed(5);
						item.ProductCategory_Name = 'Package';
						item.type = 'package';
						return item;

					})
					this.products.unshift(...res.data);
				})
			},
			async onSearchProduct(val, loading) {
				if (val.length > 2) {
					loading(true);
					await axios.post("/get_products", {
							name: val,
							isService: this.sales.isService,
							categoryId: this.selectedCategory == null ? "" : this.selectedCategory.ProductCategory_SlNo
						})
						.then(res => {
							let r = res.data;
							this.products = r.filter(item => item.status == 'a');
							this.getPackages({
								name: val
							});
							loading(false)

						})
				} else {
					loading(false)
					await this.getProducts();
				}
			},
			productTotal() {
				let prev_qty = 0;
				if (this.barcode == true) {
					let cart_prev = this.cart.find(p => (this.selectedProduct.Product_SlNo == p.productId));
					if (cart_prev) {
						prev_qty = cart_prev.quantity;
					}

					// console.log(prev_qty);
				}

				let qty = (parseFloat(this.selectedProduct.quantity) || 0) + prev_qty;
				let rate = parseFloat(this.selectedProduct.Product_SellingPrice) || 0;
				let discount = parseFloat(this.selectedProduct.discount) || 0;
				let discountAmount = 0;

				if (discount > 0 && discount <= 100) {
					discountAmount = ((qty * rate) * discount) / 100;
				}

				this.selectedProduct.discountAmount = discountAmount.toFixed(5);
				this.selectedProduct.total = ((qty * rate) - discountAmount).toFixed(5);
				console.log(this.selectedProduct.total, (qty), rate)
			},
			onSalesTypeChange() {
				if (this.selectedCustomer)
					this.selectedCustomer = {
						Customer_SlNo: '',
						Customer_Code: '',
						Customer_Name: 'General Customer',
						display_name: 'General Customer',
						Customer_Mobile: '',
						Customer_Address: '',
						Customer_Type: 'G',
						amount: 0,
						point: 0,
					}
				this.getCustomers();

				this.clearProduct();
				this.getProducts();
			},

			async productOnChange() {
				if (this.selectedProduct == null) {
					this.selectedProduct = {
						Product_SlNo: '',
						display_text: 'Select Product',
						Product_Name: '',
						Unit_Name: '',
						quantity: 0,
						Product_Purchase_Rate: '',
						Product_SellingPrice: 0,
						vat: 0,
						total: 0
					}
					return;
				}
				if ((this.selectedProduct.Product_SlNo != '' || this.selectedProduct.Product_SlNo != 0) && this.sales.isService == 'false') {
					if (this.isFree == 'yes') {
						this.selectedProduct.Product_SellingPrice = 0;
					}
					this.productStock = await axios.post('/get_product_stock', {
						productId: this.selectedProduct.Product_SlNo ? this.selectedProduct.Product_SlNo : this.selectedProduct.ComboId,
						isPackage: this.selectedProduct.ComboId ? true : false

					}).then(res => {
						return res.data;
					})
					this.productStockText = this.productStock > 0 ? "Available Stock" : "Stock Unavailable";
					if (this.barcode == true) {
						this.$refs.barcode.focus();
					} else {
						this.$refs.quantity.focus();
					}
				}

			},
			toggleProductPurchaseRate() {
				this.$refs.productPurchaseRate.type = this.$refs.productPurchaseRate.type == 'text' ? 'password' : 'text';
			},
			async barcodeProduct(event) {
				await axios.post('/get_products', {
					isService: this.sales.isService,
					categoryId: this.selectedCategory == null ? "" : this.selectedCategory.ProductCategory_SlNo,
					name: event.target.value,
					fromBarcode: 'yes'
				}).then(async res => {
					if (this.sales.salesType == 'wholesale') {
						this.products = res.data;
						this.products.map((product) => {
							return product.Product_SellingPrice = product.Product_WholesaleRate;
						})
						let prod = this.products[0];
						if (prod == undefined) {
							alert("Product not found");
							this.barcodeVal = "";
							return;
						}
						this.selectedProduct = prod;
						this.selectedProduct.quantity = prod.quantity;
						await this.productTotal();
						await this.productOnChange();
						if (parseFloat(this.productStock) < parseFloat(this.selectedProduct.quantity)) {
							alert("Stock unavailable");
							this.barcodeVal = "";
							return;
						}
						await this.addToCart();
					} else {
						this.products = res.data;
						let prod = this.products[0];
						if (prod == undefined) {
							alert("Product not found");
							this.barcodeVal = "";
							return;
						}
						this.selectedProduct = prod;
						this.selectedProduct.quantity = prod.quantity;

						await this.productTotal();
						await this.productOnChange();
						if (parseFloat(this.productStock) < parseFloat(this.selectedProduct.quantity)) {
							alert("Stock unavailable");
							this.barcodeVal = "";
							return;
						}
						await this.addToCart();
					}
					this.barcodeVal = "";
					this.productStock = '';
					this.productStockText = '';
				})
			},
			onChangeFreeProduct() {
				if (this.selectedProduct == null) {
					this.selectedProduct = {
						Product_SlNo: '',
						Product_Code: '',
						display_text: 'Select Product',
						Product_Name: '',
						Unit_Name: '',
						quantity: '',
						Product_Purchase_Rate: '',
						Product_SellingPrice: 0,
						total: ''
					}
					return
				}
				if (this.selectedProduct.Product_SlNo != '') {
					this.selectedProduct.Product_SellingPrice = 0;
					this.selectedProduct.total = 0;
					this.productTotal();
				}
			},
			cartCall(event) {
				if (event.target.value != '' && event.target.value != 0 && event.target.value != null && event.target.value != undefined) {}
			},
			addToCart() {
				let product = {
					productId: this.selectedProduct.Product_SlNo ? this.selectedProduct.Product_SlNo : this.selectedProduct.ComboId,
					productCode: this.selectedProduct.Product_Code,
					categoryName: this.selectedProduct.ProductCategory_Name,
					ProductsubCategory_ID: this.selectedProduct.ProductsubCategory_ID,
					name: this.selectedProduct.Product_Name,
					salesRate: this.selectedProduct.Product_SellingPrice,
					vat: this.selectedProduct.vat,
					quantity: this.selectedProduct.quantity ?? 1,
					discount: this.selectedProduct.discount,
					discountAmount: this.selectedProduct.discountAmount,
					total: this.selectedProduct.total,
					purchaseRate: this.selectedProduct.Product_Purchase_Rate,
					isFree: this.isFree,
					type: this.selectedProduct.ComboId ? 'package' : 'product'
				}

				if (product.productId == '' && !this.barcode) {
					alert('Select Product');
					return;
				}

				if ((product.quantity == 0 || product.quantity == '' || !product.quantity) && !this.barcode) {
					alert('Enter quantity');
					return;
				}

				let cartInd = this.cart.findIndex(p => (p.productId == product.productId) && (p.isFree == product.isFree));
				if (cartInd > -1) {
					let cartProduct = this.cart[cartInd];
					if (this.barcode) {
						product.quantity = parseFloat(+cartProduct.quantity + +product.quantity);
						product.total = product.quantity * product.salesRate;
					} else {
						product.quantity = parseFloat(+product.quantity);
						product.total = parseFloat(cartProduct.quantity * product.total).toFixed(5);
					}

				} else {
					product.total = parseFloat(parseFloat(product.salesRate) * parseFloat(product.quantity)).toFixed(5);
				}
				if (parseFloat(this.productStock) < parseFloat(product.quantity)) {
					alert("Stock unavailable");
					return;
				}
				if (cartInd > -1) {
					this.cart.splice(cartInd, 1);
				}

				this.cart.unshift(product);
				this.clearProduct();
				this.calculateTotal();
			},
			removeFromCart(ind) {
				this.cart.splice(ind, 1);
				this.calculateTotal();
			},
			clearProduct() {
				this.selectedProduct = {
					Product_SlNo: '',
					display_text: 'Select Product',
					Product_Name: '',
					Unit_Name: '',
					quantity: 0,
					Product_Purchase_Rate: '',
					Product_SellingPrice: 0,
					vat: 0,
					total: 0,
				}
				this.productStock = '';
				this.productStockText = '';
				this.isFree = 'no';
			},
			calculateTotal() {
				this.sales.subTotal = this.cart.reduce((prev, curr) => {
					return prev + parseFloat(curr.salesRate * curr.quantity)
				}, 0).toFixed(5);
				if (event.target.id != 'transportCost') {
					this.sales.vat = this.cart.reduce((prev, curr) => {
						return +prev + +(curr.total * (curr.vat / 100))
					}, 0);
				}

				if (event.target.id == 'discountPercent') {
					this.sales.discount = ((parseFloat(this.sales.subTotal) * parseFloat(this.discountPercent)) / 100).toFixed(5);
				} else if (event.target.id == 'discount') {
					this.discountPercent = (parseFloat(this.sales.discount) / parseFloat(this.sales.subTotal) * 100).toFixed(5);
				} else {
					this.sales.discount = this.cart.reduce((prev, curr) => {
						return +prev + +(curr.discountAmount)
					}, 0);

					if (this.sales.salesId != 0 && this.sales.discount == 0) {
						this.sales.discount = this.discountAmount;
					}

					this.discountPercent = (parseFloat(this.sales.discount) / parseFloat(this.sales.subTotal) * 100).toFixed(5);
				}
				this.sales.total = ((parseFloat(this.sales.subTotal) + parseFloat(this.sales.vat) + parseFloat(this.sales.transportCost)) - parseFloat(+this.sales.discount + +this.sales.pointAmount)).toFixed(5);
				if (event.target.id == 'cashPaid' || this.bankCart.length > 0) {
					this.sales.paid = parseFloat(parseFloat(this.sales.cashPaid) + parseFloat(this.sales.bankPaid)).toFixed(5);
					if (this.sales.paid == this.sales.total) {
						this.sales.returnAmount = 0;
						this.sales.due = 0;
					} else if (parseFloat(this.sales.paid) > parseFloat(this.sales.total)) {
						this.sales.returnAmount = parseFloat(this.sales.paid - this.sales.total).toFixed(5);
						this.sales.due = 0;
					} else {
						this.sales.returnAmount = 0;
						this.sales.due = parseFloat(this.sales.total - this.sales.paid).toFixed(5);
					}
				} else {
					this.sales.cashPaid = 0;
					this.sales.bankPaid = 0;
					this.sales.paid = 0;
					this.sales.due = this.sales.total;
					this.sales.returnAmount = 0;
				}
			},

			showModal() {
				$(".bankAccount").modal('show');
			},

			onChangeBankAccount() {
				if (this.selectedBank == null) {
					this.selectedBank = {
						account_id: '',
						display_name: 'select bank',
						last_digit: '',
						amount: ''
					}
					return;
				}

				if (this.selectedBank.account_id != '') {
					document.querySelector('#last_digit').select();
				}
			},

			goToAmount() {
				if (this.selectedBank.last_digit.length > 3) {
					document.querySelector('#bankAmount').select();
				}
			},

			bankAddToCart() {
				if (this.selectedBank.account_id == '') {
					alert("Please select bank account");
					return;
				}
				let bank = {
					bank_id: this.selectedBank.account_id,
					bank_name: this.selectedBank.bank_name,
					account_number: this.selectedBank.account_number,
					last_digit: this.selectedBank.last_digit,
					amount: this.selectedBank.amount
				}
				let findInd = this.bankCart.findIndex(item => item.bank_id == bank.bank_id);
				if (findInd > -1) {
					this.bankCart.splice(findInd, 1);
				}
				this.bankCart.push(bank);

				this.sales.bankPaid = this.bankCart.reduce((pr, cu) => {
					return pr + parseFloat(cu.amount)
				}, 0).toFixed(5);
				this.calculateTotal();
				this.selectedBank = {
					account_id: '',
					display_name: 'select bank',
					last_digit: '',
					amount: ''
				}
			},
			removeBankCart(sl) {
				this.bankCart.splice(sl, 1);
				this.sales.bankPaid = this.bankCart.reduce((pr, cu) => {
					return pr + parseFloat(cu.amount)
				}, 0).toFixed(5);
				this.calculateTotal();
			},
			holdSale() {
				if (this.cart.length == 0) {
					alert('Cart is empty');
					return;
				}
				let conf = prompt('Do you want to hold this sale? Enter reference invoice no');

				if (conf == null) {
					return;
				}
				if (conf == '') {
					alert('Invoice no can not be empty');
					return;
				}

				this.sales.referenceNo = conf;
				if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != null) {
					this.sales.employeeId = this.selectedEmployee.Employee_SlNo;
				} else {
					this.sales.employeeId = null;
				}

				this.sales.customerId = this.selectedCustomer.Customer_SlNo;
				this.sales.salesFrom = this.selectedBranch.brunch_id;
				let data = {
					sales: this.sales,
					cart: this.cart,
					customer: this.selectedCustomer,
					banks: this.bankCart
				}
				axios.post('/add_hold_sale', data).then(res => {
					let r = res.data;
					if (r.success) {
						alert(r.message);
						window.location = '/sales/product';
					} else {
						alert(r.message);
					}
				})
			},
			async saveSales() {
				if (this.selectedCustomer == null) {
					alert('Select Customer');
					return;
				}
				if (this.cart.length == 0) {
					alert('Cart is empty');
					return;
				}
				if (this.selectedCustomer.Customer_Type == 'G' && parseFloat(this.sales.due) != 0) {
					alert('Due sale does not accept on general customer');
					return;
				}

				await this.getCustomerDue();
				let url = "/add_sales";
				if (this.sales.salesId != 0) {
					url = "/update_sales";
					this.sales.previousDue = parseFloat((this.sales.previousDue - this.sales_due_on_update)).toFixed(5);
				}

				if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != null) {
					this.sales.employeeId = this.selectedEmployee.Employee_SlNo;
				} else {
					this.sales.employeeId = null;
				}

				this.sales.customerId = this.selectedCustomer.Customer_SlNo;
				this.sales.salesFrom = this.selectedBranch.brunch_id;
				let data = {
					sales: this.sales,
					cart: this.cart,
					customer: this.selectedCustomer,
					banks: this.bankCart
				}

				this.saleOnProgress = true;
				axios.post(url, data).then(async res => {
					let r = res.data;
					if (r.success) {
						let conf = confirm('Sale success, Do you want to view invoice?');
						if (conf) {
							window.open('/sale_invoice_print/' + r.salesId, '_blank');
							await new Promise(r => setTimeout(r, 1500));
							window.location = this.sales.isService == 'false' ? '/sales/product' : '/sales/service';
						} else {
							window.location = this.sales.isService == 'false' ? '/sales/product' : '/sales/service';
						}
					} else {
						alert(r.message);
						this.saleOnProgress = false;
					}
				})
			},
			async getSales() {
				await axios.post('/get_sales', {
					salesId: this.sales.salesId
				}).then(res => {
					let r = res.data;
					let sales = r.sales[0];
					this.sales.salesBy = sales.AddBy;
					this.sales.salesFrom = sales.SaleMaster_branchid;
					this.sales.salesDate = sales.SaleMaster_SaleDate;
					this.sales.salesType = sales.SaleMaster_SaleType;
					this.sales.customerId = sales.SalseCustomer_IDNo;
					this.sales.employeeId = sales.Employee_SlNo;
					this.sales.subTotal = sales.SaleMaster_SubTotalAmount;
					this.sales.discount = sales.SaleMaster_TotalDiscountAmount;
					this.sales.pointAmount = sales.pointAmount;
					this.sales.vat = sales.SaleMaster_TaxAmount;
					this.sales.transportCost = sales.SaleMaster_Freight;
					this.sales.total = sales.SaleMaster_TotalSaleAmount;
					this.sales.cashPaid = sales.SaleMaster_cashPaid;
					this.sales.bankPaid = sales.SaleMaster_bankPaid;
					this.sales.bank_id = sales.bank_id;
					this.sales.paid = sales.SaleMaster_PaidAmount;
					this.sales.due = sales.SaleMaster_DueAmount;
					this.sales.previousDue = sales.SaleMaster_Previous_Due;
					this.sales.note = sales.SaleMaster_Description;
					this.sales.returnAmount = sales.returnAmount;

					this.oldCustomerId = sales.SalseCustomer_IDNo;
					this.oldPreviousDue = sales.SaleMaster_Previous_Due;
					this.sales_due_on_update = sales.SaleMaster_DueAmount;

					this.vatPercent = parseFloat(this.sales.vat) * 100 / parseFloat(this.sales.subTotal);
					this.discountPercent = parseFloat(sales.SaleMaster_TotalDiscountAmount) * 100 / parseFloat(this.sales.subTotal);
					this.discountAmount = sales.SaleMaster_TotalDiscountAmount;

					this.selectedEmployee = {
						Employee_SlNo: sales.employee_id,
						Employee_Name: sales.Employee_Name
					}
					if (sales.SaleMaster_bankPaid > 0) {
						this.bankCart = r.banks;
					}

					this.selectedCustomer = {
						Customer_SlNo: sales.SalseCustomer_IDNo,
						Customer_Code: sales.Customer_Code,
						Customer_Name: sales.Customer_Name,
						display_name: sales.Customer_Type == 'G' ? 'General Customer' : `${sales.Customer_Code} - ${sales.Customer_Name}`,
						Customer_Mobile: sales.Customer_Mobile,
						Customer_Address: sales.Customer_Address,
						Customer_Type: sales.Customer_Type,
						is_member: sales.is_member,
						amount: sales.amount,
						point: (+sales.customerPoint + +sales.pointAmount) - sales.point
					}

					r.saleDetails.forEach(product => {
						let cartProduct = {
							productCode: product.Product_Code,
							productId: product.Product_IDNo,
							categoryName: product.ProductCategory_Name,
							ProductsubCategory_ID: product.ProductsubCategory_ID,
							name: product.Product_Name,
							salesRate: product.SaleDetails_Rate,
							vat: product.SaleDetails_Tax,
							quantity: product.SaleDetails_TotalQuantity,
							total: product.SaleDetails_TotalAmount,
							purchaseRate: product.Purchase_Rate,
							isFree: product.isFree,
							discount: product.SaleDetails_Discount,
							discountAmount: product.Discount_amount,
						}

						this.cart.push(cartProduct);
					})
				})
			}
		}
	})
</script>