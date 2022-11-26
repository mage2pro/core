<?php
namespace Df\API\Response;
use Df\API\Exception;
/**
 * 2017-07-05
 * @see \Df\ZohoBI\API\Validator
 * @see \Dfe\AlphaCommerceHub\API\Validator
 * @see \Dfe\Dynamics365\API\Validator\JSON
 * @see \Dfe\Moip\API\Validator
 * @see \Dfe\Qiwi\API\Validator
 * @see \Dfe\Sift\API\Validator\GetDecisions
 * @see \Dfe\Sift\API\Validator\Event
 * @see \Dfe\Square\API\Validator
 * @see \Dfe\TBCBank\API\Validator
 * @see \Dfe\Vantiv\API\Validator
 * @see \Inkifi\Mediaclip\API\Validator
 * @see \Inkifi\Pwinty\API\Validator
 */
abstract class Validator extends Exception {
	/**
	 * 2017-07-06
	 * @used-by \Df\API\Client::_p()
	 * @see \Df\ZohoBI\API\Validator::valid()
	 * @see \Dfe\AlphaCommerceHub\API\Validator::valid()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @see \Dfe\Moip\API\Validator::valid()
	 * @see \Dfe\Qiwi\API\Validator::valid()
	 * @see \Dfe\Sift\API\Validator\Event::valid()
	 * @see \Dfe\Square\API\Validator::valid()
	 * @see \Dfe\TBCBank\API\Validator::valid()
	 * @see \Dfe\Vantiv\API\Validator::valid()
	 * @see \Inkifi\Mediaclip\API\Validator::valid()
	 * @see \Inkifi\Pwinty\API\Validator::valid()
	 * @return bool
	 */
	abstract function valid():bool;

	/**
	 * 2017-07-06
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @used-by \Df\API\Client::_p()
	 * @param array(string => mixed) $r
	 */
	final function __construct(array $r) {$this->_r = $r;}

	/**
	 * 2017-12-03
	 * @override
	 * @see \Df\API\Exception::long()
	 * @used-by \Df\API\Client::_p()
	 */
	function long():string {return df_json_encode($this->_r);}

	/**
	 * 2017-07-06
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @used-by \Df\ZohoBI\API\Validator::message()
	 * @used-by \Df\ZohoBI\API\Validator::rs()
	 * @used-by \Df\ZohoBI\API\Validator::valid()
	 * @used-by \Dfe\AlphaCommerceHub\API\Validator::result()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::message()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::rs()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @used-by \Dfe\Qiwi\API\Validator::code()
	 * @used-by \Dfe\Sift\API\Validator\GetDecisions::long()
	 * @used-by \Dfe\Sift\API\Validator\GetDecisions::short()
	 * @used-by \Dfe\Sift\API\Validator\GetDecisions::valid()
	 * @used-by \Dfe\Sift\API\Validator\Event::long()
	 * @used-by \Dfe\Sift\API\Validator\Event::valid()
	 * @used-by \Dfe\TBCBank\API\Validator::long()
	 * @used-by \Inkifi\Pwinty\API\Validator::long()
	 * @used-by \Inkifi\Pwinty\API\Validator::valid()
	 * @return array(string => mixed)|string
	 */
	final protected function r(string $k = '') {return dfa($this->_r, $k);}

	/**
	 * 2017-07-06
	 * @used-by self::__construct()
	 * @used-by self::long()
	 * @used-by self::r()
	 * @var array(string => mixed)
	 */
	private $_r;
}