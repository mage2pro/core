<?php
namespace Df\StripeClone;
use Df\Payment\Operation\Source\Order as OpSource;
use Df\Payment\Token;
use Df\StripeClone\Facade\Customer as FCustomer;
/**
 * 2016-12-28
 * @see \Dfe\CheckoutCom\Charge
 * @see \Dfe\Omise\Charge
 * @see \Dfe\Paymill\Charge
 * @see \Dfe\Spryng\Charge
 * @see \Dfe\Square\Charge
 * @see \Dfe\Stripe\Charge
 * \Dfe\TwoCheckout\Charge
 * @method Method m()
 */
abstract class Charge extends \Df\Payment\Charge {
	/**
	 * 2017-02-11
	 * 2017-02-18
	 * Если ПС (как, например, Spryng) не поддерживает сохранение банковской карты
	 * для будущего повторного использования, то этот метод должен вернуть null.
	 * Этого достаточно, чтобы @used-by usePreviousCard() всегда возвращала false.
	 * @used-by usePreviousCard()
	 * @see \Dfe\Omise\Charge::cardIdPrefix()
	 * @see \Dfe\Paymill\Charge::cardIdPrefix()
	 * @see \Dfe\Spryng\Charge::cardIdPrefix()
	 * @see \Dfe\Stripe\Charge::cardIdPrefix()
	 * @return string
	 */
	abstract protected function cardIdPrefix();
	
	/**
	 * 2017-02-11
	 * 2017-02-18   
	 * Ключ, значением которого является токен банковской карты.
	 * Этот ключ передаётся как параметр ДВУХ РАЗНЫХ запросов к API ПС:
	 * 1) в запросе на проведение транзакции (charge)
	 * 2) в запросе на сохранение банковской карты для будущего повторного использования
	 * У текущих ПС либо:
	 * 1) название этого параметра для обоих запросов совпадает (Omise, Paymill, Stripe)
	 * 2) сохранение банковской карты для будущего повторного использования не поддерживается (Srpyng)
	 * @used-by request()
	 * @used-by newCard()
	 * @used-by \Dfe\Spryng\Charge::kc_Excluded()
	 * @see \Dfe\Omise\Charge::k_CardId()
	 * @see \Dfe\Paymill\Charge::k_CardId()
	 * @see \Dfe\Spryng\Charge::k_CardId()
	 * @see \Dfe\Stripe\Charge::k_CardId()
	 * @return string
	 */
	abstract protected function k_CardId();

	/**
	 * 2017-02-18
	 * Dynamic statement descripor
	 * https://mage2.pro/tags/dynamic-statement-descriptor
	 * https://stripe.com/blog/dynamic-descriptors
	 * @used-by request()
	 * @see \Dfe\Omise\Charge::k_DSD()
	 * @see \Dfe\Paymill\Charge::k_DSD()
	 * @see \Dfe\Spryng\Charge::k_DSD()
	 * @see \Dfe\Stripe\Charge::k_DSD()
	 * @return string|null
	 */
	abstract protected function k_DSD();

	/**
	 * 2017-02-11
	 * @used-by request()
	 * @see \Dfe\Omise\Charge::pCharge()
	 * @see \Dfe\Stripe\Charge::pCharge()
	 * @return array(string => mixed)
	 */
	protected function pCharge() {return [];}

	/**
	 * 2017-02-10
	 * @used-by newCard()
	 * @see \Dfe\Spryng\Charge::pCustomer()
	 * @see \Dfe\Stripe\Charge::pCustomer()
	 * @return array(string => mixed)
	 */
	protected function pCustomer() {return [];}

	/**
	 * 2017-02-18
	 * @used-by request()
	 * @see \Dfe\Spryng\Charge::k_Capture()
	 * @return string
	 */
	protected function k_Capture() {return self::K_CAPTURE;}

	/**
	 * 2017-02-18
	 * @used-by newCard()
	 * @see \Dfe\Spryng\Charge::kc_Email()
	 * @return string
	 */
	protected function kc_Email() {return self::KC_EMAIL;}

