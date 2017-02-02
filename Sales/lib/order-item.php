<?php
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Sales\Api\Data\OrderItemInterface as IOI;

/**
 * 2017-02-01
 * @param OI|IOI $i
 * @return string
 */
function df_oi_image(IOI $i) {return df_product_image_url($i->getProduct());}

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
 * 2017-02-02
 * Отныне функция упорядочивает позиции заказа по имени.
 * Ведь эта функция используется только для передачи позиций заказа в платежные системы,
 * а там они отображаются покупателю и администратору,
 * и удобно, чтобы они были упорядочены по имени.
 *
 * @used-by \Df\Payment\Charge::oiLeafs()
 * @used-by \Dfe\Klarna\V2\Charge::kl_order_lines()
 *
 * @param O $o
 * @param \Closure $f
 * @param string|null $locale [optional]
 * @return mixed[]
 */
function df_oi_leafs(O $o, \Closure $f, $locale = null) {return array_map($f,
	df_sort_names(array_values(array_filter(
		$o->getItems(), function(OI $i) {return !$i->getChildrenItems();}
	)), $locale, function(OI $i) {return $i->getName();})
);}

/**
 * 2016-05-03
 * Заметил, что у order item, которым соответствуют простые варианты настраиваемого товара,
 * цена почему-то равна нулю и содержится в родительском order item.
 *
 * 2016-08-17
 * Цена возвращается в валюте заказа (не в учётной валюте системы).
 *
 * 2017-02-01
 * Замечение №1
 * Кроме @uses \Magento\Sales\Model\Order\Item::getPrice()
 * есть ещё метод @see \Magento\Sales\Model\Order\Item::getPriceInclTax().
 * Мы используем именно getPrice(), потому что налоги нам удобнее указывать отдельной строкой,
 * а не размазывать их по товарам.
 * How is getPrice() calculated for an order item? https://mage2.pro/t/2576
 * How is getPriceInclTax() calculated for an order item? https://mage2.pro/t/2577
 * How is getRowTotal() calculated for an order item? https://mage2.pro/t/2578
 * How is getRowTotalInclTax() calculated for an order item?  https://mage2.pro/t/2579
 *
 * Замечение №2
 * Функция возвращает именно стоимость одной единицы товара, а не стоимость строки заказа
 * (потому что использует getPrice(), а не getRowTotal()).
 *
 * 2017-02-02
 * Оказывается, @uses \Magento\Sales\Model\Order\Item::getPrice()
 * может возвращать не число, а строку.
 * И тогда если $i — это вариант настраиваемого товара, то getPrice() вернёт строку «0.0000».
 * Следующей неожиданностью является то, что операция ! для такой строки возвращает false.
 * !"0.0000" === false.
 * И наша функция перестаёт корректно работать.
 * По этой причине стал использовать @uses floatval()
 *
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
 *
 * @param OI|IOI $i
 * @param bool $withTax [optional]
 * @return float
 */
function df_oi_price(IOI $i, $withTax = false) {return
	floatval($withTax ? $i->getPriceInclTax() : $i->getPrice()) ?:
		($i->getParentItem() ? df_oi_price($i->getParentItem(), $withTax) : .0)
;}

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

/**
 * 2016-08-18
 * @param OI|IOI $i
 * @return OI|IOI
 */
function df_oi_top(IOI $i) {return $i->getParentItem() ?: $i;}

/**
 * 2017-02-01
 * @used-by \Dfe\AllPay\Charge::productUrls()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @param OI|IOI $i
 * @return string
 */
function df_oi_url(IOI $i) {return df_oi_top($i)->getProduct()->getProductUrl();}