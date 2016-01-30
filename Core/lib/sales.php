<?php
use Dfe\SalesSequence\Model\Meta;
use Magento\SalesSequence\Model\Meta as _Meta;
use Magento\SalesSequence\Model\Sequence;
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
 * 2016-01-29
 * @param string $type
 * @return int|false
 */
function df_sales_entity_type_index($type) {
	return array_search($type, array_values(df_sales_entity_types()));
}

/**
 * 2016-01-11
 * @return \Magento\SalesSequence\Model\Manager
 */
function df_sales_seq_m() {return df_o(\Magento\SalesSequence\Model\Manager::class);}

/**
 * 2016-01-26
 * @param string $entityType
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return _Meta|Meta
 */
function df_sales_seq_meta($entityType, $store = null) {
	/** @var array(int => array(string => _Meta)) */
	static $cache;
	$store = df_store_id($store);
	/** @var string $cacheKey */
	$cacheKey = implode([$store, $entityType]);
	if (!isset($cache[$cacheKey])) {
		/** @var \Magento\SalesSequence\Model\ResourceModel\Meta $resource */
		$resource = df_o(\Magento\SalesSequence\Model\ResourceModel\Meta::class);
		/**
		 * 2016-01-26
		 * По аналогии с @see \Magento\SalesSequence\Model\Manager::getSequence()
		 * https://github.com/magento/magento2/blob/d50ee5/app/code/Magento/SalesSequence/Model/Manager.php#L48
		 */
		$cache[$cacheKey] = $resource->loadByEntityTypeAndStore($entityType, $store);
	}
	return $cache[$cacheKey];
}

/**
 * 2016-01-11
 * Первая реализация была наивной:
 * return df_sales_seq_m()->getSequence($entityTypeCode, df_store_id($store))->getNextValue();
 * Она неправильна тем, что метод
 * @see \Magento\SalesSequence\Model\Sequence::getNextValue()
 * @see \Magento\Framework\DB\Sequence\SequenceInterface::getNextValue()
 * не только возвращает результат, но и обладает сторонним эффектом,
 * добавляя в таблицу новую строку:
 * $this->connection->insert($this->meta->getSequenceTable(), []);
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Sequence.php#L82
 * Наша функция не имеет сторонних эффектов.
 *
 * @param string $entityTypeCode
 * @param int|string|null|bool|StoreInterface $store [optional]
 * @return string
 */
function df_sales_seq_next($entityTypeCode, $store = null) {
	/**
	 * 2016-01-11
	 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Sequence.php#L83
	 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Setup/InstallSchema.php#L123-L129
	 */
	return df_next_increment(df_sales_seq_meta($entityTypeCode, $store)->getSequenceTable());
}
