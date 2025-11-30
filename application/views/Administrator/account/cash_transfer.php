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

    #cashTransfer label {
        font-size: 13px;
    }

    #cashTransfer select {
        border-radius: 3px;
        padding: 0;
    }

    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
<div id="cashTransfer">
    <div class="row" style="border-bottom: 1px solid #ccc;padding-bottom: 15px;margin-bottom: 15px;">
        <div class="col-md-12">
            <form @submit.prevent="addTransaction">
                <div class="row">
                    <div class="col-md-5 col-md-offset-1">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Transaction ID</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7">
                                <input type="text" class="form-control" v-model="transaction.invoice">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Date</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7">
                                <input type="date" class="form-control" required v-model="transaction.date" @change="getTransactions" v-bind:disabled="userType == 'u' ? true : false">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Transfer To</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7 col-xs-11">
                                <v-select v-bind:options="branches" v-model="selectedToBranch" label="Brunch_name" @input="onChangeTransfer"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Cash Balance</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7">
                                <input type="number" min="0" class="form-control" step="0.01" v-model="transaction.previous_balance" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="form-group">
                            <label class="col-md-4 control-label">PaymentType From</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7">
                                <select class="form-control" required v-model="transaction.paymentTypeFrom" @change="onChangePaymentType">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="display: none;" :style="{ display: transaction.paymentTypeFrom == 'bank' ? '' : 'none' }" v-if="transaction.paymentTypeFrom == 'bank'">
                            <label class="col-md-4 control-label">From Bank</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7 col-xs-11">
                                <v-select v-bind:options="banks" v-model="selectedFromBank" label="display_name" @input="onChangeFromBank"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">PaymentType To</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7">
                                <select class="form-control" required v-model="transaction.paymentTypeTo" @change="onChangePaymentType">
                                    <option value="cash">Cash</option>
                                    <option value="bank">Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="display: none;" :style="{ display: transaction.paymentTypeTo == 'bank' ? '' : 'none' }" v-if="transaction.paymentTypeTo == 'bank'">
                            <label class="col-md-4 control-label">To Bank</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7 col-xs-11">
                                <v-select v-bind:options="tobanks" v-model="selectedToBank" label="display_name"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Description</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7">
                                <input type="text" class="form-control" v-model="transaction.note">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Amount</label>
                            <label class="col-md-1">:</label>
                            <div class="col-md-7">
                                <input type="number" min="0" class="form-control" step="any" required v-model="transaction.amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-7 col-md-offset-5 text-right">
                                <input type="button" class="btn btn-danger btn-xs" style="padding: 5px 15px;font-weight: 700;" value="Cancel" @click="resetForm">
                                <input type="submit" class="btn btn-success btn-xs" style="padding: 5px 15px;font-weight: 700;" value="Save">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="transactions" :filter-by="filter" style="margin-bottom: 5px;">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.invoice }}</td>
                            <td>{{ row.date }}</td>
                            <td>{{ row.from_branch }}</td>
                            <td>{{ row.paymentTypeFrom }}</td>
                            <td>{{ row.from_bank }}</td>
                            <td>{{ row.to_branch }}</td>
                            <td>{{ row.paymentTypeTo }}</td>
                            <td>{{ row.to_bank }}</td>
                            <td>{{ row.amount }}</td>
                            <td>{{ row.note }}</td>
                            <td>
                                <span class="badge badge-success" v-if="row.status == 'a'">Approved</span>
                                <span class="badge badge-danger" v-else="row.status == 'p'">Pending</span>
                            </td>
                            <td>
                                <button v-if="row.transfer_to == branchId && row.status == 'p'" type="button" @click="approveTransfer(row.id)" class="badge badge-warning">Approve</button>

                                <button v-if="row.transfer_from == branchId && row.canEditDelete" type="button" class="button edit" @click="editTransaction(row)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button v-if="row.transfer_from == branchId && row.canEditDelete" type="button" class="button" @click="deleteTransaction(row.id)">
                                    <i class="fa fa-trash"></i>
                                </button>
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
        el: '#cashTransfer',
        data() {
            return {
                transaction: {
                    id: 0,
                    invoice: "<?= $invoice; ?>",
                    date: moment().format('YYYY-MM-DD'),
                    transfer_from: "<?= $this->session->userdata('BRANCHid'); ?>",
                    paymentTypeFrom: 'cash',
                    paymentTypeTo: 'cash',
                    amount: '',
                    note: '',
                    previous_balance: 0
                },
                transactions: [],
                branches: [],
                selectedToBranch: null,
                banks: [],
                selectedFromBank: null,
                tobanks: [],
                selectedToBank: null,
                userType: '<?php echo $this->session->userdata("accountType"); ?>',
                branchId: '<?= $this->session->userdata('BRANCHid'); ?>',

                columns: [{
                        label: 'Transaction Id',
                        field: 'invoice',
                        align: 'center'
                    },
                    {
                        label: 'Date',
                        field: 'date',
                        align: 'center'
                    },
                    {
                        label: 'Transfer From',
                        field: 'from_branch',
                        align: 'center'
                    },
                    {
                        label: 'P.Type From',
                        field: 'paymentTypeFrom',
                        align: 'center'
                    },
                    {
                        label: 'From Bank',
                        field: 'from_bank',
                        align: 'center'
                    },
                    {
                        label: 'Transfer To',
                        field: 'to_branch',
                        align: 'center'
                    },
                    {
                        label: 'P.Type To',
                        field: 'paymentTypeTo',
                        align: 'center'
                    },
                    {
                        label: 'To Bank',
                        field: 'to_bank',
                        align: 'center'
                    },
                    {
                        label: 'Amount',
                        field: 'amount',
                        align: 'center'
                    },
                    {
                        label: 'Description',
                        field: 'note',
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
                filter: '',
                bank_balances: []
            }
        },
        created() {
            this.getBranches();
            this.getFromBanks();
            this.getTransactions();
            this.onChangePaymentType();
        },
        methods: {
            getCashBalance() {
                axios.get('/get_cash_and_bank_balance').then(res => {
                    this.transaction.previous_balance = res.data.cashBalance.cash_balance;
                })
            },
            getBankBalance() {
                axios.get('/get_cash_and_bank_balance').then(res => {
                    this.bank_balances = res.data.bankBalance;
                })
            },
            getBranches() {
                axios.get('/get_branches').then(res => {
                    this.branches = res.data.filter(item => item.brunch_id != this.transaction.transfer_from);
                })
            },
            getFromBanks() {
                axios.get('/get_bank_accounts').then(res => {
                    this.banks = res.data.map(item => {
                        return {
                            ...item,
                            display_name: `${item.account_name} - ${item.account_number} (${item.bank_name})`
                        }
                    });
                })
            },
            getToBanks() {
                axios.post('/get_bank_accounts', {
                    branchId: this.selectedToBranch.brunch_id
                }).then(res => {
                    this.tobanks = res.data.map(item => {
                        return {
                            ...item,
                            display_name: `${item.account_name} - ${item.account_number} (${item.bank_name})`
                        }
                    });
                })
            },
            onChangeTransfer() {
                this.getToBanks();
            },
            onChangePaymentType() {
                this.selectedFromBank = null;
                this.selectedToBank = null;
                this.transaction.previous_balance = 0;
                if (this.transaction.paymentTypeFrom == 'cash' || this.transaction.paymentTypeTo == 'cash') {
                    this.getCashBalance();
                } else {
                    this.getBankBalance();
                }
            },
            onChangeFromBank() {
                if (this.selectedFromBank) {
                    this.transaction.previous_balance = this.bank_balances.filter(item => item.account_id == this.selectedFromBank.account_id)[0].balance;
                } else {
                    this.transaction.previous_balance = 0;
                }
            },
            getTransactions() {
                let data = {
                    dateFrom: this.transaction.Tr_date,
                    dateTo: this.transaction.Tr_date
                }

                axios.post('/get_cash_transfers', data).then(res => {
                    this.transactions = res.data;
                })
            },
            addTransaction() {
                if (this.selectedToBranch == null) {
                    alert('Select branch to transfer');
                    return;
                }

                this.transaction.from_bank_id = this.selectedFromBank ? this.selectedFromBank.account_id : null;
                this.transaction.to_bank_id = this.selectedToBank ? this.selectedToBank.account_id : null;
                this.transaction.transfer_to = this.selectedToBranch ? this.selectedToBranch.brunch_id : null;

                let url = '/add_cash_transfer';
                if (this.transaction.id != 0) {
                    url = '/update_cash_transfer';
                }

                axios.post(url, this.transaction).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.getTransactions();
                        this.transaction.invoice = r.invoice;
                    }
                })
            },
            editTransaction(transaction) {
                let keys = Object.keys(this.transaction);
                keys.forEach(key => {
                    this.transaction[key] = transaction[key];
                });

                this.selectedToBranch = this.branches.find(branch => branch.brunch_id == transaction.transfer_to) || null;
                setTimeout(() => {
                    this.selectedFromBank = this.banks.find(bank => bank.account_id == transaction.from_bank_id) || null;
                }, 1500);
                setTimeout(() => {
                    this.selectedToBank = this.tobanks.find(bank => bank.account_id == transaction.to_bank_id) || null;
                }, 2000);
            },
            deleteTransaction(transactionId) {
                if (!confirm('Are you sure to delete?')) {
                    return;
                }
                axios.post('/delete_cash_transfer', {
                    transactionId: transactionId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getTransactions();
                    }
                })
            },
            approveTransfer(transactionId) {
                if (!confirm('Are you sure to approve this transfer?')) {
                    return;
                }
                axios.post('/approve_cash_transfer', {
                    transactionId: transactionId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getTransactions();
                    }
                })
            },
            resetForm() {
                this.transaction = {
                    id: 0,
                    invoice: "",
                    date: moment().format('YYYY-MM-DD'),
                    transfer_from: "<?= $this->session->userdata('BRANCHid'); ?>",
                    paymentTypeFrom: 'cash',
                    paymentTypeTo: 'cash',
                    amount: '',
                    note: '',
                    previous_balance: 0
                };
                this.getCashBalance();
            }
        }
    })
</script>