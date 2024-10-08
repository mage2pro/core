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
 */
function df_oqi_amount($i):float {
	$k0 = df_trim_text_left(df_caller_f(), 'df_oqi_'); /** @var string $k0 */
	$k1 = df_trim_text_right($k0, '_b'); /** @var string $k1 */
	$i = df_oqi_top($i);
	$k = ($k1 === $k0 ? '' : 'base_') . "{$k1}_amount"; /** @var string $k */
	df_assert($i->offsetExists($k), "[df_oqi_amount] Invalid key: `$k`.");
	return (float)$i[$k];
}

/**              
 * 2020-01-31     
 * @used-by Dfe\Sift\Payload\OQI::p()
 * @param OI|QI $i
 */
function df_oqi_currency_c($i):string {return df_oq_currency_c(df_oq($i));}

/**
 * 2017-06-09
 * @used-by Dfe\Moip\P\Preorder::pItems()
 * @used-by Dfe\Moip\Test\Order::pItems()
 * @used-by Dfe\YandexKassa\Charge::pLoan()
 * @used-by Dfe\YandexKassa\Charge::pTax()
 * @param OI|QI $i
 */
function df_oqi_desc($i, int $max = 0):string {
	$p = df_oqi_top($i)->getProduct(); /** @var P|DFP $p */
	return df_chop(strip_tags($p->getShortDescription() ?: $p->getDescription()) ?: $i->getName(), $max);
}

/**
 * 2019-11-20 It returns a value for the whole row.
 * @see df_oqi_discount_b()
 * @used-by df_oqi_price()
 * @used-by Dfe\Vantiv\Charge::pCharge()
 * @param OI|QI $i
 */
function df_oqi_discount($i):float {return df_oqi_amount($i);}

/**
 * 2019-11-20 It returns a value for the whole row.
 * @see df_oqi_discount()
 * @used-by df_oqi_price()
 * @param OI|QI $i
 */
function df_oqi_discount_b($i):float {return df_oqi_amount($i);}

/**
 * 2017-02-01
 * @used-by Dfe\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by Dfe\CheckoutCom\Charge::cProduct()
 * @used-by Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 */
function df_oqi_image($i):string {return df_product_image_url($i->getProduct());}

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
 * @used-by Dfe\YandexKassa\Charge::pTaxLeafs()
 * Yandex.Kassa does not provide a possibility to specify the shopping cart discounts in a separayte row,
 * so I use $afterDiscount = true.
 *
 * @used-by df_oqi_price()
 * @used-by df_oqi_tax_rate()
 * @used-by df_oqi_total()
 * @used-by omx_price()
 * @used-by Dfe\AlphaCommerceHub\Charge::pOrderItems()
 * @used-by Dfe\CheckoutCom\Charge::cProduct()
 * @used-by Dfe\Moip\P\Preorder::pItems()
 * @used-by Dfe\Sift\Payload\OQI::p()
 * @used-by Dfe\TwoCheckout\LineItem\Product::price()
 * @used-by Dfe\Vantiv\Charge::pCharge()
 * @used-by Dfe\YandexKassa\Charge::pLoan()
 * @used-by Dfe\YandexKassa\Charge::pTaxLeafs()
 * @used-by Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 */
function df_oqi_price($i, bool $withTax = false, bool $withDiscount = false):float {/** @var float $r */
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
 * @used-by Dfe\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by Dfe\AlphaCommerceHub\Charge::pOrderItems()
 * @used-by Dfe\CheckoutCom\Charge::cProduct()
 * @used-by Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by Dfe\Moip\P\Preorder::pItems()
 * @used-by Dfe\Sift\Payload\OQI::p()
 * @used-by Dfe\TwoCheckout\LineItem\Product::build()
 * @used-by Dfe\YandexKassa\Charge::pLoan()
 * @used-by Dfe\YandexKassa\Charge::pTaxLeafs()
 * @used-by Inkifi\Pwinty\AvailableForDownload::images()
 * @used-by Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 */
function df_oqi_qty($i):int {return intval(df_is_oi($i) ? $i->getQtyOrdered() : (df_is_qi($i) ? $i->getQty() : df_error()));}

/**
 * 2016-03-09
 * 2016-03-24
 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
 * будет содержать как настраиваемый товар, так и его простой вариант.
 * Простые варианты игнорируем (у них имена типа «New Very Prive-36-Almond»,
 * а нам удобнее видеть имена простыми, как у настраиваемого товара: «New Very Prive»).
 * 2016-07-04 Добавил параметр $sep для модуля AllPay, где разделителем должен быть символ #.
 * @used-by Df\Payment\Metadata::vars()
 * @used-by Dfe\AllPay\Charge::pCharge()
 * @used-by Dfe\IPay88\Charge::pCharge()
 * @param O|Q $oq
 */
function df_oqi_s($oq, string $sep = ', '):string {return df_ccc($sep, df_oqi_roots_m($oq,
	function($i):string {/** @var OI|QI $i */return df_cc_s($i->getName(), 1 >= ($qty = df_oqi_qty($i)) ? null : "({$qty})");}
));}

/**
 * 2019-11-20 It returns a value for the whole row.
 * @used-by Dfe\Vantiv\Charge::pCharge()
 * @param OI|QI $i
 */
function df_oqi_tax($i):float {return df_oqi_amount($i);}

/**
 * 2017-03-06
 * Возвращает налоговую ставку для позиции заказа в процентах.
 * 		$asInteger == false: 17.5% => 17.5.
 * 		$asInteger == true: 17.5% => 1750
 * 2017-09-30
 * @todo Why do not just use \Magento\Sales\Model\Order\Item::getTaxPercent()?
 * I use it for Yandex.Kassa: @see \Dfe\YandexKassa\Charge::pTax()
 * @used-by Dfe\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by Dfe\Klarna\Api\Checkout\V2\Charge\Products::p()
 * @used-by Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 * @return float|int
 */
function df_oqi_tax_rate($i, bool $asInteger = false) {
	$r = df_tax_rate(df_oqi_price($i, true), df_oqi_price($i));  /** @var float $r */
	return !$asInteger ? $r : round(100 * $r);
}

/**
 * 2017-09-30
 * @used-by Dfe\YandexKassa\Charge::pTaxLeafs()
 * The `tax_percent` field is not filled for configurable children, that is why we use @uses df_oqi_top()
 * @param OI|QI $i
 */
function df_oqi_tax_percent($i):float {return floatval(df_oqi_top($i)->getTaxPercent());}

/**
 * 2018-12-19
 * @used-by Dfe\Vantiv\Charge::pCharge()
 * @param OI|QI $i
 */
function df_oqi_total($i, bool $withTax = false, bool $withDiscount = false):float {return
	df_oqi_price($i, $withTax, $withDiscount) * $i->getQtyOrdered()
;}

/**
 * 2017-02-01
 * @used-by Dfe\GingerPaymentsBase\Charge::pOrderLines_products()
 * @used-by Dfe\AllPay\Charge::productUrls()
 * @used-by Dfe\CheckoutCom\Charge::cProduct()
 * @used-by Stock2Shop\OrderExport\Payload::items()
 * @param OI|QI $i
 */
function df_oqi_url($i):string {return df_oqi_top($i)->getProduct()->getProductUrl();}