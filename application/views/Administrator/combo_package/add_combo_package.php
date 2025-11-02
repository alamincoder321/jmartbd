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
				<label class="col-md-1 control-label no-padding-right"> Added By </label>
				<div class="col-md-2">
					<input type="text" class="form-control" v-model="sales.salesBy" readonly />
				</div>
			</div>
		</div>
	</div>


	<form class="col-xs-12 col-md-9 col-lg-9" @submit.prevent="cartCall($event)">
		<div class="widget-box">
			<div class="widget-header">
				<h4 class="widget-title">Combo Package Information</h4>
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
							
							<div class="form-group">
								<label class="col-xs-3 control-label no-padding-right"> Package Name </label>
								<div class="col-xs-9">
									<input type="text" id="salesRate" placeholder="Package Name" step="0.01" class="form-control" v-model="sales.packageName"/>
								</div>
							</div>
						</div>
						<div class="col-xs-12">
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

										<input v-show="barcode" placeholder="barcode here" ref="barcode" v-model="barcodeVal" type="text"  id="Productbarcode"  class="form-control" @change="barcodeProduct($event)" />
									</div>
									<div style="width: 13%;margin-left:2px;">
										<a class="add-button" @click="barcode = !barcode"><i class="fa fa-barcode" aria-hidden="true"></i></a>
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
									<input type="number" step="0.01" id="quantity" placeholder="Qty" class="form-control" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" autocomplete="off" required />
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
									
								</label>
								<div class="col-xs-5">
									<button type="button" @click="addToCart" style="padding: 3px 6px; background: rgb(0, 126, 187) !important; border-color: rgb(0, 126, 187) !important; outline: none; border-radius: 6px;" class="btn pull-right">Add to Cart</button>
								</div>
							</div>
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
							<td>{{ product.total }}</td>
							<td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
						</tr>

						<tr>
							<td colspan="7"></td>
						</tr>

						<tr style="font-weight: bold;">
							<td colspan="4">Note</td>
							<td colspan="3">Total</td>
						</tr>

						
					</tbody>
				</table>
			</div>
		</div>
	</form>


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
											<div class="form-group" style="margin-top: 6px;">
												<div class="col-xs-6">
													<input type="button" class="btn btn-default btn-sm" value="Package Save" v-on:click="saveSales" v-bind:disabled="saleOnProgress ? true : false" style="background: rgb(0, 126, 187) !important; outline: none; border: 0px !important; color: rgb(255, 255, 255) !important; margin-top: 0px; width: 100%; padding: 7px 5px; font-weight: bold; border-radius: 5px;">
												</div>
												<div class="col-xs-6">
													<a class="btn btn-info btn-sm" v-bind:href="`/sales/${sales.isService == 'true' ? 'service' : 'product'}`" style="background: rgb(209, 91, 71) !important; border: 0px !important; margin-top: 0px; width: 100%; padding: 7px 5px; font-weight: bold; outline: none; border-radius: 5px;">Reset</a>
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
					invoiceNo: '<?php echo $invoiceNo; ?>',
					salesBy: '<?php echo $this->session->userdata("FullName"); ?>',
					salesType: 'retail',
					salesFrom: '',
					salesDate: moment().format('YYYY-MM-DD'),
					employeeId: null,
					subTotal: 0,
					total: 0,
					packageName: '',
					qty: 0,
				
				},
				discountPercent: 0,
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
			await this.getCategory();
			await this.getBranches();
			
			this.getProducts();

			if (this.sales.salesId != 0) {
				await this.getSales();
			}

			// document.addEventListener('keydown', (e) => {
			// 	 if (event.shiftKey && (event.key === 'Enter' || event.keyCode === 13)) {
			// 		// Handle Shift + Enter (e.g., submit form)
			// 		this.saveSales();
	
			// 	}else if(event.shiftKey && (event.key === 'p' || event.keyCode === 80)){
			// 		if(this.barcode){
			// 			this.barcode = false;
			// 			setTimeout(() => {
			// 				 this.$refs.mySelect.$el.querySelector('input').focus();
			// 			},400)
			// 		}else{
			// 			this.barcode = true;
			// 			setTimeout(() => {
			// 				$('#Productbarcode').focus();
			// 			},200)
			// 		}
			// 	}

			// })
			setTimeout(() => {
				$('#Productbarcode').focus();
			},200)
		},
		methods: {

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
				this.getProducts();
			},


			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
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
							loading(false)
						})
				} else {
					loading(false)
					await this.getProducts();
				}
			},
			productTotal() {
				this.selectedProduct.total = (parseFloat(this.selectedProduct.quantity) * parseFloat(this.selectedProduct.Product_SellingPrice)).toFixed(2);
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
						productId: this.selectedProduct.Product_SlNo
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
						this.selectedProduct.quantity = 1;
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
						this.selectedProduct.quantity = 1;
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
				if(event.target.value != '' && event.target.value != 0 && event.target.value != null && event.target.value != undefined){
					
						this.addToCart(); 
						this.$refs.mySelect.$el.querySelector('input').focus();
					
					// $('#product').focus();

				}
			},
			addToCart() {
				let product = {
					productId: this.selectedProduct.Product_SlNo,
					productCode: this.selectedProduct.Product_Code,
					categoryName: this.selectedProduct.ProductCategory_Name,
					name: this.selectedProduct.Product_Name,
					salesRate: this.selectedProduct.Product_SellingPrice,
					vat: this.selectedProduct.vat,
					quantity: this.selectedProduct.quantity,
					total: this.selectedProduct.total,
					purchaseRate: this.selectedProduct.Product_Purchase_Rate,
					isFree: this.isFree
				}

				

				if (product.productId == '' && !this.barcode) {
					alert('Select Product');
					return;
				}

				if ((product.quantity == 0 || product.quantity == '' || !product.quantity) && !this.barcode) {
					alert('Enter quantity');
					return;
				}
				
				// console.log(this.barcode, product.quantity);
				

				// if ((product.salesRate == 0 || product.salesRate == '') && !this.barcode) {
				// 	alert('Enter sales rate');
				// 	return;
				// }

				let cartInd = this.cart.findIndex(p => (p.productId == product.productId) && (p.isFree == product.isFree));
				if (cartInd > -1) {
					let cartProduct = this.cart[cartInd];
					product.quantity = parseFloat(+cartProduct.quantity + +product.quantity);
					product.total = parseFloat(+cartProduct.total + +product.total).toFixed(2);
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
					return prev + parseFloat(curr.total)
				}, 0).toFixed(2);
				this.sales.vat = this.cart.reduce((prev, curr) => {
					return +prev + +(curr.total * (curr.vat / 100))
				}, 0);
				if (event.target.id == 'discountPercent') {
					this.sales.discount = ((parseFloat(this.sales.subTotal) * parseFloat(this.discountPercent)) / 100).toFixed(2);
				} else {
					this.discountPercent = (parseFloat(this.sales.discount) / parseFloat(this.sales.subTotal) * 100).toFixed(2);
				}
				this.sales.total = ((parseFloat(this.sales.subTotal) + parseFloat(this.sales.vat) + parseFloat(this.sales.transportCost)) - parseFloat(+this.sales.discount + +this.sales.pointAmount)).toFixed(2);

				if (event.target.id == 'cashPaid' || this.bankCart.length > 0) {
					this.sales.paid = parseFloat(parseFloat(this.sales.cashPaid) + parseFloat(this.sales.bankPaid)).toFixed(2);
					console.log('total paid', this.sales.paid, this.sales.cashPaid, this.sales.bankPaid);
					if(this.sales.paid == this.sales.total){
						this.sales.returnAmount = 0;
						this.sales.due = 0;
					}else if (parseFloat(this.sales.paid) > parseFloat(this.sales.total)) {
						this.sales.returnAmount = parseFloat(this.sales.paid - this.sales.total).toFixed(2);
						this.sales.due = 0;
					} else {
						this.sales.returnAmount = 0;
						this.sales.due = parseFloat(this.sales.total - this.sales.paid).toFixed(2);
					}
				} else {
					this.sales.cashPaid = this.sales.total;
					this.sales.bankPaid = 0;
					this.sales.paid = this.sales.total;
					this.sales.due = 0;
					this.sales.returnAmount = 0;
				}
				
				// this.sales.due =	this.sales.total - this.sales.bankPaid - this.sales.cashPaid

			},

			
			async saveSales() {
				if (this.cart.length == 0) {
					alert('Package  is empty');
					return;
				}

				let url = "/update_combo_package";

				if(this.sales.packageName == '' || this.sales.packageName == null){
					alert('Enter Package Name');
					return;
				}
	

				if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != null) {
					this.sales.employeeId = this.selectedEmployee.Employee_SlNo;
				} else {
					this.sales.employeeId = null;
				}

		
				
				this.sales.salesFrom = this.selectedBranch.brunch_id;
				let data = {
					sales: this.sales,
					cart: this.cart,
					
				}

				this.saleOnProgress = true;
				axios.post(url, data).then(async res => {
					let r = res.data;
					if (r.success) {
						let conf = confirm('Package Save success, Do you want to view Details?');
						if (conf) {
							window.open('/combo_package/' + r.salesId, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							// window.location = this.sales.isService == 'false' ? '/sales/product' : '/sales/service';
						} else {
							// window.location = this.sales.isService == 'false' ? '/sales/product' : '/sales/service';
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

					this.oldCustomerId = sales.SalseCustomer_IDNo;
					this.oldPreviousDue = sales.SaleMaster_Previous_Due;
					this.sales_due_on_update = sales.SaleMaster_DueAmount;

					this.vatPercent = parseFloat(this.sales.vat) * 100 / parseFloat(this.sales.subTotal);
					this.discountPercent = parseFloat(this.sales.discount) * 100 / parseFloat(this.sales.subTotal);

					this.selectedEmployee = {
						Employee_SlNo: sales.employee_id,
						Employee_Name: sales.Employee_Name
					}
					if (sales.SaleMaster_bankPaid > 0) {
						this.selectedBank = {
							account_id: sales.bank_id,
							display_name: `${sales.bank_name} - ${sales.account_number} - ${sales.account_name}`
						}
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
						point: +sales.customerPoint + +sales.pointAmount
					}

					r.saleDetails.forEach(product => {
						let cartProduct = {
							productCode: product.Product_Code,
							productId: product.Product_IDNo,
							categoryName: product.ProductCategory_Name,
							name: product.Product_Name,
							salesRate: product.SaleDetails_Rate,
							vat: product.SaleDetails_Tax,
							quantity: product.SaleDetails_TotalQuantity,
							total: product.SaleDetails_TotalAmount,
							purchaseRate: product.Purchase_Rate,
							isFree: product.isFree,
						}

						this.cart.push(cartProduct);
					})

					let gCustomerInd = this.customers.findIndex(c => c.Customer_Type == 'G');
					this.customers.splice(gCustomerInd, 1);
				})
			}
		}
	})
</script>