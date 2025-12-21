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

    #customers label {
        font-size: 13px;
    }

    #customers select {
        border-radius: 3px;
    }

    #customers .add-button {
        padding: 2.5px;
        width: 28px;
        background-color: #298db4;
        display: block;
        text-align: center;
        color: white;
    }

    #customers .add-button:hover {
        background-color: #41add6;
        color: white;
    }

    #customers input[type="file"] {
        display: none;
    }

    #customers .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 5px 12px;
        cursor: pointer;
        margin-top: 5px;
        background-color: #298db4;
        border: none;
        color: white;
    }

    #customers .custom-file-upload:hover {
        background-color: #41add6;
    }

    #customerImage {
        height: 100%;
    }
</style>
<div id="customers">
    <form @submit.prevent="saveCustomer">
        <div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom:15px;">
            <div class="col-md-5">
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Customer Id:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Code" required readonly>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Customer Name:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Name" required>
                    </div>
                </div>

                <div class="form-group clearfix" style="display: none;">
                    <label class="control-label col-md-4">Owner Name:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.owner_name">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Address:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Address">
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Employee:</label>
                    <div class="col-md-7" style="display: flex;align-items:center;margin-bottom:5px;">
                        <div style="width: 86%;">
                            <v-select v-bind:options="employees" style="margin:0;" v-model="selectedEmployee" label="display_name"></v-select>
                        </div>
                        <div style="width:13%;margin-left:2px;">
                            <span class="add-button" onclick="window.open('/employee', '_blank')"><i class="fa fa-plus"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Mobile:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="customer.Customer_Mobile" required>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Previous Due:</label>
                    <div class="col-md-7">
                        <input type="number" step="any" class="form-control" v-model="customer.previous_due" required>
                    </div>
                </div>


                <div class="form-group clearfix">
                    <div class="col-md-7 col-md-offset-4 text-right">
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
                <datatable :columns="columns" :data="customers" :filter-by="filter" style="margin-bottom: 5px;">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.sl }}</td>
                            <td>{{ row.Customer_Code }}</td>
                            <td>{{ row.Customer_Name }}</td>
                            <td>{{ row.Customer_Mobile }}</td>
                            <td>{{ row.Customer_Address }}</td>
                            <td>{{row.previous_due}}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <button type="button" class="button edit" @click="editCustomer(row)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="button" @click="deleteCustomer(row.Customer_SlNo)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
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
        el: '#customers',
        data() {
            return {
                customer: {
                    Customer_SlNo: 0,
                    Customer_Code: '<?php echo $customerCode; ?>',
                    Customer_Name: '',
                    Customer_Type: 'retail',
                    Customer_Phone: '',
                    Customer_Mobile: '',
                    Customer_Email: '',
                    Customer_OfficePhone: '',
                    Customer_Address: '',
                    owner_name: '',
                    area_ID: '',
                    Customer_Credit_Limit: 0,
                    previous_due: 0
                },
                customers: [],
                employees: [],
                selectedEmployee: null,
                districts: [],
                selectedDistrict: null,
                imageUrl: '',
                selectedFile: null,

                columns: [{
                        label: 'Sl',
                        field: 'sl',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Customer Id',
                        field: 'Customer_Code',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Customer Name',
                        field: 'Customer_Name',
                        align: 'center'
                    },
                    {
                        label: 'Contact Number',
                        field: 'Customer_Mobile',
                        align: 'center'
                    },
                    {
                        label: 'Address',
                        field: 'Customer_Address',
                        align: 'center'
                    },
                    {
                        label: 'Previous Due',
                        field: 'previous_due',
                        align: 'center'
                    },
                    {
                        label: 'Action',
                        align: 'center',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 100,
                filter: ''
            }
        },
        created() {
            this.getEmployees();
            this.getCustomers();
        },
        methods: {
            getEmployees() {
                axios.get('/get_employees').then(res => {
                    this.employees = res.data;
                })
            },
            getCustomers() {
                axios.get('/get_international_customers').then(res => {
                    this.customers = res.data.map((item, index) => {
                        item.sl = index + 1;
                        return item;
                    });
                })
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
            saveCustomer() {
                this.customer.area_ID = null;
                this.customer.employee_id = this.selectedEmployee ? this.selectedEmployee.Employee_SlNo : null;

                let url = '/add_international_customer';
                if (this.customer.Customer_SlNo != 0) {
                    url = '/update_international_customer';
                }

                let fd = new FormData();
                fd.append('image', this.selectedFile);
                fd.append('data', JSON.stringify(this.customer));

                axios.post(url, fd).then(res => {
                    let r = res.data;
                    if (r.success) {
                        alert(r.message);
                        this.resetForm();
                        this.customer.Customer_Code = r.customerCode;
                        this.getCustomers();
                    } else {
                        if (confirm(r.message)) {
                            this.customer.confirmation = 'yes';
                            this.saveCustomer();
                        }
                    }
                })
            },
            editCustomer(customer) {
                let keys = Object.keys(this.customer);
                keys.forEach(key => {
                    this.customer[key] = customer[key];
                })

                this.selectedDistrict = {
                    District_SlNo: customer.area_ID,
                    District_Name: customer.District_Name
                }

                if (customer.image_name == null || customer.image_name == '') {
                    this.imageUrl = null;
                } else {
                    this.imageUrl = '/uploads/customers/' + customer.image_name;
                }
            },
            deleteCustomer(customerId) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_international_customer', {
                    customerId: customerId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getCustomers();
                    }
                })
            },
            resetForm() {
                this.customer = {
                    Customer_SlNo: 0,
                    Customer_Code: '',
                    Customer_Name: '',
                    Customer_Type: 'retail',
                    Customer_Phone: '',
                    Customer_Mobile: '',
                    Customer_Email: '',
                    Customer_OfficePhone: '',
                    Customer_Address: '',
                    owner_name: '',
                    area_ID: '',
                    Customer_Credit_Limit: 0,
                    previous_due: 0,
                    is_member: 'no',
                    amount: 0,
                }
                this.imageUrl = '';
                this.selectedFile = null;
            }
        }
    })
</script>