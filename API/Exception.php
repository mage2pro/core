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
	 * @used-by short()
	 * @used-by \Df\API\Client::_p()
	 * @see \Df\API\Exception\HTTP::long()
	 * @see \Df\API\Response\Validator::long()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::long()
	 * @see \Dfe\Moip\API\Validator::long()
	 * @see \Dfe\Qiwi\API\Validator::long()
	 * @see \Dfe\Square\API\Validator::long()
	 * @return string
	 */
	abstract function long();

	/**
	 * 2017-07-09
	 * @used-by \Df\API\Client::_p()
	 * @see \Df\ZohoBI\API\Validator::short()
	 * @see \Dfe\AlphaCommerceHub\API\Validator::short()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::short()
	 * @see \Dfe\Moip\API\Validator::short()
	 * @see \Dfe\Square\API\Validator::short()
	 * @return string
	 */
	function short() {return $this->long();}
}