<style>
	.custom-file-upload {
		border: 1px solid #ccc;
		display: inline-block;
		padding: 5px 12px;
		cursor: pointer;
		margin-top: 5px;
		background-color: #298db4;
		border: none;
		color: white;
	}

	.custom-file-upload:hover {
		background-color: #41add6;
	}

	#customerImage {
		height: 100%;
	}

</style>



<div class="row">
	<div class="col-xs-12">
		<!-- PAGE CONTENT BEGINS -->
		<div class="form-horizontal">

			<div class="form-group">
				<div class="row">
					<div class="col-md-8">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Category Name
						</label>
						<label class="col-sm-1 control-label no-padding-right">:</label>
						<div class="col-sm-8">
							<input type="text" id="catname" name="catname" placeholder="Category Name"
								value="<?php echo $selected->ProductCategory_Name; ?>" class="col-xs-10 col-sm-4" />
							<input name="id" id="id" type="hidden"
								value="<?php echo $selected->ProductCategory_SlNo; ?>" />
							<span id="msg"></span>
							<span style="color:red;font-size:15px;">
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label no-padding-right" for="description">Description
							</label>
							<label class="col-sm-1 control-label no-padding-right">:</label>
							<div class="col-sm-8">
								<textarea class="col-xs-10 col-sm-4" name="catdescrip"
									id="catdescrip"><?php echo $selected->ProductCategory_Description; ?></textarea>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<div class=" text-center;">
							<div class="form-group clearfix">
								<div style="width: 100px;height:100px;border: 1px solid #ccc;overflow:hidden;">
									<img id="customerImage" src="/uploads/category/<?php echo $selected->ProductCategory_Image; ?>" onerror="this.src='/assets/no_image.gif'">
									
								</div>
								<div style="text-align:center;">
									<label class="custom-file-upload">
										<input type="file" id="customerImage_file" onchange="previewImage(this)" />
										Select Image
									</label>
								</div>
							</div>
						</div>
					</div>


				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
				<label class="col-sm-1 control-label no-padding-right"></label>
				<div class="col-sm-8">
					<button type="button" class="btn btn-sm btn-success" onclick="submit()" name="btnSubmit">
						Submit
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
			Category Information
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
						<th>Category Name</th>
						<th class="hidden-480">Description</th>

						<th>Action</th>
					</tr>
				</thead>

				<tbody>
					<?php
					$BRANCHid = $this->session->userdata('BRANCHid');
					$query = $this->db->query("SELECT * FROM tbl_productcategory where status='a' AND category_branchid = '$BRANCHid' order by ProductCategory_Name asc");
					$row = $query->result();
					//while($row as $row){ ?>
					<?php $i = 1;
					foreach ($row as $row) { ?>
						<tr>
								<td class="center" >
									<img src="<?php echo base_url() .'uploads/category/' . $row->ProductCategory_Image; ?>" width="50px" height="50px" onerror="this.src='/uploads/noImage.png'">
								</td>
							<td class="center" style="display:none;">
								<label class="pos-rel">
									<input type="checkbox" class="ace" />
									<span class="lbl"></span>
								</label>
							</td>

							<td><?php echo $i++; ?></td>
							<td><a href="#"><?php echo $row->ProductCategory_Name; ?></a></td>
							<td class="hidden-480"><?php echo $row->ProductCategory_Description; ?></td>
							<td>
								<div class="hidden-sm hidden-xs action-buttons">
									<a class="blue" href="#">
										<i class="ace-icon fa fa-search-plus bigger-130"></i>
									</a>

									<a class="green"
										href="<?php echo base_url() ?>Administrator/page/catedit/<?php echo $row->ProductCategory_SlNo; ?>"
										title="Eidt" onclick="return confirm('Are you sure you want to Edit this item?');">
										<i class="ace-icon fa fa-pencil bigger-130"></i>
									</a>

									<a class="red" href="#" onclick="deleted(<?php echo $row->ProductCategory_SlNo; ?>)">
										<i class="ace-icon fa fa-trash-o bigger-130"></i>
									</a>
								</div>
							</td>
						</tr>

					<?php } ?>
				</tbody>
			</table>
		</div>
		<!-- PAGE CONTENT ENDS -->
	</div><!-- /.col -->
</div><!-- /.row -->

<script type="text/javascript">
	function submit() {
    var catname = $("#catname").val().trim();
    var catdescrip = $("#catdescrip").val().trim();
    var id = $("#id").val().trim();

    if (catname === "") {
        $("#msg").html("Required Field").css("color", "red");
        return false;
    }

    var formData = new FormData();
    formData.append('catname', catname);
    formData.append('catdescrip', catdescrip);
    formData.append('id', id);

    var image = $("#customerImage_file")[0].files[0];
    if (image) {
        formData.append('image', image);
    }

    $.ajax({
        type: "POST",
        url: "<?php echo base_url(); ?>catupdate",
        data: formData,
        contentType: false,   // IMPORTANT: Prevent jQuery from setting content type
        processData: false,   // IMPORTANT: Prevent jQuery from processing the data
        success: function (response) {
            alert("Update Success");
            window.location = '/category';
        },
        error: function (xhr, status, error) {
            console.error("Update Failed:", error);
            alert("Update failed. Please try again.");
        }
    });
}

</script>
<script type="text/javascript">
	function deleted(id) {
		var deletedd = id;
		var inputdata = 'deleted=' + deletedd;
		//alert(inputdata);
		var urldata = "<?php echo base_url(); ?>Administrator/page/catdelete";
		$.ajax({
			type: "POST",
			url: urldata,
			data: inputdata,
			success: function (data) {
				//$("#saveResult").html(data);
				alert("data");
				// window.location.href = '<?php echo base_url(); ?>category';
			}
		});
	}

		function previewImage(event) {
			const WIDTH = 200;
			const HEIGHT = 200;
			console.log(event.files[0]);
			document.querySelector('#customerImage').src = URL.createObjectURL(event.files[0]);
		}
</script>