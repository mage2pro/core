<?php
namespace Df\API\Response;
use Df\API\Exception;
use Df\Core\Exception as DFE;
/**
 * 2017-07-05
 * @see \Df\ZohoBI\API\Validator
 * @see \Dfe\AlphaCommerceHub\API\Validator
 * @see \Dfe\Dynamics365\API\Validator\JSON
 * @see \Dfe\Moip\API\Validator
 * @see \Dfe\Qiwi\API\Validator
 * @see \Dfe\Square\API\Validator
 */
abstract class Validator extends Exception {
	/**
	 * 2017-07-06
	 * @used-by validate()
	 * @see \Df\ZohoBI\API\Validator::valid()
	 * @see \Dfe\AlphaCommerceHub\API\Validator::valid()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @see \Dfe\Moip\API\Validator::valid()
	 * @see \Dfe\Qiwi\API\Validator::valid()
	 * @see \Dfe\Square\API\Validator::valid()
	 * @return bool
	 */
	abstract function valid();

	/**
	 * 2017-07-06
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @used-by \Df\API\Client::p()
	 * @param mixed $r
	 */
	final function __construct($r) {$this->_r = $r;}

	/**
	 * 2017-12-03
	 * @override
	 * @see \Df\API\Exception::long()
	 * @used-by \Df\API\Client::_p()
	 * @return string
	 */
	function long() {return df_json_encode($this->_r);}

	/**
	 * 2017-07-06
	 * @used-by \Df\ZohoBI\API\Validator::message()
	 * @used-by \Df\ZohoBI\API\Validator::rs()
	 * @used-by \Df\ZohoBI\API\Validator::valid()
	 * @used-by \Dfe\AlphaCommerceHub\API\Validator::result()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::message()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::rs()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @used-by \Dfe\Qiwi\API\Validator::code()
	 * @return mixed
	 */
	final protected function r() {return $this->_r;}

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @used-by long()
	 * @used-by r()
	 * @var mixed
	 */
	private $_r;
}