<?php
namespace Df\API\Response;
use Df\API\Client;
use Df\Core\Exception as DFE;
/**
 * 2017-07-05
 * @see \Df\ZohoBI\API\Validator
 * @see \Dfe\Dynamics365\API\Validator\JSON
 */
abstract class Validator {
	/**
	 * 2017-07-06
	 * @used-by validate()
	 * @see \Df\ZohoBI\API\Validator::message()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::message()
	 * @return string
	 */
	abstract protected function message();

	/**
	 * 2017-07-06
	 * @used-by validate()
	 * @see \Df\ZohoBI\API\Validator::rs()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::rs()
	 * @return string
	 */
	abstract protected function rs();

	/**
	 * 2017-07-06
	 * @used-by validate()
	 * @see \Df\ZohoBI\API\Validator::title()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::title()
	 * @return string
	 */
	abstract protected function title();

	/**
	 * 2017-07-06
	 * @used-by validate()
	 * @see \Df\ZohoBI\API\Validator::valid()
	 * @see \Dfe\Dynamics365\API\Validator\JSON::valid()
	 * @return bool
	 */
	abstract protected function valid();

	/**
	 * 2017-07-06
	 * @used-by \Df\API\Client::p()
	 * @param Client $c
	 * @param mixed $r
	 */
	final function __construct(Client $c, $r) {$this->_c = $c; $this->_r = $r;}

	/**
	 * 2017-07-06
	 * @used-by \Df\ZohoBI\API\Validator::title()
	 * @return Client
	 */
	final protected function c() {return $this->_c;}

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
	 * 2017-07-05
	 * @used-by \Df\API\Client::p()
	 * @throws DFE
	 */
	final function validate() {
		if (!$this->valid()) {
			/** @var string $message */
			$message = $this->message();
			/** @var string $req */
			$req = df_zf_http_last_req($this->_c->c());
			/** @var string $res */
			$res = $this->rs();
			/** @var DFE $ex */
			$ex = df_error_create(
				"The «{$this->_c->path()}» {$this->title()} API request has failed: «{$message}»."
				."\nThe full error description:\n$res"
				."\nThe full request:\n$req"
			);
			/** @var string $m */
			df_log_l($m = $this->_c->m(), $ex);
			df_sentry($m, $message, ['extra' => ['Request' => $req, 'Response' => $res]]);
			throw $ex;
		}
	}

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @used-by c()
	 * @var Client
	 */
	private $_c;

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @used-by r()
	 * @var mixed
	 */
	private $_r;
}