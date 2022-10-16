<?php
namespace Df\Sentry;
use \Exception as E;
use Df\Core\Exception as DFE;
final class Client {
	/**
	 * 2020-06-27
	 * @used-by df_sentry_m()
	 * @param int $projectId
	 * @param string $keyPublic
	 * @param string $keyPrivate
	 */
	function __construct($projectId, $keyPublic, $keyPrivate) {
		$this->_projectId = $projectId;
		$this->_keyPublic = $keyPublic;
		$this->_keyPrivate = $keyPrivate;
		$this->_user = null;
		$this->context = new Context;
		$this->curl_path = 'curl';
		$this->error_types = null;
		$this->extra_data = [];
		$this->logger = 'php';
		$this->severity_map = null;
		$this->site = dfa($_SERVER, 'SERVER_NAME');
		$this->tags = [];
		$this->timeout = 2;
		$this->trust_x_forwarded_proto = null;

		$this->sdk = ['name' => 'mage2.pro', 'version' => df_core_version()];
		$this->serializer = new Serializer;
		$this->transaction = new TransactionStack;
		if (!df_is_cli() && isset($_SERVER['PATH_INFO'])) {
			$this->transaction->push($_SERVER['PATH_INFO']);
		}
	}

	/**
	 * 2020-06-27
	 * @used-by df_sentry()
	 * @param string $m
	 * @param array $d
	 */
	function captureMessage($m, array $d) {$this->capture([
		'message' => $m, 'sentry.interfaces.Message' => ['formatted' => $m, 'message' => $m, 'params' => []]
	] + $d);}

	/**
	 * 2017-01-10
	 * 2019-05-20
	 * I intentionally use array_merge_recursive() instead of @see df_extend()
	 * because I want values to be merged for a duplicate key.
	 * I is needed for @see df_sentry_extra_f()
	 * @used-by df_sentry_extra()
	 * @used-by df_sentry_extra_f()
	 * @param array(string => mixed) $a
	 */
	function extra(array $a) {$this->context->extra = array_merge_recursive($this->context->extra, $a);}

	/**
	 * 2017-01-10 «/» can not be used in a tag.
	 * 2017-02-09
	 * Hieroglyphs (e.g. «歐付寶 O'Pay (allPay)») can not be used too:
	 * it leads to the «Discarded invalid value for parameter 'tags'» error.
	 * @used-by df_sentry_tags()
	 * @uses df_translit_url()
	 * @param array(string => string) $a
	 */
	function tags(array $a) {$this->context->tags = dfak_transform($a, 'df_translit_url') + $this->context->tags;}

	/**
	 * @used-by df_sentry_m()
	 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @param array(string => mixed) $data
	 * @param bool $merge [optional]
	 */
	function user(array $d, $merge = true) {
		$this->context->user = $d + (!$merge || !$this->context->user ? [] : $this->context->user);
	}

	/**
	 * 2020-06-28
	 * @used-by df_sentry()
	 * @param E|DFE $e
	 * @param array(string => mixed) $data
	 */
	function captureException(E $e, array $data) {
		$eOriginal = $e; /** @var E $eOriginal */
		do {
			$isDFE = $e instanceof DFE; /** @var bool $isDFE */
			$dataI = [
				'type' => $isDFE ? $e->sentryType() : get_class($e)
				,'value' => $this->serializer->serialize($isDFE ? $e->messageSentry() : $e->getMessage())
			];
			$trace = $e->getTrace();
			$needAddCurrentFrame = !self::needSkipFrame($trace[0]); /** @var bool $needAddCurrentFrame */
			while (self::needSkipFrame($trace[0])) {
				array_shift($trace);
			}
			if ($needAddCurrentFrame) {
				array_unshift($trace, ['file' => $e->getFile(), 'line' => $e->getLine()]);
			}
			$dataI['stacktrace'] = ['frames' => Trace::info($trace)];
			$exceptions[] = $dataI;
		} while ($e = $e->getPrevious());
		$data['exception'] = ['values' => array_reverse($exceptions)];
		if (empty($data['level'])) {
			if (method_exists($eOriginal, 'getSeverity')) {
				/** 2020-06-28 @uses \ErrorException::getSeverity() */
				$data['level'] = $this->translateSeverity($eOriginal->getSeverity());
			}
			else {
				$data['level'] = self::ERROR;
			}
		}
		return $this->capture($data, $trace);
	}
	
