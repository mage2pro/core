<?php
namespace Df\Checkout\Model;
/**
 * 2016-07-14
 * @method array|null getDfMessages()
 * @method void setDfMessages(array $messages)
 * @method void unsDfMessages()
 *
 * 2016-08-22
 * @method array|null getDfCustomer()
 * @used-by \Df\Customer\Observer\CopyFieldset\OrderAddressToCustomer::execute()
 *
 * 2016-08-22
 * @method void setDfCustomer(array $data)
 * @used-by df_ci_save()
 */
class Session extends \Magento\Checkout\Model\Session {}