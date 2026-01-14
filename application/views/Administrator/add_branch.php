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

    #branches .add-button {
        padding: 2.5px;
        width: 28px;
        background-color: #298db4;
        display: block;
        text-align: center;
        color: white;
    }

    #branches .add-button:hover {
        background-color: #41add6;
        color: white;
    }


    .multiple_select_items .dropdown-toggle,
    .multiple_select_items {
        height: auto;
    }

    .multiple_select_items  .vs__selected-options {
        overflow: unset;
        flex-wrap: wrap;
    }

    .multiple_select_items  span.selected-tag {
        position: static;
    }


</style>
<div id="branches">
    <form @submit.prevent="saveData">
        <div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom:15px;">
            <div class="col-md-5 col-md-offset-1">
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Branch Name:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="branch.Brunch_name" required>
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Branch Title:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="branch.Brunch_title">
                    </div>
                </div>
                
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Print Type:</label>
                    <div class="col-md-7">
                        <label for="a4">
                            <input type="radio" id="a4" value="1" v-model="branch.print_type"> A4 Size
                        </label>
                        <label for="a42">
                            <input type="radio" id="a42" value="2" v-model="branch.print_type"> 1/2 of A4 Size
                        </label>

                        <label for="pos">
                            <input type="radio" id="pos" value="3" v-model="branch.print_type"> POS Size
                        </label>
                    </div>
                </div>
                
            </div>

            <div class="col-md-5">
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Area:</label>
                    <div class="col-md-7">
                        <v-select v-bind:options="districts" v-model="selectedDistrict" label="District_Name"></v-select>
                    </div>
                    <div class="col-md-1" style="padding:0;margin-left: -15px;"><a href="/area" target="_blank" class="add-button"><i class="fa fa-plus"></i></a></div>
                </div>
                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Address:</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" v-model="branch.Brunch_address">
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="col-md-4" for="status">
                        <input type="checkbox" name="status" id="status" :true-value="'a'" :false-value="'p'" v-model="branch.status" />
                        <span>Is Active</span>
                    </label>
                    <div class="col-md-7 text-right">
                        <input type="submit" class="btn btn-success btn-sm" value="Save">
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
                <datatable :columns="columns" :data="branches" :filter-by="filter" style="margin-bottom: 5px;">
                    <template scope="{ row }">
                        <tr :style="{background: row.status == 'p' ? '#ffd589ad' : ''}">
                            <td>{{ row.sl }}</td>
                            <td>{{ row.Brunch_name }}</td>
                            <td>{{ row.Brunch_title }}</td>
                            <td>{{ row.District_Name }}</td>
                            <td>{{ row.Brunch_address }}</td>
                            <td>
                                <span v-if="row.print_type == 1">A4 Size</span>
                                <span v-if="row.print_type == 2">1/2 of A4 Size</span>
                                <span v-if="row.print_type == 3">POS Size</span>
                            </td>
                            <td>
                                <span v-if="row.status == 'a'" class="badge badge-success">Active</span>
                                <span v-if="row.status == 'p'" class="badge badge-danger">Inactive</span>
                            </td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <button type="button" class="button edit" @click="editData(row)">
                                        <i class="fa fa-pencil"></i>
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
        el: '#branches',
        data() {
            return {
                branch: {
                    brunch_id: 0,
                    Brunch_name: '',
                    Brunch_title: '',
                    Brunch_address: '',
                    print_type: 1,
                    status: 'a'
                },
                branches: [],
                districts: [],
                selectedDistrict: null,

                columns: [{
                        label: 'Sl.',
                        field: 'sl',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Branch Name',
                        field: 'Brunch_name',
                        align: 'center'
                    },
                    {
                        label: 'Branch Title',
                        field: 'Brunch_title',
                        align: 'center'
                    },
                    {
                        label: 'Branch Area',
                        field: 'District_Name',
                        align: 'center'
                    },
                    {
                        label: 'Branch Address',
                        field: 'Brunch_address',
                        align: 'center'
                    },
                    {
                        label: 'Print Type',
                        field: 'print_type',
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
                per_page: 100,
                filter: ''
            }
        },
        created() {
            this.getDistricts();
            this.getBranches();
        },
        methods: {
            getDistricts() {
                axios.get('/get_districts').then(res => {
                    this.districts = res.data;
                })
            },
            getBranches() {
                axios.get('/get_branches').then(res => {
                    this.branches = res.data.map((item, index) => {
                        item.sl = index + 1;
                        return item;
                    });
                })
            },
            saveData() {
                if (this.selectedDistrict == null) {
                    alert("Please select a Area");
                    return;
                }

                // old code
                this.branch.area_id = this.selectedDistrict.District_SlNo;

                let url = '/add_branch';
                if (this.branch.brunch_id != 0) {
                    url = '/update_branch';
                }

                axios.post(url, this.branch).then(res => {
                    let r = res.data;
                    if (r.success) {
                        alert(r.message);
                        this.resetForm();
                        this.selectedDistrict  = null;
                        this.getBranches();
                    }
                })
            },
            editData(branch) {
                let keys = Object.keys(this.branch);
                keys.forEach(key => {
                    this.branch[key] = branch[key];
                })
                setTimeout(() => {
                    this.selectedDistrict = this.districts.find(d => d.District_SlNo == branch.area_id);
                }, 1500);
            },

            resetForm() {
                this.branch = {
                    brunch_id: 0,
                    Brunch_name: '',
                    Brunch_title: '',
                    Brunch_address: '',
                    print_type: 1,
                    status: 'a'
                }
            }
        }
    })
</script>