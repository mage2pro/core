<?php
namespace Df\API;
use Df\API\Exception as E;
use Df\API\Exception\HTTP as eHTTP;
use Df\API\Response\Validator;
use Df\Core\Exception as DFE;
use Zend_Http_Client as C;
use Zend\Filter\FilterChain;
use Zend\Filter\FilterInterface as IFilter;
/**
 * 2017-07-05
 * @see \Df\Zoho\API\Client
 * @see \Dfe\Dynamics365\API\Client
 * @see \Dfe\Moip\API\Client
 * @see \Dfe\Salesforce\API\Client
 */
abstract class Client {
	/**
	 * 2017-07-05
	 * @used-by __construct()
	 * @used-by p()
	 * @see \Df\ZohoBI\API\Client::uriBase()
	 * @see \Dfe\Dynamics365\API\Client::uriBase()
	 * @see \Dfe\Moip\API\Client::uriBase()
	 * @see \Dfe\Salesforce\API\Client::uriBase()
	 * @see \Dfe\ZohoCRM\API\Client::uriBase()
	 * @return string
	 */
	abstract protected function uriBase();

	/**
	 * 2017-07-02
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Df\Zoho\API\Client::i()
	 * @used-by \Dfe\Dynamics365\API\Facade::metadata()
	 * @used-by \Dfe\Dynamics365\API\Facade::p()
	 * @param string $path
	 * @param string|array(string => mixed) $p [optional]
	 * @param string|null $method [optional]
	 * @throws DFE
	 */
	final function __construct($path, $p = [], $method = null) {
		$this->_path = $path;
		$this->_c = df_zf_http();
		$this->_c->setMethod($method = $method ?: C::GET);
		$this->_filtersReq = new FilterChain;
		$this->_filtersRes = new FilterChain;
		$this->_construct();
		$p += $this->commonParams($path);
		C::GET === $method ? $this->_c->setParameterGet($p) : (
			is_array($p = $this->_filtersReq->filter($p)) ? $this->_c->setParameterPost($p) :
				$this->_c->setRawData($p)
		);
		/**
		 * 2017-07-06
		 * @uses uriBase() is important here, because the rest cache key parameters can be the same
		 * for multiple APIs (e.g. for Zoho Books and Zoho Inventory).
		 * 2017-07-07
		 * @uses headers() is important here, because the response can depend on the HTTP headers
		 * (it is true for Microsoft Dynamics 365 and Zoho APIs,
		 * because the authentication token is passed through the headers).
		 */
		$this->_ckey = dfa_hash([$this->uriBase(), $path, $method, $p, $this->headers()]);
	}

	/**
	 * 2017-06-30
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Dfe\Dynamics365\API\Facade::p()
	 * @used-by \Dfe\ZohoBooks\API\R::p()
	 * @throws DFE
	 * @return mixed|null
	 */
	final function p() {return df_cache_get_simple($this->_ckey, function() {
		$c = $this->_c; /** @var C $c */
		$c->setHeaders($this->headers());
		$c->setUri("{$this->uriBase()}/$this->_path");
		try {
			$res = $c->request(); /** @var \Zend_Http_Response $res */
			if (!($resBody = $res->getBody()) && $res->isError()) { /** @var string $resBody */
				throw new eHTTP($res);
			}
			else {
				/** @var mixed|null $result */
				// 2017-08-08
				// «No Content»
				// «The server has successfully fulfilled the request
				// and that there is no additional content to send in the response payload body»
				// https://httpstatuses.com/204
				if (!$resBody && 204 === $res->getStatus()) {
					$result = null;
				}
				else {
					$result = $this->_filtersRes->filter($resBody);
					if ($validatorC = $this->responseValidatorC() /** @var string $validatorC */) {
						$validator = new $validatorC($result); /** @var Validator $validator */
						if (!$validator->valid()) {
							throw $validator;
						}
					}
				}
			}
		}
		catch (\Exception $e) {
			/** @var string $long */ /** @var string $short */
			list($long, $short) = $e instanceof E ? [$e->long(), $e->short()] : [null, df_ets($e)];
			$req = df_zf_http_last_req($c); /** @var string $req */
			$title = df_api_name($m = df_module_name($this)); /** @var string $m */ /** @var string $title */
			/** @var DFE $ex */
			$ex = df_error_create("The «{$this->_path}» {$title} API request has failed: «{$short}».\n" . (
				$long === $short ? "Request:\n{$req}" : df_cc_kv([
					'The full error description' => $long, 'Request' => $req
				])
			));
			df_log_l($m, $ex);
			df_sentry($m, $short, ['extra' => ['Request' => $req, 'Response' => $long]]);
			throw $ex;
		}
		return $result;
	});}

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @see \Df\Zoho\API\Client::_construct()
	 * @see \Dfe\Dynamics365\API\Client\JSON::_construct()
	 * @see \Dfe\Moip\API\Client::_construct()
	 * @see \Dfe\Salesforce\API\Client::_construct()
	 */
	protected function _construct() {}

