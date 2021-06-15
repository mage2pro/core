// 2021-06-14
// "Magento 2.3.5 does not warn about an incorrect (e.g. negative) value of the «Qty» field of the «Advanced Inventory» popup
// on the backend product form": https://github.com/canadasatellite-ca/site/issues/180
var config = {config: {mixins: {
	'Magento_ConfigurableProduct/js/variations/variations': {'Df_ConfigurableProduct/variations/variations': true}
}}};