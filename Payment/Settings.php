<?php
namespace Df\Payment;
use Magento\Framework\App\ScopeInterface as S;
abstract class Settings extends \Df\Core\Settings {
	/**
	 * 2016-02-27
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	public function enable($s = null) {return $this->b(__FUNCTION__, $s);}

	/**
	 * 2016-03-02
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	public function test($s = null) {return $this->b(__FUNCTION__, $s);}
}


