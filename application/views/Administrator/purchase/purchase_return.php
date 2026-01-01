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

	tr td,
	tr th {
		vertical-align: middle !important;
	}
</style>

<div class="row" id="purchaseReturn">
	<div class="col-xs-12 col-md-12">
		<div class="row">
			<div class="col-xs-12 col-md-12">
				<div class="row" style="border-radius: 5px; border: 2px solid #007ebb; margin: 0px 0px 10px; padding: 7px 0px; background: #f3f3f3;">
					<div class="form-group">
						<label for="" class="col-xs-4 col-md-1">Supplier</label>
						<div class="col-xs-8 col-md-3">
							<v-select :options="suppliers" style="margin-bottom: 0;" v-model="selectedSupplier" label="display_name" @input="getProducts"></v-select>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-xs-4 col-md-2">Return Date</label>
						<div class="col-xs-8 col-md-2">
							<input type="date" style="margin-bottom: 0;" class="form-control" v-model="purchaseReturn.returnDate" required>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-xs-4 col-md-1">Note</label>
						<div class="col-xs-8 col-md-3">
							<input type="text" style="margin-bottom: 0;" class="form-control" v-model="purchaseReturn.note">
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-5">
				<div class="row" style="border-radius: 5px; border: 2px solid #007ebb; margin: 0px 0px 10px; padding: 7px 0px;">
					<h3 style="margin: 0; padding-left: 12px;margin-bottom: 10px;border-bottom: 1px solid gray;padding-bottom: 5px;">Product Information</h3>
					<form @submit.prevent="addToCart">
						<div class="form-group">
							<label for="" class="col-xs-4 col-md-3">Product:</label>
							<div class="col-xs-8 col-md-9">
								<v-select :options="products" v-model="selectedProduct" label="display_text" @input="onChangeProduct"></v-select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-xs-4 col-md-3">Pur. Rate:</label>
							<div class="col-xs-8 col-md-4">
								<input type="number" step="any" min="0" class="form-control" v-model="selectedProduct.Product_Purchase_Rate" @input="productTotal">
							</div>
							<label for="" class="col-xs-4 col-md-1">Qty:</label>
							<div class="col-xs-8 col-md-4">
								<input type="number" step="any" min="0" ref="quantity" class="form-control" v-model="selectedProduct.quantity" @input="productTotal">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-xs-4 col-md-3">Total:</label>
							<div class="col-xs-8 col-md-9">
								<input type="number" step="any" min="0" readonly class="form-control" v-model="selectedProduct.total">
							</div>
						</div>
						<div class="form-group">
							<div class="col-xs-12 col-md-6">
								<span style="display: none;" :style="{display: productStock > 0 ? '' : 'none'}" v-if="productStock > 0">Available Stock: {{ productStock }} {{selectedProduct.Unit_Name}}</span>
								<span style="display: none;" :style="{display: productStock <= 0 ? '' : 'none'}" v-if="productStock <= 0" class="text-danger">Unavailable Stock</span>
							</div>
							<div class="col-xs-12 col-md-6 text-right">
								<button type="submit" class="btn btn-xs btn-danger" style="padding: 5px 15px;border-radius: 5px;">Add to Cart</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="col-xs-12 col-md-7">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>Sl.</th>
							<th>Product</th>
							<th>Quantity</th>
							<th>Rate</th>
							<th>Total</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(product, index) in cart" :key="index" style="display: none;" :style="{ display: cart.length > 0 ? '' : 'none' }">
							<td>{{ index + 1 }}</td>
							<td>{{ product.Product_Name }} - {{product.Product_Code}}</td>
							<td>{{ product.return_quantity }}</td>
							<td>{{ product.return_rate }}</td>
							<td>{{ product.return_amount }}</td>
							<td>
								<button class="text-danger" @click="cart.splice(index, 1); calculateTotal();"><i class="fa fa-trash"></i></button>
							</td>
						</tr>
						<tr v-if="cart.length == 0">
							<td colspan="6" class="text-center">No data found</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="4" class="text-right">Total</th>
							<th>{{ purchaseReturn.total }}</th>
							<th>
								<button class="btn btn-xs btn-success" :disabled="save_disabled" @click="savePurchaseReturn">
									<span v-if="!save_disabled">Save Return</span>
									<span v-else>Saving...</span>
								</button>
							</th>
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
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#purchaseReturn',
		data() {
			return {
				suppliers: [],
				selectedSupplier: null,
				products: [],
				selectedProduct: {
					Product_SlNo: '',
					Product_Name: '',
					display_text: '',
					Product_Purchase_Rate: 0,
					quantity: 0,
					total: 0
				},
				productStock: 0,
				cart: [],
				purchaseReturn: {
					returnId: parseInt('<?php echo $returnId; ?>'),
					returnDate: moment().format('YYYY-MM-DD'),
					total: 0.00,
					note: ''
				},
				userType: '<?php echo $this->session->userdata("accountType"); ?>',
				save_disabled: false,
			}
		},
		created() {
			this.getSuppliers();
			this.getProducts();
			if (this.purchaseReturn.returnId != 0) {
				this.getReturn();
			}
		},
		methods: {
			getSuppliers() {
				axios.get('/get_suppliers').then(res => {
					this.suppliers = res.data;
					// this.suppliers.unshift({
					// 	Supplier_SlNo: null,
					// 	Supplier_Name: 'General Supplier',
					// 	Supplier_Type: 'G',
					// 	display_name: 'General Supplier'
					// })
				})
			},

			getProducts() {
				axios.post('/get_products', {
					supplierId: this.selectedSupplier ? this.selectedSupplier.Supplier_SlNo : null
				}).then(res => {
					this.products = res.data;
				})
			},

			async onChangeProduct() {
				if (this.selectedProduct == null) {
					this.selectedProduct = {
						Product_SlNo: '',
						Product_Name: '',
						display_text: '',
						Product_Purchase_Rate: 0,
						quantity: 0,
						total: 0
					};
					return;
				}
				if (this.selectedProduct.Product_SlNo != '') {
					this.productStock = await axios.post('/get_product_stock', {
						productId: this.selectedProduct.Product_SlNo,
						supplierId: this.selectedSupplier ? this.selectedSupplier.Supplier_SlNo : null
					}).then(res => {
						return res.data;
					});

					this.$refs.quantity.focus();
				}
			},

			productTotal() {
				this.selectedProduct.total = (parseFloat(this.selectedProduct.Product_Purchase_Rate) * parseFloat(this.selectedProduct.quantity)).toFixed(2);
			},

			addToCart() {
				if (this.selectedProduct == null || this.selectedProduct.Product_SlNo == '') {
					alert('Select product');
					return;
				}

				if (this.selectedProduct.quantity <= 0) {
					alert('Enter quantity');
					this.$refs.quantity.focus();
					return;
				}

				if (this.selectedProduct.Product_Purchase_Rate <= 0) {
					alert('Enter purchase rate');
					return;
				}

				if (parseFloat(this.selectedProduct.quantity) > parseFloat(this.productStock)) {
					alert('Stock unavailable');
					this.$refs.quantity.focus();
					return;
				}

				let existingProduct = this.cart.find(product => product.Product_IDNo == this.selectedProduct.Product_SlNo);
				if (existingProduct) {
					alert('Product already added in cart');
					return;
				} else {
					this.cart.push({
						Product_IDNo: this.selectedProduct.Product_SlNo,
						Product_Code: this.selectedProduct.Product_Code,
						Product_Name: this.selectedProduct.Product_Name,
						return_quantity: parseFloat(this.selectedProduct.quantity),
						return_rate: this.selectedProduct.Product_Purchase_Rate,
						return_amount: this.selectedProduct.total
					});
				}

				this.selectedProduct = {
					Product_SlNo: '',
					Product_Name: '',
					display_text: '',
					Product_Purchase_Rate: 0,
					quantity: 0,
					total: 0
				};

				this.calculateTotal();
			},

			calculateTotal() {
				this.purchaseReturn.total = this.cart.reduce((prev, cur) => {
					return prev + (cur.return_amount ? parseFloat(cur.return_amount) : 0.00)
				}, 0);
			},
			savePurchaseReturn() {
				if (!confirm('Are you sure to save?')) return;
				let filteredCart = this.cart.filter(product => product.return_quantity > 0 && product.return_rate > 0);

				if (filteredCart.length == 0) {
					alert('No products to return');
					return;
				}

				if (this.purchaseReturn.returnDate == null || this.purchaseReturn.returnDate == '') {
					alert('Enter date');
					return;
				}

				if (this.selectedSupplier == null || this.selectedSupplier.Supplier_SlNo == null) {
					alert('Select supplier');
					return;
				}

				this.purchaseReturn.Supplier_SlNo = this.selectedSupplier.Supplier_SlNo;
				let data = {
					purchaseReturn: this.purchaseReturn,
					cart: filteredCart
				}

				let url = '/add_purchase_return';
				if (this.purchaseReturn.returnId != 0) {
					url = '/update_purchase_return';
				}
				this.save_disabled = true;
				axios.post(url, data).then(async res => {
					let r = res.data;
					if (r.success) {
						let conf = confirm('Success. Do you want to view invoice?');
						if (conf) {
							window.open('/purchase_return_invoice/' + r.id, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/purchaseReturns';
						} else {
							window.location = '/purchaseReturns';
						}
					}
				})
			},

			getReturn() {
				axios.post('/get_purchase_returns', {
					id: this.purchaseReturn.returnId
				}).then(async res => {
					let purchaseReturn = res.data.returns[0];
					this.selectedSupplier = {
						Supplier_SlNo: purchaseReturn.Supplier_IDdNo,
						Supplier_Code: purchaseReturn.Supplier_Code,
						Supplier_Name: purchaseReturn.Supplier_Name,
						display_name: `${purchaseReturn.Supplier_Code} - ${purchaseReturn.Supplier_Name}`
					}
					this.purchaseReturn.returnDate = purchaseReturn.PurchaseReturn_ReturnDate;
					this.purchaseReturn.total = purchaseReturn.PurchaseReturn_ReturnAmount;
					this.purchaseReturn.note = purchaseReturn.PurchaseReturn_Description;

					res.data.returnDetails.forEach(detail => {
						this.cart.push({
							Product_IDNo: detail.PurchaseReturnDetailsProduct_SlNo ,
							Product_Code: detail.Product_Code,
							Product_Name: detail.Product_Name,
							return_quantity: detail.PurchaseReturnDetails_ReturnQuantity,
							return_rate: detail.PurchaseReturnDetails_Rate,
							return_amount: detail.PurchaseReturnDetails_ReturnAmount
						});
					});
				})
			}
		}
	})
</script>