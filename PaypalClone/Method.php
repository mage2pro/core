<?php
namespace Df\PaypalClone;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-22
 * @see \Dfe\AllPay\Method
 * @see \Dfe\SecurePay\Method
 */
abstract class Method extends \Df\Payment\Method {}