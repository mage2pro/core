<?php
namespace Df\Payment;
/**
 * 2017-02-28
 * @see \Df\GingerPaymentsBase\Test\CaseT
 * @see \Dfe\AlphaCommerceHub\Test\CaseT
 * @see \Dfe\Klarna\Test\Charge
 * @see \Dfe\Moip\Test\CaseT
 * @see \Dfe\Omise\Test\CaseT
 * @see \Dfe\Paymill\Test\CaseT
 * @see \Dfe\Robokassa\Test\CaseT
 * @see \Dfe\Spryng\Test\CaseT
 * @see \Dfe\Square\Test\CaseT
 * @see \Dfe\Stripe\Test\CaseT
 * @see \Dfe\TBCBank\Test\CaseT
 */
class TestCase extends \Df\Core\TestCase {
	/**
	 * 2017-02-28
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @param object|string|null $m [optional]
	 */
    protected function m($m = null):Method {return dfc($this, function($m) {return dfpm($m ?: $this);}, [$m]);}

	/**
	 * 2017-03-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Core\TestCase::s()
	 * @used-by \Dfe\TBCBank\Test\CaseT\Init::transId()
	 * @param object|string|null $m [optional]
	 */
    protected function s($m = null):Settings {return $m ? dfps($m) : $this->m()->s();}
}