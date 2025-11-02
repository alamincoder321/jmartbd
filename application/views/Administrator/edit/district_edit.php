<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function () {
		$('#forbidden_categories').select2();
	});
</script>

<style>
	span.selection,
	span.select2-selection.select2-selection--multiple {
		height: max-content;
		display: inline-block;
		width: 100%;
	}
</style>
<div class="row">
	<div class="col-xs-12">
		<!-- PAGE CONTENT BEGINS -->
		<div class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Area Name </label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-8">
					<input type="text" id="district" name="district" placeholder="Area Name"
						value="<?php echo $selected->District_Name; ?>" class="col-xs-10 col-sm-4" />
					<input name="id" id="id" type="hidden" value="<?php echo $selected->District_SlNo; ?>" />
					<span id="msg"></span>
					<?php echo form_error('district'); ?>
					<span style="color:red;font-size:15px;">
				</div>
			</div>
			<br />
			<div class="form-group mt-2">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Forbidden Categories </label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-8">
					<?php
					$carerow = explode(',', $selected->forbidden_categories);
					?>
					<div class="col-xs-10 col-sm-4" style="padding:0px;">
						<select name="forbidden_categories" id="forbidden_categories" class=" form-control " multiple>
							<?php foreach ($categories as $row) { ?>
								<option value="<?php echo $row->ProductCategory_SlNo; ?>" <?php if (in_array($row->ProductCategory_SlNo, $carerow))
									   echo 'selected'; ?>>
									<?php echo $row->ProductCategory_Name; ?>
								</option>
							<?php } ?>

						</select>

					</div>
					<span id="msg"></span>
					<?php echo form_error('forbidden_categories'); ?>
					<span style="color:red;font-size:15px;">
				</div>
			</div>
			<br />
			<div class="form-group mt-2">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Delivery Charges </label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-8">
					<input type="text" id="delivery_charge" name="delivery_charge" placeholder="Delivery Charges"
						value="<?php echo $selected->delivery_charge; ?>" class="col-xs-10 col-sm-4" />
					<span id="msg"></span>
					<?php echo form_error('delivery_charge'); ?>
					<span style="color:red;font-size:15px;">
				</div>
			</div>
			<br />
			<div class="form-group mt-2">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Special Discount Avialable
				</label>
				<label class="col-sm-1 control-label no-padding-right">:</label>
				<div class="col-sm-8">
					<select name="special_discount" id="special_discount" class="col-xs-10 col-sm-4">
						<option <?= $selected->special_discount == 1 ? 'selected' : ''; ?> value="1">Yes</option>
						<option <?= $selected->special_discount == 0 ? 'selected' : ''; ?> value="0">No</option>
					</select>
					<span id="msg"></span>
					<?php echo form_error('special_discount'); ?>
					<span style="color:red;font-size:15px;">
				</div>
			</div>
			<br />

			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
				<label class="col-sm-1 control-label no-padding-right"></label>
				<div class="col-sm-8">
					<button type="button" class="btn btn-sm btn-info" onclick="submit()" name="btnSubmit">
						Update
						<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
					</button>
				</div>
			</div>

		</div>
	</div>
</div>



<div class="row">
	<div class="col-xs-12">

		<div class="clearfix">
			<div class="pull-right tableTools-container"></div>
		</div>
		<div class="table-header">
			Area Information
		</div>

		<!-- div.table-responsive -->

		<!-- div.dataTables_borderWrap -->
		<div id="saveResult">
			<table id="dynamic-table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th class="center" style="display:none;">
							<label class="pos-rel">
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</th>
						<th>SL No</th>
						<th>Area Name</th>
						<th>Forbidden Categories</th>
						<th>Delivery Charge</th>
						<th>Special Discount</th>
						<th>Action</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$BRANCHid = $this->session->userdata('BRANCHid');
					$query = $this->db->query("
						SELECT d.*, 
							(
								SELECT GROUP_CONCAT(pc.ProductCategory_Name SEPARATOR ', ')
								FROM tbl_productcategory pc
								WHERE FIND_IN_SET( pc.ProductCategory_SlNo, d.forbidden_categories)
							) as forbidden_categories
						FROM tbl_district d
						WHERE d.status = 'a'
						ORDER BY d.District_Name ASC
					");
					$row = $query->result();

					//echo "<pre>";print_r($row);exit;
					?>
					<?php $i = 1;
					foreach ($row as $row) { ?>
						<tr>
							<td class="center" style="display:none;">
								<label class="pos-rel">
									<input type="checkbox" class="ace" />
									<span class="lbl"></span>
								</label>
							</td>

							<td><?php echo $i++; ?></td>
							<td><a href="#"><?php echo $row->District_Name; ?></a></td>
							<td><a href="#"><?php echo $row->forbidden_categories; ?></a></td>
							<td><a href="#"><?php echo $row->delivery_charge; ?></a></td>
							<td><a href="#"><?php echo $row->special_discount ? 'Yes' : 'No'; ?></a></td>
							<td>
								<div class="hidden-sm hidden-xs action-buttons">
									<a class="blue" href="#">
										<i class="ace-icon fa fa-search-plus bigger-130"></i>
									</a>

									<a class="green"
										href="<?php echo base_url() ?>areaedit/<?php echo $row->District_SlNo; ?>"
										title="Eidt" onclick="return confirm('Are you sure you want to Edit this item?');">
										<i class="ace-icon fa fa-pencil bigger-130"></i>
									</a>

									<a class="red" href="#" onclick="deleted(<?php echo $row->District_SlNo; ?>)">
										<i class="ace-icon fa fa-trash-o bigger-130"></i>
									</a>
								</div>
							</td>
						</tr>

					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
	function submit() {
		var id = $("#id").val();
		var district = $("#district").val();
		var delivery_charge = $("#delivery_charge").val();
		var forbidden_categories = $("#forbidden_categories").val();
		var special_discount = $("#special_discount").val();
		if (district == "") {
			$("#msg").html("Required Filed").css("color", "red");
			return false;
		}
		var catname = encodeURIComponent(catname);
		var inputdata = 'district=' + district + '&id=' + id + '&delivery_charge=' + delivery_charge + '&forbidden_categories=' + forbidden_categories + '&special_discount=' + special_discount;
		var urldata = "<?php echo base_url(); ?>areaupdate";
		$.ajax({
			type: "POST",
			url: urldata,
			data: inputdata,
			success: function (data) {
				if (data == "false") {
					alert("This area allready exists");
				} else {
					alert("Update Success");
					window.location = '/area';
				}
			}
		});
	}
</script>

<script type="text/javascript">
	function deleted(id) {
		var deletedd = id;
		var inputdata = 'deleted=' + deletedd;
		var confirmation = confirm("are you sure you want to delete this ?");
		var urldata = "<?php echo base_url() ?>areadelete";
		if (confirmation) {
			$.ajax({
				type: "POST",
				url: urldata,
				data: inputdata,
				success: function (data) {
					//$("#saveResult").html(data);
					alert("Delete Success");
					window.location.href = '<?php echo base_url(); ?>Administrator/page/area';
				}
			});
		};
	}
</script>