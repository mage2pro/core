<?php
namespace Df\Payment\Exception\Webhook;
/**
 * 2017-01-06
 * @used-by \Df\Payment\WebhookA::execute()
 * @used-by \Df\Payment\WebhookF\Json::_class()
 */
final class NotImplemented extends \Df\Payment\Exception\Webhook\Factory {
	/**
	 * 2017-01-06
	 * @override
	 * @see \Df\Payment\Exception\Webhook\Factory::__construct()
	 * @used-by \Df\Payment\WebhookF\Json::_class()
	 * @param array(string => mixed) $req
	 * @param string|object $module
	 * @param string $type
	 */
	function __construct(array $req, $module, $type) {
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
	 * @used-by \Df\Payment\WebhookA::notImplemented()
	 * @return string|object
	 */
	function module() {return $this->_module;}

	/**
	 * 2017-01-17
	 * @used-by \Df\Payment\WebhookA::notImplemented()
	 * @return string
	 */
	function type() {return $this->_type;}

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