<style>
    .v-select {
        margin-bottom: 5px;
        float: right;
        min-width: 200px;
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
<div id="discountFixed">
    <div class="row">
        <div class="col-md-12" style="margin: 0;">
            <form class="form-inline" @submit.prevent="getProducts">
                <div class="form-group">
                    <label for="category">Category</label>
                    <v-select :options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
                </div>
                <div class="form-group" style="margin-top: -1px;">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
        </fieldset>
    </div>

    <div class="row" style="display:none;" v-bind:style="{display: products.length > 0 ? '' : 'none'}">
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered table-hover" id="discountFixedTable">
                    <thead>
                        <tr>
                            <th rowspan="2">Sl</th>
                            <th rowspan="2">Product Code</th>
                            <th rowspan="2">Product Name</th>
                            <th rowspan="2">PurchaseRate</th>
                            <th rowspan="2">SaleRate</th>
                            <th rowspan="2" colspan="2">Discount Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(product, sl) in products">
                            <td>{{ sl + 1 }}</td>
                            <td>{{ product.Product_Code }}</td>
                            <td style="text-align: left;">{{ product.Product_Name }}</td>
                            <td style="text-align: left;">{{ product.Product_Purchase_Rate }}</td>
                            <td style="text-align: left;">{{ product.Product_SellingPrice }}</td>
                            <td style="text-align:right;">
                                <input type="number" min="0" step="any" style="margin: 0;" class="form-control text-center" v-model="product.discountAmount" @input="calculateDiscount(product)">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-center">
                                <button type="button" @click="saveDiscount" style="padding: 4px 13px;margin: 3px 0;">Submit</button>
                            </td>
                        </tr>
                    </tbody>
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
        el: '#discountFixed',
        data() {
            return {
                searchType: '',
                categories: [],
                selectedCategory: null,
                products: [],
                branches: [],
                selectedBranch: null
            }
        },
        created() {
            this.getCategory();
        },
        methods: {
            getCategory() {
                axios.get("/get_categories")
                    .then(res => {
                        this.categories = res.data;
                    })
            },
            getProducts() {
                let data = {
                    categoryId: this.selectedCategory ? this.selectedCategory.ProductCategory_SlNo : '',
                }
                axios.post('/get_check_discount_product', data).then(res => {
                    this.products = res.data;
                })
            },

            calculateDiscount(product) {
                if (
                    product.discountAmount &&
                    product.Product_SellingPrice &&
                    product.Product_Purchase_Rate
                ) {
                    if (parseFloat(product.discountAmount) > parseFloat(product.Product_SellingPrice - product.Product_Purchase_Rate)) {
                        product.discountAmount = parseFloat(product.Product_SellingPrice - product.Product_Purchase_Rate).toFixed(2);
                    }
                    let discountPercent = (parseFloat(product.discountAmount) / parseFloat(product.Product_SellingPrice)) * 100;
                    product.discount = discountPercent.toFixed(2);
                } else {
                    product.discount = 0;
                }
            },

            saveDiscount() {
                let data = {
                    products: this.products
                }
                axios.post("/add_product_discount", data)
                    .then(res => {
                        if (res.data.status) {
                            alert(res.data.message);
                            this.products = [];
                        }
                    })
            }
        }
    })
</script>