<?php
namespace Df\Sentry;
use Df\Core\Exception as DFE;
use Magento\Framework\App\ErrorHandler;
use \Exception as E;
final class Client {
	/**
	 * 2020-06-27
	 * @used-by df_sentry_m()
	 */
	function __construct(int $projectId, string $keyPublic, string $keyPrivate) {
		$this->_projectId = $projectId;
		$this->_keyPublic = $keyPublic;
		$this->_keyPrivate = $keyPrivate;
		$this->_user = null;
		$this->_context = new Context;
		$this->curl_path = 'curl';
		$this->logger = 'php';
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
	 */
	function captureMessage(string $m, array $d):void {$this->capture([
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
	function extra(array $a):void {$this->_context->extra = array_merge_recursive($this->_context->extra, $a);}

	/**
	 * 2017-01-10 «/» can not be used in a tag.
	 * 2017-02-09
	 * Hieroglyphs (e.g. «歐付寶 O'Pay (allPay)») can not be used too:
	 * it leads to the «Discarded invalid value for parameter 'tags'» error.
	 * @used-by df_sentry_tags()
	 * @uses df_translit_url()
	 * @param array(string => string) $a
	 */
	function tags(array $a):void {$this->_context->tags = dfak_transform($a, 'df_translit_url') + $this->_context->tags;}

	/**
	 * @used-by df_sentry_m()
	 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::webhook()
	 * @used-by \Df\Payment\W\Handler::log()
	 * @param array(string => mixed) $data
	 */
	function user(array $d, bool $merge = true):void {
		$this->_context->user = $d + (!$merge || !$this->_context->user ? [] : $this->_context->user);
	}

	/**
	 * 2020-06-28
	 * @used-by df_sentry()
	 * @param E|DFE $e
	 * @param array(string => mixed) $data
	 */
	function captureException(E $e, array $data):void {
		$eOriginal = $e; /** @var E $eOriginal */
		do {
			$isDFE = $e instanceof DFE; /** @var bool $isDFE */
			$dataI = [
				'type' => $isDFE ? $e->sentryType() : get_class($e)
				,'value' => $this->serializer->serialize($isDFE ? $e->messageD() : $e->getMessage())
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
		$this->capture($data, $trace);
	}

	/**
	 * 2022-11-11
	 * @used-by self::capture()
	 * @return array(string => array(string => string))
	 */
	private function get_http_data():array {
		$headers = [];
		foreach ($_SERVER as $key => $value) {
			if (0 === strpos($key, 'HTTP_')) {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
			} elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH']) && $value !== '') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))))] = $value;
			}
		}
		$result = [
			'method' => df_request_method(),
			'url' => df_current_url(),
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
	 * @used-by self::capture()
	 * @return array|array[]|null[]
	 */
	private function get_user_data():array {
		$user = $this->_context->user;
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
	 * @used-by self::captureException()
	 * @used-by self::captureMessage()
	 * @param mixed $data
	 */
	private function capture($data, array $trace = []):string {
		$data += [
			'culprit' => $this->transaction->peek()
			,'event_id' => $this->uuid4()
			,'extra' => []
			,'level' => self::ERROR
			# 2020-07-08
			# «Undefined index: message in vendor/mage2pro/core/Sentry/Client.php on line 186»:
			# https://github.com/mage2pro/core/issues/104
			# 2022-11-30
			# «Deprecated Functionality: substr():
			# Passing null to parameter #1 ($string) of type string is deprecated
			# in vendor/justuno.com/core/Sentry/Client.php on line 187»:
			# https://github.com/justuno-com/core/issues/378
			,'message' => substr(dfa($data, 'message', ''), 0, self::MESSAGE_LIMIT)
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
		 * 1) $this->tags — это теги, которые были заданы в конструкторе: @see self::__construct()
		 * Они имеют наинизший приоритет.
		 * 2) Намеренно использую здесь + вместо @see df_extend(),
		 * потому что массив tags должен быть одномерным (и поэтому для него + достаточно),
		 * а массив extra хоть и может быть многомерен, однако вряд ли для нас имеет смысл
		 * слияние его элементов на внутренних уровнях вложенности.
		 */
		$data['tags'] += $this->_context->tags + $this->tags;
		/** @var array(string => mixed) $extra */
		$extra = $data['extra'] + $this->_context->extra;
		# 2017-01-03
		# Этот полный JSON в конце массива может быть обрублен в интерфейсе Sentry
		# (и, соответственно, так же обрублен при просмотре события в формате JSON
		# по ссылке в шапке экрана события в Sentry),
		# однако всё равно удобно видеть данные в JSON, пусть даже в обрубленном виде.
		/** 2023-07-25 @used-by \Df\Sentry\Extra::adjust() */
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
	 * @used-by self::send()
	 * @param array(string => mixed) $data
	 */
	private function encode(array &$data):string {
		$r = df_json_encode($data);
		if (function_exists('gzcompress')) {
			$r = gzcompress($r);
		}
		return base64_encode($r);
	}

	/**
	 * 2020-06-27
	 * @used-by self::__construct()
	 * @used-by self::capture()
	 * @param array(string => mixed) $data
	 */
	private function send(array &$data):void {
		$domain = 1000 > $this->_projectId ? 'log.mage2.pro' : 'sentry.io'; /** @var string $domain */ # 2018-08-25
		$this->send_http("https://$domain/api/{$this->_projectId}/store/", $this->encode($data), [
			'Content-Type' => 'application/octet-stream'
			,'User-Agent' => $this->getUserAgent()
			,'X-Sentry-Auth' => 'Sentry ' . df_csv_pretty(df_map_k(df_clean([
				'sentry_timestamp' => sprintf('%F', microtime(true))
				,'sentry_client' => $this->getUserAgent()
				,'sentry_version' => 6
				,'sentry_key' => $this->_keyPublic
				,'sentry_secret' => $this->_keyPrivate
			]), function($k, $v) {return "$k=$v";}))
		]);
	}

	/**
	 * 2020-06-27
	 * @used-by self::send()
	 * @param array(string => mixed) $headers
	 */
	private function send_http(string $url, string $data, array $headers = []):void {
		# 2022-10-16 https://www.php.net/manual/migration80.incompatible.php#migration80.incompatible.resource2object
		$c = curl_init($url); /** @var resource|\CurlHandle $c */
		try {
			curl_setopt($c, CURLOPT_HTTPHEADER, df_map_k(
				# 2020-06-28 The `Expect` headers prevents the `100-continue` response form server (Fixes GH-216)
				function($k, $v) {return df_kv([$k => $v]);}, $headers + ['Expect' => ''])
			);
			curl_setopt($c, CURLOPT_POST, 1);
			/**
			 * 2023-01-28
			 * «Argument 2 passed to Df\Sentry\Client::send_http() must be of the type array, string given,
			 * called in vendor/mage2pro/core/Sentry/Client.php on line 257»: https://github.com/mage2pro/core/issues/194
			 * 2023-07-15
			 * 1) `CURLOPT_POSTFIELDS`:
			 *		«The full dmata to post in a HTTP "POST" operation.
			 *		This paraeter can either be passed as a urlencoded string like 'para1=val1&para2=val2&...'
			 *		or as an array with the field name as key and field data as value.
			 *		If value is an array, the `Content-Type` header will be set to `multipart/form-data`.
			 *		Files can be sent using CURLFile or CURLStringFile, in which case value must be an array.»
			 * https://www.php.net/manual/function.curl-setopt.php
			 * 2) In my case the value is always a string: @see self::encode()
			 */
			curl_setopt($c, CURLOPT_POSTFIELDS, $data);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt_array($c, $this->get_curl_options());
			curl_exec($c);
			# 2023-07-15
			if ($err = curl_errno($c)) {
				df_log_l($this, [
					'URL' => $url
					,'cURL error' => ['code' => $err, 'message' => curl_error($c)]
					,'Headers' => $headers
				], 'sentry');
			}
		}
		finally {
			curl_close($c);
		}
	}

	/**
	 * 2020-06-27
	 * @used-by self::send_http()
	 * @return array(string => mixed)
	 */
	private function get_curl_options():array {
		$r = [
			CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
			# 2023-07-15
			# 1) "The Sentry's TLS certificate (`cacert.pem`) has expired": https://github.com/mage2pro/core/issues/221
			# 2) We do not need to verify Sentry clients: we accept logs from all clients.
			,CURLOPT_SSL_VERIFYHOST => 0
			,CURLOPT_SSL_VERIFYPEER => false
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
	 * @used-by self::capture()
	 */
	private function uuid4():string {
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

	/** @used-by self::captureException() */
	private function translateSeverity(string $s):string {return in_array($s, [E_NOTICE, E_STRICT, E_USER_NOTICE])
		? self::INFO
		: (in_array($s, [E_COMPILE_WARNING, E_CORE_WARNING, E_DEPRECATED, E_USER_DEPRECATED, E_USER_WARNING, E_WARNING])
			? 'warning' : self::ERROR
		)
	;}

	/**
	 * 2016-12-23
	 * @used-by self::get_curl_options()
	 * @used-by self::send()
	 */
	private function getUserAgent():string {return 'mage2.pro/' . df_core_version();}

	/**
	 * 2016-12-23
	 * @used-by self::captureException()
	 * @param array(string => string|int|array) $frame
	 */
	private static function needSkipFrame(array $frame):bool {return ErrorHandler::class === dfa($frame, 'class');}

	/**
	 * 2020-06-27
	 * @used-by self::capture()
	 * @param array(string => mixed) $d
	 */
	private function sanitize(array &$d):void {
		foreach(['request', 'user', 'extra', ['contexts', 5]] as $k) {
			list($k, $depth) = is_array($k) ? $k : [$k, 3];
			if (!empty($d[$k])) {
				$d[$k] = $this->serializer->serialize($d[$k], $depth);
			}
		}
		if (!empty($d['tags'])) {
			foreach ($d['tags'] as $k => $v) {
				$d['tags'][$k] = @(string)$v;
			}
		}
	}

	/**
	 * 2022-12-09
	 * @used-by self::__construct()
	 * @used-by self::extra()
	 * @used-by self::tags()
	 * @used-by self::user()
	 * @used-by self::get_user_data()
	 * @used-by self::capture()
	 * @var Context
	 */
	private $_context;

	/**
	 * 2020-06-28
	 * @used-by self::__construct()
	 * @var string
	 */
	private $_keyPrivate;

	/**
	 * 2020-06-28
	 * @used-by self::__construct()
	 * @used-by self::send()
	 * @var string
	 */
	private $_keyPublic;

	/**
	 * 2020-06-28
	 * @used-by self::__construct()
	 * @used-by self::capture()
	 * @used-by self::send()
	 * @var int
	 */
	private $_projectId;

	private $serializer;
	/**
	 * 2022-12-09
	 * @used-by df_sentry()
	 */
	const DEBUG = 'debug';
	const ERROR = 'error';
	const INFO = 'info';
	/**
	 * 2020-06-28
	 * @used-by self::capture()
	 * @used-by self::captureException()
	 * @used-by \Df\Sentry\Trace::get_default_context()
	 * @used-by \Df\Sentry\Trace::get_frame_context()
	 * @used-by \Df\Sentry\Trace::info()
	 */
	const MESSAGE_LIMIT = 1024;
}