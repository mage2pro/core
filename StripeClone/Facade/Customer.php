<?php
namespace Df\StripeClone\Facade;
/**
 * 2017-02-10
 * @see \Dfe\Moip\Facade\Customer
 * @see \Dfe\Omise\Facade\Customer
 * @see \Dfe\Paymill\Facade\Customer
 * @see \Dfe\Spryng\Facade\Customer
 * @see \Dfe\Square\Facade\Customer
 * @see \Dfe\Stripe\Facade\Customer
 * @see \Dfe\TBCBank\Facade\Customer
 * @see \Dfe\Vantiv\Facade\Customer
 */
abstract class Customer extends \Df\Payment\Facade {
	/**
	 * 2017-02-10
	 * 2017-07-16
	 * If a PSP does not support this operation for a token (like Moip and Spryng),
	 * then the method should just return the token.
	 * @see \Dfe\Moip\Facade\Customer::cardAdd()
	 * https://github.com/mage2pro/moip/blob/0.7.2/Facade/Customer.php#L37-L55
	 * @see \Dfe\Spryng\Facade\Customer::cardAdd()
	 * https://github.com/mage2pro/spryng/blob/1.1.10/Facade/Customer.php#L18-L27
	 * 2022-11-13
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @see \Dfe\Moip\Facade\Customer::cardAdd()
	 * @see \Dfe\Omise\Facade\Customer::cardAdd()
	 * @see \Dfe\Paymill\Facade\Customer::cardAdd()
	 * @see \Dfe\Spryng\Facade\Customer::cardAdd()
	 * @see \Dfe\Square\Facade\Customer::cardAdd()
	 * @see \Dfe\Stripe\Facade\Customer::cardAdd()
	 * @see \Dfe\TBCBank\Facade\Customer::cardAdd()
	 * @see \Dfe\Vantiv\Facade\Customer::cardAdd()
	 * @param object $c
	 */
	abstract function cardAdd($c, string $token):string;
	
	/**
	 * 2017-02-10
	 * Этот метод должен регистрировать в ПС не только покупателя, но и его банковскую карту.
	 * Stripe и Omise умеют делать это сразу (в ответ на единый запрос к ПС),
	 * а вот для Paymill банковскую карту надо регистрировать отдельным запросом к ПС.
	 * 2022-11-12 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @see \Dfe\Moip\Facade\Customer::create()
	 * @see \Dfe\Omise\Facade\Customer::create()
	 * @see \Dfe\Paymill\Facade\Customer::create()
	 * @see \Dfe\Spryng\Facade\Customer::create()
	 * @see \Dfe\Square\Facade\Customer::create()
	 * @see \Dfe\Stripe\Facade\Customer::create()
	 * @see \Dfe\TBCBank\Facade\Customer::create()
	 * @see \Dfe\Vantiv\Facade\Customer::create()
	 * @param array(string => mixed) $p
	 * @return object
	 */
	abstract function create(array $p);

	/**
	 * 2017-02-10
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @see \Dfe\Moip\Facade\Customer::id()
	 * @see \Dfe\Omise\Facade\Customer::id()
	 * @see \Dfe\Paymill\Facade\Customer::id()
	 * @see \Dfe\Spryng\Facade\Customer::id()
	 * @see \Dfe\Square\Facade\Customer::id()
	 * @see \Dfe\Stripe\Facade\Customer::id()
	 * @see \Dfe\TBCBank\Facade\Customer::id()
	 * @see \Dfe\Vantiv\Facade\Customer::id()
	 * @param object $c
	 */
	abstract function id($c):string;

