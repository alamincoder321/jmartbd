<style>
	.inline-radio {
		display: inline;
	}

	#branch .Inactive {
		color: red;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<div class="col-sm-4 col-sm-offset-1">
			<?php if ($selected) { ?>
				<form class="form-vertical" method="post" enctype="multipart/form-data"
					action="<?php echo base_url(); ?>company_profile_Update">


					<div class="form-group">

						<label class="control-label" for="">Company Logo</label>
						<div class="col-sm-12">
							<div class="left">
								<?php if ($selected->Company_Logo_thum != "") { ?>
									<img id="hideid"
										src="<?php echo base_url() . 'uploads/company_profile_thum/' . $selected->Company_Logo_thum; ?>"
										alt="" style="width:100px">
								<?php } else { ?>
									<img id="hideid" src="<?php echo base_url(); ?>images/No-Image-.jpg" alt=""
										style="width:200px">
								<?php } ?>
								<img id="preview" src="#" style="width:100px;height:100px" hidden>
							</div>
						</div>

						<div>
							<input name="companyLogo" id="companyLogo" type="file" onchange="readURL(this)"
								class="form-control" style="height:35px;" />
						</div>
					</div>
					<br/>

					<div class="form-group">
						<label class="control-label" for="login_img">Login Logo</label>
						<div class="col-sm-12">
							<div class="left">
								<?php if ($selected->login_img != "") { ?>
									<img id="hideid1"
										src="<?php echo base_url() . 'uploads/' . $selected->login_img; ?>"
										alt="" style="width:100px">
								<?php } else { ?>
									<img id="hideid1" src="<?php echo base_url(); ?>images/No-Image-.jpg" alt=""
										style="width:200px">
								<?php } ?>
								<img id="preview1" src="#" style="width:100px;height:100px" hidden>
							</div>
						</div>
						<div>
							<input name="login_img" id="login_img" type="file" onchange="readURL1(this)" class="form-control"
								style="height:35px;" />
						</div>
					</div>
					<br>


					<div class="form-group">
						<label class="control-label" for="banner_img">banner img</label>
						<div class="col-sm-12">
							<div class="left">
								<?php if ($selected->banner_img != "") { ?>
									<img id="hideid2"
										src="<?php echo base_url() . 'uploads/' . $selected->banner_img; ?>"
										alt="" style="width:100px">
								<?php } else { ?>
									<img id="hideid2" src="<?php echo base_url(); ?>images/No-Image-.jpg" alt=""
										style="width:200px">
								<?php } ?>
								<img id="preview2" src="#" style="width:100px;height:100px" hidden>
							</div>
						</div>
						<div>
							<input name="banner_img" id="banner_img" type="file" onchange="readURL2(this)"
								class="form-control" style="height:35px;" />
						</div>
					</div>

					<div class="form-group" style="margin-top:15px">
						<label class="control-label" for="form-field-1"> Company Name </label>
						<div>
							<input name="Company_name" type="text" id="Company_name"
								value="<?php echo $selected->Company_Name; ?>" class="form-control" />
							<input name="iidd" type="hidden" id="iidd" value="<?php echo $selected->Company_SlNo; ?>"
								class="txt" />
						</div>
					</div>

					<div class="form-group" style="margin-top:15px">
						<label class="control-label" for="form-field-1"> Description </label>
						<div>
							<textarea id="Description" name="Description"
								class="form-control"><?php echo $selected->Repot_Heading; ?></textarea>
						</div>
					</div>
					<div class="form-group" style="margin-top:15px">
						<label class="control-label" for="form-field-1"> Invoice Footer </label>
						<div>
							<textarea id="invoice_footer" name="invoice_footer"
								class="form-control"><?php echo $selected->invoice_footer; ?></textarea>
						</div>
					</div>

					<div class="form-group" style="margin-top:15px;">
						<label class="col-sm-4 control-label" for=""> </label>
						<label class="col-sm-1 control-label"></label>
						<div class="col-sm-7 text-right">
							<button type="submit" name="btnSubmit" title="Update" class="btn btn-sm btn-info">
								Update
								<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
							</button>
						</div>
					</div>
				</form>
				<?php } ?>

		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>

<script type="text/javascript">
	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				document.getElementById('preview').src = e.target.result;
			}
			reader.readAsDataURL(input.files[0]);
			$("#hideid").hide();
			$("#preview").show();
		}
	}
	function readURL1(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				document.getElementById('preview1').src = e.target.result;
			}
			reader.readAsDataURL(input.files[0]);
			$("#hideid1").hide();
			$("#preview1").show();
		}
	}
	function readURL2(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				document.getElementById('preview2').src = e.target.result;
			}
			reader.readAsDataURL(input.files[0]);
			$("#hideid2").hide();
			$("#preview2").show();
		}
	}
</script>