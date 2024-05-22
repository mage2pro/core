<?php
namespace Df\API\Response;
use Df\API\Exception;
/**
 * 2017-07-05
 * @see \Dfe\AlphaCommerceHub\API\Validator
 * @see \Dfe\Dynamics365\API\Validator\JSON
 * @see \Dfe\FacebookLogin\ResponseValidator
 * @see \Dfe\GoogleFont\ResponseValidator
 * @see \Dfe\Moip\API\Validator
 * @see \Dfe\Qiwi\API\Validator
 * @see \Dfe\Sift\API\Validator\Event
 * @see \Dfe\Sift\API\Validator\GetDecisions
 * @see \Dfe\Square\API\Validator
 * @see \Dfe\TBCBank\API\Validator
 * @see \Dfe\Vantiv\API\Validator
 * @see \Dfe\ZohoBI\API\Validator
 * @see \Inkifi\Mediaclip\API\Validator
 * @see \Inkifi\Pwinty\API\Validator
 */
abstract class Validator extends Exception {
	/**
	 * 2017-07-06
	 * @used-by \Df\API\Client::_p()
	 * @see \Dfe\AlphaCommerceHub\API\Validator::valid()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @see \Dfe\FacebookLogin\ResponseValidator::valid()
	 * @see \Dfe\Moip\API\Validator::valid()
	 * @see \Dfe\Qiwi\API\Validator::valid()
	 * @see \Dfe\Sift\API\Validator\Event::valid()
	 * @see \Dfe\Square\API\Validator::valid()
	 * @see \Dfe\TBCBank\API\Validator::valid()
	 * @see \Dfe\Vantiv\API\Validator::valid()
	 * @see \Dfe\ZohoBI\API\Validator::valid()
	 * @see \Inkifi\Mediaclip\API\Validator::valid()
	 * @see \Inkifi\Pwinty\API\Validator::valid()
	 */
	abstract function valid():bool;

	/**
	 * 2017-07-06
	 * @override
	 * @see \Df\Core\Exception::__construct()
	 * @used-by \Df\API\Client::_p()
	 * @param array(string => mixed) $r
	 */
	final function __construct(array $r) {
		$this->_r = $r;
		# 2024-05-22
		# "Provide an ability to specify a context for a `Df\Core\Exception` instance":
		# https://github.com/mage2pro/core/issues/375
		$this->context($r);
	}

	/**
	 * 2017-12-03
	 * @override
	 * @see \Df\API\Exception::long()
	 * @used-by \Df\API\Client::_p()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::long()
	 * @see \Dfe\Moip\API\Validator::long()
	 * @see \Dfe\Qiwi\API\Validator::long()
	 * @see \Dfe\Sift\API\Validator\Event::long()
	 * @see \Dfe\Sift\API\Validator\GetDecisions::long()
	 * @see \Dfe\Square\API\Validator::long()
	 * @see \Dfe\TBCBank\API\Validator::long()
	 * @see \Dfe\Vantiv\API\Validator::long()
	 * @see \Inkifi\Mediaclip\API\Validator::long()
	 * @see \Inkifi\Pwinty\API\Validator::long()
	 */
	function long():string {return df_json_encode($this->_r);}

	/**
	 * 2024-05-22 "Remove `Df\Core\Exception::$_data`": https://github.com/mage2pro/core/issues/385
	 * @override
	 * @see \Df\Core\Exception::message()
	 * @used-by df_xts()
	 */
	final function message():string {return $this->short();}

	/**
	 * 2017-07-06
	 * 2022-10-24
	 * `mixed` as a return type is not supported by PHP < 8: https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * @used-by \Dfe\AlphaCommerceHub\API\Validator::result()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::long()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::message()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::short()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @used-by \Dfe\FacebookLogin\ResponseValidator::valid()
	 * @used-by \Dfe\Moip\API\Validator::error()
	 * @used-by \Dfe\Moip\API\Validator::errors()
	 * @used-by \Dfe\Qiwi\API\Validator::code()
	 * @used-by \Dfe\Sift\API\Validator\Event::long()
	 * @used-by \Dfe\Sift\API\Validator\Event::valid()
	 * @used-by \Dfe\Sift\API\Validator\GetDecisions::long()
	 * @used-by \Dfe\Sift\API\Validator\GetDecisions::short()
	 * @used-by \Dfe\Sift\API\Validator\GetDecisions::valid()
	 * @used-by \Dfe\Square\API\Validator::errors()
	 * @used-by \Dfe\TBCBank\API\Validator::long()
	 * @used-by \Dfe\Vantiv\API\Validator::long()
	 * @used-by \Dfe\Vantiv\API\Validator::valid()
	 * @used-by \Dfe\ZohoBI\API\Validator::message()
	 * @used-by \Dfe\ZohoBI\API\Validator::rs()
	 * @used-by \Dfe\ZohoBI\API\Validator::valid()
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