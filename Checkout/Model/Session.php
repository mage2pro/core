<?php
namespace Df\Checkout\Model;
/**
 * 2017-11-17
 * @method string|null getLastRealOrderId()  
 * 2019-11-16
 * @method int|null getLastOrderId()
 * 		getLastRealOrderId() returns the increment ID
 * 		getLastOrderId() returns the numeric ID
 * @see \Magento\Checkout\Model\Type\Onepage::saveOrder()
 */
class Session extends \Magento\Checkout\Model\Session {}