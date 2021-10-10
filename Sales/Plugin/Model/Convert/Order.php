<?php
namespace Df\Sales\Plugin\Model\Convert;
use Closure as F;
use Magento\Sales\Model\Convert\Order as Sb;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Invoice as I;
# 2021-10-10
# "Magento 2 core bug:
# invoice items added by `\Magento\Sales\Model\Service\InvoiceService::prepareInvoice()` are discarded later
# because the collection regards itself as not loaded": https://github.com/mage2pro/core/issues/161
final class Order {
	/**
	 * 2021-05-04
	 * @see \Magento\AdobeStockImageAdminUi\Model\Listing\DataProvider::getData()
	 * @param Sb $sb
	 * @param F $f
	 * @return array(string => mixed)
	 */
	function aroundToInvoice(Sb $sb, F $f, O $o) {
		$r = $f($o); /** @var I $i */
		$r->getItemsCollection()->load();
		return $r;
	}
}
