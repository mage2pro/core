<?php
namespace Df\StripeClone;
use Df\StripeClone\Facade\Customer as FCustomer;
/**
 * 2016-12-28
 * @see \Dfe\Omise\Charge
 * @see \Dfe\Paymill\Charge
 * @see \Dfe\Stripe\Charge
 * @method Method m()
 */
abstract class Charge extends \Df\Payment\Charge\WithToken {
	/**
	 * 2017-02-11
	 * @used-by usePreviousCard()
	 * @see \Dfe\Omise\Charge::cardIdPrefix()
	 * @see \Dfe\Paymill\Charge::cardIdPrefix()
	 * @see \Dfe\Stripe\Charge::cardIdPrefix()
	 * @return mixed
	 */
	abstract protected function cardIdPrefix();

	/**
	 * 2017-02-10
	 * @used-by newCard()
	 * @see \Dfe\Omise\Charge::customerParams()
	 * @see \Dfe\Paymill\Charge::customerParams()
	 * @see \Dfe\Stripe\Charge::customerParams()
	 * @return array(string => mixed)
	 */
	abstract protected function customerParams();

	/**
	 * 2017-02-11
	 * @used-by _request()
	 * @see \Dfe\Omise\Charge::keyCardId()
	 * @see \Dfe\Paymill\Charge::keyCardId()
	 * @see \Dfe\Stripe\Charge::keyCardId()
	 * @return mixed
	 */
	abstract protected function keyCardId();

	/**
	 * 2017-02-11
	 * @used-by _request()
	 * @see \Dfe\Omise\Charge::scRequest()
	 * @see \Dfe\Paymill\Charge::scRequest()
	 * @see \Dfe\Stripe\Charge::scRequest()
	 * @return array(string => mixed)
	 */
	abstract protected function scRequest();

	/**
	 * 2016-12-28
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__NEED_CAPTURE, DF_V_BOOL, false);
	}

	/**
	 * 2017-02-10
	 * @used-by \Dfe\Omise\Charge::_request()
	 * @used-by \Dfe\Stripe\Charge::_request()
	 * @return bool
	 */
	final protected function needCapture() {return $this[self::$P__NEED_CAPTURE];}

	/**
	 * 2016-12-28
	 * @used-by request()
	 * @return array(string => mixed)
	 */
	private function _request() {return [
		/**
		 * 1) Для Stripe:
		 * 2016-03-07
		 * https://stripe.com/docs/api/php#create_charge-amount
		 */
		self::K_AMOUNT => $this->amount()
		/**
		 * 1) Для Stripe:
		 * 2016-03-07
		 * https://stripe.com/docs/api/php#create_charge-source
		 * «A payment source to be charged, such as a credit card.
		 * If you also pass a customer ID,
		 * the source must be the ID of a source belonging to the customer.
		 * Otherwise, if you do not pass a customer ID,
		 * the source you provide must either be a token,
		 * like the ones returned by Stripe.js,
		 * or a associative array containing a user's credit card details,
		 * with the options described below.
		 * Although not all information is required, the extra info helps prevent fraud.»
		 *
		 * 2) Для Omise:
		 * 2016-11-13
		 * «(required or optional) A valid unused TOKEN_ID or CARD_ID
		 * In the case of the CARD_ID the customer parameter must be present
		 * and be the owner of the card.
		 * For the TOKEN_ID, the customer must not be passed.»
		 */
		,$this->keyCardId() => $this->cardId()
		/**
		 * 1) Для Stripe:
		 * 2016-03-07
		 * https://stripe.com/docs/api/php#create_charge-customer
		 * «The ID of an existing customer that will be charged in this request.»
		 *
		 * 2016-03-09
		 * Пустое значение передавать нельзя:
		 * «You have passed a blank string for 'customer'.
		 * You should remove the 'customer' parameter from your request or supply a non-blank value.»
		 */
		,self::K_CUSTOMER => $this->customerId()
	] + $this->scRequest();}

	/**
	 * 2017-02-10
	 * Возможны 3 ситуации:
	 * 1) Зарегистрированный в ПС покупатель с зарегистрированной в ПС картой.
	 * 2) Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * 3) Незарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * @used-by _request()
	 * @return string
	 */
	private function cardId() {return
		$this->usePreviousCard() ? $this->token() : df_last($this->newCard())
	;}

	/**
	 * 2016-08-23
	 * @used-by _request()
	 * @return string
	 */
	private function customerId() {return
		$this->customerIdSaved() ?: df_first($this->newCard())
	;}

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
			$customer = $fc->create($this->customerParams());
			df_ci_save($this, $customerId = $fc->id($customer));
			$cardId = $fc->cardIdForJustCreated($customer);
		}
		return [$customerId, $cardId];
	});}

	/**
	 * 2016-08-23
	 * Отныне параметр «token» может содержать не только токен новой карты
	 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * но и идентификатор ранее использовавшейся карты
	 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * @used-by cardId()
	 * @used-by newCard()
	 * @return bool
	 */
	private function usePreviousCard() {return dfc($this, function() {return
		df_starts_with($this->token(), "{$this->cardIdPrefix()}_")
	;});}

	/**
	 * 2016-12-28
	 * @used-by \Dfe\Stripe\Method::charge()
	 * @param Method $method
	 * @param string $token
	 * @param float|null $amount [optional]
	 * @param bool $capture [optional]
	 * @return array(string => mixed)
	 */
	static function request(Method $method, $token, $amount = null, $capture = true) {return
		(new static([
			self::$P__AMOUNT => $amount
			,self::$P__NEED_CAPTURE => $capture
			,self::$P__METHOD => $method
			,self::$P__TOKEN => $token
		]))->_request();
	}

	/** @var string */
	private static $P__NEED_CAPTURE = 'need_capture';

	/**
	 * 2017-02-11
	 * @used-by _request()
	 */
	const K_AMOUNT = 'amount';

	/**
	 * 2017-02-11
	 * @used-by _request()
	 */
	const K_CUSTOMER = 'customer';
}