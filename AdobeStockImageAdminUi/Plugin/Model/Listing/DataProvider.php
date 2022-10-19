<?php
namespace Df\AdobeStockImageAdminUi\Plugin\Model\Listing;
use Closure as F;
# 2022-10-19
# If Magento is installed via Composer, then the `Magento_AdobeStockImageAdminUi` module is installed automatically.
# If Magento is installed via Git, then the `Magento_AdobeStockImageAdminUi` module can be installed via Composer:
# 	composer2 require magento/adobe-stock-integration:*
# https://github.com/magento/adobe-stock-integration/wiki/Installation
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
	function aroundGetData(Sb $sb, F $f): array {return
		df_cfg('adobe_stock/integration/enabled') ? $f() : ['items' => [], 'totalRecords' => 0]
	;}
}