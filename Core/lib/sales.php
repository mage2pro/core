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
	 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Manager.php#L48-L51
	 */
	/** @var \Magento\SalesSequence\Model\ResourceModel\Meta $metaResource */
	$metaResource = df_o(\Magento\SalesSequence\Model\ResourceModel\Meta::class);
	/** @var \Magento\SalesSequence\Model\Meta $meta */
	$meta = $metaResource->loadByEntityTypeAndStore($entityTypeCode, df_store_id($store));
	/**
	 * 2016-01-11
	 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Sequence.php#L83
	 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Setup/InstallSchema.php#L123-L129
	 */
	return df_next_increment($meta['sequence_table']);
}
