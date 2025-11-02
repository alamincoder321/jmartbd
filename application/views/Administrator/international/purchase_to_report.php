<div id="internationalpurchaseInvoice">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<internationalpurchase-invoice v-bind:purchase_id="purchaseId"></internationalpurchase-invoice>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/components/internationalpurchaseInvoice.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script>
	new Vue({
		el: '#internationalpurchaseInvoice',
		components: {
			internationalpurchaseInvoice
		},
		data(){
			return {
				purchaseId: parseInt('<?php echo $purchaseId;?>')
			}
		}
	})
</script>

