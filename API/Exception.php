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
	 * @used-by \Df\API\Client::p()
	 * @see \Df\API\Exception\HTTP::long()
	 * @see \Df\ZohoBI\API\Validator::long()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::long()
	 * @see \Dfe\Moip\API\Validator::long()
	 * @see \Dfe\Qiwi\API\Validator::long()
	 * @return string
	 */
	abstract function long();

	/**
	 * 2017-07-09
	 * @used-by \Df\API\Client::p()
	 * @see \Df\API\Exception\HTTP::short()
	 * @see \Df\ZohoBI\API\Validator::short()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::short()
	 * @see \Dfe\Moip\API\Validator::short()
	 * @see \Dfe\Qiwi\API\Validator::short()
	 * @return string
	 */
	abstract function short();
}