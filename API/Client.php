<?php
namespace Df\API;
use Df\API\Exception as E;
use Df\API\Exception\HTTP as eHTTP;
use Df\API\Response\Validator;
use Df\Core\Exception as DFE;
use Zend\Filter\FilterChain;
use Zend\Filter\FilterInterface as IFilter;
use Zend_Http_Client as C;
/**
 * 2017-07-05
 * @see \Df\Zoho\API\Client
 * @see \Dfe\AlphaCommerceHub\API\Client
 * @see \Dfe\Dynamics365\API\Client
 * @see \Dfe\Moip\API\Client
 * @see \Dfe\Qiwi\API\Client
 * @see \Dfe\Salesforce\API\Client
 * @see \Dfe\Square\API\Client
 */
abstract class Client {
	/**
	 * 2017-07-05
	 * @used-by __construct()
	 * @used-by url()
	 * @see \Df\ZohoBI\API\Client::urlBase()
	 * @see \Dfe\AlphaCommerceHub\API\Client::urlBase()
	 * @see \Dfe\Dynamics365\API\Client::urlBase()
	 * @see \Dfe\Moip\API\Client::urlBase()
	 * @see \Dfe\Qiwi\API\Client::urlBase()
	 * @see \Dfe\Salesforce\API\Client::urlBase()
	 * @see \Dfe\Square\API\Client::urlBase()
	 * @see \Dfe\ZohoCRM\API\Client::urlBase()
	 * @return string
	 */
	abstract protected function urlBase();

	/**
	 * 2017-07-02
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Df\Zoho\API\Client::i()
	 * @used-by \Dfe\Dynamics365\API\Facade::metadata()
	 * @used-by \Dfe\Dynamics365\API\Facade::p()
	 * @param string $path
	 * @param string|array(string => mixed) $p [optional]
	 * @param string|null $method [optional]
	 * @param array(string => mixed) $zfConfig [optional]
	 * @throws DFE
	 */
	final function __construct($path, $p = [], $method = null, array $zfConfig = []) {
		$this->_path = $path;
		$this->_c = df_zf_http(null, $zfConfig);
		$this->_method = $method = $method ?: C::GET;
		$this->_c->setMethod($this->_method);
		$this->_filtersReq = new FilterChain;
		$this->_filtersResAV = new FilterChain;
		$this->_filtersResBV = new FilterChain;
		$this->_construct();
		$p += $this->commonParams($path);
		C::GET === $method ? $this->_c->setParameterGet($p) : (
			is_array($p = $this->_filtersReq->filter($p)) ? $this->_c->setParameterPost($p) :
				$this->_c->setRawData($p)
		);
		if (!$this->destructive()) {
			/**
			 * 2017-07-06
			 * @uses urlBase() is important here, because the rest cache key parameters can be the same
			 * for multiple APIs (e.g. for Zoho Books and Zoho Inventory).
			 * 2017-07-07
			 * @uses headers() is important here, because the response can depend on the HTTP headers
			 * (it is true for Microsoft Dynamics 365 and Zoho APIs,
			 * because the authentication token is passed through the headers).
			 */
			$this->_ckey = dfa_hash([$this->urlBase(), $path, $method, $p, $this->headers()]);
		}
	}

	/**
	 * 2017-06-30
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Dfe\Dynamics365\API\Facade::p()
	 * @used-by \Dfe\ZohoBooks\API\R::p()
	 * @throws DFE
	 * @return mixed|null
	 */
	final function p() {
		$tag = df_cts($this, '_'); /** @var string $tag */
		if ($d = $this->destructive()) { /** @var bool $d */
			df_cache_clean_tag($tag);
		}
		return $d ? $this->_p() : df_cache_get_simple($this->_ckey, function() {return $this->_p();}, [$tag]);
	}