	/**
	 * 2017-02-10
	 * 2017-02-11 Отныне метод должен вернуть null для удалённого покупателя.
	 * 2018-11-13
	 * Currently, in all modules except one (TBCBank) $id is just a customer identifier (a string).
	 * But out framework supports $id of artibutrary structure, and the TBCBank module uses it:
	 * $id has the following structure there:
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
	 * The top-level keys are bank card tokens there, and their values form the corresponding bank card labels.
	 * So the TBCBank module (unlike the rest modules) does not do any API requests
	 * to retrieve a customer's saved cards.
	 * @used-by self::get()
	 * @see \Dfe\Moip\Facade\Customer::_get()
	 * @see \Dfe\Omise\Facade\Customer::_get()
	 * @see \Dfe\Paymill\Facade\Customer::_get()
	 * @see \Dfe\Spryng\Facade\Customer::_get()
	 * @see \Dfe\Square\Facade\Customer::_get()
	 * @see \Dfe\Stripe\Facade\Customer::_get()
	 * @see \Dfe\TBCBank\Facade\Customer::_get()
	 * @see \Dfe\Vantiv\Facade\Customer::_get()
	 * @param string|array(string => mixed) $id
	 * @return object|array(string => mixed)|null
	 */
	abstract protected function _get($id);

	/**
	 * 2017-02-11
	 * 2018-11-14 $c is an array for TBCBank
	 * @used-by self::cards()
	 * @see \Dfe\Moip\Facade\Customer::cardsData()
	 * @see \Dfe\Omise\Facade\Customer::cardsData()
	 * @see \Dfe\Paymill\Facade\Customer::cardsData()
	 * @see \Dfe\Spryng\Facade\Customer::cardsData()
	 * @see \Dfe\Square\Facade\Customer::cardsData()
	 * @see \Dfe\Stripe\Facade\Customer::cardsData()
	 * @see \Dfe\TBCBank\Facade\Customer::cardsData()
	 * @see \Dfe\Vantiv\Facade\Customer::cardsData()
	 * @param object|array(string => mixed) $c
	 * @return object[]|array(array(string => string))
	 */
	abstract protected function cardsData($c);

	/**
	 * 2017-10-10
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @see \Dfe\Square\Facade\Customer::addCardInASeparateStepForNewCustomers()
	 */
	function addCardInASeparateStepForNewCustomers():bool {return false;}

	/**
	 * 2017-02-10
	 * 2017-02-18 Добавил обработку ПС (Spryng), которые не поддерживают сохранение карт.
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * @used-by \Df\StripeClone\Payer::newCard()
	 * @param object $c
	 * @return string|null
	 */
	final function cardIdForJustCreated($c) {/** @var Card|null $card */return
		!($card = df_first($this->cards($c))) ? null : df_result_sne($card->id())
	;}

	/**
	 * 2017-02-10
	 * 2018-11-14 $c is an array for TBCBank
	 * @used-by self::cardsActive()
	 * @used-by self::cardIdForJustCreated()
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @param object|array(string => mixed) $c
	 * @return Card[]
	 */
	final function cards($c):array {return array_map(function($d) {return Card::create($this, $d);}, $this->cardsData($c));}

	/**
	 * 2018-11-13
	 * 2018-11-14 $c is an array for TBCBank
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @param object|array(string => mixed) $c
	 * @return Card[]
	 */
	final function cardsActive($c):array {return array_filter($this->cards($c), function(Card $card) {return
		$card->isActive()
	;});}

	/**
	 * 2017-02-10
	 * 2017-02-11 Отныне метод должен вернуть null для удалённого покупателя.
	 * 2017-02-24
	 * «I have switched my Stripe account and got the «No such customer» error»: https://mage2.pro/t/3337
	 * 2018-11-13
	 * Currently, in all modules except one (TBCBank) $d is just a customer identifier (a string).
	 * But out framework supports $d of artibutrary structure, and the TBCBank module uses it:
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
	 * The top-level keys are bank card tokens there, and their values form the corresponding bank card labels.
	 * So the TBCBank module (unlike the rest modules) does not do any API requests
	 * to retrieve a customer's saved cards.
	 * @used-by \Df\StripeClone\ConfigProvider::cards()
	 * @used-by \Df\StripeClone\Payer::newCard()  
	 * @used-by \Dfe\Stripe\Method::cardType()
	 * @param string|array(string => mixed) $d
	 * @return object|array(string => mixed)|null
	 */
	final function get($d) {try {return $this->_get($d);} catch (\Exception $e) {return null;}}
}