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

    #vehicles label {
        font-size: 13px;
    }

    #vehicles select {
        border-radius: 3px;
    }

    #vehicles .add-button {
        padding: 2.5px;
        width: 28px;
        background-color: #298db4;
        display: block;
        text-align: center;
        color: white;
    }

    #vehicles .add-button:hover {
        background-color: #41add6;
        color: white;
    }

    #vehicles input[type="file"] {
        display: none;
    }

    #vehicles .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 5px 12px;
        cursor: pointer;
        margin-top: 5px;
        background-color: #298db4;
        border: none;
        color: white;
    }

    #vehicles .custom-file-upload:hover {
        background-color: #41add6;
    }

    #vehicleImage {
        height: 100%;
    }
</style>
<div id="vehicles">
    <form @submit.prevent="saveData">
        <div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom:15px;">
            <div class="col-md-5">
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Vehicle Name:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="vehicle.name" required />
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Vehicle Type:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="vehicle.vehicle_type">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Model Year:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="vehicle.model_year">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Manufacturer Name:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="vehicle.manufacturer_name">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">TAX Token Number:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="vehicle.tax_token_number">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">TAX Token Expire:</label>
                    <div class="col-md-7">
                        <input type="date" class="form-control" v-model="vehicle.tax_token_expiry">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Road Permit Number:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="vehicle.road_permit_number">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Road Permit Expire:</label>
                    <div class="col-md-7">
                        <input type="date" class="form-control" v-model="vehicle.road_permit_expiry">
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Insurance Number:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="vehicle.insurance_number">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Insurance Expiry:</label>
                    <div class="col-md-7">
                        <input type="date" class="form-control" v-model="vehicle.insurance_expiry">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Buy Price:</label>
                    <div class="col-md-7">
                        <input type="number" min="0" step="any" class="form-control" v-model="vehicle.buy_price">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Kilometer Run:</label>
                    <div class="col-md-7">
                        <input type="number" min="0" step="any" class="form-control" v-model="vehicle.kilometers_run">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Per Liter KM:</label>
                    <div class="col-md-7">
                        <input type="number" min="0" step="any" class="form-control" v-model="vehicle.per_liter_km">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4" for="status" style="display: flex;align-items: center;gap: 8px;margin-top: 8px;">
                        <input type="checkbox" style="width: 19px;height: 19px;margin:0;cursor:pointer;" id="status" v-model="vehicle.status" :true-value="`a`" :false-value="`p`" />
                        <span style="margin: 0;cursor:pointer;">Is Active</span>
                    </label>
                    <div class="col-md-7 text-right">
                        <input type="submit" class="btn btn-success btn-sm" value="Save">
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-center;">
                <div class="form-group clearfix">
                    <div style="width: 100px;height:100px;border: 1px solid #ccc;overflow:hidden;">
                        <img id="vehicleImage" v-if="imageUrl == '' || imageUrl == null" src="/assets/no_image.gif">
                        <img id="vehicleImage" v-if="imageUrl != '' && imageUrl != null" v-bind:src="imageUrl">
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
                <datatable :columns="columns" :data="vehicles" :filter-by="filter" style="margin-bottom: 5px;">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.sl }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.vehicle_type }}</td>
                            <td>{{ row.model_year }}</td>
                            <td>{{ row.manufacturer_name }}</td>
                            <td>{{ row.tax_token_number }}</td>
                            <td>
                                <span class="badge badge-success" v-if="row.status == 'a'">Active</span>
                                <span class="badge badge-danger" v-if="row.status == 'p'">Pending</span>
                            </td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <button type="button" class="button edit" @click="editData(row)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="button" @click="deleteData(row.id)">
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
        el: '#vehicles',
        data() {
            return {
                vehicle: {
                    id: 0,
                    name: '',
                    vehicle_type: '',
                    model_year: '',
                    manufacturer_name: '',
                    tax_token_number: '',
                    tax_token_expiry: '',
                    road_permit_number: '',
                    road_permit_expiry: '',
                    insurance_number: '',
                    insurance_expiry: '',
                    buy_price: 0,
                    kilometers_run: '',
                    per_liter_km: 0,
                    status: 'a'
                },
                vehicles: [],
                imageUrl: '',
                selectedFile: null,

                columns: [{
                        label: 'Sl.',
                        field: 'sl',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Vehicle Name',
                        field: 'name',
                        align: 'center'
                    },
                    {
                        label: 'Vehicle Type',
                        field: 'vehicle_type',
                        align: 'center'
                    },
                    {
                        label: 'Model Year',
                        field: 'model_year',
                        align: 'center'
                    },
                    {
                        label: 'Manufacturer Name',
                        field: 'manufacturer_name',
                        align: 'center'
                    },
                    {
                        label: 'Tax Token Number',
                        field: 'tax_token_number',
                        align: 'center'
                    },
                    {
                        label: 'Status',
                        field: 'status',
                        align: 'center'
                    },
                    {
                        label: 'Action',
                        align: 'center',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 10,
                filter: ''
            }
        },
        created() {
            this.getVehicles();
        },
        methods: {
            getVehicles() {
                axios.get('/get_vehicles').then(res => {
                    this.vehicles = res.data.map((item, index) => {
                        item.sl = index + 1;
                        return item;
                    });
                })
            },
            previewImage() {
                if (event.target.files.length > 0) {
                    this.selectedFile = event.target.files[0];
                    this.imageUrl = URL.createObjectURL(this.selectedFile);
                } else {
                    this.selectedFile = null;
                    this.imageUrl = null;
                }
            },
            saveData() {
                let url = '/add_vehicle';
                if (this.vehicle.id != 0) {
                    url = '/update_vehicle';
                }

                let fd = new FormData();
                fd.append('image', this.selectedFile);
                fd.append('data', JSON.stringify(this.vehicle));

                axios.post(url, fd).then(res => {
                    let r = res.data;
                    if (r.success) {
                        alert(r.message);
                        this.resetForm();
                        this.getVehicles();
                    }
                })
            },
            editData(vehicle) {
                let keys = Object.keys(this.vehicle);
                keys.forEach(key => {
                    this.vehicle[key] = vehicle[key];
                })

                if (vehicle.image_name == null || vehicle.image_name == '') {
                    this.imageUrl = null;
                } else {
                    this.imageUrl = '/' + vehicle.image_name;
                }
            },
            deleteData(id) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_vehicle', {
                    id: id
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getVehicles();
                    }
                })
            },
            resetForm() {
                this.vehicle = {
                    id: 0,
                    name: '',
                    vehicle_type: '',
                    model_year: '',
                    manufacturer_name: '',
                    tax_token_number: '',
                    tax_token_expiry: '',
                    road_permit_number: '',
                    road_permit_expiry: '',
                    insurance_number: '',
                    insurance_expiry: '',
                    buy_price: 0,
                    kilometers_run: '',
                    per_liter_km: 0,
                    status: 'a'
                };
                this.imageUrl = '';
                this.selectedFile = null;
            }
        }
    })
</script>