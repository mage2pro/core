<?php
namespace Df\Payment;
# 2018-12-18 https://github.com/mcred/detect-credit-card-type/blob/09064531/src/Detector.php
final class BankCardNetworkDetector {
	/**
	 * 2018-12-19
	 * @used-by \Dfe\Vantiv\Facade\Card::brand()
	 * @param string $c
	 */
	static function label($c):string {return dftr($c, [
		self::AE => 'American Express'
		,self::DN => 'Diners Club'
		,self::DS => 'Discover'
		,self::JC => 'JCB'
		,self::MC => 'MasterCard'
		,self::VI => 'Visa'
	]);}

	/**
	 * 2018-12-18
	 * @used-by \Dfe\Vantiv\Facade\Card::brandCode()
	 * @param string $n
	 * @return string|null
	 */
	static function p($n) {
		$r = null;  /** @var string|null $r */
		$types = [
			self::AE => function($n) {return !!preg_match("/^3$|^3[47][0-9]{0,13}$/i", $n);}
			,self::DN => function($n) {return !!preg_match("/^3(?:0[0-5]|[68][0-9])[0-9]{4,}$/i", $n);}
			,self::DS => function($n) {return !!preg_match(
				"/^6$|^6[05]$|^601[1]?$|^65[0-9][0-9]?$|^6(?:011|5[0-9]{2})[0-9]{0,12}$/i", $n
			);}
			,self::JC => function($n) {return !!preg_match("/^(?:2131|1800|35[0-9]{3})[0-9]{3,}$/i", $n);}
			,self::MC => function($n) {return !!preg_match(
				"/^5[1-5][0-9]{5,}|222[1-9][0-9]{3,}|22[3-9][0-9]{4,}|2[3-6][0-9]{5,}|27[01][0-9]{4,}|2720[0-9]{3,}$/i", $n
			);}
			,self::VI => function($n) {return !!preg_match("/^4[0-9]{0,15}$/i", $n);}
		];
		foreach ($types as $t => $f) { /** @var string $t */
			if ($f($n)) {
				$r = $t;
				break;
			}
		}
		return $r;
	}

	/**
	 * 2018-12-18
	 * @used-by self::label()
	 * @used-by self::p()
	 * @used-by \Dfe\Vantiv\Facade\Card::brandCodeE()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::type()
	 */
	const AE = 'amex';
	/**
	 * 2018-12-18
	 * @used-by self::label()
	 * @used-by self::p()
	 * @used-by \Dfe\Vantiv\Facade\Card::brandCodeE()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::type()
	 */
	const DN = 'diners';
	/**
	 * 2018-12-18
	 * @used-by self::label()
	 * @used-by self::p()
	 * @used-by \Dfe\Vantiv\Facade\Card::brandCodeE()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::type()
	 */
	const DS = 'discover';
	/**
	 * 2018-12-18
	 * @used-by self::label()
	 * @used-by self::p()
	 * @used-by \Dfe\Vantiv\Facade\Card::brandCodeE()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::type()
	 */
	const JC = 'jcb';
	/**
	 * 2018-12-18
	 * @used-by self::label()
	 * @used-by self::p()
	 * @used-by \Dfe\Vantiv\Facade\Card::brandCodeE()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::type()
	 */
	const MC = 'mastercard';
	/**
	 * 2018-12-18
	 * @used-by self::label()
	 * @used-by self::p()
	 * @used-by \Dfe\Vantiv\Facade\Card::brandCodeE()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::type()
	 */
	const VI = 'visa';
}