	/**
	 * 2017-07-08
	 * @used-by __construct()
	 * @see \Df\ZohoBI\API\Client::commonParams()
	 * @param string $path
	 * @return array(string => mixed)
	 */
	protected function commonParams($path) {return [];}

	/**
	 * 2017-07-05
	 * @used-by __construct()
	 * @used-by p()
	 * @see \Df\ZohoBI\API\Client::headers()
	 * @see \Dfe\Dynamics365\API\Client::headers()
	 * @see \Dfe\Moip\API\Client::headers()
	 * @see \Dfe\Salesforce\API\Client::headers()
	 * @return array(string => string)
	 */
	protected function headers() {return [];}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\Moip\API\Client::_construct()
	 */
	final protected function reqJson() {$this->addFilterReq('df_json_encode');}

	/**
	 * 2017-07-06
	 * @used-by \Df\Zoho\API\Client::_construct()
	 * @used-by \Dfe\Dynamics365\API\Client\JSON::_construct()
	 * @used-by \Dfe\Moip\API\Client::_construct()
	 * @used-by \Dfe\Salesforce\API\Client::_construct()
	 */
	final protected function resJson() {$this->addFilterRes('df_json_decode');}

	/**
	 * 2017-07-05 A descendant class can return null if it does not need to validate the responses.
	 * @used-by p()
	 * @see \Df\ZohoBI\API\Client::responseValidatorC()
	 * @see \Dfe\Dynamics365\API\Client\JSON::responseValidatorC()
	 * @see \Dfe\Moip\API\Client::responseValidatorC()
	 * @return string
	 */
	protected function responseValidatorC() {return null;}

	/**
	 * 2017-07-13
	 * @used-by reqJson()
	 * @param callable|IFilter $f
	 * @param int $priority
	 */
	private function addFilterReq($f, $priority = FilterChain::DEFAULT_PRIORITY) {
		$this->_filtersReq->attach($f, $priority);
	}

	/**
	 * 2017-07-06
	 * @used-by resJson()
	 * @param callable|IFilter $f
	 * @param int $priority
	 */
	private function addFilterRes($f, $priority = FilterChain::DEFAULT_PRIORITY) {
		$this->_filtersRes->attach($f, $priority);
	}

	/**
	 * 2017-07-07
	 * Adds $f at the lowest priority (it will be applied after all other filters).
	 * Currently, it is not used anywhere.
	 * @param callable|IFilter $f
	 */
	private function appendFilterRes($f) {$this->_filtersRes->attach(
		$f, df_zf_pq_min($this->_filtersRes->getFilters()) - 1
	);}

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by p()
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
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by addFilterReq()
	 * @used-by p()
	 * @var FilterChain
	 */
	private $_filtersReq;

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @used-by addFilterRes()
	 * @used-by appendFilterRes()
	 * @used-by p()
	 * @var FilterChain
	 */
	private $_filtersRes;

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by p()
	 * @var string
	 */
	private $_path;
}