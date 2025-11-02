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
<div id="customerListReport">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
            <form class="form-inline">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" style="width:150px;" v-model="searchType" v-on:change="onChangeSearchType" style="padding:0px;">
                        <option value="">All</option>
                        <option value="employee">By Employee</option>
                        <option value="area">By Area</option>
                    </select>
                </div>
                <div class="form-group" style="display: none" v-bind:style="{display: searchType == 'employee' ? '' : 'none'}">
                    <label>Select Employee</label>
                    <v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select employee"></v-select>
                </div>
                <div class="form-group" style="display: none" v-bind:style="{display: searchType == 'area' ? '' : 'none'}">
                    <label>Select Area</label>
                    <v-select v-bind:options="areas" v-model="selectedArea" label="District_Name" placeholder="Select area"></v-select>
                </div>

                <div class="form-group">
                    <input type="button" class="btn btn-primary" value="Show Report" v-on:click="getCustomers" style="margin-top: -4px; border: 0px; padding: 3px 6px;">
                </div>
            </form>
        </div>
    </div>
    <div style="display:none;" v-bind:style="{display: customers.length > 0 ? '' : 'none'}">
        <div class="row">
            <div class="col-md-12">
                <a href="" @click.prevent="printCustomerList"><i class="fa fa-print"></i> Print</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" id="printContent">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <th>Sl</th>
                            <th>Customer Id</th>
                            <th>Customer Name</th>
                            <th>Address</th>
                            <th>Contact No.</th>
                        </thead>
                        <tbody>
                            <tr v-for="(customer, sl) in customers">
                                <td>{{ sl + 1 }}</td>
                                <td>{{ customer.Customer_Code }}</td>
                                <td>{{ customer.Customer_Name }}</td>
                                <td>{{ customer.Customer_Address }} {{ customer.District_Name }}</td>
                                <td>{{ customer.Customer_Mobile }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div style="display:none;text-align:center;" v-bind:style="{display: customers.length > 0 ? 'none' : ''}">
        No records found
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#customerListReport',
        data() {
            return {
                searchType: '',
                employees: [],
                selectedEmployee: null,
                areas: [],
                selectedArea: null,
                customers: [],
            }
        },
        created() {
            this.getCustomers();
        },
        methods: {
            onChangeSearchType() {
                this.selectedEmployee = null;
                this.selectedArea = null;
                if (this.searchType == 'area') {
                    this.getAreas();
                } else if (this.searchType == 'employee') {
                    this.getEmployees();
                }
            },
            getEmployees() {
                axios.get('/get_employees').then(res => {
                    this.employees = res.data.map(item => {
                        item.display_name = `${item.Employee_Name} - ${item.Employee_ID}`;
                        return item;
                    });
                })
            },
            getAreas() {
                axios.get('/get_districts').then(res => {
                    this.areas = res.data;
                })
            },

            getCustomers() {
                let filter = {
                    areaId: this.selectedArea == null ? null : this.selectedArea.District_SlNo,
                    employeeId: this.selectedEmployee == null ? null : this.selectedEmployee.Employee_SlNo
                }
                axios.post('/get_customers', filter).then(res => {
                    this.customers = res.data;
                })
            },

            async printCustomerList() {
                let printContent = `
                    <div class="container">
                        <h4 style="text-align:center">Customer List</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#printContent').innerHTML}
							</div>
						</div>
                    </div>
                `;

                let printWindow = window.open('', '', `width=${screen.width}, height=${screen.height}`);
                printWindow.document.write(`
                    <?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
                `);

                printWindow.document.body.innerHTML += printContent;
                printWindow.focus();
                await new Promise(r => setTimeout(r, 1000));
                printWindow.print();
                printWindow.close();
            }
        }
    })
</script>