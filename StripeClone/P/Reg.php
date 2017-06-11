<?php
namespace Df\StripeClone\P;
use Df\Payment\Token;
use Df\StripeClone\Method as M;
/**
 * 2017-06-11
 * @see \Dfe\Moip\P\Reg
 * @see \Dfe\Spryng\P\Reg
 * @see \Dfe\Stripe\P\Reg
 * @method M m()
 */
class Reg extends \Df\Payment\Operation {
	/**
	 * 2017-06-11
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by k_CardId()
	 * @used-by \Dfe\Moip\P\Reg::v_CardId()
	 * @return Charge
	 */
	protected function charge() {return Charge::sn($this->m());}

	/**
	 * 2017-06-11
	 * Ключ, значением которого является токен банковской карты.
	 * Этот ключ передаётся как параметр в запросе на сохранение банковской карты
	 * для будущего повторного использования при регистрации нового покупателя.
	 * @used-by request()
	 * @used-by \Dfe\Spryng\P\Reg::k_Excluded()
	 * @see \Dfe\Moip\P\Reg::k_CardId()
	 * @return string
	 */
	protected function k_CardId() {return $this->charge()->k_CardId();}

	/**
	 * 2017-06-11
	 * @used-by newCard()
	 * @see \Dfe\Spryng\P\Reg::k_Email()
	 * @return string
	 */
	protected function k_Email() {return self::K_EMAIL;}

	/**
	 * 2017-06-11
	 * @used-by request()
	 * @see \Dfe\Spryng\P\Reg::k_Excluded()
	 * @return string[]
	 */
	protected function k_Excluded() {return [];}

	/**
	 * 2017-06-11
	 * @used-by request()
	 * @see \Dfe\Moip\P\Reg::p()
	 * @see \Dfe\Spryng\P\Reg::p()
	 * @see \Dfe\Stripe\P\Reg::p()
	 * @return array(string => mixed)
	 */
	protected function p() {return [];}

	/**
	 * 2017-06-11
	 * @used-by request()
	 * @see \Dfe\Moip\P\Reg::v_CardId()
	 * @param string $id
	 * @return string|array(string => mixed)
	 */
	protected function v_CardId($id) {return $id;}

	/**
	 * 2017-06-11
	 * @used-by \Df\StripeClone\P\Charge::newCard()
	 * @param M $m
	 * @return array(string => mixed)
	 */
	final static function request(M $m) {
		/** @var self $i */
		$i = df_new(df_con_heir($m, __CLASS__), $m);
		return df_clean_keys([
			self::K_DESCRIPTION => $i->customerName()
			,$i->k_CardId() => $i->v_CardId(Token::get($i->ii()))
			,$i->k_Email() => $i->customerEmail()
		], $i->k_Excluded()) + $i->p();
	}

	/**
	 * 2017-06-11
	 * @used-by request()
	 * @used-by \Dfe\Paymill\Facade\Customer::create()
	 * @used-by \Dfe\Spryng\P\Reg::k_Excluded()
	 */
	const K_DESCRIPTION = 'description';

	/**
	 * 2017-06-11
	 * @used-by k_Email()
	 * @used-by request()
	 * @used-by \Dfe\Paymill\Facade\Customer::create()
	 */
	const K_EMAIL = 'email';
}