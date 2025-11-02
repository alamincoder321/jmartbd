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

    #products label {
        font-size: 13px;
    }

    #products select {
        border-radius: 3px;
    }

    #products .add-button {
        padding: 2.5px;
        width: 28px;
        background-color: #298db4;
        display: block;
        text-align: center;
        color: white;
    }

    #products .add-button:hover {
        background-color: #41add6;
        color: white;
    }

    #products input[type="file"] {
        display: none;
    }

    #products .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 5px 12px;
        cursor: pointer;
        margin-top: 5px;
        background-color: #298db4;
        border: none;
        color: white;
    }

    #products .custom-file-upload:hover {
        background-color: #41add6;
    }

    #customerImage {
        height: 100%;
    }

    tr td {
        vertical-align: middle !important;
    }
</style>


<div id="feedbacks" >
    <!-- list feedback -->
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                Feedback History
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="products" class="table table-striped table-bordered table-hover" cellspacing="0"
                        width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Sl</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Store</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Description</th>
                                <th class="text-center">Complain Type</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="feedback in feedbacks">
                                <td class="text-center">{{ feedbacks.indexOf(feedback) + 1 }}</td>
                                <td class="text-center">{{ feedback.name }}</td>
                                <td class="text-center">{{ feedback.store }}</td>
                                <td class="text-center">{{ feedback.department }}</td>
                                <td class="text-center">{{ feedback.description }}</td>
                                <td class="text-center">{{ feedback.complain_type }}</td>
                                <td class="text-center">{{ feedback.created_at }}</td>
                                <td class="text-center">{{ feedback.status }}</td>
                                <td class="text-center"><a v-if="feedback.status == 'Pending'" class="btn btn-success" href="javascript:void(0)" @click="updateStatus(feedback.id, 'Resolved')">Resolved</a></td>
                               
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
        el: '#feedbacks',
        data() {
            return {
                feedback: {
                    'id': '',
                    'title': '',
                    imageUrl: '',
                    selectedFile: null,

                },
                feedbacks: null,

             
                page: 1,
                per_page: 10,
                filter: ''
            }
        },
        created() {
            this.getfeedbacks();
        },
        methods: {
            getfeedbacks() {
                axios.get('/get_feedbacks').then(res => {
                    this.feedbacks = res.data;
                  
                 
                })
            },
            updateStatus(id, status) {
                axios.post('/updateStatus', {
                    data:{
                        id: id,
                        status: status
                    }
                }).then(res => {
                    if(res.data.status){
                        // alert(res.data.message);
                        this.getfeedbacks();
                    }else{
                        alert('Something went wrong');
                    }
                })
            },

        }
    })
</script>