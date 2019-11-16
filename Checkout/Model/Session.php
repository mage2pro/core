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
 * @method void setDfCustomer(array $data)
 * @used-by df_ci_save()
 *
 * 2017-11-17
 * @method string|null getLastRealOrderId()  
 * 2019-11-16
 * @method int|null getLastOrderId()
 * 		getLastRealOrderId() returns the increment ID
 * 		getLastOrderId() returns the numeric ID
 * @see \Magento\Checkout\Model\Type\Onepage::saveOrder()
 */
class Session extends \Magento\Checkout\Model\Session {}