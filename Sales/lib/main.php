<?php
/**
 * 2016-01-29
 * @param string $type
 * @return int|false
 */
function df_sales_entity_type_index($type) {
	return array_search($type, array_values(df_sales_entity_types()));
}

/**
 * 2016-01-11
 * @return array(string => string)
 */
function df_sales_entity_types() {return [
	'Order' => 'order'
	,'Invoice' => 'invoice'
	,'Shipment' => 'shipment'
	,'Credit Memo' => 'creditmemo'
];}