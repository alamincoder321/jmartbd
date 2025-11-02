
<style>
	.v-select {
		width: 100%;
		height: 25px;
	}

	.v-select .dropdown-toggle {
		height: 29px;
		border-radius: 0;
	}

	.v-select input[type=search] {
		margin: 0;
	}
</style>
<div id="users">
	<form @submit.prevent="saveUser">
		<div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom:15px;">
			<div class="col-md-6">
				<div class="form-group">
					<label class="col-xs-4 control-label" for="txtFirstName"> Full Name </label>
					<label class="col-xs-1 control-label">:</label>
					<div class="col-xs-6">
						<input type="text" name="txtFirstName" id="txtFirstName" placeholder="Full Name" v-model="user.FullName" class="form-control" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-4 control-label" for="user_email"> User Email </label>
					<label class="col-xs-1 control-label">:</label>
					<div class="col-xs-6">
						<input type="email" name="user_email" id="user_email" v-model="user.UserEmail" placeholder="User Email" class="form-control" />
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-4 control-label" for="branch_id"> Select Branch </label>
					<label class="col-xs-1 control-label">:</label>
					<div class="col-xs-6">
						<select class="form-control" style="padding:0;" name="branch_id" v-model="user.branch_id" id="branch_id">
							<option value="1" v-if="companyProfile">{{companyProfile.Company_Name}}</option>
						</select>
						<div id="brand_" class="col-xs-12"></div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-4 control-label" for="UserType"> User Type </label>
					<label class="col-xs-1 control-label">:</label>
					<div class="col-xs-6">
						<select class="form-control" style="padding:0;" name="UserType" v-model="user.UserType" id="UserType">
							<option value="a">Admin</option>
							<option value="u">User</option>
							<option value="e">Entry User</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-xs-6">
				<div class="form-group">
					<label class="col-xs-4 control-label" for="username"> User name </label>
					<label class="col-xs-1 control-label">:</label>
					<div class="col-xs-6">
						<input type="text" id="username" @input="checkUserName" name="username" v-model="user.User_Name" autocomplete="off" placeholder="User name" class="form-control" />
						<div id="usermes" class="col-xs-12"></div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-4 control-label" for="Password"> Password </label>
					<label class="col-xs-1 control-label">:</label>
					<div class="col-xs-6">
						<input type="password" @input="checkPassword($event)" id="assword" name="Password" v-model="user.Password" placeholder="Password" autocomplete="off" class="form-control" />
						<div id="usermes" class="col-xs-12"></div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-xs-4 control-label" for="rePassword"> Re-Password </label>
					<label class="col-xs-1 control-label">:</label>
					<div class="col-xs-6">
						<input type="password" @input="checkPassword($event)" id="rePassword" name="rePassword" placeholder="Re-Password" v-model="user.Re_Password" class="form-control" />
						<div style="padding-left:0;font-style:italic;" id="error-txt" class="col-xs-12"></div>
					</div>
				</div>

				<div class="form-group">
					<div class="col-xs-5"></div>
					<label class="col-xs-3 control-label" for="status"> <input type="checkbox" id="status" true-value="a" false-value="p" v-model="user.status"> Is Active </label>
					<div class="col-xs-3 text-right">
						<button type="submit" name="btnSave" title="Save" class="btn btn-sm btn-success">
							Save
							<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
						</button>
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
				<datatable :columns="columns" :data="users" :filter-by="filter" style="margin-bottom: 5px;">
					<template scope="{ row }">
						<tr :style="{background: row.status == 'p' ? '#7a7a7a' : ''}">
							<td>{{ row.sl }}</td>
							<td>{{ row.FullName }}</td>
							<td>{{ row.User_Name }}</td>
							<td>{{ row.UserEmail }}</td>
							<td>
								<span v-if="row.UserType == 'm'" class="badge" style="background: gray;">Super Admin</span>
								<span v-if="row.UserType == 'a'" class="badge badge-success">Admin</span>
								<span v-if="row.UserType == 'e'" class="badge badge-primary">Entry User</span>
								<span v-if="row.UserType == 'u'" class="badge badge-warning">User</span>
							</td>
							<td>{{row.Brunch_name}}</td>
							<td>
								<span v-if="row.status == 'a'" class="badge badge-success">Active</span>
								<span v-if="row.status == 'p'" class="badge badge-danger">Deactive</span>
							</td>
							<td>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<i style="font-size: 14px;cursor:pointer;" v-if="row.UserType == 'e' || row.UserType == 'u'" class="btnAccess fa fa-users" @click="window.open('/access/'+row.User_SlNo)"></i>
									<i style="font-size: 14px;cursor:pointer;" v-if="row.UserType == 'e' || row.UserType == 'u' || row.UserType == 'a'" class="btnEdit fa fa-pencil button" @click="editUser(row)"></i>
									<i style="font-size: 14px;cursor:pointer;" v-if="row.UserType == 'e' || row.UserType == 'u'" class="btnDelete fa fa-trash" @click="deleteUser(row.User_SlNo)"></i>
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
		el: '#users',
		data() {
			return {
				user: {
					User_SlNo: '',
					FullName: '',
					User_Name: '',
					UserEmail: '',
					Password: '',
					Re_Password: '',
					branch_id: '<?= $this->session->userdata('BRANCHid'); ?>',
					UserType: 'a',
					status: 'a',
				},
				branches: [],

				users: [],
				columns: [{
						label: 'Sl',
						field: 'Sl',
						align: 'center',
						filterable: false
					},
					{
						label: 'Full Name',
						field: 'FullName',
						align: 'center'
					},
					{
						label: 'Username',
						field: 'User_Name',
						align: 'center'
					},
					{
						label: 'Email',
						field: 'UserEmail',
						align: 'center'
					},
					{
						label: 'User Type',
						field: 'UserType',
						field: 'Brunch_name',
						align: 'center'
					},
					{
						label: 'Branch',
						align: 'center'
					},
					{
						label: 'status',
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
				filter: '',

				userId: '<?php echo $this->session->userdata("userId"); ?>',
				companyProfile: null
			}
		},
		created() {
			this.getBranches();
			this.getUsers();
			this.getCompanyProfile();
		},
		methods: {
			getBranches() {
				axios.get('/get_branches').then(res => {
					this.branches = res.data;
				})
			},
			getUsers() {
				axios.get('/get_users').then(res => {
					this.users = res.data.map((item, index) => {
						item.sl = index + 1;
						return item;
					});
				})
			},
			getCompanyProfile() {
				axios.get('/get_company_profile').then(res => {
					this.companyProfile = res.data;
				});
			},
			saveUser() {
				if (this.user.FullName == '') {
					alert('Full Name is empty');
					return;
				}
				if (this.user.User_Name == '') {
					alert('Username is empty');
					return;
				}
				if (this.user.Password == '') {
					alert('Password is empty');
					return;
				}
				if (this.user.Re_Password == '') {
					alert('Re-Password is empty');
					return;
				}
				let url = '/add_user';
				if (this.user.User_SlNo != 0) {
					url = '/update_user';
				}
				axios.post(url, this.user)
					.then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.clearForm();
							this.getUsers();
						}
					})

			},
			editUser(user) {
				let keys = Object.keys(this.user);
				keys.forEach(key => {
					this.user[key] = user[key];
				})
				this.user.branch_id = user.Brunch_ID;
			},
			deleteUser(userId) {
				let deleteConfirm = confirm('Are you sure?');
				if (deleteConfirm == false) {
					return;
				}
				axios.post('/delete_user', {
					userId: userId
				}).then(res => {
					let r = res.data;
					alert(r.message);
					if (r.success) {
						this.getUsers();
					}
				})
			},

			changestatus(user) {
				if (user.UserType != 'm' || this.userId != user.User_SlNo) {
					let deleteConfirm = confirm('Are you sure?');
					if (deleteConfirm == false) {
						return;
					}
					axios.post('/change_user_status', {
						userId: user.User_SlNo,
						status: user.status == 'p' ? 'a' : 'p'
					}).then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getUsers();
						}
					})
				}
			},

			checkUserName() {
				axios.post('/check_username', {
					User_Name: this.user.User_Name
				}).then(res => {
					let r = res.data;
				})
			},

			clearForm() {
				let keys = Object.keys(this.user);
				keys.forEach(key => {
					if (typeof(this.user[key]) == "string") {
						this.user[key] = '';
					} else if (typeof(this.user[key]) == "number") {
						this.user[key] = 0;
					}
					this.user.userBranch_id = "<?= $this->session->userdata('BRANCHid'); ?>";
					this.user.UserType = 'a';
					this.user.status = 'a';
				})
				$("#error-txt").text('');
			},

			checkPassword(event) {
				$("#error-txt").text('');
				if (this.user.Password != "" && this.user.Re_Password != '') {
					if (this.user.Password == this.user.Re_Password) {
						$("#error-txt").text("Both password match").css({
							color: 'green'
						});
					} else {
						$("#error-txt").text('Both Password not match').css({
							color: 'red'
						});
					}
				} else {
					$("#error-txt").text('');
				}
			}
		}
	})
</script>