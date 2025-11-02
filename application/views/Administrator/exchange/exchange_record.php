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

    #priceList label {
        font-size: 13px;
        margin-top: 3px;
    }

    #priceList select {
        border-radius: 3px;
        padding: 0px;
        font-size: 13px;
    }

    #priceList .form-group {
        margin-right: 10px;
    }

    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
<div id="priceList">
    <div class="row" style="border-bottom: 1px solid #ccc;padding: 5px 0;">
        <div class="col-md-12">
            <form class="form-inline" @submit.prevent="getReports">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" v-model="searchType">
                        <option value="">All</option>
                        <option value="customer">By Customer</option>
                    </select>
                </div>

                <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'customer' ? '' : 'none'}">
                    <label>Customer</label>
                    <v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" @search="onSearchCustomer" placeholder="select customer"></v-select>
                </div>
                <div class="form-group">
                    <label>RecordType</label>
                    <select class="form-control" v-model="recordType" @change="onChangeRecordType">
                        <option value="without">Without Detail</option>
                        <option value="with">With Detail</option>
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

    <div class="row" style="display:none;margin-top: 15px;" v-bind:style="{display: exchanges.length > 0 ? '' : 'none'}">
        <div class="col-md-12" style="margin-bottom: 10px;">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table v-show="recordType=='with'" :style="{display: recordType=='with' ? '' : 'none'}" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(exchange, sl) in exchanges">
                            <tr>
                                <td>{{ sl + 1 }}</td>
                                <td>{{ exchange.invoice }}</td>
                                <td>{{ exchange.date }}</td>
                                <td style="text-align: left;">{{ exchange.Customer_Name }}</td>
                                <td style="text-align:left;">{{ exchange.exchangeDetails[0].Product_Name }}</td>
                                <td style="text-align:right;">{{ exchange.exchangeDetails[0].quantity }}</td>
                                <td style="text-align:right;">{{ exchange.exchangeDetails[0].sale_rate }}</td>
                                <td style="text-align:right;">{{ exchange.exchangeDetails[0].total }}</td>
                            </tr>
                            <tr v-for="(item, ind) in exchange.exchangeDetails.slice(1)">
                                <td v-if="ind == 0" colspan="4" :rowspan="exchange.exchangeDetails.length - 1"></td>
                                <td style="text-align:left;">{{ item.Product_Name }}</td>
                                <td style="text-align:right;">{{ item.quantity }}</td>
                                <td style="text-align:right;">{{ item.sale_rate }}</td>
                                <td style="text-align:right;">{{ item.total }}</td>
                            </tr>
                            <tr>
                                <td colspan="7">
                                    <strong>Total</strong>
                                </td>
                                <td style="text-align:right;">
                                    <strong>SaleTotal:</strong> {{ exchange.saletotal }}<br>
                                    <strong>ExchangeTotal:</strong> {{ exchange.exchangeAmount }}<br>
                                    <strong>Total:</strong> {{ exchange.total }}
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <table v-show="recordType=='without'" :style="{display: recordType=='without' ? '' : 'none'}" :style="{display: }" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>SaleTotal</th>
                            <th>ExchangeTotal</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(exchange, sl) in exchanges">
                            <td>{{ sl + 1 }}</td>
                            <td>{{ exchange.invoice }}</td>
                            <td>{{ exchange.date }}</td>
                            <td style="text-align: left;">{{ exchange.Customer_Name }}</td>
                            <td style="text-align:right;">{{ exchange.saletotal }}</td>
                            <td style="text-align:right;">{{ exchange.exchangeAmount }}</td>
                            <td style="text-align:right;">{{ exchange.total }}</td>
                            <td>
                                <button type="button" @click="deleteData(exchange)" class="badge badge-danger">delete</button>
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
        el: '#priceList',
        data() {
            return {
                searchType: '',
                recordType: 'without',
                dateFrom: moment().format("YYYY-MM-DD"),
                dateTo: moment().format("YYYY-MM-DD"),
                exchanges: [],
                selectedProduct: null,
                customers: [],
                selectedCustomer: null
            }
        },
        created() {
            this.getCustomers();
        },
        methods: {
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
            onChangeRecordType() {
                this.exchanges = [];
            },
            getReports() {
                let data = {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo,
                    customerId: this.selectedCustomer ? this.selectedCustomer.Customer_SlNo : null
                };
                axios.post('/get_exchanges', data).then(res => {
                    this.exchanges = res.data;
                })
            },
            deleteData(exchange) {
                if (confirm("Are you sure you want to delete this exchange?")) {
                    axios.post('/delete_exchange', {
                        exchangeId: exchange.id
                    }).then(res => {
                        alert(res.data.message);
                        if (res.data.success) {
                            this.getReports();
                        }
                    })
                }
            },
            async print() {
                let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h3 style="text-align:center">Exchange Record</h3>
                            </div>
                        </div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}, left=0, top=0`);
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.body.innerHTML += reportContent;

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>