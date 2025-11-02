<style>
    .v-select {
        margin-top: -2.5px;
        float: right;
        min-width: 220px;
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

    .card {
        border: 1px solid gray;
        border-radius: 5px;
        padding: 5px;
    }

    .card-header {
        background: gainsboro;
        padding: 10px 10px;
    }

    .card-header .card-title {
        margin: 0;
    }

    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
<div id="exchanges">
    <div class="row" style="border: 1px solid gray; padding: 8px 0px; border-radius: 5px; box-shadow: 0px 0px 0px 3px #007ebb; margin: 0;">
        <div class="col-md-12">
            <form class="form-inline" id="searchForm" @submit.prevent="getExchangeSale">
                <div class="form-group">
                    <label style="margin: 0;margin-top: -5px;">Customer</label>
                    <v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" @input="onChangeCustomer" @search="onSearchCustomer"></v-select>
                </div>

                <div class="form-group">
                    <label style="margin: 0;margin-top: -5px;">Invoice</label>
                    <v-select v-bind:options="invoices" v-model="selectedInvoice" label="invoice_text" @search="onSearchSale"></v-select>
                </div>

                <div class="form-group" style="margin-top: -2px;">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </div>
    <div class="row" style="margin-top: 8px;">
        <div class="col-xs-12 col-md-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Sales Product Information</h4>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered table-striped" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>UnitPrice</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in sales.saleDetails" v-if="sales.saleDetails.length > 0" style="display: none;" :style="{display: sales.saleDetails.length > 0 ? '' : 'none'}">
                                <td v-text="index + 1"></td>
                                <td style="text-align: left;" v-text="`${item.Product_Name} - ${item.Product_Code}`"></td>
                                <td v-text="item.SaleDetails_TotalQuantity"></td>
                                <td v-text="item.SaleDetails_Rate"></td>
                                <td v-text="item.SaleDetails_TotalAmount"></td>
                                <td>
                                    <input type="checkbox" v-show="item.is_exchange == 'false'" @change="addToCart(item)" v-model="item.isExchange" :true-value="`yes`" :false-value="`no`" style="cursor:pointer;width: 18px; height: 18px; margin:0;">
                                    <span v-show="item.is_exchange != 'false'">Already Exchange</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card" style="margin-top: 5px;display:none;" :style="{display: sales.saleDetails.filter(item => item.isExchange == 'yes').length > 0 ? '' : 'none'}" v-show="sales.saleDetails.filter(item => item.isExchange == 'yes').length > 0">
                <div class="card-header">
                    <h4 class="card-title">Exchange Product Information</h4>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered table-striped" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>UnitPrice</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in carts" :key="index">
                                <td v-text="index + 1"></td>
                                <td style="text-align: left;">
                                    <v-select style="margin: 0 !important;width:100%;" :options="products" v-model="item.selectedProduct" label="display_text" @input="updateCartItem(item)" @search="onSearchProduct"></v-select>
                                </td>
                                <td>
                                    <input type="number" min="0" step="any" v-model="item.quantity" style="padding: 2px 5px; text-align: center;" @input="updateQtyRate(item)">
                                </td>
                                <td>
                                    <input type="number" min="0" step="any" v-model="item.sale_rate" style="padding: 2px 5px; text-align: center;" @input="updateQtyRate(item)">
                                </td>
                                <td v-text="(item.total = (item.quantity * item.sale_rate).toFixed(2))"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Amount Information</h4>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered table-striped" style="margin-bottom: 0;">
                        <tbody>
                            <tr>
                                <td style="text-align: left;">Date</td>
                                <td style="text-align: right;">
                                    <input type="date" v-model="exchange.date" class="form-control">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">Sale Total</td>
                                <td style="text-align: right;" v-text="exchange.saletotal"></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">Exc. Amount</td>
                                <td style="text-align: right;" v-text="exchange.exchangeAmount"></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">Total</td>
                                <td style="text-align: right;" v-text="exchange.total"></td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">CashPaid</td>
                                <td style="text-align: right;">
                                    <input type="number" min="0" step="any" class="form-control" style="margin: 0;" v-model="exchange.cashPaid">
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;">BankPaid</td>
                                <td style="text-align: right;">
                                    <input type="number" min="0" step="any" class="form-control" style="margin: 0;" v-model="exchange.bankPaid">
                                </td>
                            </tr>
                            <tr style="background: #fff;display: none;" :style="{display: exchange.bankPaid > 0 ? '' : 'none'}" v-show="exchange.bankPaid > 0">
                                <td colspan="2" style="padding: 3px 4px;">
                                    <v-select style="width: 100%;margin:0;" v-bind:options="banks" v-model="selectedBank" label="display_name" placeholder="Select Bank Name"></v-select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="button" @click="saveExchange" style="width: 100%;">Save Exchange</button>
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
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#exchanges',
        data() {
            return {
                products: [],
                customers: [],
                selectedCustomer: {
                    Customer_SlNo: '',
                    Customer_Code: '',
                    Customer_Name: '',
                    display_name: 'select customer',
                    Customer_Mobile: '',
                    Customer_Address: '',
                    Customer_Type: '',
                },
                invoices: [],
                selectedInvoice: null,
                exchange: {
                    date: moment().format('YYYY-MM-DD'),
                    saletotal: 0,
                    exchangeAmount: 0,
                    total: 0,
                    cashPaid: 0,
                    bankPaid: 0,
                },
                sales: {
                    saleDetails: []
                },
                carts: [],
                banks: [],
                selectedBank: null,
            }
        },
        created() {
            this.getBank();
            this.getCustomers();
            this.getSales();
            this.getProducts();
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
            getProducts() {
                axios.post('/get_products', {
                    isService: 'false',
                    forSearch: 'yes'
                }).then(res => {
                    this.products = res.data;
                })
            },
            async onSearchProduct(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get_products", {
                            name: val,
                            isService: 'false',
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
            async getCustomers() {
                await axios.post('/get_customers', {
                    forSearch: 'yes'
                }).then(res => {
                    this.customers = res.data;
                    this.customers.unshift({
                        Customer_SlNo: '',
                        Customer_Code: '',
                        Customer_Name: 'General Customer',
                        display_name: 'General Customer',
                        Customer_Mobile: '',
                        Customer_Address: '',
                        Customer_Type: 'G'
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
            onChangeCustomer() {
                this.selectedInvoice = null;
                this.sales = {
                    saleDetails: []
                }
                if (this.selectedCustomer == null) {
                    this.selectedCustomer = {
                        Customer_SlNo: '',
                        Customer_Code: '',
                        Customer_Name: '',
                        display_name: 'select customer',
                        Customer_Mobile: '',
                        Customer_Address: '',
                        Customer_Type: '',
                    };
                    return;
                }
                if (this.selectedCustomer.Customer_Type != '') {
                    this.getSales('yes');
                }
            },
            getSales(search = null) {
                let filter = {
                    customerId: this.selectedCustomer.Customer_SlNo != '' ? this.selectedCustomer.Customer_SlNo : null,
                }
                if (this.selectedCustomer.Customer_Type == 'G') {
                    filter.customerType = 'G';
                }
                if (search == null) {
                    filter.forSearch = 'yes';
                }
                axios.post('/get_sales', filter)
                    .then(res => {
                        this.invoices = res.data.sales;
                    })
            },
            async onSearchSale(val, loading) {
                if (val.length > 3) {
                    loading(true);
                    await axios.post("/get_sales", {
                            name: val,
                        })
                        .then(res => {
                            let r = res.data;
                            this.invoices = r.filter(item => item.status == 'a')
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getSales();
                }
            },
            getExchangeSale() {
                if (this.selectedInvoice == null) {
                    alert("Please select an invoice to exchange.");
                    return;
                }
                let filter = {
                    customerId: this.selectedCustomer ? this.selectedCustomer.Customer_SlNo : null,
                    saleId: this.selectedInvoice ? this.selectedInvoice.SaleMaster_SlNo : null
                }
                axios.post('/get_sales_record', filter)
                    .then(res => {
                        this.sales = res.data.map(sale => {
                            sale.saleDetails = sale.saleDetails.map(item => {
                                item.isExchange = 'no';
                                return item;
                            });
                            return sale;
                        })[0];
                    })

            },

            addToCart(item) {
                if (item.isExchange == 'yes') {
                    let cartItem = {
                        sale_detail_id: item.SaleDetails_SlNo,
                        selectedProduct: null,
                        product_id: "",
                        quantity: 1,
                        sale_rate: 0,
                        purchase_rate: 0,
                        total: 0,
                    }
                    this.carts.push(cartItem);
                    this.calculateTotal();
                } else {
                    let index = this.carts.findIndex(cart => cart.sale_detail_id == item.SaleDetails_SlNo);
                    if (index > -1) {
                        this.carts.splice(index, 1);
                    }
                    this.calculateTotal();
                }

            },

            async updateCartItem(item) {
                if (item.selectedProduct == null) {
                    item.product_id = "";
                    return;
                }
                let productStock = await axios.post('/get_product_stock', {
                    productId: item.selectedProduct.Product_SlNo
                }).then(res => {
                    return res.data;
                })
                item.quantity = item.quantity;
                if (productStock < item.quantity) {
                    alert("Product stock is unavailable.");
                    item.quantity = 1;
                    item.prodcut_id = "";
                    item.selectedProduct = null;
                    return;
                }
                item.product_id = item.selectedProduct.Product_SlNo;
                item.purchase_rate = item.selectedProduct.Product_Purchase_Rate;
                item.sale_rate = item.selectedProduct.Product_SellingPrice;
                item.total = (item.quantity * item.sale_rate).toFixed(2);

                this.calculateTotal();
            },

            async updateQtyRate(item) {
                let productStock = await axios.post('/get_product_stock', {
                    productId: item.selectedProduct.Product_SlNo
                }).then(res => {
                    return res.data;
                })
                if (productStock < item.quantity) {
                    alert("Product stock is unavailable.");
                    item.quantity = 1;
                    item.prodcut_id = "";
                    item.selectedProduct = null;
                    return;
                }
                item.total = (item.quantity * item.sale_rate).toFixed(2);
                this.calculateTotal();
            },

            calculateTotal() {
                this.exchange.saletotal = this.sales.saleDetails.filter(item => item.isExchange == 'yes').reduce((acc, item) => acc + parseFloat(item.SaleDetails_TotalAmount), 0).toFixed(2);
                this.exchange.exchangeAmount = this.carts.reduce((acc, item) => acc + parseFloat(item.total), 0).toFixed(2);
                this.exchange.total = (this.exchange.exchangeAmount - this.exchange.saletotal).toFixed(2);
                if (this.exchange.total < 0) {
                    this.exchange.total = parseFloat(0).toFixed(2);
                }
            },

            saveExchange() {
                if (this.carts.length == 0) {
                    alert("Cart is empty.")
                    return;
                }
                for (let i = 0; i < this.carts.length; i++) {
                    if (this.carts[i].product_id == "") {
                        alert("Please select product for exchange.");
                        return;
                    }
                }
                if (this.exchange.saletotal > this.exchange.exchangeAmount) {
                    alert("Exchange amount cannot be less than sale amount.");
                    return;
                }
                if ((+this.exchange.cashPaid + +this.exchange.bankPaid) > this.exchange.total) {
                    alert("Paid amount cannot be greater than total amount.");
                    return;
                }
                if ((+this.exchange.cashPaid + +this.exchange.bankPaid) < this.exchange.total) {
                    alert("Paid amount cannot be less than total amount.");
                    return;
                }
                if (this.exchange.bankPaid > 0 && this.selectedBank == null) {
                    alert("Please select bank account.");
                    return;
                }
                this.exchange.bank_id = this.selectedBank ? this.selectedBank.account_id : null;

                let formdata = new FormData();
                formdata.append('exchange', JSON.stringify(this.exchange));
                formdata.append('carts', JSON.stringify(this.carts));
                formdata.append('sales', JSON.stringify(this.sales));

                axios.post('/add_exchange', formdata)
                    .then(res => {
                        if (res.data.success) {
                            alert(res.data.message);
                            this.clearData();
                        }
                    })
            },

            clearData() {
                this.sales = {
                    saleDetails: []
                }
                this.carts = []
                this.exchange = {
                    date: moment().format('YYYY-MM-DD'),
                    saletotal: 0,
                    exchangeAmount: 0,
                    total: 0,
                    cashPaid: 0,
                    bankPaid: 0,
                };
            }
        }
    })
</script>