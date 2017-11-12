<?php
namespace Df\StripeClone;
use Df\Payment\Token;
use Df\StripeClone\Facade\Charge as FCharge;
use Df\StripeClone\Facade\Customer as FCustomer;
use Df\StripeClone\P\Reg;
use Magento\Customer\Model\Customer as C;
use Magento\Sales\Model\Order as O;
/**
 * 2017-06-12
 * @used-by \Df\StripeClone\P\Charge::request()
 * @see \Dfe\Stripe\Payer
 * @method Method m()
 * @method static Payer s(Method $m)
 */
class Payer extends \Df\Payment\Facade {
	/**
	 * 2017-02-10
	 * Возможны 3 ситуации:
	 * 1) Зарегистрированный в ПС покупатель с зарегистрированной в ПС картой.
	 * 2) Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * 3) Незарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * 2017-07-16
	 * 1) If a PSP does not support the cards saving (like Spryng),
	 * or supports it only on the payment step (like Moip),
	 * then the method returns a token, not a card ID.
	 * 2) If a PSP supports the cards saving here,
	 * but the customer is already registered and has chosen to pay with an alleady registered card,
	 * then the method returns the card ID, but with the $this->token() code:
	 * @uses token() returns a card ID in this case.
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return string
	 */
	function cardId() {return
		!$this->tokenIsNew() || $this->tokenIsSingleUse() ? $this->token() : df_last($this->newCard())
	;}

	/**
	 * 2016-08-23
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @used-by \Dfe\Moip\P\Preorder::p()
	 * @return string|null
	 */
	function customerId() {return $this->tokenIsSingleUse() ? null: (
		$this->customerIdSaved() ?: df_first($this->newCard())
	);}

	/**
	 * 2016-08-23
	 * Отныне параметр «token» может содержать не только токен новой карты
	 * (например: «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * но и идентификатор ранее использовавшейся карты
	 * (например: «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * 2017-02-18
	 * Если ПС (как, например, Spryng) не поддерживает сохранение банковской карты
	 * для будущего повторного использования, то @uses cardIdPrefix() должна вернуть null,
	 * и тогда tokenIsNew() всегда вернёт true,
	 * @used-by cardId()
	 * @used-by newCard()
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return bool
	 */
	function tokenIsNew() {return dfc($this, function() {return
		FCharge::s($this->m())->tokenIsNew($this->token())
	;});}

	/**
	 * 2016-08-23
	 * For Stripe it could be not only a new bank card token (like «tok_18lWSWFzKb8aMux1viSqpL5X»),
	 * but also an ID of a previosuly used bank card (like «card_18lGFRFzKb8aMux1Bmcjsa5L»).
	 * 2017-11-12 Since last month, it could be also a Stripe's source ID (like «src_1BMxGwFzKb8aMux1dThSCfhP»).
	 * @used-by cardId()
	 * @used-by newCard()
	 * @used-by tokenIsNew()
	 * @used-by \Dfe\Stripe\Payer::tokenIsSingleUse()
	 * @return string
	 */
	final protected function token() {return Token::get($this->ii());}

	/**
	 * 2017-11-12
	 * Some Stripe's sources are single-use: https://stripe.com/docs/sources#single-use-or-reusable
	 * «Stripe API Documentation» → «Payment Methods Supported by the Sources API» → «Single-use or reusable»:
	 * «If a source can only be used once, this parameter is set to `single_use`
	 * and a source must be created each time a customer makes a payment.
	 * Such sources should not be attached to customers and should be charged directly instead.»
	 * @used-by cardId()
	 * @used-by customerId()
	 * @see \Dfe\Stripe\Payer::tokenIsSingleUse()
	 * @return bool
	 */
	protected function tokenIsSingleUse() {return false;}

	/**
	 * 2016-08-23
	 * @used-by customerId()
	 * @used-by newCard()
	 * @return string
	 */
	private function customerIdSaved() {return dfc($this, function() {return df_ci_get(
		$this->m(), !($id = df_order($this->ii())->getCustomerId()) ? null : df_customer($id)
	);});}

	/**
	 * 2017-06-12
	 * 2017-07-16 It returns a 2-tuple: [customer ID, card ID].
	 * @used-by cardId()
	 * @used-by customerId()
	 * @return string[]
	 */
	private function newCard() {return dfc($this, function() {
		$m = $this->m(); /** @var Method $m */
		$isCard = $m->isCard(); /** @var bool $isCard */
		df_assert(!$isCard || $this->tokenIsNew());
		$customer = null; /** @var object|null $customer */
		$cardId = null; /** @var string $cardId */
		$fc = FCustomer::s($m); /** @var FCustomer $fc */
		if ($customerId = $this->customerIdSaved() /** @var string $customerId */) {
			// 2016-08-23 https://stripe.com/docs/api/php#retrieve_customer
			// 2017-02-10 Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
			// 2017-07-16 We ensure here that the customer with the ID given is really exist in the PSP.
			$customer = $fc->get($customerId);
			// 2017-02-24
			// We can get here, for example, if the store's administrator has switched
			// his Stripe account in the extension's settings: https://mage2.pro/t/3337
			if (!$customer) {
				df_ci_save($this->m(), null);
				$customerId = null;
			}
		}
		if ($customer) {
			if ($isCard) {
				// 2016-08-23
				// Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
				// Сохраняем её: https://stripe.com/docs/api#create_card
				/**
				 * 2017-07-16
				 * If a PSP does not support the «cardAdd» operation for a token (like Moip and Spryng),
				 * then the facade should just return the token.
				 * @see \Dfe\Moip\Facade\Customer::cardAdd()
				 * https://github.com/mage2pro/moip/blob/0.7.2/Facade/Customer.php#L37-L55
				 * @see \Dfe\Spryng\Facade\Customer::cardAdd()
				 * https://github.com/mage2pro/spryng/blob/1.1.10/Facade/Customer.php#L18-L27
				 */
				$cardId = $fc->cardAdd($customer, $this->token());
				df_assert_sne($cardId);
			}
		}
		else {
			// 2017-06-11 It registers the customer in the PSP.
			// 2016-08-22 Stripe: https://stripe.com/docs/api/php#create_customer
			// 2016-11-15 Omise: https://www.omise.co/customers-api#customers-create
			// 2017-02-11 Paymill: https://developers.paymill.com/API/index#create-new-client-
			$customer = $fc->create(Reg::request($m));
			df_ci_save($this->m(), $customerId = $fc->id($customer));
			if ($isCard) {
				// 2017-02-18 Вторая часть условия — для ПС (Spryng), которые не поддерживают сохранение карт.
				$cardId =
					// 2017-10-10 Square supports a card saving, but requires an additional step to do it.
					$fc->addCardInASeparateStepForNewCustomers()
					? $fc->cardAdd($customer, $this->token())
					// 2017-07-16
					// Moip supports a card saving, but does not allow to do it on the customer's registration.
					// The card is saved in Moip only on a payment request.
					// https://github.com/mage2pro/moip/blob/0.7.0/P/Reg.php#L24-L29
					// https://github.com/mage2pro/moip/blob/0.7.0/T/CaseT/Customer.php#L94-#L106
					: ($fc->cardIdForJustCreated($customer) ?: $this->token())
				;
			}
		}
		if ($cardId) {
			Token::exchangedSet($this->m()->getCode(), $cardId);
		}
		return [$customerId, $cardId];
	});}
}