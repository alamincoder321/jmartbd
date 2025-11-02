<style scoped>
	.article {
		min-height: 65px;
		max-height: 100px;
		float: left !important;
		writing-mode: tb-rl;
		line-height: 0;
		font-weight: 700;
		transform: rotate(180deg);
	}

	.content {
		width: 120px;
		float: left !important;
		padding: 2px;
	}

	.name {
		height: auto;
		width: 120px;
		font-size: 11px;
	}

	.img {
		height: 60px;
		width: 120px;
	}

	.pid {
		height: 15px;
		width: 120px;
	}

	.price {
		height: 10px;
		width: 120px;
	}

	.date {
		height: 90px;
		width: 20px;
		float: right;
		writing-mode: tb-rl;
	}

	.mytext {
		height: 25px !important;
		padding: 2px;
	}

	@media print {
		.page-break {
			display: block;
			page-break-before: always;
		}

		.article {
			min-height: 65px;
			max-height: 100px;
			float: left !important;
			writing-mode: tb-rl;
			line-height: 0;
			font-weight: 700;
			transform: rotate(180deg);
		}

		.content {
			width: 120px;
			float: left !important;
			padding: 2px;
		}
	}

	input[type="checkbox"] {
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		border: 1px solid gray;
		border-radius: 5px;
		outline: none;
	}

	input[type="checkbox"]:focus {
		outline: none;
	}

	input[type="checkbox"]:checked {
		background-color: #08df3d;
	}

	input[type="checkbox"]:checked>label {
		color: #08df3d;
	}
</style>
<div id="productList">
	<form @submit.prevent="barcodeGenerate">
		<div class="row" style="margin:0;display:flex;justify-content:center;">
			<div class="col-md-10 col-xs-12" style="padding: 0;">

				<div class="col-md-6" style="padding: 0;">
					<div class="form-group clearfix">
						<label class="control-label col-xs-4 col-md-4">Product Code:</label>
						<div class=" col-xs-8 col-md-7">
							<input type="text" class="form-control" id="code" value="<?= $product->Product_Code; ?>" readonly>
						</div>
					</div>
					<div class="form-group clearfix">
						<label class="control-label col-xs-4 col-md-4">Sale Rate:</label>
						<div class="col-xs-8 col-md-7">
							<input type="number" min="0" step="0.01" id="price" class="form-control" value="<?= $product->Product_SellingPrice; ?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group clearfix">
						<label class="control-label col-xs-4 col-md-4">Quantity:</label>
						<div class="col-xs-8 col-md-7">
							<input type="number" min="0" step="1" id="quantity" class="form-control" autocomplete="off">
						</div>
					</div>

				</div>

				<div class="col-md-6" style="padding: 0;">
					<div class="form-group clearfix">
						<label class="control-label col-xs-4 col-md-4">Product Name:</label>
						<div class="col-xs-8 col-md-7">
							<input type="text" class="form-control" id="name" value="<?= $product->Product_Name; ?>" autocomplete="off">
						</div>
					</div>
					<div class="form-group clearfix">
						<label class="control-label col-xs-4 col-md-4">Article:</label>
						<div class="col-xs-8 col-md-7">
							<input type="text" class="form-control" id="article" autocomplete="off">
						</div>
					</div>
					<div class="form-group clearfix">
						<label for="" class="col-md-4 col-xs-4" style="position: relative;">
							<input type="checkbox" style="width: 45px;height: 45px;cursor:pointer;" v-model="is_single" id="is_single" @change="products = []" />
							<label for="is_single" style="margin: 0px; cursor: pointer; position: absolute; left: 16px; top: 16px;">Single</label>
						</label>
						<div class="col-xs-2 col-md-2" style="padding-right: 0;display:none;" v-show="is_single" :style="{display: is_single ? '' : 'none'}">
							Width:
							<input type="number" step="0.01" min="0" class="form-control" v-model="xAxis" />
						</div>
						<div class="col-xs-2 col-md-2" style="display: none;" v-show="is_single" :style="{display: is_single ? '' : 'none'}">
							Height:
							<input type="number" step="0.01" min="0" class="form-control" v-model="yAxis" />
						</div>
						<div class="text-right col-xs-7 col-md-7" :class="is_single ? 'col-xs-3 col-md-3': 'col-xs-7 col-md-7'">
							<button style="padding: 11px 16px;" :disabled="onProgress" type="submit" class="btnSave">Generate</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div style="display:none;" v-bind:style="{display: products.length > 0 ? '' : 'none'}">
		<div class="row" style="display: flex;justify-content:center;">
			<div class="col-md-8 col-md-offset-2 text-right">
				<a style="cursor: pointer;text-decoration:none;background: #e3e3e3;padding:5px 10px;border-radius:3px;" onclick="printpage(event)">
					<i class="fa fa-print"></i> Print
				</a>
			</div>
		</div>
		<div class="row" style="display: flex;justify-content:center;">
			<div class="output col-md-8 page-break">
				<div v-show="!is_single" style="padding:3px;float: left; height: 95px; width: 140px; border: 1px solid #ddd;" v-for="(item, sl) in products">
					<div style="width: 100%; text-align: center;">
						<p style="font-size: 12px;margin:0;line-height: 1;">{{item.article}}</p>
						<p style="font-size: 10px;margin:0px 0px 2px 1px;padding:2px 0 0 0;font-weight: bolder;text-align: center;line-height: 1;">{{item.name}}</p>
						<img class="barcode" style="line-height: 0;" />
						<p style="margin:0;font-size: 12px;margin-top:-3px; text-align: center;font-weight: 900;">{{item.code}}</p>
						<p style="margin:0;margin-top:-1px;text-align: center;font-size: 12px;font-weight: bolder;"><?= $this->session->userdata('Currency_Name'); ?> {{item.sale_rate}}</p>
					</div>
				</div>
				<div v-show="is_single" style="float:left;margin:0px;padding:0; overflow:hidden;border:1px solid #ccc;box-sizing:border-box;border-bottom:none" :style="[{width: xAxis+'in', height: yAxis+'in'}]" v-for="(item, sl) in products">
					<div style="text-align: center;margin:0;padding:0px 0px 0px 0px;" :style="[{width: xAxis+'in', height: yAxis+'in'}]">
						<p style="font-size: 10px;margin:0;line-height: 1;margin-top:1.5px;">{{item.article}}</p>
						<p style="font-size: 10px;margin:0px 0px 2px 1px;padding:2px 0 0 0;font-weight: bolder;text-align: center;line-height: 1;">{{item.name}}</p>
						<img class="singlebarcode" style="line-height: 0;" />
						<p style="margin:0;font-size: 10px;margin-top:-3px; text-align: center;font-weight: 900;">{{item.code}}</p>
						<p style="margin:0;margin-top:-2px;text-align: center;font-size: 10px;font-weight: bolder;"><?= $this->session->userdata('Currency_Name'); ?> {{item.sale_rate}}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/JsBarcode.all.min.js"></script>
