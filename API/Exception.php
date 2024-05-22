<?php
namespace Df\API;
/**
 * 2017-07-09
 * Unfortunately, PHP allows to throw only the @see \Exception descendants.
 * @see \Df\API\Exception\HTTP
 * @see \Df\API\Response\Validator
 */
abstract class Exception extends \Df\Core\Exception {
	/**
	 * 2017-07-09
	 * @used-by self::message()
	 * @used-by self::short()
	 * @used-by \Df\API\Client::_p()
	 * @see \Df\API\Exception\HTTP::long()
	 * @see \Df\API\Response\Validator::long()
	 */
	abstract function long():string;

	/**
	 * 2017-07-09
	 * @used-by \Df\API\Client::_p()
	 * @see \Dfe\AlphaCommerceHub\API\Validator::short()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::short()
	 * @see \Dfe\Sift\API\Validator\GetDecisions::short()
	 * @see \Dfe\Square\API\Validator::short()
	 * @see \Dfe\ZohoBI\API\Validator::short()
	 */
	function short():string {return $this->long();}
}