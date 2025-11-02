<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
	<?php
	$company = $this->db->query("select * from tbl_company")->row();
	?>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title><?= $company->Company_Name; ?> - Login Page</title>
	<link rel="icon" type="image/x-icon" href="/uploads/company_profile_org/<?= $company->Company_Logo_org ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
	<link href="<?php echo base_url(); ?>assets/css/toastr.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/login/css/style.css">
</head>

<body>
	<div class="container">
		<div class="cover">
			<div class="front" style="border-right: 1px dashed #2d402d;">
				<img src="<?php echo base_url().'uploads/'.$company->login_img; ?>" alt="">
				<div class="text">
					<span class="text-1">Online Super Shop Software</span>
				</div>
			</div>
		</div>
		<div class="forms">
			<div class="form-content">
				<div class="login-form">
					<div class="title">Login your account</div>
					<form onsubmit="Login(event)">
						<div class="input-boxes">
							<div class="input-box" style="margin: 0;">
								<i class="fas fa-envelope"></i>
								<input type="text" name="username" placeholder="Username" autocomplete="off">
							</div>
							<p class="error-username" style="font-style:italic;font-size:12px;color:red;"></p>
							<div class="input-box password" style="margin: 0;">
								<i class="fas fa-lock"></i>
								<input type="password" name="password" placeholder="Password" autocomplete="off">
								<i class="fa fa-eye passwordeye" style="position: absolute;top: 34%;right: 10px;cursor:pointer;" onclick="passwordShow(event)"></i>
							</div>
							<p class="error-password" style="font-style:italic;font-size:12px;color:red;"></p>
							<!-- <div class="text"><a href="#">Forgot password?</a></div> -->
							<div class="button input-box">
								<input type="submit" value="Login">
							</div>
							<!-- <div class="text sign-up-text">Don't have an account? <label for="flip">Sigup now</label></div> -->
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script src="<?php echo base_url(); ?>assets/js/jquery-2.1.4.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/toastr.min.js"></script>
	<script>
		toastr.options = {
			"closeButton": true,
			"debug": false,
			"progressBar": true,
			"positionClass": "toast-top-right",
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "5000",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		function Login(event) {
			event.preventDefault();
			let formdata = new FormData(event.target);

			$.ajax({
				url: "/Login/procedure",
				method: "POST",
				data: formdata,
				dataType: "JSON",
				processData: false,
				contentType: false,
				success: res => {
					if (res.status) {
						location.href = "/Administrator/"
					} else {
						toastr.error(res.message)
					}
				}
			})
		}
		// show password
		function passwordShow(event) {
			let password = $(".password").find('input').prop('type');
			if (password == 'password') {
				$(".password").find('.passwordeye').removeProp('class').prop('class', 'fa fa-eye-slash passwordeye')
				$(".password").find('input').removeProp('type').prop('type', 'text');
			} else {
				$(".password").find('.passwordeye').removeProp('class').prop('class', 'fa fa-eye passwordeye')
				$(".password").find('input').removeProp('type').prop('type', 'password');
			}
		}
	</script>
</body>

</html>