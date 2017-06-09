<?php
namespace Df\Payment;
/**
 * 2017-02-28
 * @see \Df\GingerPaymentsBase\T\TestCase
 * @see \Dfe\Iyzico\T\TestCase
 * @see \Dfe\Klarna\T\TestCase
 * @see \Dfe\Moip\T\TestCase
 * @see \Dfe\Omise\T\CaseT
 * @see \Dfe\Paymill\T\TestCase
 * @see \Dfe\Robokassa\T\TestCase
 * @see \Dfe\Spryng\T\TestCase
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