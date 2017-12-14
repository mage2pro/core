<?php
namespace Df\Quote\Model;
use Magento\Eav\Model\Entity\Collection\AbstractCollection as AC;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
// 2017-12-14
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Quote extends \Magento\Quote\Model\Quote {
	/**
	 * 2017-12-14
	 * "Provide a workaround for the Magento 2 bug:
	 * «@see \Magento\Quote\Model\Quote::assignCustomerWithAddressChange()
	 * incorrectly rewrites an already chosen shipping address with the default one»":
	 * https://github.com/mage2pro/core/issues/63
	 * https://mage2.pro/t/5163
	 * @used-by df_oq_country_sb()
	 * @param \Closure $f
	 * @param O|Q $oq
	 * @return mixed|null
	 */
	final static function runOnFreshAC(\Closure $f, $oq) {
		$r = null; /** @var mixed $r */
		if (!df_is_q($oq)) {
			$r = $f();
		}
		else {
			/**
			 * 2017-12-14
			 * @see \Magento\Quote\Model\Quote::$_addresses
			 * https://github.com/magento/magento2/blob/2.2.2/app/code/Magento/Quote/Model/Quote.php#L132-L137
			 */
			$ac = $oq->_addresses; /** @var AC $ac */
			try {
				$oq->_addresses = null;
				$r = $f();
			}
			finally {$oq->_addresses = $ac;}
		}
		return $r;
	}
}