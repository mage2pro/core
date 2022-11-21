<?php
/**
 * 2016-10-23
 * Используемой реализации, видимо, идентична такая: sprintf('%.2F', df_float($value))
 * В то же время реализация sprintf('%.2f', df_float($value)) вовсе не идентична используемой,
 * потому что она использует десятичный разделитель текущей локали: для России — запятую.
 * https://php.net/manual/function.sprintf.php
 * 3 => 3.00
 * 3.333 => 3.33
 * 3.300 => 3.30
 * https://3v4l.org/AUTCA
 * @used-by dff_2f()
 * @used-by dff_2i()
 * @used-by \Dfe\Qiwi\Method::amountFormat()
 * @used-by \Dfe\Robokassa\Method::amountFormat()
 * @used-by \Dfe\SecurePay\Charge::amountFormat()
 * @used-by \Dfe\TwoCheckout\Method::amountFormat()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @used-by \Dfe\YandexKassa\Method::amountFormat()
 * @used-by \TFC\Core\B\Checkout\Success::_toHtml() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/42)
 * @used-by \TFC\GoogleShopping\Att\Price::format() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/6)
 */
function dff_2(float $v, int $prec = 2):string {return number_format($v, $prec, '.', '');}

/**
 * 2016-09-08
 * @used-by \Dfe\Color\Image::labels()
 * @used-by \TFC\Core\Plugin\Paypal\Model\Cart::aroundGetAmounts()
 * @param float|int|string $v
 */
function dff_2f($v):float {return floatval(dff_2(floatval($v)));}

/**
 * 2016-10-23 Для нецелых чисел работает как @see dff_2(), а для целых — отбрасывает десятичную часть.
 * 3 => 3
 * 3.333 => 3.33
 * 3.300 => 3.30
 * https://3v4l.org/AUTCA
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
 * @param int|float $v
 */
function dff_2i($v, int $prec = 2):string {return is_int($v) ? (string)$v : dff_2($v, $prec);}

/**
 * 2015-04-09 Форматирует вещественное число с отсечением незначащих нулей после запятой.
 * 2016-10-23
 * 3 => 3
 * 3.333 => 3.333
 * 3.300 => 3.3
 * 2022-10-29 @deprecated It is unused.
 * @param float|int $v
 */
function dff_chop0($v):string {
	$f = df_float($v); /** @var float $f */
	$intPart = (int)$f; /** @var int $intPart */
	# намеренно используем «==»
	return $f == $intPart ? (string)$intPart : rtrim(sprintf('%f', $f), '0');
}

/**
 * 2017-09-29
 * 2017-11-03
 * I now provide the $deviation argument to @uses dff_eq0() to fix the issue:
 * «Unable to generate tax data for Yandex.Kassa.
 * The order's grand total is 3000.00. The calculated grand total from tax data is 2999.80.»
 * https://github.com/mage2pro/yandex-kassa/issues/2
 * I use deviation of 0.1% of $a.
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Sales\Model\Order::afterCanInvoice() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/256)
 * @used-by \Customweb\RealexCw\Helper\InvoiceItem::getInvoiceItems()	tradefurniturecompany.co.uk
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * @param float|string|int $a
 * @param float|string|int $b
 */
function dff_eq($a, $b):bool {return dff_eq0(floatval($a) - floatval($b), .001 * $a);}

/**      
 * 2016-09-08
 * 2017-11-03
 * I have added the $deviation argument to fix the issue:
 * «Unable to generate tax data for Yandex.Kassa.
 * The order's grand total is 3000.00. The calculated grand total from tax data is 2999.80.»
 * https://github.com/mage2pro/yandex-kassa/issues/2
 * By default, 0.1% deviation is allowed.
 * @used-by dff_eq()
 * @used-by dfp_refund()
 * @used-by \Customweb\RealexCw\Helper\InvoiceItem::getInvoiceItems()	tradefurniturecompany.co.uk
 * @used-by \Df\Sales\Plugin\Model\ResourceModel\Order\Handler\State::aroundCheck()
 * @used-by \Dfe\Color\Image::labels()
 * @used-by \Dfe\TwoCheckout\Charge::lineItems()
 * @used-by \Dfe\TwoCheckout\Method::_refund()
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeaf()
 * @used-by \TFC\Core\Plugin\Paypal\Model\Cart::aroundGetAmounts()
 */
function dff_eq0(float $a, float $deviation = .001):bool {return abs($a) < $deviation;}