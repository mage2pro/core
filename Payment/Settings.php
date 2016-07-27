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
	 * 2016-07-27
	 *
	 * «Ask for the Billing Address?»
	 * If checked, Magento will require the billing address.
	 * It it the default Magento behaviour.
	 * If unchecked, Magento will not require the billing address, and even will not ask for it.
	 * @see \Df\Customer\Settings\BillingAddress

	 * «The billing address is key for them to justify their purchase as a cost for their company»
	 * http://ux.stackexchange.com/a/60859
	 *
	 * «The billing address is for the invoice. If I buy something for personal use
	 * the invoice shouldn't have my company as recipient since I bought it, not the company.
	 * That difference can be important for accounting, taxation, debt collection and other legal reasons.»
	 * http://ux.stackexchange.com/questions/60846#comment94596_60859
	 *
	 * @return bool
	 */
	public function askForBillingAddress() {return $this->b(__FUNCTION__, null, true);}

	/**
	 * 2016-03-02
	 * @param null|string|int|S $s [optional]
	 * @return bool
	 */
	public function test($s = null) {return $this->b(__FUNCTION__, $s);}
}


