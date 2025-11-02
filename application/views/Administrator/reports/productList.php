<style scoped>
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
<div id="productList">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
            <form class="form-inline">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" style="width:150px;" v-model="searchType" v-on:change="onChangeSearchType" style="padding:0px;">
                        <option value="">All</option>
                        <option value="category">By Category</option>
                        <option value="discount">Discount Product</option>
                    </select>
                </div>
                <div class="form-group" style="display: none" v-bind:style="{display: searchType == 'category' ? '' : 'none'}">
                    <label>Select Category</label>
                    <v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name" placeholder="Select category"></v-select>
                </div>

                <div class="form-group">
                    <input type="button" class="btn btn-primary" value="Show Report" v-on:click="getProducts" style="margin-top: -4px; border: 0px; padding: 3px 6px;">
                </div>
            </form>
        </div>
    </div>
    <div style="display:none;" v-bind:style="{display: products.length > 0 ? '' : 'none'}">
        <div class="row">
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
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" id="reportTable">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Product Id</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th v-if="searchType == 'discount'">Discount(%)</th>
                                <th v-if="searchType == 'discount'">Discount Amount</th>
                                <th style="text-align: right;">Sale Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, sl) in products">
                                <td style="text-align:center;">{{ sl + 1 }}</td>
                                <td>{{ product.Product_Code }}</td>
                                <td>{{ product.Product_Name }}</td>
                                <td>{{ product.ProductCategory_Name }}</td>
                                <td v-if="searchType == 'discount'">{{ product.discount }}</td>
                                <td v-if="searchType == 'discount'">{{ parseFloat((product.Product_SellingPrice * product.discount) / 100).toFixed(2) }}</td>
                                <td style="text-align:right;">{{ product.Product_SellingPrice }}</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#productList',
        data() {
            return {
                searchType: '',
                categories: [],
                selectedCategory: null,
                areas: [],
                products: [],
            }
        },
        methods: {
            onChangeSearchType() {
                this.selectedCategory = null;
                if (this.searchType == 'category') {
                    this.getCategory();
                }
            },
            getCategory() {
                axios.get('/get_categories').then(res => {
                    this.categories = res.data;
                })
            },
            getProducts() {
                let filter = {
                    categoryId: this.selectedCategory == null ? null : this.selectedCategory.ProductCategory_SlNo
                }
                if (this.searchType == 'discount') {
                    filter.discount = 'yes';
                }
                axios.post('/get_products', filter).then(res => {
                    this.products = res.data;
                })
            },
            async print() {
                let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 style="text-align:center">${this.searchType == 'discount' ? 'Discount ' : ''}Product List</h4 style="text-align:center">
                            </div>
                        </div>
					</div>
					<div class="container">
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

                await new Promise(resolve => setTimeout(resolve, 3000));
                mywindow.print();
                mywindow.close();
            },

            excelExport() {
                let onlyData = this.products.map(item => {
                    return {
                        'Product Id': item.Product_Code,
                        'Product Name': item.Product_Name,
                        'Category': item.ProductCategory_Name,
                        'Sale Price': item.Product_SellingPrice,
                        ...(this.searchType == 'discount' ? {
                            'Discount(%)': item.discount,
                            'Discount Amount': parseFloat((item.Product_SellingPrice * item.discount) / 100).toFixed(2)
                        } : {})
                    }
                })

                const worksheet = XLSX.utils.json_to_sheet(onlyData);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Skipped Rows");
                // Excel download
                XLSX.writeFile(workbook, "ProductList.xlsx");
            }
        }
    })
</script>