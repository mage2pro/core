<?php
namespace Df\Customer;
use Magento\Customer\Model\Session\Storage;
# 2021-10-26 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
final class Session extends \Df\Core\Session {
	/**
	 * 2021-10-26
	 * @override
	 * @see \Df\Core\Session::c()
	 * @used-by \Df\Core\Session::__construct()
	 * @return string
	 */
	protected function c() {return Storage::class;}
}