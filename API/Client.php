<?php
namespace Df\API;
use \Df\API\Response\IFilter;
use Df\API\Response\Validator;
use Df\Core\Exception as DFE;
use Zend_Http_Client as C;
/**
 * 2017-07-05
 * @see \Df\Zoho\API\Client
 * @see \Dfe\Dynamics365\API\Client
 */
abstract class Client {
	/**
	 * 2017-07-05
	 * @used-by p()
	 * @see \Dfe\Dynamics365\API\Client::headers()
	 * @see \Df\ZohoBI\API\Client::headers()
	 * @return array(string => string)
	 */
	abstract protected function headers();

	/**
	 * 2017-07-05
	 * @used-by p()
	 * @see \Dfe\Dynamics365\API\Client::uriBase()
	 * @see \Dfe\ZohoBooks\API\Client::uriBase()
	 * @see \Dfe\ZohoInventory\API\Client::uriBase()
	 * @return string
	 */
	abstract protected function uriBase();

	/**
	 * 2017-07-02
	 * @used-by \Df\Zoho\API\Client::i()
	 * @used-by \Dfe\Dynamics365\API\Facade::metadata()
	 * @used-by \Dfe\Dynamics365\API\Facade::p()
	 * @param string $path
	 * @param array(string => mixed) $p [optional]
	 * @param string|null $method [optional]
	 * @throws DFE
	 */
	final function __construct($path, array $p = [], $method = null) {
		$this->_path = $path;
		$this->_c = new C;
		$this->_c->setMethod($method = $method ?: C::GET);
		C::GET === $method ? $this->_c->setParameterGet($p) : $this->_c->setParameterPost($p);
		/**
		 * 2017-07-06
		 * @uses uriBase() is important here, because the rest cache key parameters could be the same
		 * for multiple APIs (e.g. for Zoho Books and Zoho Inventory).
		 */
		$this->_ckey = implode('::', [$this->uriBase(), $path, $method, dfa_hash($p)]);
	}

	/**
	 * 2017-07-02
	 * @used-by p()
	 * @used-by \Df\API\Response\Validator::validate()
	 * @return C
	 */
	final function c() {return $this->_c;}

	/**
	 * 2017-07-06
	 * @used-by \Df\ZohoBI\API\Validator::title()
	 * @used-by \Df\API\Response\Validator::validate()
	 * @return string
	 */
	function m() {return df_module_name($this);}

	/**
	 * 2017-06-30
	 * @used-by \Dfe\Dynamics365\API\Facade::p()
	 * @used-by \Dfe\ZohoBooks\API\R::p()
	 * @throws DFE
	 * @return string
	 */
	final function p() {return df_cache_get_simple($this->_ckey, function() {
		/** @var C $c */
		$c = $this->c();
		$c->setConfig(['timeout' => 120]);
		$c->setHeaders($this->headers());
		$c->setUri("{$this->uriBase()}/{$this->path()}");
		/** @var mixed $r */
		$r = $c->request()->getBody();
		/** @var string $filterC */
		if ($filterC = $this->responseFilterC()) {
			/** @var IFilter $filter */
			$filter = new $filterC;
			$r = $filter->filter($r);
		}
		/** @var string $validatorC */
		if ($validatorC = $this->responseValidatorC()) {
			/** @var Validator $validator */
			$validator = new $validatorC($this, $r);
			$validator->validate();
		}
		return $r;
	});}

	/**
	 * 2017-07-02
	 * @used-by p()
	 * @used-by \Df\API\Response\Validator::validate()
	 * @return C
	 */
	final function path() {return $this->_path;}

	/**
	 * 2017-07-05 A descendant class can return null if it does not need to filter the responses.
	 * @used-by p()
	 * @see \Dfe\Dynamics365\API\Client\JSON::responseFilterC()
	 * @see \Df\Zoho\API\Client::responseFilterC()
	 * @return string|null
	 */
	protected function responseFilterC() {return null;}

	/**
	 * 2017-07-05 A descendant class can return null if it does not need to validate the responses.
	 * @used-by p()
	 * @see \Df\ZohoBI\API\Client::responseValidatorC()
	 * @see \Dfe\Dynamics365\API\Client\JSON::responseValidatorC()
	 * @return string
	 */
	protected function responseValidatorC() {return null;}

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by c()
	 * @var C
	 */
	private $_c;

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by p()
	 * @var string
	 */
	private $_ckey;

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by path()
	 * @var string
	 */
	private $_path;
}