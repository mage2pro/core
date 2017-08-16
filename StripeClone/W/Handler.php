<?php
namespace Df\StripeClone\W;
/**
 * 2016-12-26
 * @see \Dfe\Omise\W\Handler\Charge\Capture
 * @see \Dfe\Omise\W\Handler\Charge\Complete
 * @see \Dfe\Omise\W\Handler\Refund\Create
 * @see \Dfe\Paymill\W\Handler\Refund\Succeeded
 * @see \Dfe\Paymill\W\Handler\Transaction\Succeeded
 * @see \Dfe\Stripe\W\Handler\Charge\Captured
 * @see \Dfe\Stripe\W\Handler\Charge\Refunded
 * @method Event e()
 */
abstract class Handler extends \Df\Payment\W\Handler {}