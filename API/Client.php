<?php
namespace Df\API;
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
	 * @see \Df\Zoho\API\BI\Client::headers()
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
	 * @used-by \Dfe\Dynamics365\API\R::metadata()
	 * @used-by \Dfe\Dynamics365\API\R::p()
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
		$this->_ckey = implode('::', [$path, $method, dfa_hash($p)]);
	}

	/**
	 * 2017-06-30
	 * @used-by \Dfe\Dynamics365\API\R::p()
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
			/** @var Response\IFilter $filter */
			$filter = new $filterC;
			$r = $filter->filter($r);
		}
		/** @var string $validatorC */
		if ($validatorC = $this->responseValidatorC()) {
			/** @var Response\IValidator $validator */
			$validator = new $validatorC;
			$validator->validate($this, $r);
		}
		return $r;
	});}

	/**
	 * 2017-07-02
	 * @used-by p()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::p()
	 * @return C
	 */
	final function path() {return $this->_path;}

	/**
	 * 2017-07-02
	 * @used-by p()
	 * @used-by \Dfe\Dynamics365\API\Validator\JSON::p()
	 * @return C
	 */
	final function c() {return $this->_c;}

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