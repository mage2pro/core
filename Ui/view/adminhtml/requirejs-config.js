// 2021-06-12
// "Magento 2.3.5 does not warn about an incorrect (e.g. negative) value of the «Qty» field of the «Advanced Inventory» popup
// on the backend product form": https://github.com/canadasatellite-ca/site/issues/180
var config = {config: {mixins: {'Magento_Ui/js/form/element/abstract': {'Df_Ui/form/element/abstract': true}}}};