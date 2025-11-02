<style>
    .v-select {
        float: right;
        min-width: 200px;
        margin-left: 5px;
        border-radius: 4px !important;
        margin-top: -2px;
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

<div id="transferList">
    <div class="row" style="margin:0;">
        <div class="col-md-12">
            <form class="form-inline" @submit.prevent="getTransfers">
                <div class="form-group">
                    <label>Transfer from</label>
                    <v-select v-bind:options="branches" v-model="selectedBranch" label="Brunch_name" placeholder="Select Branch"></v-select>
                </div>

                <div class="form-group">
                    <label>Date from</label>
                    <input type="date" class="form-control" v-model="filter.dateFrom">
                </div>

                <div class="form-group">
                    <label>to</label>
                    <input type="date" class="form-control" v-model="filter.dateTo">
                </div>

                <div class="form-group">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" style="display:none;" v-bind:style="{display: transfers.length > 0 ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Transfer Date</th>
                            <th>Transfer by</th>
                            <th>Transfer From</th>
                            <th>Status</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(transfer, sl) in transfers" :style="{background: transfer.status == 'p' ? '#ffd587' : ''}">
                            <td>{{ sl + 1 }}</td>
                            <td>{{ transfer.transfer_date }}</td>
                            <td>{{ transfer.transfer_by_name }}</td>
                            <td>{{ transfer.transfer_from_name }}</td>
                            <td>
                                {{transfer.status == 'p' ? 'Pending' : 'Received'}}
                            </td>
                            <td>{{ transfer.note }}</td>
                            <td>
                                <i class="btnEdit fa fa-file" style="font-size: 14px;" @click="window.open(`/transfer_invoice/${transfer.transfer_id}`, '_blank')" title="View invoice"></i>
                                <i class="fa fa-check" style="color: green;font-size:15px;cursor:pointer;" v-if="transfer.status == 'p'" @click="receivedTransfer(transfer)"></i>
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
        el: '#transferList',
        data() {
            return {
                filter: {
                    branch: null,
                    dateFrom: moment().format('YYYY-MM-DD'),
                    dateTo: moment().format('YYYY-MM-DD')
                },
                branches: [],
                selectedBranch: null,
                transfers: []
            }
        },
        created() {
            this.getBranches();
        },
        methods: {
            getBranches() {
                axios.get('/get_branches').then(res => {
                    let thisBranchId = parseInt("<?php echo $this->session->userdata('BRANCHid'); ?>");
                    let ind = res.data.findIndex(branch => branch.brunch_id == thisBranchId);
                    res.data.splice(ind, 1);
                    this.branches = res.data;
                })
            },

            getTransfers() {
                if (this.selectedBranch != null) {
                    this.filter.branch = this.selectedBranch.brunch_id;
                } else {
                    this.filter.branch = null;
                }

                axios.post('/get_receives', this.filter).then(res => {
                    this.transfers = res.data;
                })
            },

            receivedTransfer(transfer) {
                if (confirm("Are you sure?")) {
                    axios.post('/receivedTransfer', {
                            transferId: transfer.transfer_id
                        })
                        .then(res => {
                            alert(res.data.message);
                            this.getTransfers();
                        })
                }
            },
        }
    })
</script>