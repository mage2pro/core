<?php
use Magento\Store\Api\Data\StoreInterface;
/**
 * 2016-01-11
 * @return array(string => string)
 */
function df_sales_entity_types() {
	return [
		'Order' => 'order'
		,'Invoice' => 'invoice'
		,'Shipment' => 'shipment'
		,'Credit Memo' => 'creditmemo'
	];
}

/**
 * 2016-01-11
 * @return \Magento\SalesSequence\Model\Manager
 */
function df_sales_seq_m() {return df_o(\Magento\SalesSequence\Model\Manager::class);}

/**
 * 2016-01-11
 * @param string $entityTypeCode
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string
 */
function df_sales_seq_next($entityTypeCode, $store = null) {
	return df_sales_seq_m()->getSequence($entityTypeCode, df_store_id($store))->getNextValue();
}
