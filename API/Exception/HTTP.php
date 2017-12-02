<?php
namespace Df\API\Exception;
use \Zend_Http_Response as R;
// 2017-08-08
/** @used-by \Df\API\Client::p() */
final class HTTP extends \Df\API\Exception {
	/**
	 * 2017-08-08
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @used-by \Df\API\Client::p()
	 * @param R $r
	 */
	function __construct(R $r) {$this->_r = $r;}

	/**
	 * 2017-08-08
	 * @override
	 * @see \Df\API\Exception::long()
	 * @used-by \Df\API\Client::_p()
	 * @used-by \Df\API\Exception::short()
	 * @return string
	 */
	function long() {return "{$this->_r->getStatus()} {$this->_r->getMessage()}";}

	/**
	 * 2017-08-08
	 * @used-by __construct()
	 * @used-by long()
	 * @var R
	 */
	private $_r;
}