<?php
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;

/**
 * 2017-04-20
 * @used-by df_oqi_leafs()
 * @used-by Yaman\Ordermotion\Observer::BuildOrderDetail()
 * @param OI|QI $i
 */
function df_oqi_is_leaf($i):bool {return df_is_oi($i) ? !$i->getChildrenItems() : (
	df_is_qi($i) ? !$i->getChildren() : df_error()
);}

/**
 * 2016-09-07
 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
 * будет содержать как настраиваемый товар, так и его простой вариант.
 * Настраиваемые товары мы отфильтровываем.
 *
 * 2017-01-31
 * Добавил @uses array_values(), чтобы функция возвращала именно mixed[], а не array(itemId => mixed).
 * Это важно, потому что эту функцию мы используем только для формирования запросов к API платёжных систем,
 * а этим системам порой (например, Klarna) не всё равно,
 * получат они в JSON-запросе массив или хэш с целочисленными индексами.
 *
 * array_values() надо применять именно после array_filter(),
 * потому что array_filter() создаёт дыры в индексах результата.
 *
 * 2017-02-02
 * Отныне функция упорядочивает позиции заказа по имени.
 * Ведь эта функция используется только для передачи позиций заказа в платежные системы,
 * а там они отображаются покупателю и администратору, и удобно, чтобы они были упорядочены по имени.
 *
 * 2022-11-22 $locale используется для упорядочивания элементов.
 *
 * @used-by Df\Payment\Operation::oiLeafs()
 * @used-by Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by Dfe\Moip\Test\Order::pItems()
 * @used-by Dfe\Sift\Observer\Sales\OrderPlaceAfter::execute()
 * @used-by Inkifi\Consolidation\Processor::pids()
 * @used-by Stock2Shop\OrderExport\Payload::items()
 * @param O|Q $oq
 * @return array(int => mixed)|OI[]|QI[]
 */
function df_oqi_leafs($oq, Closure $f = null, string $l = ''):array {
	$r = df_sort(array_values(array_filter(
		$oq->getItems(), function($i) {/** @var OI|QI $i */ return df_oqi_is_leaf($i);}
	)), function($i) {/** @var OI|QI $i */ return $i->getName();}, true, $l); /** @var OI[]|QI[] $r */
	/**
	 * 2020-02-04 
	 * If we got here from the `sales_order_place_after` event, then the order is not yet saved,
	 * and order items are not yet associated with the order.
	 * I associate order items with the order manually to make @see OI::getOrder() working properly.
	 */
	if (df_is_o($oq)) {
		foreach ($r as $i) {/** @var OI $i */
			if (!$i->getOrderId()) {
				$i->setOrder($oq);
			}
		}
	}
	return !$f ? $r : array_map($f, $r);
}

/**
 * 2016-09-07
 * 2021-05-30
 * 1) The previous implementation:
 * 		array_filter($oq->getItems(), function($i) {return !$i->getParentItem();})
 * https://github.com/mage2pro/core/blob/7.5.0/Sales/lib/order-item.php#L245-L253
 * 2) I have made the new implemention by analogy with:
 * 2.1) @see \Magento\Quote\Model\Quote::getAllVisibleItems()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Quote/Model/Quote.php#L1373-L1387
 * https://github.com/magento/magento2/blob/2.4.2-p1/app/code/Magento/Quote/Model/Quote.php#L1439-L1453
 * 2.2) @see \Magento\Sales\Model\Order::getAllVisibleItems()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Sales/Model/Order.php#L1338-L1350
 * https://github.com/magento/magento2/blob/2.4.2-p1/app/code/Magento/Sales/Model/Order.php#L1509-L1523
 * @used-by df_oqi_roots_m()
 * @param O|Q $oq
 * @return array(OI|QI)
 */
function df_oqi_roots($oq) {return $oq->getAllVisibleItems();}

/**
 * 2016-09-07
 * @used-by df_oqi_s()
 * @param O|Q $oq
 */
function df_oqi_roots_m($oq, Closure $f):array {return array_map($f, df_oqi_roots($oq));}

/**
 * 2016-08-18
 * @used-by df_oqi_amount()
 * @used-by df_oqi_desc()
 * @used-by df_oqi_tax_percent()
 * @used-by df_oqi_url()
 * @used-by omx_parse_sku()
 * @used-by Dfe\TwoCheckout\LineItem\Product::options()
 * @used-by Yaman\Ordermotion\Observer::BuildOrderDetail()
 * @param OI|QI $i
 * @return OI|QI
 */
function df_oqi_top($i) {return $i->getParentItem() ?: $i;}