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
<div id="sliders">
    <!-- Edit or update -->
    <form id="update_or_create" method="post" @submit.prevent="formSubmit" class="row" enctype="multipart/form-data">
        <div class="col-md-6">
            <div class="form-group">
                <input type="hidden" class="form-control" id="id" v-model="slider.id" hidden="true">
                <label class="control-label" for="title">Title:</label>
                <input type="text" class="form-control" id="title" v-model="slider.title" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-2 text-center;">
                    <div class="form-group clearfix">
                        <div style="width: auto;height:80px;border: 1px solid #ccc;">
                            <img id="customerImage" v-if="slider.imageUrl == '' || slider.imageUrl == null"
                                src="/assets/no_image.gif">
                            <img id="customerImage" v-if="slider.imageUrl != '' && slider.imageUrl != null"
                                v-bind:src="slider.imageUrl">
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
        </div>

        <div class="col-md-12">
            <button type="button" class="btn btn-danger pull-right" @click="resetForm">Reset</button>
            <button type="submit" class="btn btn-success pull-right" v-if="slider.id == ''">Save</button>
            <button type="submit" class="btn btn-success pull-right" v-if="slider.id != ''">Update</button>
        </div>
        <br/>

    </form>



    <!-- list slider -->
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                Manage Sliders
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="products" class="table table-striped table-bordered table-hover" cellspacing="0"
                        width="100%">
                        <thead>
                            <tr>
                                <th class="text-center">Sl</th>
                                <th class="text-center">Title</th>
                                <th class="text-center">Image</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="slider in sliders">
                                <td class="text-center">{{ sliders.indexOf(slider) + 1 }}</td>
                                <td class="text-center">{{ slider.title }}</td>
                                <td class="text-center">
                                    <img style="width:auto;height:50px" v-bind:src="slider.imageSrc">
                                </td>
                                <td class="text-center">
                                    <a href="" v-on:click.prevent="editData(slider)"><i class="fa fa-edit"></i></a>
                                    <a href="" v-on:click.prevent="deleteSlider(slider.id)"><i
                                            class="fa fa-trash"></i></a>
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
        el: '#sliders',
        data() {
            return {
                slider: {
                    'id': '',
                    'title': '',
                    imageUrl: '',
                    selectedFile: null,

                },
                sliders: null,

                columns: [{
                    label: 'Title',
                    field: 'title',
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
            this.getSliders();
        },
        methods: {
            getSliders() {
                axios.get('/get_sliders').then(res => {
                    this.sliders = res.data;
                    this.sliders = res.data.map((item, index) => {
                        item.imageSrc = item.image ? `/uploads/slider/${item.image}` : '/uploads/noImage.png';
                        item.sl = index + 1;
                        return item;
                    })

                    console.log(this.sliders);
                })
            },

            resetForm() {
                this.slider.id = '';
                this.slider.title = '';
                this.slider.imageUrl = '';
                this.slider.selectedFile = null;
            },


            formSubmit() {
                const formData = new FormData();
                formData.append('id', this.slider.id);
                formData.append('title', this.slider.title);
                formData.append('selectedFile', this.slider.selectedFile);
                axios.post('/update_or_create_slider', formData).then(res => {
                    alert(res.data.message);
                    this.getSliders();
                    this.resetForm();
                })
            },

            editData(slider) {
                this.slider.id = slider.id;
                this.slider.title = slider.title;
                this.slider.imageUrl = slider.imageSrc;
            },

            deleteSlider(id) {
                if (confirm('Are you sure?')) {
                    axios.post('/delete_slider', {
                        id: id
                    }).then(res => {
                        alert(res.data.message);
                        this.getSliders();
                    })
                }
            },

            previewImage(event) {
                if (event.target.files[0]) {
                    let reader = new FileReader();
                    reader.readAsDataURL(event.target.files[0]);
                    reader.onload = (ev) => {
                        let img = new Image();
                        img.src = ev.target.result;
                        img.onload = async e => {
                            // Use the original image dimensions
                            let canvas = document.createElement('canvas');
                            canvas.width = img.width;
                            canvas.height = img.height;

                            const context = canvas.getContext("2d");
                            context.drawImage(img, 0, 0);

                            let new_img_url = canvas.toDataURL(event.target.files[0].type);
                            this.slider.imageUrl = new_img_url;

                            const resizedImage = await new Promise(rs => canvas.toBlob(rs, event.target.files[0].type, 1));
                            this.slider.selectedFile = new File([resizedImage], event.target.files[0].name, {
                                type: resizedImage.type
                            });
                        };
                    };
                } else {
                    event.target.value = '';
                }

            },
        }
    })
</script>