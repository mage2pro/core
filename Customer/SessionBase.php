<?php
namespace Df\Customer;
/**
 * 2021-10-28 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
 * @see \CanadaSatellite\Bambora\Session
 * @see \Df\Customer\Session
 * @see \Dfe\TBCBank\Session
 * @see \Frugue\Core\Session
 */
abstract class SessionBase extends \Df\Core\Session {
	/**
	 * 2021-10-26
	 * @override
	 * @see \Df\Core\Session::c()
	 * @used-by \Df\Core\Session::__construct()
	 */
	final protected function c():string {return \Magento\Customer\Model\Session\Storage::class;}
}