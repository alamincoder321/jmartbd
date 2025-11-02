<style>
	.v-select {
		margin-bottom: 5px;
	}

	.v-select.open .dropdown-toggle {
		border-bottom: 1px solid #ccc;
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

	#products label {
		font-size: 13px;
	}

	#products select {
		border-radius: 3px;
	}

	#products .add-button {
		padding: 2.5px;
		width: 28px;
		background-color: #298db4;
		display: block;
		text-align: center;
		color: white;
	}

	#products .add-button:hover {
		background-color: #41add6;
		color: white;
	}

	#products input[type="file"] {
		display: none;
	}

	#products .custom-file-upload {
		border: 1px solid #ccc;
		display: inline-block;
		padding: 5px 12px;
		cursor: pointer;
		margin-top: 5px;
		background-color: #298db4;
		border: none;
		color: white;
	}

	#products .custom-file-upload:hover {
		background-color: #41add6;
	}

	#customerImage {
		height: 100%;
	}

	tr td {
		vertical-align: middle !important;
	}
</style>
<div id="products">
	<form @submit.prevent="saveProduct">
		<div class="row" style="padding-bottom: 3px;margin-bottom: 5px; border-bottom: 1px solid #ccc;">
			<div class="col-md-5">
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Product Id:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_Code">
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Category:</label>
					<div class="col-md-7">
						<v-select v-bind:options="categories" v-model="selectedCategory" v-on:input="getsubCategories" label="ProductCategory_Name"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/category" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Sub Category:</label>
					<div class="col-md-7">
						<v-select v-bind:options="subcategories" v-model="selectedsubCategory" label="ProductsubCategory_Name"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/subcategory" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Product Name:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_Name" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Unit:</label>
					<div class="col-md-7">
						<v-select v-bind:options="units" v-model="selectedUnit" label="Unit_Name"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/unit" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Product Type:</label>
					<div class="col-md-7">
						<select class="form-control" style="padding: 0;" id="productType" v-model="product.productType">
							<option value="">select type</option>
							<option value="discount">Discount</option>
							<option value="offer">Offer</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Supplier:</label>
					<div class="col-md-7">
						<v-select v-bind:options="suppliers" v-model="selectedSupplier" label="display_name"></v-select>
					</div>
					<div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/unit" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
				</div>
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Reorder Level:</label>
					<div class="col-md-7">
						<input type="number" min="0" step="any" id="Product_ReOrederLevel" class="form-control" v-model="product.Product_ReOrederLevel">
					</div>
				</div>
				<div class="form-group clearfix">
					<label class="control-label col-md-4">Purchase Rate:</label>
					<div class="col-md-7">
						<input type="text" id="purchase_rate" class="form-control" v-model="product.Product_Purchase_Rate" required v-bind:disabled="product.is_service ? true : false">
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="control-label col-md-4">Sales Rate:</label>
					<div class="col-md-7">
						<input type="text" class="form-control" v-model="product.Product_SellingPrice" required>
					</div>
				</div>

				<div class="form-group clearfix" v-if="product.productType == 'discount'">
					<label class="control-label col-md-4">Discount Amount:</label>
					<div class="col-md-7">
						<input type="number" min="0" step="any" class="form-control" v-model="product.discountAmount" @input="calculateDiscount" required>
					</div>
				</div>

				<div class="form-group clearfix">
					<div class="col-md-11 text-right">
						<input type="submit" class="btn btn-success btn-sm" value="Save">
					</div>
				</div>
			</div>
			<div class="col-md-2 text-center;">
				<div class="form-group clearfix">
					<div style="width: 100px;height:100px;border: 1px solid #ccc;overflow:hidden;">
						<img id="customerImage" v-if="imageUrl == '' || imageUrl == null" src="/assets/no_image.gif">
						<img id="customerImage" v-if="imageUrl != '' && imageUrl != null" v-bind:src="imageUrl">
					</div>
					<div style="text-align:center;">
						<label class="custom-file-upload">
							<input type="file" @change="previewImage" />
							Select Image
						</label>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6" style="display: none; border: 1px solid gray;padding-top: 5px;border-radius: 5px;" :style="{display: product.productType == 'offer' ? '' : 'none'}" v-if="product.productType == 'offer'">
						<div class="form-group clearfix">
							<label class="control-label col-md-4">Offer Name:</label>
							<div class="col-md-8">
								<input type="text" class="form-control" v-model="offer.name">
							</div>
						</div>
						<div class="form-group clearfix">
							<label class="control-label col-md-4">Date From:</label>
							<div class="col-md-3">
								<input type="date" class="form-control" v-model="offer.dateFrom">
							</div>
							<label class="control-label col-md-2">To:</label>
							<div class="col-md-3">
								<input type="date" class="form-control" v-model="offer.dateTo">
							</div>
						</div>
						<div class="form-group clearfix">
							<label class="control-label col-md-4">Range Quantity:</label>
							<div class="col-md-3">
								<input type="number" min="0" step="any" class="form-control" v-model="offer.range_quantity">
							</div>
							<label class="control-label no-padding col-md-2">Offer Qty:</label>
							<div class="col-md-3">
								<input type="number" min="0" step="any" class="form-control" v-model="offer.offer_quantity">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="row">
		<div class="col-sm-12 form-inline">
			<div class="form-group">
				<label for="filter" class="sr-only">Filter</label>
				<input type="text" class="form-control" v-model="filter" placeholder="Filter">
			</div>
		</div>
		<div class="col-md-12">
			<div class="table-responsive">
				<datatable :columns="columns" :data="products" :filter-by="filter">
					<template scope="{ row }">
						<tr>
							<td>{{ row.sl }}</td>
							<td>
								<a :href="row.imageSrc">
									<img :src="row.imageSrc" style="width: 45px; height: 45px; border: 1px solid gray; border-radius: 5px; padding: 1px;" />
								</a>
							</td>
							<td>{{ row.Product_Code }}</td>
							<td>{{ row.Product_Name }}</td>
							<td>{{ row.ProductCategory_Name }}</td>
							<td>{{ row.ProductsubCategory_Name }}</td>
							<td>{{ row.Supplier_Name }}</td>
							<td>{{ row.Product_Purchase_Rate }}</td>
							<td>{{ row.Product_SellingPrice }}</td>
							<td>{{ row.discountAmount }}</td>
							<td>{{ row.Unit_Name }}</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<button type="button" class="button edit" @click="editProduct(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<button type="button" class="button" @click="deleteProduct(row.Product_SlNo)">
										<i class="fa fa-trash"></i>
									</button>
								<?php } ?>
								<button type="button" class="button" @click="window.location = `/barcode/${row.Product_SlNo}`">
									<i class="fa fa-barcode"></i>
								</button>
							</td>
						</tr>
					</template>
				</datatable>
				<datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
			</div>
		</div>
	</div>


