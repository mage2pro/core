<?php
namespace Df\PaypalClone\Source;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Sales\Model\Order as O;
/**
 * 2016-07-17
 * Эта опция задаёт способ идентификации платежа.
 * Наиболее очевидным и удобным способом является использование идентификатора заказа.
 * Однако allPay допускает в идентификаторе платежа (параметре «MerchantTradeNo»)
 * только цифры и латинские буквы:
 * «Merchant trade number».
 * Varchar(20)
 * «Merchant trade number could not be repeated.
 * It is composed with upper and lower cases of English letter and numbers.»
 *
 * В принципе, стандартные номера заказов удовлетворяют этим условиям,
 * но вот нестандартные, вида ORD-2016/07-00274
 * (которые делает наш модуль Sales Documents Numberation) — не удовлетворяют.
 *
 * Поэтому если магазин использует нестандартные номера заказов,
 * то ему для идентификации платежей надо использовать не номера заказов,
 * а автосоздаваемые идентификаторы платежей.
 */
final class Identification extends \Df\Config\Source {
	/**
	 * 2016-07-17
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return [
		'increment_id' => 'Visible ID (like «10000365»)', self::$ID => 'Internal ID (like «365»)'
	];}

	/**
	 * 2016-07-17
	 * @used-by \Df\PaypalClone\Charge::id()
	 * @param O $o
	 * @return string
	 * @throws \Exception|LE
	 */
	static function get(O $o) {
		/** @var string $result */
		if (self::$ID === dfps($o)->v('identification')) {
			// 2016-08-27 Метод должен возвращать неизменное значение для конкретного заказа.
			/** @var array(string => string) $cache */
			df_assert(ctype_digit($result = $o->getId() ?: df_next_increment('sales_order')));
		}
		else if (!ctype_digit($result = $o->getIncrementId())) {
			df_error(__(
				"«%1» is not allowed as a payment identifier for %2, "
				."because it should contain only digits.<br/>"
				."Please set the «<b>Internal ID</b>» value for the "
				."«Mage2.PRO» → «Payment» → «{%2}» → «Payment Identification Type» backend option."
				,$result, dfpm_title($o)
			));
		}
		return $result;
	}

	/** @var string */
	private static $ID = 'id';
}