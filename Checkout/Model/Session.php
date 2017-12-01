<?php
namespace Df\Checkout\Model;
/**
 * 2016-07-14
 * @method array|null getDfMessages()
 * @method void setDfMessages(array $messages)
 * @method void unsDfMessages()
 *
 *
 * 2016-08-22
 * @method array|null getDfCustomer()
 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
 * @method void setDfCustomer(array $data)
 * @used-by df_ci_save()
 *
 *
 * 2017-12-01
 * I should save the order's MerchantTxnID to the customer's PHP session
 * before redirecting the customer to an AlphaCommerceHub's hosted payment page,
 * because AlphaCommerceHub does not provide it in a `SuccessURL` request for a PayPal payment:
 * https://github.com/mage2pro/alphacommercehub/issues/62
 *
 * @method string|null getDfPID()
 * @used-by \Dfe\AlphaCommerceHub\W\Reader::_construct()
 *
 * @method void setDfPID(string $v)
 * @used-by \Dfe\AlphaCommerceHub\Charge::pCharge()
 *
 * 2017-11-17
 * @method int|null getLastRealOrderId()
 */
class Session extends \Magento\Checkout\Model\Session {}