<script>
	let code = "<?= $product->Product_Code ?>";
	JsBarcode(".barcode", code, {
		format: "CODE128",
		width: 1,
		height: 30,
		fontSize: 12,
		margin: 0,
		displayValue: false
	});
	JsBarcode(".singlebarcode", code, {
		format: "CODE128",
		width: 1,
		height: 40,
		fontSize: 12,
		margin: 0,
		displayValue: false
	});
	new Vue({
		el: '#productList',
		data() {
			return {
				products: [],

				onProgress: false,
				is_single: false,
				xAxis: 1.5,
				yAxis: 1,
			}
		},

		methods: {
			async barcodeGenerate(event) {
				this.products = [];
				this.onProgress = true;
				await new Promise(resolve => setTimeout(resolve, 500));
				let code = $("#code").val();
				let name = $("#name").val();
				let article = $("#article").val();
				let qty = $("#quantity").val();
				let price = $("#price").val();
				if (qty == '' || qty == 0) {
					alert("Quantity is empty");
					this.onProgress = false;
					return;
				}
				var product = {
					code: code,
					name: name,
					sale_rate: parseFloat(price).toFixed(2),
					article: article,
				}
				for (let index = 0; index < qty; index++) {
					this.products.push(product);
				}
				this.onProgress = false;
			}
		},
	})

	async function printpage(event) {
		event.preventDefault();
		var printContent = document.querySelector('.output').innerHTML;
		var originalContent = document.body.innerHTML;
		document.body.innerHTML = `
            <html>
                <head>
                    <title>Barcode Generate</title>
                    <style>
                        .article {
                            min-height: 65px;
                            max-height: 100px;
                            float: left !important;
                            writing-mode: tb-rl;
                            line-height: 0;
                            font-weight: 700;
                            transform: rotate(180deg);
                        }
                    </style>
                </head>
                <body>
                    <table style="width:100%;"><tr><td></td>${printContent}</tr></table>
                </body>
            </html>
        `;
		window.print();
		document.body.innerHTML = originalContent;
	}
</script>