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
 * @method Method m()
 * @method static Payer s(Method $m)
 */
final class Payer extends \Df\Payment\Facade {
	/**
	 * 2017-02-10
	 * Возможны 3 ситуации:
	 * 1) Зарегистрированный в ПС покупатель с зарегистрированной в ПС картой.
	 * 2) Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * 3) Незарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
	 * @used-by \Df\StripeClone\P\Charge::cardId()
	 * @return string
	 */
	function cardId() {return $this->usePreviousCard() ? $this->token() : df_last($this->newCard());}

	/**
	 * 2016-08-23
	 * @used-by \Df\StripeClone\P\Charge::customerId()
	 * @used-by \Dfe\Moip\P\Preorder::p()
	 * @return string
	 */
	function customerId() {return $this->customerIdSaved() ?: df_first($this->newCard());}

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
	 * @used-by \Df\StripeClone\P\Charge::request()
	 * @return bool
	 */
	function usePreviousCard() {return dfc($this, function() {return
		($p = FCharge::s($this->m())->cardIdPrefix()) && df_starts_with($this->token(), "{$p}_")
	;});}

	/**
	 * 2016-08-22
	 * @used-by \Dfe\Stripe\P\Reg::p()
	 * @return C|null
	 */
	private function c() {return dfc($this, function() {/** @var int|null $id $id */return
		!($id = $this->o()->getCustomerId()) ? null : df_customer($id)
	;});}

	/**
	 * 2016-08-23
	 * @used-by customerId()
	 * @used-by newCard()
	 * @return string
	 */
	private function customerIdSaved() {return dfc($this, function() {return
		df_ci_get($this->m(), $this->c())
	;});}

	/**
	 * 2017-06-12
	 * 2017-07-16 It returns a 2-tuple: [customer ID, card ID].
	 * @return string[]
	 */
	private function newCard() {return dfc($this, function() {
		df_assert(!$this->usePreviousCard());
		$customer = null; /** @var object|null $customer */
		$cardId = null; /** @var string $cardId */
		$fc = FCustomer::s($this->m()); /** @var FCustomer $fc */
		if ($customerId = $this->customerIdSaved() /** @var string $customerId */) {
			// 2016-08-23 https://stripe.com/docs/api/php#retrieve_customer
			// 2017-02-10 Зарегистрированный в ПС покупатель с незарегистрированной в ПС картой.
			// 2017-07-16 We ensure here that the customer with the ID given is really exist in the PSP.
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
			// 2017-06-11 It registers the customer in the PSP.
			// 2016-08-22 Stripe: https://stripe.com/docs/api/php#create_customer
			// 2016-11-15 Omise: https://www.omise.co/customers-api#customers-create
			// 2017-02-11 Paymill: https://developers.paymill.com/API/index#create-new-client-
			$customer = $fc->create(Reg::request($this->m()));
			df_ci_save($this, $customerId = $fc->id($customer));
			// 2017-02-18 Вторая часть условия — для ПС (Spryng), которые не поддерживают сохранение карт.
			$cardId = $fc->cardIdForJustCreated($customer) ?: $this->token();
		}
		return [$customerId, $cardId];
	});}

	/**
	 * 2017-06-12
	 * @return O
	 */
	final protected function o() {return df_order($this->ii());}

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
	private function token() {return Token::get($this->ii());}
}