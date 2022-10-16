<?php
use Df\Catalog\Model\Product as DFP;
use Magento\Catalog\Model\Product as P;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;

/**
 * 2019-11-20
 * 1) It returns a value for the whole row.
 * 2) We should use @uses df_oqi_top() because money amounts are absent for configurable children.
 * @used-by df_oqi_discount()
 * @used-by df_oqi_discount_b()
 * @used-by df_oqi_tax()
 * @param OI|QI $i
 * @return float
 */
function df_oqi_amount($i) {
	$k0 = df_trim_text_left(df_caller_f(), 'df_oqi_'); /** @var string $k0 */
	$k1 = df_trim_text_right($k0, '_b'); /** @var string $k1 */
	$i = df_oqi_top($i);
	$k = ($k1 === $k0 ? '' : 'base_') . "{$k1}_amount"; /** @var string $k */
	df_assert($i->offsetExists($k), "[df_oqi_amount] Invalid key: `$k`.");
	return (float)$i[$k];
}

/**              
 * 2020-01-31     
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param OI|QI $i
 * @return string
 */
function df_oqi_currency_c($i) {return df_oq_currency_c(df_oq($i));}

/**
 * 2017-06-09
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @used-by \Dfe\Moip\Test\Order::pItems()
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
 * 2019-11-20 It returns a value for the whole row.
 * @see df_oqi_discount_b()
 * @used-by df_oqi_price()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 * @param OI|QI $i
 * @return float
 */
function df_oqi_discount($i) {return df_oqi_amount($i);}

/**
 * 2019-11-20 It returns a value for the whole row.
 * @see df_oqi_discount()
 * @used-by df_oqi_price()
 * @param OI|QI $i
 * @return float
 */
function df_oqi_discount_b($i) {return df_oqi_amount($i);}

/**
 * 2019-11-20 It returns a value for the whole row.
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @param OI|QI $i
 * @return float
 */
function df_oqi_tax($i) {return df_oqi_amount($i);}

/**
 * 2017-02-01
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by \Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 * @return string
 */
function df_oqi_image($i) {return df_product_image_url($i->getProduct());}

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
 * @used-by \Df\Payment\Operation::oiLeafs()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by \Dfe\Moip\Test\Order::pItems()
 * @used-by \Dfe\Sift\Observer\Sales\OrderPlaceAfter::execute()
 * @used-by \Inkifi\Consolidation\Processor::pids()
 * @used-by \Stock2Shop\OrderExport\Payload::items()
 *
 * @param O|Q $oq
 * @param \Closure|null $f [optional]
 * @param string|null $locale [optional] Используется для упорядочивания элементов.
 * @return array(int => mixed)|OI[]|QI[]
 */