	/**
	 * 2017-02-18
	 * @used-by request()
	 * @see \Dfe\Spryng\Charge::k_Excluded()
	 * @return string[]
	 */
	protected function k_Excluded() {return [];}

	/**
	 * 2017-02-18
	 * @used-by newCard()
	 * @see \Dfe\Spryng\Charge::kc_Excluded()
	 * @return string[]
	 */
	protected function kc_Excluded() {return [];}

	/**
	 * 2017-02-10
	 * Возможны 3 ситуации:
	 * 1) Зарегистрированный в ПС покупатель с зарегистрированной в ПС картой.
	 * 2) Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * 3) Незарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * @used-by request()
	 * @return string
	 */
	private function cardId() {return
		$this->usePreviousCard() ? $this->token() : df_last($this->newCard())
	;}

	/**
	 * 2016-08-23
	 * @used-by request()
	 * @return string
	 */
	private function customerId() {return $this->customerIdSaved() ?: df_first($this->newCard());}

	/**
	 * 2016-08-23
	 * @used-by customerId()
	 * @used-by newCard()
	 * @return string
	 */
	private function customerIdSaved() {return dfc($this, function() {return 
		df_ci_get($this, $this->c())
	;});}

	/**
	 * 2016-08-22
	 * Даже если покупатель в момент покупки ещё не имеет учётной записи в магазине,
	 * то всё равно разумно зарегистрировать его в ПС и сохранить данные его карты,
	 * потому что Magento уже после оформления заказа предложит такому покупателю зарегистрироваться,
	 * и покупатель вполне может согласиться: https://mage2.pro/t/1967
	 *
	 * Если покупатель согласится создать учётную запись в магазине,
	 * то мы попадаем в @see \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
	 * и там из сессии передаём данные в свежесозданную учётную запись.
	 *
	 * 2017-02-10
	 * Тут возможны 2 ситуации:
	 * 1) Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * 2) Незарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * Карта у нас ГАРАНТИРОВАННО НЕЗНАКОМАЯ (новая).
	 *
	 * @return string[]
	 * Первое значение результата — customer ID
	 * Второе значение результата — card ID
	 *
	 * @used-by cardId()
	 * @used-by customerId()
	 */
	private function newCard() {return dfc($this, function() {
		df_assert(!$this->usePreviousCard());
		/** @var object|null $customer */
		$customer = null;
		/** @var string $cardId */
		$cardId = null;
		/** @var FCustomer $fc */
		$fc = FCustomer::s($this->m());
		/** @var string $customerId */
		if ($customerId = $this->customerIdSaved()) {
			// 2017-02-10
			// Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
			// 2016-08-23
			// https://stripe.com/docs/api/php#retrieve_customer
			$customer = $fc->get($customerId);
			// 2017-02-24
			// We can get here, for example, if the store's administrator has switched
			// his Stripe account in the extension's settings: https://mage2.pro/t/3337
			if (!$customer) {
				df_ci_save($this, null);
				$customerId = null;
			}
		}
		if ($customer) {
			// 2016-08-23
			// Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
			// Сохраняем её: https://stripe.com/docs/api#create_card
			$cardId = $fc->cardAdd($customer, $this->token());
			df_assert_sne($cardId);
		}
		else {
			// 2016-08-22 Stripe: https://stripe.com/docs/api/php#create_customer
			// 2016-11-15 Omise: https://www.omise.co/customers-api#customers-create
			// 2017-02-11 Paymill: https://developers.paymill.com/API/index#create-new-client-
			$customer = $fc->create(df_clean_keys([
				self::KC_DESCRIPTION => $this->customerName()
				,$this->k_CardId() => $this->token()
				,$this->kc_Email() => $this->customerEmail()
			], $this->kc_Excluded()) + $this->pCustomer());
			df_ci_save($this, $customerId = $fc->id($customer));
			// 2017-02-18
			// Вторая часть условия — для ПС (Spryng), которые не поддерживают сохранение карт.
			$cardId = $fc->cardIdForJustCreated($customer) ?: $this->token();
		}
		return [$customerId, $cardId];
	});}