	/**
	 * 2017-07-06
	 * @used-by __construct()
	 * @see \Df\Zoho\API\Client::_construct()
	 * @see \Dfe\AlphaCommerceHub\API\Client::_construct()
	 * @see \Dfe\Dynamics365\API\Client\JSON::_construct()
	 * @see \Dfe\Moip\API\Client::_construct()
	 * @see \Dfe\Qiwi\API\Client::_construct()
	 * @see \Dfe\Salesforce\API\Client::_construct()
	 */
	protected function _construct() {}

	/**
	 * 2017-07-06
	 * @used-by resJson()
	 * @used-by \Dfe\Qiwi\API\Client::_construct()
	 * @param callable|IFilter $f
	 * @param int $priority
	 */
	final protected function addFilterResBV($f, $priority = FilterChain::DEFAULT_PRIORITY) {
		$this->_filtersResBV->attach($f, $priority);
	}

	/**
	 * 2017-07-08
	 * @used-by __construct()
	 * @see \Df\ZohoBI\API\Client::commonParams()
	 * @see \Dfe\AlphaCommerceHub\API\Client::commonParams()
	 * @param string $path
	 * @return array(string => mixed)
	 */
	protected function commonParams($path) {return [];}

	/**
	 * 2017-07-05
	 * @used-by __construct()
	 * @used-by p()
	 * @see \Df\ZohoBI\API\Client::headers()
	 * @see \Dfe\AlphaCommerceHub\API\Client::headers()
	 * @see \Dfe\Dynamics365\API\Client::headers()
	 * @see \Dfe\Moip\API\Client::headers()
	 * @see \Dfe\Qiwi\API\Client::headers()
	 * @see \Dfe\Salesforce\API\Client::headers()
	 * @return array(string => string)
	 */
	protected function headers() {return [];}

	/**
	 * 2017-08-10
	 * @used-by \Dfe\Square\API\Client::headers()
	 * @return string
	 */
	final protected function method() {return $this->_method;}

	/**
	 * 2017-12-02
	 * @used-by \Dfe\AlphaCommerceHub\API\Client::commonParams()
	 * @return string
	 */
	final protected function path() {return $this->_path;}

	/**
	 * 2017-07-13
	 * @used-by \Dfe\Moip\API\Client::_construct()
	 * @used-by \Dfe\Square\API\Client::_construct()
	 */
	final protected function reqJson() {$this->addFilterReq('df_json_encode');}

	/**
	 * 2017-07-06
	 * @used-by \Df\Zoho\API\Client::_construct()
	 * @used-by \Dfe\Dynamics365\API\Client\JSON::_construct()
	 * @used-by \Dfe\Moip\API\Client::_construct()
	 * @used-by \Dfe\Qiwi\API\Client::_construct()
	 * @used-by \Dfe\Salesforce\API\Client::_construct()
	 * @used-by \Dfe\Square\API\Client::_construct()
	 */
	final protected function resJson() {$this->addFilterResBV('df_json_decode');}

	/**
	 * 2017-07-05 A descendant class can return null if it does not need to validate the responses.
	 * @used-by p()
	 * @see \Df\ZohoBI\API\Client::responseValidatorC()
	 * @see \Dfe\Dynamics365\API\Client\JSON::responseValidatorC()
	 * @see \Dfe\Moip\API\Client::responseValidatorC()
	 * @see \Dfe\Qiwi\API\Client::responseValidatorC()
	 * @return string
	 */
	protected function responseValidatorC() {return null;}

	/**
	 * 2017-10-08
	 * Some APIs return their results with a non-important root tag, which is uses only as a syntax sugar.
	 * Look at the Square Connect API v2, for example: https://docs.connect.squareup.com/api/connect/v2
	 * A response to `GET /v2/locations` looks like:
	 *		{"locations": [{<...>}, {<...>}, {<...>}]}
	 * [Square] An example of a response to `GET /v2/locations`: https://mage2.pro/t/4647
	 * The root `locations` tag is just a syntax sugar, so it is convenient to strip it.
	 * @uses df_first()
	 */
	final protected function resStripRoot() {$this->addFilterResAV('df_first');}

