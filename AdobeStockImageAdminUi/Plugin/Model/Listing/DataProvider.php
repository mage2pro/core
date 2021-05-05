<?php
namespace Df\AdobeStockImageAdminUi\Plugin\Model\Listing;
use Closure as F;
use Magento\AdobeStockImageAdminUi\Model\Listing\DataProvider as Sb;
# 2021-05-04
# «Failed to retrieve Adobe Stock search files results» / «Api Key is required»:
# https://github.com/canadasatellite-ca/site/issues/74
final class DataProvider {
	/**
	 * 2021-05-04
	 * @see \Magento\AdobeStockImageAdminUi\Model\Listing\DataProvider::getData()
	 * @param Sb $sb
	 * @param F $f
	 * @return array(string => mixed)
	 */
	function aroundGetData(Sb $sb, F $f) {return
		df_cfg('adobe_stock/integration/enabled') ? $f() : ['items' => [], 'totalRecords' => 0]
	;}
}