	private function get_http_data() {
		$headers = [];
		foreach ($_SERVER as $key => $value) {
			if (0 === strpos($key, 'HTTP_')) {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
			} elseif (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH')) && $value !== '') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))))] = $value;
			}
		}
		$result = [
			'method' => df_request_method(),
			'url' => $this->get_current_url(),
			'query_string' => dfa($_SERVER, 'QUERY_STRING'),
		];
		# dont set this as an empty array as PHP will treat it as a numeric array
		# instead of a mapping which goes against the defined Sentry spec
		if (!empty($post = df_request_o()->getPost()->toArray())) {
			$result['data'] = $post;
		}
		# 2017-01-03 Отсюда куки тоже нужно удалить, потому что Sentry пытается их отсюда взять.
		unset($headers['Cookie']);
		if (!empty($headers)) {
			$result['headers'] = $headers;
		}
		return ['request' => $result];
	}

	/**
	 * 2020-06-27
	 * @used-by capture()
	 * @return array|array[]|null[]
	 */
	private function get_user_data() {
		$user = $this->context->user;
		if ($user === null) {
			if (!function_exists('session_id') || !session_id()) {
				return [];
			}
			/**
			 * 2017-09-27
			 * Previously, it was the following code here:
			 *	if (!empty($_SESSION)) {
			 *		$user['data'] = $_SESSION;
			 *	}
			 * I have removed it because of «Direct use of $_SESSION Superglobal detected»:
			 * https://github.com/mage2pro/core/issues/31
			 * I think, I do not need to log the session at all.
			 */
			$user = ['id' => session_id()];
		}
		return ['user' => $user];
	}

	/**
	 * 2017-04-08
	 * @used-by captureException()
	 * @used-by captureMessage()
	 * @param mixed $data
	 * @param mixed[] $trace [optional]
	 * @return mixed
	 */
	private function capture($data, array $trace = []) {
		$data += [
			'culprit' => $this->transaction->peek()
			,'event_id' => $this->uuid4()
			,'extra' => []
			,'level' => self::ERROR
			# 2020-07-08
			# «Undefined index: message in vendor/mage2pro/core/Sentry/Client.php on line 186»:
			# https://github.com/mage2pro/core/issues/104
			,'message' => substr(dfa($data, 'message'), 0, self::MESSAGE_LIMIT)
			,'platform' => 'php'
			,'project' => $this->_projectId
			,'sdk' => $this->sdk
			,'site' => $this->site
			,'tags' => $this->tags
			,'timestamp' => gmdate('Y-m-d\TH:i:s\Z')
		];
		if (!df_is_cli()) {
			$data += $this->get_http_data();
		}
		$data += $this->get_user_data();
		/**
		 * 2017-01-10
		 * 1) $this->tags — это теги, которые были заданы в конструкторе:
		 * @see \Df\Sentry\Client::__construct()
		 * Они имеют наинизший приоритет.
		 * 2) Намеренно использую здесь + вместо @see df_extend(),
		 * потому что массив tags должен быть одномерным (и поэтому для него + достаточно),
		 * а массив extra хоть и может быть многомерен, однако вряд ли для нас имеет смысл
		 * слияние его элементов на внутренних уровнях вложенности.
		 */
		$data['tags'] += $this->context->tags + $this->tags;
		/** @var array(string => mixed) $extra */
		$extra = $data['extra'] + $this->context->extra + $this->extra_data;
		# 2017-01-03
		# Этот полный JSON в конце массива может быть обрублен в интерфейсе Sentry
		# (и, соответственно, так же обрублен при просмотре события в формате JSON
		# по ссылке в шапке экрана события в Sentry),
		# однако всё равно удобно видеть данные в JSON, пусть даже в обрубленном виде.
		$data['extra'] = Extra::adjust($extra) + ['_json' => df_json_encode($extra)];
		$data = df_clean($data);
		if ($trace && !isset($data['stacktrace']) && !isset($data['exception'])) {
			$data['stacktrace'] = ['frames' => Trace::info($trace)];
		}
		$this->sanitize($data);
		$this->send($data);
		return $data['event_id'];
	}

	/**
	 * 2020-06-27
	 * @used-by send()
	 * @param array(string => mixed) $data
	 * @return string
	 */
	private function encode(&$data) {
		$r = df_json_encode($data);
		if (function_exists('gzcompress')) {
			$r = gzcompress($r);
		}
		$r = base64_encode($r);
		return $r;
	}

