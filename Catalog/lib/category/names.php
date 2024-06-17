<?php
use Magento\Catalog\Model\Product as P;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Api\Data\StoreInterface as IStore;

/**
 * 2020-02-05
 * @see df_store_names()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param int|string|P|OI|QI $p
 * @param int|string|null|bool|IStore $s [optional]
 * @return string[]
 */
function df_category_names($p, $s = false):array {return df_each(
	df_product($p, $s)->getCategoryCollection()->addAttributeToSelect($k = 'name'), $k
);}