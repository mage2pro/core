<?php
namespace Df\API\Exception;
use \Zend_Http_Response as R;
// 2017-08-08
final class HTTP extends \Df\API\Exception {
	/**
	 * 2017-08-08
	 * @used-by \Df\API\Client::p()
	 * @param R $r
	 */
	function __construct(R $r) {$this->_r = $r;}

	/**
	 * 2017-08-08
	 * @used-by short()
	 * @used-by \Df\API\Client::p()
	 * @see \Df\ZohoBI\API\Validator::long()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::long()
	 * @see \Dfe\Moip\API\Validator::long()
	 * @return string
	 */
	function long() {return "{$this->_r->getStatus()} {$this->_r->getMessage()}";}

	/**
	 * 2017-08-08
	 * @used-by \Df\API\Client::p()
	 * @see \Df\ZohoBI\API\Validator::short()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::short()
	 * @see \Dfe\Moip\API\Validator::short()
	 * @return string
	 */
	function short() {return $this->long();}

	/**
	 * 2017-08-08
	 * @used-by __construct()
	 * @used-by long()
	 * @used-by short()
	 * @var R
	 */
	private $_r;
}