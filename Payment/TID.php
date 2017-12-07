<?php
namespace Df\Payment;
use Df\Payment\Method as M;
// 2017-03-22
final class TID {
	/**
	 * 2017-03-22
	 * У Stripe-подобных ПС идентификаторы длинные и глобально уникальные.
	 * 1) Omise: chrg_test_56oviy1lbc2r36ezo90, rfnd_test_56psvraeq0thje8rkxu
	 * 2) Paymill: tran_5390b8a15831cadd1e45dd8c5cd1, refund_2c9cd9a13357f2454522
	 * 3) Spryng: 58a37e16b7f62b5161856629 (24 символа)
	 * 4) Stripe: ch111_19ZMqvFzKb8aMux1iY2kx2xo, re_19xVUBFzKb8aMux1O9Qzn6ov
	 *
	 * 2017-03-23
	 * Ещё вчера я думал:
	 * «У всех вроде идентификаторы не короче 16 символов,
	 * поэтому именно эту длину считаю критерием уникальности.
	 * Для таких модулей мы можем позволить себе вызывать @see i2e() внутри e2i():
	 * это позволяет нам преобразовывать один внутренний идентификатор в другой:
	 * отсечь один тип и добавить другой.»
	 * Однако ЭТО НЕВЕРНО и приводит к некорректной работе при внешних идентификаторах с дефисами,
	 * например: «ORD-2017/03-00807».
	 *
	 * @used-by \Df\Payment\Init\Action::e2i()
	 * @used-by \Df\PaypalClone\W\Nav::e2i()
	 * @used-by \Df\StripeClone\Method::e2i()
	 * @used-by \Df\StripeClone\W\Nav::e2i()
	 * @used-by \Dfe\AlphaCommerceHub\Method::_refund()
	 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 *
	 * @param string $id
	 * @param string|null $t [optional]
	 * @return string
	 */
	function e2i($id, $t = null) {return df_ccc(self::$S, 15 < strlen($id) ? $id : $this->md5($id), $t);}

	/**
	 * 2017-03-22
	 * 2017-03-29
	 * Сменил алгоритм для поддержки содержащих дефисы идентификаторов
	 * (Ginger Payments, Kassa Compleet): «76b7df51-a124-4a47-838d-e02b523de880».
	 * @used-by \Df\Payment\Method::tidFormat()
	 * @used-by \Df\StripeClone\Method::i2e()
	 * @used-by \Dfe\AlphaCommerceHub\Method::_refund()
	 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
	 * @param string $id
	 * @param bool $force [optional]
	 * @return string
	 */
	function i2e($id, $force = false) {
		/**
		 * 2017-12-07
		 * @todo Unfortunately, I forgot why I did this prohibition.
		 * Today I have added the $force flag: it is @used-by \Dfe\AlphaCommerceHub\Method::_refund()
		 * to overcome the prohibition.
		 */
		if (!$force && df_starts_with($id, self::$MD5)) {
			df_error("The i2e() usage is forbidden for the {$this->_m->titleB()} payment module.");
		}
		$pos = strrpos($id, '-'); /** @var int|false $pos */
		return false === $pos ? $id : substr($id, 0, $pos - strlen($id));
	}

	/**
	 * 2017-03-22
	 * @used-by s()
	 * @param M $m
	 */
	private function __construct(M $m) {$this->_m = $m;}

	/**
	 * 2017-03-22
	 * У PayPal-подобных ПС идентификаторы могут быть короткими
	 * и поэтому случайно могут пересекаться с идентификаторами других ПС.
	 * Поэтому для них использую приставку в виде кода модуля
	 * и перевожу посредством @uses md5() к унифицированному виду
	 * (гарантированном не содержащему дефисов и других неожиданных символов).
	 * @used-by e2i()
	 * @param string $id
	 * @return string
	 */
	private function md5($id) {return self::$MD5 . substr(
		md5($this->_m->getCode() . $id), 0, -strlen(self::$MD5)
	);}

	/**
	 * 2017-03-22
	 * @used-by __construct()
	 * @used-by i2e()
	 * @used-by md5()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-03-22
	 * @used-by \Df\Payment\Method::tid()
	 * @param string|object $m
	 * @return self
	 */
	static function s($m) {return dfcf(function(M $m) {return new self($m);}, [dfpm($m)]);}

	/**
	 * 2017-03-22
	 * @used-by i2e()
	 * @used-by md5()
	 * @var string
	 */
	private static $MD5 = 'md5';
	/**
	 * 2017-03-22
	 * @used-by i2e()
	 * e2i
	 * @var string
	 */
	private static $S = '-';
}