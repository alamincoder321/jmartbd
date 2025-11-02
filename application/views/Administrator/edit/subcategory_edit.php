<div class="row">
<div class="col-xs-12">
	<!-- PAGE CONTENT BEGINS -->
	<div class="form-horizontal">
		
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Select Category Name  </label>
			<label class="col-sm-1 control-label no-padding-right">:</label>
			<div class="col-sm-3">
				<select name="catname" id="catname" class="form-control" required>	
					<option value="">Select Category </option>
					<?php
					$category = $this->db->get('tbl_productcategory')->result();
					foreach ($category as $row) {
						?>
						<option value="<?php echo $row->ProductCategory_SlNo; ?>" <?php if ($selected->ProductCategory_ID == $row->ProductCategory_SlNo) echo 'selected'; ?>><?php echo $row->ProductCategory_Name; ?></option>
						<?php
					}
					?>
				</select>
				<span id="msg"></span>
				<?php echo form_error('catname'); ?>
				<span style="color:red;font-size:15px;">
			</div>
			<a href="<?php echo base_url(); ?>category" class="btn btn-primary">+</a>
		</div>


		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Sub Category Name  </label>
			<label class="col-sm-1 control-label no-padding-right">:</label>
			<div class="col-sm-8">
				<input type="text" id="subcatname" name="subcatname" placeholder="Sub Category Name"  value="<?php echo $selected->ProductsubCategory_Name; ?>" class="col-xs-10 col-sm-4" />
				<input name="id" id="id" type="hidden" value="<?php echo $selected->ProductCategory_SlNo; ?>"/>
				<span id="msg"></span>
				<span style="color:red;font-size:15px;">
			</div>
		</div>
		
		<div class="form-group">
			<label class="col-sm-3 control-label no-padding-right" for="description">Description </label>
			<label class="col-sm-1 control-label no-padding-right">:</label>
			<div class="col-sm-8">
				<textarea class="col-xs-10 col-sm-4" name="catdescrip" id="catdescrip"><?php echo $selected->ProductCategory_Description; ?></textarea>
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
			Sub Category Information
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
						<th>Sub Category Name</th>
						<th>Category Name</th>
						<th class="hidden-480">Description</th>

						<th>Action</th>
					</tr>
				</thead>

				<tbody>
					<?php 
					$BRANCHid=$this->session->userdata('BRANCHid');
					$query = $this->db->query("SELECT sub.*, c.ProductCategory_Name FROM tbl_produsubctcategory sub left join tbl_productcategory c on c.ProductCategory_SlNo = sub.ProductCategory_ID where sub.status='a' AND sub.category_branchid = '$BRANCHid' order by sub.ProductsubCategory_Name  asc");
					$row = $query->result();
					//while($row as $row){ ?>
					<?php $i=1; foreach($row as $row){ ?>
					<tr>
						<td class="center" style="display:none;">
							<label class="pos-rel">
								<input type="checkbox" class="ace" />
								<span class="lbl"></span>
							</label>
						</td>

						<td><?php echo $i++; ?></td>
						<td><a href="#"><?php echo $row->ProductsubCategory_Name; ?></a></td>
						<td><a href="#"><?php echo $row->ProductCategory_Name; ?></a></td>
						<td class="hidden-480"><?php echo $row->ProductCategory_Description; ?></td>
						<td>
						<div class="hidden-sm hidden-xs action-buttons">
								<a class="blue" href="#">
									<i class="ace-icon fa fa-search-plus bigger-130"></i>
								</a>

								<?php if($this->session->userdata('accountType') != 'u'){?>
								<a class="green" href="<?php echo base_url() ?>Administrator/page/subcatedit/<?php echo $row->ProductCategory_SlNo; ?>" title="Eidt" onclick="return confirm('Are you sure you want to Edit this item?');">
									<i class="ace-icon fa fa-pencil bigger-130"></i>
								</a>

								<a class="red" href="#" onclick="deleted(<?php echo $row->ProductCategory_SlNo; ?>)">
									<i class="ace-icon fa fa-trash-o bigger-130"></i>
								</a>
								<?php }?>
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
    function submit(){
		var subcatname= $("#subcatname").val();
        var catname= $("#catname").val();
        var catdescrip= $("#catdescrip").val();
        var id= $("#id").val();
        if(catname==""){
            $("#msg").html("Required Filed").css("color","red");
            return false;
        }
        var catname=encodeURIComponent(catname);
        var inputdata = 'catname='+catname+'&catdescrip='+catdescrip+'&id='+id+'&subcatname='+subcatname;
        var urldata = "<?php echo base_url();?>Administrator/page/subcatupdate";
        $.ajax({
            type: "POST",
            url: urldata,
            data: inputdata,
            success:function(data){
				alert("Update Success");
				window.location = '/subcategory';
            }
        });
    }
</script>


<script type="text/javascript">
    function deleted(id){
        var deletedd= id;
        var inputdata = 'deleted='+deletedd;
        if(confirm("Are You Sure Want to delete This?")){
        var urldata = "<?php echo base_url();?>subcatdelete";
        $.ajax({
            type: "POST",
            url: urldata,
            data: inputdata,
            success:function(data){
                alert(data);
				window.location.href='<?php echo base_url(); ?>subcategory';
            }
        });
		};
    }
</script>
