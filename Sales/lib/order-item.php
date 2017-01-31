<?php
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Api\Data\OrderItemInterface as IOI;

/**
 * 2016-08-18
 * @param OI|IOI $item
 * @return OI|IOI
 */
function df_oi_parent(IOI $item) {return $item->getParentItem() ?: $item;}

/**
 * 2016-05-03
 * Заметил, что у order item, которым соответствуют простые варианты настраиваемого товара,
 * цена почему-то равна нулю и содержится в родительском order item.
 * 2016-08-17
 * Цена возвращается в валюте заказа (не в учётной валюте системы).
 * @param OI|IOI $item
 * @return float
 */
function df_oi_price(IOI $item) {
	return $item->getPrice() ?: (
		$item->getParentItem() ? df_oi_price($item->getParentItem()) : 0
	);
}

/**
 * 2016-09-07
 * Если товар является настраиваемым, то
 * @uses \Magento\Sales\Model\Order::getItems()
 * будет содержать как настраиваемый товар, так и его простой вариант.
 * Настраиваемые товары мы отфильтровываем.
 *
 * 2017-01-31
 * Добавил @uses array_values(),
 * чтобы функция фозвращала именно mixed[], а не array(itemId => mixed).
 * Это важно, потому что эту функцию мы используем
 * только для формирования запросов к API платёжных систем,
 * а этим системам порой (например, Klarna) не всё равно,
 * получат они в JSON-запросе массив или хэш с целочисленными индексами.
 *
 * array_values() надо применять именно после array_filter(),
 * потому что array_filter() создаёт дыры в индексах результата.
 *
 * @used-by \Df\Payment\Charge::oiLeafs()
 * @used-by \Dfe\Klarna\V2\Charge::kl_order_lines()
 *
 * @param O $o
 * @param \Closure $f
 * @return mixed[]
 */
function df_oi_leafs(O $o, \Closure $f) {return array_map($f, array_values(array_filter(
	$o->getItems(), function(OI $i) {return !$i->getChildrenItems();}
)));}

/**
 * 2016-09-07
 * @param O $o
 * @return string[]
 */
function df_oi_roots(O $o) {return
	array_filter($o->getItems(), function(OI $i) {return !$i->getParentItem();})
;}

/**
 * 2016-09-07
 * @param O $o
 * @param \Closure $f
 * @return mixed[]
 */
function df_oi_roots_m(O $o, \Closure $f) {return array_map($f, df_oi_roots($o));}

/**
 * 2016-03-09
 * @param O $order
 *
 * 2016-03-24
 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
 * будет содержать как настраиваемый товар, так и его простой вариант.
 * Простые варианты игнорируем (у них имена типа «New Very Prive-36-Almond»,
 * а нам удобнее видеть имена простыми, как у настраиваемого товара: «New Very Prive»).
 *
 * 2016-07-04
 * Добавил этот параметр для модуля AllPay, где разделителем должен быть символ #.
 * @param string $separator [optional]
 * @return string
 */
function df_oi_s(O $order, $separator = ', ') {return
	df_ccc($separator, df_oi_roots_m($order, function(OI $i) {
		/** @var int $qty */
		$qty = $i->getQtyOrdered();
		return df_cc_s($i->getName(), 1 >= $qty ? null : "({$qty})");
	}))
;}