</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#products',
		data() {
			return {
				product: {
					Product_SlNo: '',
					Product_Code: "<?php echo $productCode; ?>",
					Product_Name: '',
					ProductCategory_ID: '',
					ProductsubCategory_ID: '',
					Supplier_SlNo: '',
					brand: '',
					Product_ReOrederLevel: 0,
					Product_Purchase_Rate: 0,
					Product_SellingPrice: 0,
					Product_WholesaleRate: 0,
					Unit_ID: '',
					vat: 0,
					discount: 0,
					discountAmount: 0,
					note: '',
					is_service: false,
					ProductCategory_ID: 0,
					ProductsubCategory_ID: 0,
					productType: ''
				},
				offer: {
					name: '',
					dateFrom: '',
					dateTo: '',
					product_id: '',
					range_quantity: '',
					offer_quantity: ''
				},
				imageUrl: '',
				selectedFile: null,
				products: [],
				categories: [],
				subcategories: [],
				selectedCategory: null,
				selectedsubCategory: null,
				brands: [],
				selectedBrand: null,
				units: [],
				selectedUnit: null,
				suppliers: [],
				selectedSupplier: null,

				columns: [{
						label: 'Sl',
						field: 'sl',
						align: 'center'
					},
					{
						label: 'Image',
						field: 'imageSrc',
						align: 'center',
						filterable: false
					},
					{
						label: 'Product Id',
						field: 'Product_Code',
						align: 'center',
					},
					{
						label: 'Product Name',
						field: 'Product_Name',
						align: 'center'
					},
					{
						label: 'Category',
						field: 'ProductCategory_Name',
						align: 'center'
					},
					{
						label: 'Sub Category',
						field: 'ProductsubCategory_Name',
						align: 'center'
					},
					{
						label: 'Supplier',
						field: 'Supplier_Name',
						align: 'center'
					},
					{
						label: 'Purchase Price',
						field: 'Product_Purchase_Rate',
						align: 'center'
					},
					{
						label: 'Sales Price',
						field: 'Product_SellingPrice',
						align: 'center'
					},
					{
						label: 'Discount Amount',
						field: 'discountAmount',
						align: 'center'
					},
					{
						label: 'Unit',
						field: 'Unit_Name',
						align: 'center'
					},
					{
						label: 'Action',
						align: 'center',
						filterable: false
					}
				],
				page: 1,
				per_page: 20,
				filter: ''
			}
		},
		created() {
			this.getCategories();
			this.getBrands();
			this.getUnits();
			this.getProducts();
			this.getsubCategories();
			this.getSuppliers();
		},
		methods: {
			changeIsService() {
				if (this.product.is_service) {
					this.product.Product_Purchase_Rate = 0;
				}
			},
			getSuppliers() {
				axios.get('/get_suppliers').then(res => {
					this.suppliers = res.data;
				})
			},
			getCategories() {
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			getsubCategories() {
				let url = '/get_subcategories?id=' + (this.selectedCategory ? this.selectedCategory.ProductCategory_SlNo : '');
				axios.get(url).then(res => {
					this.subcategories = res.data;
				});

				if (this.selectedsubCategory && this.selectedCategory) {
					if (this.selectedsubCategory.ProductCategory_ID != this.selectedCategory.ProductCategory_SlNo) {
						this.selectedsubCategory = null;
					}
				}
			},
			getBrands() {
				axios.get('/get_brands').then(res => {
					this.brands = res.data;
				})
			},
			getUnits() {
				axios.get('/get_units').then(res => {
					this.units = res.data;
				})
			},
			getProducts() {
				axios.get('/get_products').then(res => {
					this.products = res.data.map((item, index) => {
						item.imageSrc = item.image_name ? `/uploads/products/${item.image_name}` : '/uploads/noImage.png';
						item.sl = index + 1;
						return item;
					});
				})
			},

			calculateDiscount() {
				if (
					this.product.discountAmount &&
					this.product.Product_SellingPrice &&
					this.product.Product_Purchase_Rate
				) {
					if (parseFloat(this.product.discountAmount) > parseFloat(this.product.Product_SellingPrice - this.product.Product_Purchase_Rate)) {
						this.product.discountAmount = parseFloat(this.product.Product_SellingPrice - this.product.Product_Purchase_Rate).toFixed(2);
					}
					let discountPercent = (parseFloat(this.product.discountAmount) / parseFloat(this.product.Product_SellingPrice)) * 100;
					this.product.discount = discountPercent.toFixed(2);
				} else {
					this.product.discount = 0;
				}
			},

			saveProduct() {
				if (this.selectedCategory == null) {
					alert('Select category');
					return;
				} else {
					this.product.ProductCategory_ID = this.selectedCategory.ProductCategory_SlNo;
				}


				if (this.selectedsubCategory == null) {
					alert('Select sub category');
					return;
				} else {
					this.product.ProductsubCategory_ID = this.selectedsubCategory.ProductCategory_SlNo;
				}
				if (this.selectedSupplier == null) {
					alert('Select supplier');
					return;
				} else {
					this.product.Supplier_SlNo = this.selectedSupplier.Supplier_SlNo;
				}


				if (this.selectedUnit == null) {
					alert('Select unit');
					return;
				} else {
					this.product.Unit_ID = this.selectedUnit.Unit_SlNo;
				}


				if (this.selectedBrand != null) {
					this.product.brand = this.selectedBrand.brand_SiNo;
				}





				// console.log(this.product);




				let fd = new FormData();
				fd.append('image', this.selectedFile);
				fd.append('data', JSON.stringify(this.product));

				let url = '/add_product';
				if (this.product.Product_SlNo != 0) {
					url = '/update_product';
				}
				axios.post(url, fd, {
						onUploadProgress: upe => {
							let progress = Math.round(upe.loaded / upe.total * 100);
						}
					})
					.then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.clearForm();
							this.product.Product_Code = r.productId;
							this.getProducts();
						}
					})

			},
			editProduct(product) {
				let keys = Object.keys(this.product);
				keys.forEach(key => {
					this.product[key] = product[key];
				})

				this.product.is_service = product.is_service == 'true' ? true : false;

				this.selectedCategory = {
					ProductCategory_SlNo: product.ProductCategory_ID,
					ProductCategory_Name: product.ProductCategory_Name
				}




				this.selectedsubCategory = {
					ProductCategory_SlNo: product.ProductsubCategory_ID,
					ProductsubCategory_Name: product.ProductsubCategory_Name,
					ProductCategory_ID: product.ProductCategory_ID
				}

				this.selectedUnit = {
					Unit_SlNo: product.Unit_ID,
					Unit_Name: product.Unit_Name
				}

				if (product.image_name == null || product.image_name == '') {
					this.imageUrl = null;
				} else {
					this.imageUrl = '/uploads/products/' + product.image_name;
				}
			},
			deleteProduct(productId) {
				let deleteConfirm = confirm('Are you sure?');
				if (deleteConfirm == false) {
					return;
				}
				axios.post('/delete_product', {
					productId: productId
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getProducts();
					}
				})
			},
			clearForm() {
				let keys = Object.keys(this.product);
				keys.forEach(key => {
					if (typeof(this.product[key]) == "string") {
						this.product[key] = '';
					} else if (typeof(this.product[key]) == "number") {
						this.product[key] = 0;
					}
				})

				// this.selectedCategory = null;
				// this.selectedsubCategory = null;
				// this.selectedUnit = null;
				// this.selectedBrand = null;

				this.imageUrl = '';
				this.selectedFile = null;
			},
			previewImage(event) {
				const WIDTH = 200;
				const HEIGHT = 200;
				if (event.target.files[0]) {
					let reader = new FileReader();
					reader.readAsDataURL(event.target.files[0]);
					reader.onload = (ev) => {
						let img = new Image();
						img.src = ev.target.result;
						img.onload = async e => {
							let canvas = document.createElement('canvas');
							canvas.width = WIDTH;
							canvas.height = HEIGHT;
							const context = canvas.getContext("2d");
							context.drawImage(img, 0, 0, canvas.width, canvas.height);
							let new_img_url = context.canvas.toDataURL(event.target.files[0].type);
							this.imageUrl = new_img_url;
							const resizedImage = await new Promise(rs => canvas.toBlob(rs, 'image/jpeg', 1))
							this.selectedFile = new File([resizedImage], event.target.files[0].name, {
								type: resizedImage.type
							});
						}
					}
				} else {
					event.target.value = '';
				}
			},
		}
	})
</script>