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
	 * 2024-05-22 "Remove `Df\Core\Exception::$_data`": https://github.com/mage2pro/core/issues/385
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by df_xts()
	 */
	final function message():string {return $this->long();}

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