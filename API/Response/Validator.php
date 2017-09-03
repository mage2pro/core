<?php
namespace Df\API\Response;
use Df\API\Exception;
use Df\Core\Exception as DFE;
/**
 * 2017-07-05
 * @see \Df\ZohoBI\API\Validator
 * @see \Dfe\Dynamics365\API\Validator\JSON
 * @see \Dfe\Moip\API\Validator
 * @see \Dfe\Qiwi\API\Validator
 */
abstract class Validator extends Exception {
	/**
	 * 2017-07-06
	 * @used-by validate()
	 * @see \Df\ZohoBI\API\Validator::valid()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @see \Dfe\Moip\API\Validator::valid()
	 * @see \Dfe\Qiwi\API\Validator::valid()
	 * @return bool
	 */
	abstract function valid();

	/**
	 * 2017-07-06
	 * @used-by \Df\API\Client::p()
	 * @param mixed $r
	 */
	final function __construct($r) {$this->_r = $r;}

	/**
	 * 2017-07-06
	 * @used-by \Df\ZohoBI\API\Validator::message()
	 * @used-by \Df\ZohoBI\API\Validator::rs()
	 * @used-by \Df\ZohoBI\API\Validator::valid()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::message()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::rs()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @return mixed
	 */
	final protected function r() {return $this->_r;}

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @used-by r()
	 * @var mixed
	 */
	private $_r;
}