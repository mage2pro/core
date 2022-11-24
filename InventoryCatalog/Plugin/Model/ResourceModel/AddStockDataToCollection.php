<?php
namespace Df\InventoryCatalog\Plugin\Model\ResourceModel;
use Closure as F;
use Magento\Catalog\Model\ResourceModel\Product\Collection as C;
use Magento\Framework\DB\Select as S;
use Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection as Sb;
use Magento\InventoryIndexer\Indexer\IndexStructure as IS;
/**
 * 2020-11-23
 * @see \Df\CatalogInventory\Plugin\Model\ResourceModel\Stock\Status
 * "Add an ability to preserve out of stock (including just disabled) products in a collection
 * despite of the `cataloginventory/options/show_out_of_stock` option's value": https://github.com/mage2pro/core/issues/148
 */
final class AddStockDataToCollection {
	/**
	 * 2020-11-23
	 * @see \Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection::execute()
	 */
	function aroundExecute(Sb $sb, F $f, C $c, bool $skipAbsent, int $stockId):void {
		if (!self::$PRESERVE_ABSENT_F && !$c->getFlag(self::PRESERVE_ABSENT)) {
			$f($c, $skipAbsent, $stockId);
		}
		else {
			/**
			 * 2020-11-23
			 * It is not enough to just pass $skipAbsent = false to the plugged method,
			 * because the method uses `JOIN` insteaf of `LEFT JOIN` for the `cataloginventory_stock_status` table.
			 * But the table is empty if the `cataloginventory/options/show_out_of_stock` option is false.
			 * So I am forced to reimplement the whole method to just replace `JOIN` with `LEFT JOIN`.
			 * Interestingly, the same solution is used by the deprecated method
			 * @see \Magento\CatalogInventory\Model\ResourceModel\Stock\Status::addStockDataToCollection():
			 *	$method = $isFilterInStock ? 'join' : 'joinLeft';
			 *	$collection->getSelect()->$method(
			 *		['stock_status_index' => $this->getMainTable()],
			 *		$joinCondition,
			 *		['is_salable' => 'stock_status']
			 *	);
			 * https://github.com/magento/magento2/blob/2.4.1/app/code/Magento/CatalogInventory/Model/ResourceModel/Stock/Status.php#L245-L246
			 */
			$s = $c->getSelect(); /** @var S $s */
			if ($stockId === df_default_stock_provider()->getId()) {
				$s->joinLeft(
					['stock_status_index' => df_table('cataloginventory_stock_status')],
					sprintf('%s.entity_id = stock_status_index.product_id', C::MAIN_TABLE_ALIAS),
					[IS::IS_SALABLE => 'stock_status']
				);
			}
			else {
				$s->join(
					['product' => df_table('catalog_product_entity')],
					sprintf('product.entity_id = %s.entity_id', C::MAIN_TABLE_ALIAS),
					[]
				);
				$s->joinLeft(
					['stock_status_index' => df_stock_index_table_name_resolver()->execute($stockId)],
					'product.sku = stock_status_index.' . IS::SKU,
					[IS::IS_SALABLE]
				);
			}
		}
	}

	/**
	 * 2020-11-23
	 * 2021-02-26
	 * Despite the class uses classes absent in Magento < 2.3:
	 * @see \Magento\InventoryIndexer\Indexer\IndexStructure and
	 * @see \Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection
	 * it still proverly serves the @see PRESERVE_ABSENT constant
	 * and @see $PRESERVE_ABSENT_F static property (I have checked it).
	 * @used-by df_pc_preserve_absent()
	 * @used-by self::aroundExecute()
	 * @used-by \Df\CatalogInventory\Plugin\Model\ResourceModel\Stock\Status::beforeAddStockDataToCollection()
	 */
	const PRESERVE_ABSENT = 'mage2pro_preserve_absent';

	/**
	 * 2020-11-23
	 * 2021-02-26
	 * Despite the class uses classes absent in Magento < 2.3:
	 * @see \Magento\InventoryIndexer\Indexer\IndexStructure and
	 * @see \Magento\InventoryCatalog\Model\ResourceModel\AddStockDataToCollection
	 * it still proverly serves the @see PRESERVE_ABSENT constant
	 * and @see $PRESERVE_ABSENT_F static property (I have checked it).
	 * @used-by df_pc_preserve_absent_f()
	 * @used-by self::aroundExecute()
	 * @used-by \Df\CatalogInventory\Plugin\Model\ResourceModel\Stock\Status::beforeAddStockDataToCollection()
	 * @var bool
	 */
	static $PRESERVE_ABSENT_F;
}