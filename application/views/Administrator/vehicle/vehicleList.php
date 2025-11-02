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

    tr th,
    tr td {
        vertical-align: middle !important;
    }
</style>
<div id="priceList">
    <div class="row" style="border-bottom: 1px solid #ccc;padding: 5px 0;">
        <div class="col-md-12">
            <form class="form-inline" @submit.prevent="getVehicles">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" v-model="searchType">
                        <option value="">All</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: -5px;">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </div>

    <div class="row" style="display:none;margin-top: 15px;" v-bind:style="{display: vehicles.length > 0 ? '' : 'none'}">
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
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered table-condensed" id="priceListTable">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Vehicle Name</th>
                            <th>Vehicle Type</th>
                            <th>Model Year</th>
                            <th>Manufacturer Name</th>
                            <th>TAX Token Number</th>
                            <th>TAX Token Expire</th>
                            <th>Road Permit Number</th>
                            <th>Road Permit Expire</th>
                            <th>Insurance Number</th>
                            <th>Insurance Expiry</th>
                            <th>Buy Price</th>
                            <th>Kilometer Run</th>
                            <th>Per Liter KM</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, sl) in vehicles">
                            <td>{{ sl + 1 }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.vehicle_type }}</td>
                            <td>{{ item.model_year }}</td>
                            <td>{{ item.manufacturer_name }}</td>
                            <td>{{ item.tax_token_number }}</td>
                            <td>{{ item.tax_token_expiry }}</td>
                            <td>{{ item.road_permit_number }}</td>
                            <td>{{ item.road_permit_expiry }}</td>
                            <td>{{ item.insurance_number }}</td>
                            <td>{{ item.insurance_expiry }}</td>
                            <td>{{ item.buy_price }}</td>
                            <td>{{ item.kilometers_run }}</td>
                            <td>{{ item.per_liter_km }}</td>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#priceList',
        data() {
            return {
                searchType: '',
                vehicles: [],
            }
        },
        methods: {
            getVehicles() {
                axios.get('/get_vehicles').then(res => {
                    this.vehicles = res.data;
                })
            },
            async print() {
                let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h3 style="text-align:center">Vehicle List</h3>
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
            },

            excelExport() {
                let onlyData = this.vehicles.map(item => {
                    return {
                        'Vehicle Name': item.name,
                        'Vehicle Type': item.vehicle_type,
                        'Model Year': item.model_year,
                        'Manufacturer Name': item.manufacturer_name,
                        'TAX Token Number': item.tax_token_number,
                        'TAX Token Expire': item.tax_token_expiry,
                        'Road Permit Number': item.road_permit_number,
                        'Road Permit Expire': item.road_permit_expiry,
                        'Insurance Number': item.insurance_number,
                        'Insurance Expiry': item.insurance_expiry,
                        'Buy Price': item.buy_price,
                        'Kilometer Run': item.kilometers_run,
                        'Per Liter KM': item.per_liter_km,
                    };
                })

                const worksheet = XLSX.utils.json_to_sheet(onlyData);
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, "Skipped Rows");
                // Excel download
                XLSX.writeFile(workbook, "VehiceList.xlsx");
            }
        }
    })
</script>