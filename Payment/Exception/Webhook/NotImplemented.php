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
	 * @param string|object $module
	 * @param string $eventType
	 */
	public function __construct($module, $eventType) {
		$module = dfp_method_title($module);
		parent::__construct(
			"The «{$eventType}» events are intentionally ignored by our {$module} module."
		);
	}
}