<?php
namespace Df\Payment;
/**
 * 2017-02-28
 * @see \Df\GingerPaymentsBase\T\CaseT
 * @see \Dfe\AlphaCommerceHub\T\CaseT
 * @see \Dfe\Iyzico\T\CaseT
 * @see \Dfe\Klarna\T\Charge
 * @see \Dfe\Moip\T\CaseT
 * @see \Dfe\Omise\T\CaseT
 * @see \Dfe\Paymill\T\CaseT
 * @see \Dfe\Robokassa\T\CaseT
 * @see \Dfe\Spryng\T\CaseT
 * @see \Dfe\Square\T\CaseT
 * @see \Dfe\Stripe\T\CaseT
 */
class TestCase extends \Df\Core\TestCase {
	/**
	 * 2017-02-28
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @param object|string|null $m [optional]
	 * @return Method
	 */
    protected function m($m = null) {return dfc($this, function($m) {return dfpm($m ?: $this);}, [$m]);}

	/**
	 * 2017-03-27
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @override
	 * @see \Df\Core\TestCase::s()
	 * @param object|string|null $m [optional]
	 * @return Settings
	 */
    protected function s($m = null) {return $m ? dfps($m) : $this->m()->s();}
}