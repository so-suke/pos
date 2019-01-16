var app = new Vue({
  el: '#app',
  data: {},
  methods: {
		toSalesDetail: function(event) {
			console.log('toSalesDetail')
			const target = event.target;
			const sales_detail_id = target.closest('tr').dataset.sales_detail_id;
			this.$refs.salesDetailID.value = sales_detail_id;
			this.$refs.toSalesDetailForm.submit();
		}
	},
});
