<?php
namespace Df\Payment\Exception\Webhook;
/**
 * 2017-01-06
 * @used-by \Df\Payment\Action\Webhook::execute()
 * @used-by \Df\StripeClone\WebhookF::_class()
 */
class NotImplemented extends \Exception {
	/**
	 * 2017-01-06
	 * @override
	 * @see \Exception::__construct()
	 * @used-by \Df\StripeClone\WebhookF::_class()
	 * @param string $eventType
	 */
	public function __construct($eventType) {parent::__construct(
		"The «{$eventType}» events are intentionally ignored by our Magento module."
	);}
}