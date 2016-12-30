<?php
namespace Df\Payment\Webhook;
// 2016-07-09
class Exception extends \Df\Payment\Exception {
	/**
	 * 2016-07-09
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @param string $message
	 * @param Response $response
	 */
	public function __construct($message, Response $response) {
		parent::__construct($message);
		$this->_response = $response;
	}

	/**
	 * 2016-07-10
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @return string
	 */
	public function message() {return df_cc_n(
		$this->getMessage(), Report::ic(df_con_heir($this, Report::class), $this->response())
	);}

	/** @return Response */
	protected function response() {return $this->_response;}

	/**
	 * 2016-07-09
	 * @var Response
	 */
	private $_response;
}