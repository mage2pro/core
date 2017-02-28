<?php
namespace Df\Payment;
/**
 * 2017-02-28
 * @see \Df\GingerPaymentsBase\T\TestCase
 */
class TestCase extends \Df\Core\TestCase {
	/**
	 * 2017-02-28
	 * I intentionally do not use the PHP «final» keyword here,
	 * so descendant classes can refine the method's return type using PHPDoc.
	 * @final
	 * @param object|string|null $m [optional]
	 * @return Method
	 */
    protected function m($m = null) {return dfc($this, function($m) {return
		dfp_method($m ?: $this)
	;}, [$m]);}
}