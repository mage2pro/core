<?php
use Dfe\SalesSequence\Model\Meta;
use Magento\SalesSequence\Model\Manager;
use Magento\SalesSequence\Model\Meta as MetaM;
use Magento\SalesSequence\Model\ResourceModel\Meta as MetaR;
use Magento\Store\Api\Data\StoreInterface;
/**
 * 2016-01-11
 * @used-by df_cm_set_increment_id()
 */
function df_sales_seq_m():Manager {return df_o(Manager::class);}

/**
 * 2016-01-26
 * @used-by df_sales_seq_next()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::updateNextNumber()
 * @used-by \Dfe\SalesSequence\Plugin\Model\Manager::aroundGetSequence()
 * @param string $entityType
 * @param int|string|null|bool|StoreInterface $s [optional]
 * @return MetaM|Meta
 */
function df_sales_seq_meta($entityType, $s = null) {return dfcf(function($entityType, $sid) {
	$r = df_o(MetaR::class); /** @var MetaR $r */
	/**
	 * 2016-01-26
	 * By analogy with @see \Magento\SalesSequence\Model\Manager::getSequence()
	 * https://github.com/magento/magento2/blob/d50ee5/app/code/Magento/SalesSequence/Model/Manager.php#L48
	 */
	return $r->loadByEntityTypeAndStore($entityType, $sid);
}, [$entityType, df_store_id($s)]);}

/**
 * 2016-01-11
 * Первая реализация была наивной:
 * 		return df_sales_seq_m()->getSequence($entityTypeCode, df_store_id($store))->getNextValue();
 * Она неправильна тем, что метод
 * @see \Magento\SalesSequence\Model\Sequence::getNextValue()
 * @see \Magento\Framework\DB\Sequence\SequenceInterface::getNextValue()
 * не только возвращает результат, но и обладает сторонним эффектом,
 * добавляя в таблицу новую строку:
 * 		$this->connection->insert($this->meta->getSequenceTable(), []);
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Sequence.php#L82
 * Наша функция не имеет сторонних эффектов.
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Model/Sequence.php#L83
 * https://github.com/magento/magento2/blob/d50ee54/app/code/Magento/SalesSequence/Setup/InstallSchema.php#L123-L129
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::nextNumbersFromDb()
 * @param string $entityTypeCode
 * @param int|string|null|bool|StoreInterface $store [optional]
 */
function df_sales_seq_next($entityTypeCode, $store = null):string {return df_next_increment(
	df_sales_seq_meta($entityTypeCode, $store)->getSequenceTable()
);}