	/**
	 * 2017-12-02
	 * @used-by _p()
	 * @see \Dfe\AlphaCommerceHub\API\Client::url()
	 * @return string
	 */
	protected function url() {return "{$this->urlBase()}/$this->_path";}

	/**
	 * 2017-08-10
	 * @used-by p()
	 * @throws DFE
	 * @return mixed|null
	 */
	private function _p() {
		$c = $this->_c; /** @var C $c */
		$c->setHeaders($this->headers());
		$c->setUri($this->url());
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
					$result = $this->_filtersResBV->filter($resBody);
					if ($validatorC = $this->responseValidatorC() /** @var string $validatorC */) {
						$validator = new $validatorC($result); /** @var Validator $validator */
						if (!$validator->valid()) {
							throw $validator;
						}
					}
					$result = $this->_filtersResAV->filter($result);
				}
			}
		}
		catch (\Exception $e) {
			/** @var string $long */ /** @var string $short */
			list($long, $short) = $e instanceof E ? [$e->long(), $e->short()] : [null, df_ets($e)];
			$req = df_zf_http_last_req($c); /** @var string $req */
			$title = df_api_name($m = df_module_name($this)); /** @var string $m */ /** @var string $title */
			$ex = df_error_create("A `{$this->_path}` {$title} API request has failed: «{$short}».\n" . (
				$long === $short ? "Request:\n{$req}" : df_cc_kv([
					'The full error description' => $long, 'Request' => $req
				])
			)); /** @var DFE $ex */
			df_log_l($m, $ex);
			df_sentry($m, $short, ['extra' => ['Request' => $req, 'Response' => $long]]);
			throw $ex;
		}
		return $result;
	}

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
	 * 2017-10-08
	 * @used-by resStripRoot()
	 * @param callable|IFilter $f
	 * @param int $priority
	 */
	private function addFilterResAV($f, $priority = FilterChain::DEFAULT_PRIORITY) {
		$this->_filtersResAV->attach($f, $priority);
	}

	/**
	 * 2017-10-08
	 * Adds $f at the lowest priority (it will be applied after all other filters).
	 * Currently, it is not used anywhere.
	 * @param callable|IFilter $f
	 */
	private function appendFilterResAV($f) {$this->_filtersResAV->attach(
		$f, df_zf_pq_min($this->_filtersResAV->getFilters()) - 1
	);}

	/**
	 * 2017-07-07
	 * Adds $f at the lowest priority (it will be applied after all other filters).
	 * Currently, it is not used anywhere.
	 * @param callable|IFilter $f
	 */
	private function appendFilterResBV($f) {$this->_filtersResBV->attach(
		$f, df_zf_pq_min($this->_filtersResBV->getFilters()) - 1
	);}

	/**
	 * 2017-08-10
	 * @used-by __construct()
	 * @used-by p()
	 * @return bool
	 */
	private function destructive() {return C::GET !== $this->_method;}

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
	 * 2017-10-08 This filter chain is applied to a result after the result validation.
	 * @used-by __construct()
	 * @used-by addFilterResAV()
	 * @used-by appendFilterResAV()
	 * @used-by p()
	 * @var FilterChain
	 */
	private $_filtersResAV;

	/**
	 * 2017-07-06
	 * 2017-10-08 This filter chain is applied to a result before the result validation.
	 * @used-by __construct()
	 * @used-by addFilterResBV()
	 * @used-by appendFilterResBV()
	 * @used-by p()
	 * @var FilterChain
	 */
	private $_filtersResBV;

	/**
	 * 2017-08-10
	 * @used-by __construct()
	 * @used-by destructive()
	 * @used-by method()
	 * @var string
	 */
	private $_method;

	/**
	 * 2017-07-02
	 * @used-by __construct()
	 * @used-by path()
	 * @used-by url()
	 * @var string
	 */
	private $_path;
}