	/**
	 * 2016-08-23
	 * Для Stripe этот параметр может содержать не только токен новой карты
	 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * но и идентификатор ранее использовавшейся карты
	 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * @used-by cardId()
	 * @used-by newCard()
	 * @used-by usePreviousCard()
	 * @return string
	 */
	private function token() {return Token::get($this->op());}

	/**
	 * 2016-08-23
	 * Отныне параметр «token» может содержать не только токен новой карты
	 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * но и идентификатор ранее использовавшейся карты
	 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * 2017-02-18
	 * Если ПС (как, например, Spryng) не поддерживает сохранение банковской карты
	 * для будущего повторного использования, то @uses cardIdPrefix() должна вернуть null,
	 * и тогда usePreviousCard() всегда вернёт false,
	 * @used-by cardId()
	 * @used-by newCard()
	 * @return bool
	 */
	private function usePreviousCard() {return dfc($this, function() {return
		($p = $this->cardIdPrefix()) && df_starts_with($this->token(), "{$p}_")
	;});}

	/**
	 * 2016-12-28
	 * 2016-03-07 Stripe: https://stripe.com/docs/api/php#create_charge
	 * 2016-11-13 Omise: https://www.omise.co/charges-api#charges-create
	 * 2017-02-11 Paymill https://developers.paymill.com/API/index#-transaction-object
	 * @used-by \Dfe\Stripe\Method::chargeNew()
	 * @param Method $m
	 * @param float $amount
	 * @param bool $capture [optional]
	 * @return array(string => mixed)
	 */
	final static function request(Method $m, $amount, $capture = true) {
		/** @var self $i */
		$i = df_new(df_con_heir($m, __CLASS__), new OpSource($m, $amount));
		/** @var Settings $s */
		$s = $i->s();
		return df_clean_keys([
			self::K_AMOUNT => $i->amountF()
			,self::K_CURRENCY => $i->currencyC()
			,self::K_CUSTOMER => $i->customerId()
			// 2016-03-08
			// Для Stripe текст может иметь произвольную длину: https://mage2.pro/t/903
			,self::K_DESCRIPTION => $i->description()
			,$i->k_Capture() => $capture
		  	,$i->k_CardId() => $i->cardId()
			// 2017-02-18
			// «Dynamic statement descripor»
			// https://mage2.pro/tags/dynamic-statement-descriptor
			// https://stripe.com/blog/dynamic-descriptors
			// https://support.stripe.com/questions/does-stripe-support-dynamic-descriptors
			,$i->k_DSD() => $s->dsd()
		], $i->k_Excluded()) + $i->pCharge();
	}

	/**
	 * 2017-02-11
	 * @used-by request()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 */
	const K_AMOUNT = 'amount';

	/**
	 * 2017-02-11
	 * @used-by k_Capture()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 */
	const K_CAPTURE = 'capture';

	/**
	 * 2017-02-11
	 * @used-by request()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 * @used-by \Dfe\Spryng\Charge::k_Excluded()
	 */
	const K_CURRENCY = 'currency';

	/**
	 * 2017-02-11
	 * @used-by request()
	 * @used-by \Dfe\Paymill\Facade\Charge::create()
	 */
	const K_CUSTOMER = 'customer';

	/**
	 * 2017-02-11
	 * @used-by request()
	 * @used-by \Dfe\Paymill\Facade\Customer::create()
	 * @used-by \Dfe\Spryng\Charge::k_Excluded()
	 */
	const K_DESCRIPTION = 'description';

	/**
	 * 2017-02-11
	 * @used-by newCard()
	 * @used-by \Dfe\Paymill\Facade\Customer::create()
	 * @used-by \Dfe\Spryng\Charge::kc_Excluded()
	 */
	const KC_DESCRIPTION = 'description';

	/**
	 * 2017-02-11
	 * @used-by kc_Email()
	 * @used-by newCard()
	 * @used-by \Dfe\Paymill\Facade\Customer::create()
	 */
	const KC_EMAIL = 'email';
}