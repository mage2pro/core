<?php
namespace Df\Payment\Exception\Webhook;
/**
 * 2017-01-06
 * @used-by \Df\Payment\Action\Webhook::execute()
 * @used-by \Df\StripeClone\WebhookF::_class()
 */
final class NotImplemented extends \Df\Payment\Exception\Webhook\Factory {
	/**
	 * 2017-01-06
	 * @override
	 * @see \Df\Payment\Exception\Webhook\Factory::__construct()
	 * @used-by \Df\StripeClone\WebhookF::_class()
	 * @param array(string => mixed) $req
	 * @param string|object $module
	 * @param string $type
	 */
	public function __construct(array $req, $module, $type) {
		$this->_module = $module;
		$this->_type = $type;
		/** @var string $title */
		$title = dfp_method_title($module);
		parent::__construct($req,
			"The «{$type}» events are intentionally ignored by this {$title} module."
		);
	}

	/**
	 * 2017-01-17
	 * @used-by \Df\Payment\Action\Webhook::notImplemented()
	 * @return string|object
	 */
	public function module() {return $this->_module;}

	/**
	 * 2017-01-17
	 * @used-by \Df\Payment\Action\Webhook::notImplemented()
	 * @return string
	 */
	public function type() {return $this->_type;}

	/**
	 * 2017-01-17
	 * @used-by __construct()
	 * @used-by module()
	 * @var string|object
	 */
	private $_module;

	/**
	 * 2017-01-17
	 * @used-by __construct()
	 * @used-by type()
	 * @var string
	 */
	private $_type;
}