	/**
	 * 2020-06-27
	 * @used-by __construct()
	 * @used-by capture()
	 * @param array $data
	 */
	private function send(&$data) {
		$domain = 1000 > $this->_projectId ? 'log.mage2.pro' : 'sentry.io'; /** @var string $domain */ # 2018-08-25
		$this->send_http("https://$domain/api/{$this->_projectId}/store/", $this->encode($data), [
			'Content-Type' => 'application/octet-stream'
			,'User-Agent' => $this->getUserAgent()
			,'X-Sentry-Auth' => 'Sentry ' . df_csv_pretty(df_map_k(df_clean([
				'sentry_timestamp' => sprintf('%F', microtime(true))
				,'sentry_client' => $this->getUserAgent()
				,'sentry_version' => self::PROTOCOL
				,'sentry_key' => $this->_keyPublic
				,'sentry_secret' => $this->_keyPrivate
			]), function($k, $v) {return "$k=$v";}))
		]);
	}

	/**
	 * 2020-06-27
	 * @used-by send()
	 * @param string $url
	 * @param array $data
	 * @param array $headers
	 */
	private function send_http($url, $data, $headers = []) {
		# 2022-10-16 https://www.php.net/manual/migration80.incompatible.php#migration80.incompatible.resource2object
		$c = curl_init($url); /** @var resource|\CurlHandle $c */
		try {
			curl_setopt($c, CURLOPT_HTTPHEADER, df_map_k(
				# 2020-06-28 The `Expect` headers prevents the `100-continue` response form server (Fixes GH-216)
				function($k, $v) {return df_kv([$k => $v]);}, $headers + ['Expect' => ''])
			);
			curl_setopt($c, CURLOPT_POST, 1);
			curl_setopt($c, CURLOPT_POSTFIELDS, $data);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt_array($c, $this->get_curl_options());
			curl_exec($c);
			$errno = curl_errno($c);
			# CURLE_SSL_CACERT || CURLE_SSL_CACERT_BADFILE
			if ($errno == 60 || $errno == 77) {
				curl_setopt($c, CURLOPT_CAINFO, df_module_file($this, 'cacert.pem'));
				curl_exec($c);
			}
		}
		finally {
			curl_close($c);
		}
	}

	/**
	 * 2020-06-27
	 * @used-by send_http()
	 * @return array(string => mixed)
	 */
	private function get_curl_options() {
		$r = [
			CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
			,CURLOPT_SSL_VERIFYHOST => 2
			,CURLOPT_SSL_VERIFYPEER => true
			,CURLOPT_USERAGENT => $this->getUserAgent()
			,CURLOPT_VERBOSE => false
		];
		/** @var int $t */
		if (!defined('CURLOPT_TIMEOUT_MS')) {
			# fall back to the lower-precision timeout.
			$t = max(1, ceil($this->timeout));
			$r += [CURLOPT_CONNECTTIMEOUT => $t, CURLOPT_TIMEOUT => $t];
		}
		else {
			# MS is available in curl >= 7.16.2
			$t = max(1, ceil(1000 * $this->timeout));
			# some versions of PHP 5.3 don't have this defined correctly
			if (!defined('CURLOPT_CONNECTTIMEOUT_MS')) {
				//see http://stackoverflow.com/questions/9062798/php-curl-timeout-is-not-working/9063006#9063006
				define('CURLOPT_CONNECTTIMEOUT_MS', 156);
			}
			$r += [CURLOPT_CONNECTTIMEOUT_MS => $t, CURLOPT_TIMEOUT_MS => $t];
		}
		return $r;
	}

	/**
	 * Generate an uuid4 value
	 *
	 * @return string
	 */
	private function uuid4()
	{
		$uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			# 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),

			# 16 bits for "time_mid"
			mt_rand(0, 0xffff),

			# 16 bits for "time_hi_and_version",
			# four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,

			# 16 bits, 8 bits for "clk_seq_hi_res",
			# 8 bits for "clk_seq_low",
			# two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,

			# 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);

		return str_replace('-', '', $uuid);
	}

	/**
	 * Return the URL for the current request
	 *
	 * @return string|null
	 */
	private function get_current_url()
	{
		# When running from commandline the REQUEST_URI is missing.
		if (!isset($_SERVER['REQUEST_URI'])) {
			return null;
		}

		# HTTP_HOST is a client-supplied header that is optional in HTTP 1.0
		$host = (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']
			: (!empty($_SERVER['LOCAL_ADDR'])  ? $_SERVER['LOCAL_ADDR']
			: (!empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '')));

		$httpS = $this->isHttps() ? 's' : '';
		return "http{$httpS}://{$host}{$_SERVER['REQUEST_URI']}";
	}

