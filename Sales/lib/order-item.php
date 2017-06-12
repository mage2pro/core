<?php
use Df\Catalog\Model\Product as DFP;
use Magento\Catalog\Model\Product as P;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;

/**
 * 2017-06-09
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @param OI|QI $i
 * @param int|null $length [optional]
 * @return string
 */
function df_oqi_desc($i, $length = null) {
	/** @var P|DFP $p */
	$p = df_oqi_top($i)->getProduct();
	return df_chop(strip_tags($p->getShortDescription() ?: $p->getDescription()) ?: $i->getName(), $length);
}

/**
 * 2017-02-01
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @param OI|QI $i
 * @return string
 */
function df_oqi_image($i) {return df_product_image_url($i->getProduct());}

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
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge::kl_order_lines()
 *
 * @param O|Q $oq
 * @param \Closure $f
 * @param string|null $locale [optional] Используется для упорядочивания элементов.
 * @return mixed[]
 */
function df_oqi_leafs($oq, \Closure $f, $locale = null) {return array_map($f,
	df_sort_names(array_values(array_filter(
		$oq->getItems(), function($i) {/** @var OI|QI $i */ return df_oqi_is_leaf($i);}
	)), $locale, function($i) {/** @var OI|QI $i */ return $i->getName();})
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
 * @used-by df_oqi_tax_rate()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
 *
 * @param OI|QI $i
 * @param bool $withTax [optional]
 * @return float
 */
function df_oqi_price($i, $withTax = false) {return
	floatval($withTax ? $i->getPriceInclTax() : (
		df_is_oi($i) ? $i->getPrice() :
			// 2017-04-20
			// У меня $i->getPrice() для quote item возвращает значение в учётной валюте:
			//видимо, из-за дефекта ядра
			df_currency_convert_from_base($i->getBasePrice(), $i->getQuote()->getQuoteCurrencyCode())
	)) ?:
		($i->getParentItem() ? df_oqi_price($i->getParentItem(), $withTax) : .0)
;}

/**
 * 2017-03-06
 * Используем @used intval(),
 * потому что @uses \Magento\Sales\Model\Order\Item::getQtyOrdered() возвращает вещественное число.
 * @used-by df_oqi_s()
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::build()
 * @param OI|QI $i
 * @return string
 */
function df_oqi_qty($i) {return intval(df_is_oi($i) ? $i->getQtyOrdered() : (
	df_is_qi($i) ? $i->getQty() : df_error()
));}

/**
 * 2016-09-07
 * @param O|Q $oq
 * @return string[]
 */
function df_oqi_roots($oq) {return array_filter(
	$oq->getItems(), function($i) {/** @var OI|QI $i */ return !$i->getParentItem();}
);}

/**
 * 2016-09-07
 * @param O|Q $oq
 * @param \Closure $f
 * @return mixed[]
 */
function df_oqi_roots_m($oq, \Closure $f) {return array_map($f, df_oqi_roots($oq));}

/**
 * 2016-03-09
 * @param O|Q $oq
 *
 * 2016-03-24
 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
 * будет содержать как настраиваемый товар, так и его простой вариант.
 * Простые варианты игнорируем (у них имена типа «New Very Prive-36-Almond»,
 * а нам удобнее видеть имена простыми, как у настраиваемого товара: «New Very Prive»).
 *
 * @used-by \Df\Payment\Metadata::vars()
 * @used-by \Dfe\AllPay\Charge::pCharge()
 * @used-by \Dfe\IPay88\Charge::pCharge()
 *
 * 2016-07-04
 * Добавил этот параметр для модуля AllPay, где разделителем должен быть символ #.
 * @param string $separator [optional]
 * @return string
 */
function df_oqi_s($oq, $separator = ', ') {return
	df_ccc($separator, df_oqi_roots_m($oq, function($i) {/** @var OI|QI $i */return df_cc_s(
		$i->getName(), 1 >= ($qty = df_oqi_qty($i)) ? null : "({$qty})"
	);}))
;}

/**
 * 2017-03-06
 * Возвращает налоговую ставку для позиции заказа в процентах.
 * $asInteger == false: 17.5% => 17.5.
 * $asInteger == true: 17.5% => 1750
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @param OI|QI $i
 * @param bool $asInteger [optional]
 * @return float
 */
function df_oqi_tax_rate($i, $asInteger = false) {
	/** @var float $result */
	$result = 100 * (df_oqi_price($i, true) - ($withoutTax = df_oqi_price($i))) / $withoutTax;
	return !$asInteger ? $result : round(100 * $result);
}

/**
 * 2016-08-18
 * @param OI|QI $i
 * @return OI|QI
 */
function df_oqi_top($i) {return $i->getParentItem() ?: $i;}

/**
 * 2017-02-01
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\AllPay\Charge::productUrls()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @param OI|QI $i
 * @return string
 */
function df_oqi_url($i) {return df_oqi_top($i)->getProduct()->getProductUrl();}