function df_oqi_leafs($oq, \Closure $f = null, $locale = null) {
	$r = df_sort_names(array_values(array_filter(
		$oq->getItems(), function($i) {/** @var OI|QI $i */ return df_oqi_is_leaf($i);}
	)), $locale, function($i) {/** @var OI|QI $i */ return $i->getName();}); /** @var OI[]|QI[] $r */
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
 * 2016-05-03
 * Заметил, что у order item, которым соответствуют простые варианты настраиваемого товара,
 * цена почему-то равна нулю и содержится в родительском order item.
 *
 * 2016-08-17 Цена возвращается в валюте заказа (не в учётной валюте системы).
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
 * @used-by df_oqi_price()
 * @used-by df_oqi_tax_rate()
 * @used-by df_oqi_total()
 * @used-by omx_price()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItems()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::price()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @used-by \Dfe\YandexKassa\Charge::pLoan()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @used-by \Stock2Shop\OrderExport\Payload::items()
 *
 * @param OI|QI $i
 * @param bool $withTax [optional]
 * @param bool $withDiscount [optional]
 * @return float
 */
function df_oqi_price($i, $withTax = false, $withDiscount = false) {/** @var float $r */
	$r = floatval($withTax ? $i->getPriceInclTax() : (
		df_is_oi($i) ? $i->getPrice() :
			# 2017-04-20 У меня $i->getPrice() для quote item возвращает значение в учётной валюте: видимо, из-за дефекта ядра.
			df_currency_convert_from_base($i->getBasePrice(), $i->getQuote()->getQuoteCurrencyCode())
	)) ?: ($i->getParentItem() ? df_oqi_price($i->getParentItem(), $withTax) : .0);
	/**
	 * 2017-09-30
	 * We should use @uses df_oqi_top(), because the `discount_amount` and `base_discount_amount` fields
	 * are not filled for the configurable children.
	 */
	return !$withDiscount ? $r : ($r - (df_is_oi($i) ? df_oqi_discount($i) :
		df_currency_convert_from_base(df_oqi_discount_b($i), $i->getQuote()->getQuoteCurrencyCode())
	) / df_oqi_qty($i));
}

/**
 * 2017-03-06
 * Используем @uses intval(),
 * потому что @uses \Magento\Sales\Model\Order\Item::getQtyOrdered() возвращает вещественное число.
 * @used-by df_oqi_is_leaf()                           
 * @used-by df_oqi_price()
 * @used-by df_oqi_s()
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\AlphaCommerceHub\Charge::pOrderItems()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by \Dfe\Moip\P\Preorder::pItems()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::build()
 * @used-by \Dfe\YandexKassa\Charge::pLoan()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
 * @used-by \Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 * @return int
 */
function df_oqi_qty($i) {return intval(df_is_oi($i) ? $i->getQtyOrdered() : (df_is_qi($i) ? $i->getQty() : df_error()));}

/**
 * 2016-09-07
 * 2018-08-11 @deprecated It is unused.
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
 * @param O|Q $oq
 * @return array(OI|QI)
 */
function df_oqi_roots($oq) {return $oq->getAllVisibleItems();}

/**
 * 2016-09-07
 * @used-by df_oqi_s()
 * @param O|Q $oq
 * @param \Closure $f
 * @return mixed[]
 */
function df_oqi_roots_m($oq, \Closure $f) {return array_map($f, df_oqi_roots($oq));}

/**
 * 2016-03-09
 * 2016-03-24
 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
 * будет содержать как настраиваемый товар, так и его простой вариант.
 * Простые варианты игнорируем (у них имена типа «New Very Prive-36-Almond»,
 * а нам удобнее видеть имена простыми, как у настраиваемого товара: «New Very Prive»).
 * @used-by \Df\Payment\Metadata::vars()
 * @used-by \Dfe\AllPay\Charge::pCharge()
 * @used-by \Dfe\IPay88\Charge::pCharge()
 * @param O|Q $oq
 * @param string $sep [optional] 2016-07-04 Добавил этот параметр для модуля AllPay,
 * где разделителем должен быть символ #.
 * @return string
 */
function df_oqi_s($oq, $sep = ', ') {return df_ccc($sep, df_oqi_roots_m($oq, function($i) {/** @var OI|QI $i */return df_cc_s(
	$i->getName(), 1 >= ($qty = df_oqi_qty($i)) ? null : "({$qty})"
);}));}

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
 * @used-by \Stock2Shop\OrderExport\Payload::items()
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
 * @used-by df_oqi_amount()
 * @used-by df_oqi_desc()
 * @used-by df_oqi_tax_percent()
 * @used-by df_oqi_url()
 * @used-by omx_parse_sku()
 * @used-by \Dfe\TwoCheckout\LineItem\Product::top()
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 * @used-by \Yaman\Ordermotion\Observer::BuildOrderDetail()
 * @param OI|QI $i
 * @return OI|QI
 */
function df_oqi_top($i) {return $i->getParentItem() ?: $i;}

/**
 * 2018-12-19
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @param OI|QI $i
 * @param bool $withTax [optional]
 * @param bool $withDiscount [optional]
 * @return float
 */
function df_oqi_total($i, $withTax = false, $withDiscount = false) {return
	df_oqi_price($i, $withTax, $withDiscount) * $i->getQtyOrdered()
;}

/**
 * 2017-02-01
 * @used-by \Df\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by \Dfe\AllPay\Charge::productUrls()
 * @used-by \Dfe\CheckoutCom\Charge::cProduct()
 * @used-by \Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 * @return string
 */
function df_oqi_url($i) {return df_oqi_top($i)->getProduct()->getProductUrl();}