	/**
	 * Was the current request made over https?
	 *
	 * @return bool
	 */
	private function isHttps()
	{
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
			return true;
		}

		if (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
			return true;
		}

		if (!empty($this->trust_x_forwarded_proto) &&
			!empty($_SERVER['X-FORWARDED-PROTO']) &&
			$_SERVER['X-FORWARDED-PROTO'] === 'https') {
			return true;
		}

		return false;
	}

	/**
	 * @used-by captureException()
	 * @param string $severity  PHP E_$x error constant
	 * @return string           Sentry log level group
	 */
	private function translateSeverity($severity) {
		if (is_array($this->severity_map) && isset($this->severity_map[$severity])) {
			return $this->severity_map[$severity];
		}
		switch ($severity) {
			case E_COMPILE_ERROR:      return Client::ERROR;
			case E_COMPILE_WARNING:    return Client::WARN;
			case E_CORE_ERROR:         return Client::ERROR;
			case E_CORE_WARNING:       return Client::WARN;
			case E_ERROR:              return Client::ERROR;
			case E_NOTICE:             return Client::INFO;
			case E_PARSE:              return Client::ERROR;
			case E_RECOVERABLE_ERROR:  return Client::ERROR;
			case E_STRICT:             return Client::INFO;
			case E_USER_ERROR:         return Client::ERROR;
			case E_USER_NOTICE:        return Client::INFO;
			case E_USER_WARNING:       return Client::WARN;
			case E_WARNING:            return Client::WARN;
		}
		if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
			switch ($severity) {
			case E_DEPRECATED:         return Client::WARN;
			case E_USER_DEPRECATED:    return Client::WARN;
		  }
		}
		return Client::ERROR;
	}

	/**
	 * Provide a map of PHP Error constants to Sentry logging groups to use instead
	 * of the defaults in translateSeverity()
	 *
	 * @param array $map
	 */
	function registerSeverityMap($map)
	{
		$this->severity_map = $map;
	}

	/**
	 * 2016-12-23
	 * @used-by get_curl_options()
	 * @return string
	 */
	private function getUserAgent() {return 'mage2.pro/' . df_core_version();}

	/**
	 * 2016-12-23
	 * @used-by captureException()
	 * @param array(string => string|int|array) $frame
	 * @return bool
	 */
	private static function needSkipFrame(array $frame) {return
		\Magento\Framework\App\ErrorHandler::class === dfa($frame, 'class')
	;}

	/**
	 * 2020-06-27
	 * @used-by capture()
	 * @param $data
	 */
	private function sanitize(&$data) {
		if (!empty($data['request'])) {
			$data['request'] = $this->serializer->serialize($data['request']);
		}
		if (!empty($data['user'])) {
			$data['user'] = $this->serializer->serialize($data['user'], 3);
		}
		if (!empty($data['extra'])) {
			$data['extra'] = $this->serializer->serialize($data['extra']);
		}
		if (!empty($data['tags'])) {
			foreach ($data['tags'] as $key => $value) {
				$data['tags'][$key] = @(string)$value;
			}
		}
		if (!empty($data['contexts'])) {
			$data['contexts'] = $this->serializer->serialize($data['contexts'], 5);
		}
	}
	
	public $context;
	public $extra_data;
	public $severity_map;
	/**
	 * 2020-06-27
	 * @used-by capture()
	 * @used-by captureException()
	 * @used-by setAppPath()
	 * @var string|null
	 */
	private $app_path;
	private $error_types;
	/**
	 * 2020-06-28
	 * @used-by __construct()
	 * @var string
	 */
	private $_keyPrivate;
	/**
	 * 2020-06-28
	 * @used-by __construct()
	 * @used-by send()
	 * @var string
	 */
	private $_keyPublic;
	/**
	 * 2020-06-28
	 * @used-by __construct()
	 * @used-by capture()
	 * @used-by send()
	 * @var int
	 */
	private $_projectId;
	private $reprSerializer;
	private $serializer;
	const DEBUG = 'debug';
	const ERROR = 'error';
	const FATAL = 'fatal';
	const INFO = 'info';
	/**
	 * 2020-06-28
	 * @used-by capture()
	 * @used-by captureException()
	 * @used-by \Df\Sentry\Trace::get_default_context()
	 * @used-by \Df\Sentry\Trace::get_frame_context()
	 * @used-by \Df\Sentry\Trace::info()
	 */
	const MESSAGE_LIMIT = 1024;
	const PROTOCOL = '6';
	const WARN = 'warning';
	const WARNING = 'warning';
}