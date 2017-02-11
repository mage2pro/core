<?php
namespace Df\Payment\Exception\Webhook;
/**
 * 2017-01-11
 * 2017-01-17
 * @see \Df\Payment\Exception\Webhook\NotImplemented
 */
class Factory extends \Df\Payment\Exception {
	/**
	 * 2017-01-11
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @used-by \Df\Payment\WebhookF::error()
	 * @see \Df\Payment\Exception\Webhook\NotImplemented::__construct()
	 * @param array(string => mixed) $req
	 * @param string $message
	 */
	function __construct(array $req, $message) {
		$this->_req = $req;
		parent::__construct($message);
	}

	/**
	 * 2017-01-11
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @used-by \Df\Payment\Action\Webhook::notImplemented()
	 * @return array(string => mixed)
	 */
	function req() {return $this->_req;}

	/**
	 * 2017-01-11
	 * @used-by \Df\Payment\Exception\Webhook\Factory::__construct()
	 * @used-by req()
	 * @var array(string => mixed)
	 */
	private $_req;
}