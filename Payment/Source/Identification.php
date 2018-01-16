<?php
namespace Df\Payment\Source;
use Df\Core\Exception as DFE;
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
	 * 2016-08-27 Метод должен возвращать неизменное значение для конкретного заказа.
	 * @used-by \Df\PaypalClone\Charge::id()
	 * @used-by \Dfe\Qiwi\Charge::id()
	 * @param O $o
	 * @return string
	 * @throws DFE
	 */
	static function get(O $o) {
		$s = dfps($o); /** @var \Df\Payment\Settings $s */
		/** @var string $r */
		// 2017-08-14
		// I intentionally use the negative condition here
		// because «increment_id» is the default value, and it is assumed when there is no a value at all.
		$r = $s->v('idPrefix') . (self::$ID !== $s->v('identification') ? $o->getIncrementId() : (
			$o->getId() ?: df_next_increment('sales_order')
		));
		if ($rules = $s->v('identification_rules') /** @var array(string => string|int|null)|null $rules */) {
			$error = function($cause) use($r, $o) {df_error(
				'«%1» is not allowed as a payment identifier for %2 because %3.<br/>'
				.'Please set the «<b>Internal ID</b>» value for the '
				.'«Mage2.PRO» → «Payment» → «%2» → «Payment Identification Type» backend option.'
				,$r, dfpm_title($o), $cause
			);}; /** @var \Closure $error */
			if (($maxLength = dfa($rules, 'max_length')) && $maxLength < ($length = strlen($r))) {
				$error(__('a payment identifier should contain not more than %1 characters, but the current identifier contains %2 characters', $maxLength, $length));
			}
			/** @var array $matches */  /** @var array(string => string) $regex */
			if (($regex = dfa($rules, 'regex')) && !preg_match($regex['pattern'], $r, $matches)) {
				$error(__($regex['message']));
			}
			/** @var int $maxInt */
			if (isset($rules['ctype_digit']) && !ctype_digit($r)) {
				$error(__('a payment identifier should contain only digits'));
			}
			/** @var int $maxInt */
			if (($maxInt = intval(dfa($rules, 'max_int'))) && intval($r) > $maxInt) {
				$error(__('a payment identifier should be a natural number not greater than %1', $maxInt));
			}
		}
		return $r;
	}

	/** @var string */
	private static $ID = 'id';
}