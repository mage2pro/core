<?php
namespace Df\Checkout;
use Magento\Framework\Phrase;
# 2021-10-22 "Improve the custom session data handling interface": https://github.com/mage2pro/core/issues/163
final class Session extends SessionBase {
	/**
	 * 2021-10-26
	 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
	 * @param array(string => mixed)|string $v [optional]
	 * @return self|array(string => mixed)
	 */
	function customer($v = DF_N) {return df_prop($this, $v, []);}

	/**
	 * 2021-10-26
	 * @used-by df_checkout_message()
	 * @used-by \Df\Checkout\B\Messages::_toHtml()
	 * @param array(array(string => bool|Phrase))|string $v [optional]
	 * @return self|array(array(string => bool|Phrase))
	 */
	function messages($v = DF_N) {return df_prop($this, $v, []);}
}