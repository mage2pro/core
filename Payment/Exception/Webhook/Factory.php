<?php
// 2017-01-11
namespace Df\Payment\Exception\Webhook;
class Factory extends \Df\Payment\Exception {
	/**
	 * 2017-01-11
	 * @used-by \Df\Payment\WebhookF::error()
	 * @param array(string => mixed) $req
	 * @param string $message
	 */
	public function __construct(array $req, $message) {
		$this->_req = $req;
		parent::__construct($message);
	}

	/**
	 * 2017-01-11
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @return array(string => mixed)
	 */
	public function req() {return $this->_req;}

	/**
	 * 2017-01-11
	 * @used-by \Df\Payment\Exception\Webhook\Factory::__construct()
	 * @used-by req()
	 * @var array(string => mixed)
	 */
	private $_req;
}