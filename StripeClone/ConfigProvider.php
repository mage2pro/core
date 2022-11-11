<?php
namespace Df\StripeClone;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Customer as FCustomer;
use Df\StripeClone\Facade\Card;
/**
 * 2016-11-12
 * @see \Dfe\Moip\ConfigProvider
 * @see \Dfe\Spryng\ConfigProvider
 * @see \Dfe\Stripe\ConfigProvider
 * @see \Dfe\Paymill\ConfigProvider
 * @see \Dfe\TwoCheckout\ConfigProvider
 * 2017-03-03
 * The class is not abstract anymore: you can use it as a base for a virtual type.
 * 1) Checkout.com:
 * https://github.com/mage2pro/checkout.com/blob/1.1.32/etc/frontend/di.xml?ts=4#L20-L24
 * https://github.com/mage2pro/checkout.com/blob/1.1.32/etc/frontend/di.xml?ts=4#L9
 *
 * 2) Omise:
 * https://github.com/mage2pro/omise/blob/1.5.8/etc/frontend/di.xml?ts=4#L20-L22
 * https://github.com/mage2pro/omise/blob/1.5.8/etc/frontend/di.xml?ts=4#L9
 *
 * 3) Square:
 * https://github.com/mage2pro/square/blob/1.0.25/etc/frontend/di.xml?ts=4#L20-L22
 * https://github.com/mage2pro/square/blob/1.0.25/etc/frontend/di.xml?ts=4#L9
 *
 * @method Method m()
 */
class ConfigProvider extends \Df\Payment\ConfigProvider\BankCard {
	/**
	 * 2017-02-09
	 * @used-by self::config()
	 * @used-by \Dfe\Moip\ConfigProvider::config()
	 * @return array(string => string)
	 */
	final protected function cards():array {
		$r = []; /** @var array(string => string) $r */
		$m = $this->m(); /** @var Method $m */
		/**
		 * 2017-07-28
		 * The `$m instanceof Method` check is for the «Square» module:
		 * its server part does not use the Df_StripeClone module, but the client part does use it.
		 * So we need this the `$m instanceof Method` check to evade the error:
		 * «Cannot instantiate abstract class Df\StripeClone\Facade\Customer
		 * in mage2pro/core/Payment/Facade.php:88»: https://github.com/mage2pro/square/issues/2
		 * 
		 * 2018-11-13
		 * Currently, in all modules except one (TBCBank) $customerData is just a customer identifier (a string).
		 * But out framework supports $customerData of artibutrary structure, and the TBCBank module uses it:
		 * $data has the following structure there:
		 *	{
		 *		"4349958401": {
		 *			"CARD_NUMBER": "5***********1223",
		 *			"RECC_PMNT_EXPIRY": "1019"
		 *		},
		 *		"1779958449": {
		 *			"CARD_NUMBER": "4***********3333",
		 *			"RECC_PMNT_EXPIRY": "1120"
		 *		}
		 *	}
		 * The top-level keys are bank card tokens there, 
		 * and their values form the corresponding bank card labels.
		 * So the TBCBank module (unlike the rest modules) does not do any API requests
		 * to retrieve a customer's saved cards.
		 */
		/** @var string|null|array(string => mixed) $customerData */
		if ($m instanceof Method && ($customerData = df_ci_get($this->m()))) {
			$this->s()->init();
			$fc = FCustomer::s($m); /** @var FCustomer $fc */
			# 2018-11-14 $customer is an array for TBCBank: it is the same as $customerData (see above).
			if (!($customer = $fc->get($customerData))/** @var object|array(string => mixed) $customer */) {
				# 2017-02-24
				# We could be here, for example, if the store's administrator has changed
				# his Stripe account in the module's settings: https://mage2.pro/t/3337
				df_ci_save($this->m(), null);
			}
			else {
				$r = array_map(function(Card $c) use($m) {
					/**
					 * 2017-07-24
					 * Unfortunately, the one-liner fails on PHP 5.6.30:
					 * return ['id' => $c->id(), 'label' => (CF::s($m, $c))->label()];
					 * «syntax error, unexpected '->' (T_OBJECT_OPERATOR), expecting ']'»
					 * https://github.com/mage2pro/core/issues/16
					 * See also: https://github.com/mage2pro/core/issues/15
					 *
					 * 2017-07-31
					 * Interestingly, the following code works on all PHP versions >= 5.4.0:
					 * class A {function f() {return __METHOD__;}
					 * echo (new A)->f();
					 * https://3v4l.org/nANqg
					 *
					 * The following code works on all PHP versions >= 5.0.0:
					 *	class A {
					 *		function f() {return __METHOD__;}
					 *		static function s() {return new self;}
					 *	}
					 *	echo A::s()->f();
					 * https://3v4l.org/1qr48
					 *
					 * The same is true for the following code:
					 *	class A {
					 *		function f() {return $this->_v;}
					 *		final protected function __construct($v) {$this->_v = $v;}
					 *		private $_v;
					 *		final static function s($v) {return new self($v);}
					 *	}
					 *	echo A::s('test')->f();
					 * https://3v4l.org/2E8H6
					 *
					 * So, I was unable to reproduce the issue...
					 * Maybe the problem customer uses a non-standard (patched) PHP version?
					 *
					 * 2017-11-08
					 * Note 1.
					 * One-liners like (!($e = $a->e())->b()) are really not supported by PHP < 7:
					 * https://3v4l.org/lJjvS
					 * Note 2.
					 * At the same time, some `))->` one-liners are supported by PHP >= 5.4, e.g:
					 * static function p() {return (new self())->b();}
					 * https://3v4l.org/LJlDE
					 * Note 3.
					 * If we add extra brackets to the code from the Note 2,
					 * then the code will be incompatible with PHP < 7:
					 * static function p() {return ((new self()))->b();}
					 */
					$cf = CF::s($m, $c); /** @var CF $cf */
					return ['id' => $c->id(), 'label' => $cf->label()];
				}, $fc->cardsActive($customer));
			}
		}
		# 2018-11-14
		# It is important, because otherwise this.cards.length will return `undefined` on frontend:
		# https://github.com/mage2pro/core/blob/3.11.1/Payment/view/frontend/web/card.js#L207
		return array_values($r);
	}

	/**
	 * 2016-11-12
	 * @override
	 * @see \Df\Payment\ConfigProvider::config()
	 * @used-by \Df\Payment\ConfigProvider::getConfig()
	 * @see \Dfe\Moip\ConfigProvider::config()
	 * @see \Dfe\Paymill\ConfigProvider::config()
	 * @see \Dfe\Stripe\ConfigProvider::config()
	 * @see \Dfe\TwoCheckout\ConfigProvider::config()
	 * @return array(string => mixed)
	 */
	protected function config():array {return [
		'cards' => $this->cards(), 'publicKey' => $this->s()->publicKey()
	] + parent::config();}
}