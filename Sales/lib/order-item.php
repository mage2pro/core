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
 * @used-by \Dfe\Moip\T\Order::pItems()
 * @used-by \Dfe\YandexKassa\Charge::pLoan()
 * @used-by \Dfe\YandexKassa\Charge::pTax()
 * @param OI|QI $i
 * @param int|null $max [optional]
 * @return string
 */
function df_oqi_desc($i, $max = null) {
	$p = df_oqi_top($i)->getProduct(); /** @var P|DFP $p */
	return df_chop(strip_tags($p->getShortDescription() ?: $p->getDescription()) ?: $i->getName(), $max);
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
 * @return array(int => mixed)
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
 * 2017-09-25 The function returns the product unit price, not the order row price.
 * 2017-09-30
 * I have added the $afterDiscount flag.
 * It is used oly for the shopping cart price rules.
 * $afterDiscount = false: the functon will return a result BEFORE discounts subtraction.
 * $afterDiscount = true: the functon will return a result AFTER discounts subtraction.
 * For now, I use $afterDiscount = true only for Yandex.Market:
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * Yandex.Kassa does not provide a possibility to specify the shopping cart discounts in a separayte row,
 * so I use $afterDiscount = true.
 *
 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItems()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
 * @used-by \Dfe\YandexKassa\Charge::pLoan()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @used-by df_oqi_tax_rate()
 * @param OI|QI $i
 * @param bool $withTax [optional]
 * @param bool $withDiscount [optional]
 * @return float
 */
function df_oqi_price($i, $withTax = false, $withDiscount = false) {
	/** @var float $r */
	$r = floatval($withTax ? $i->getPriceInclTax() : (
		df_is_oi($i) ? $i->getPrice() :
			// 2017-04-20
			// У меня $i->getPrice() для quote item возвращает значение в учётной валюте:
			// видимо, из-за дефекта ядра
			df_currency_convert_from_base($i->getBasePrice(), $i->getQuote()->getQuoteCurrencyCode())
	)) ?: ($i->getParentItem() ? df_oqi_price($i->getParentItem(), $withTax) : .0);
	/**
	 * 2017-09-30
	 * We should use @uses df_oqi_top(), because the `discount_amount` and `base_discount_amount` fields
	 * are not filled for the configurable children.
	 */
	return !$withDiscount ? $r : ($r - (df_is_oi($i) ? df_oqi_top($i)->getDiscountAmount() :
		df_currency_convert_from_base(
			df_oqi_top($i)->getBaseDiscountAmount(), $i->getQuote()->getQuoteCurrencyCode()
		)
	) / df_oqi_qty($i));
}

/**
 * 2017-03-06
 * Используем @used intval(),
 * потому что @uses \Magento\Sales\Model\Order\Item::getQtyOrdered() возвращает вещественное число.
 * @used-by df_oqi_s()
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItems()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::build()
 * @used-by \Dfe\YandexKassa\Charge::pLoan()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @param OI|QI $i
 * @return int
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
 * 2017-09-30
 * @todo Why do not just use \Magento\Sales\Model\Order\Item::getTaxPercent()?
 * I use it for Yandex.Kassa: @see \Dfe\YandexKassa\Charge::pTax()
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @param OI|QI $i
 * @param bool $asInteger [optional]
 * @return float|int
 */
function df_oqi_tax_rate($i, $asInteger = false) {
	$r = df_tax_rate(df_oqi_price($i, true), df_oqi_price($i));  /** @var float $r */
	return !$asInteger ? $r : round(100 * $r);
}

/**
 * 2017-09-30
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * The `tax_percent` field is not filled for the configurable childs, so we should use @uses df_oqi_top()
 * @param OI|QI $i
 * @return float
 */
function df_oqi_tax_percent($i) {return floatval(df_oqi_top($i)->getTaxPercent());}

/**
 * 2016-08-18
 * @used-by df_oqi_desc()
 * @used-by df_oqi_tax_percent()
 * @used-by df_oqi_url()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::top()
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