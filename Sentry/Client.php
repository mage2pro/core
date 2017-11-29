<?php
namespace Df\Sentry;
use Df\Core\Exception as DFE;
use \Exception as E;
class Client
{
    const PROTOCOL = '6';

    const DEBUG = 'debug';
    const INFO = 'info';
    const WARN = 'warning';
    const WARNING = 'warning';
    const ERROR = 'error';
    const FATAL = 'fatal';

    const MESSAGE_LIMIT = 1024;

    public $breadcrumbs;
    public $context;
    public $extra_data;
    public $severity_map;
    public $store_errors_for_bulk_send = false;

    protected $error_handler;
    protected $error_types;

    protected $serializer;
    protected $reprSerializer;

    function __construct($options_or_dsn=null, $options=[])
    {
        if (is_array($options_or_dsn)) {
            $options = array_merge($options_or_dsn, $options);
        }

        if (!is_array($options_or_dsn) && !empty($options_or_dsn)) {
            $dsn = $options_or_dsn;
        } elseif (!empty($_SERVER['SENTRY_DSN'])) {
            $dsn = @$_SERVER['SENTRY_DSN'];
        } elseif (!empty($options['dsn'])) {
            $dsn = $options['dsn'];
        } else {
            $dsn = null;
        }

        if (!empty($dsn)) {
            $options = array_merge($options, self::parseDSN($dsn));
        }

        $this->logger = Util::get($options, 'logger', 'php');
        $this->server = Util::get($options, 'server');
        $this->secret_key = Util::get($options, 'secret_key');
        $this->public_key = Util::get($options, 'public_key');
        $this->project = Util::get($options, 'project', 1);
        $this->auto_log_stacks = (bool) Util::get($options, 'auto_log_stacks', false);
        $this->name = Util::get($options, 'name', Compat::gethostname());
        $this->site = Util::get($options, 'site', $this->_server_variable('SERVER_NAME'));
        $this->tags = Util::get($options, 'tags', []);
        $this->release = Util::get($options, 'release', null);
        $this->environment = Util::get($options, 'environment', null);
        $this->trace = (bool) Util::get($options, 'trace', true);
        $this->timeout = Util::get($options, 'timeout', 2);
        $this->message_limit = Util::get($options, 'message_limit', self::MESSAGE_LIMIT);
        $this->exclude = Util::get($options, 'exclude', []);
        $this->severity_map = null;
        $this->http_proxy = Util::get($options, 'http_proxy');
        $this->extra_data = Util::get($options, 'extra', []);
        $this->send_callback = Util::get($options, 'send_callback', null);
        $this->curl_method = Util::get($options, 'curl_method', 'sync');
        $this->curl_path = Util::get($options, 'curl_path', 'curl');
        $this->curl_ipv4 = Util::get($options, 'curl_ipv4', true);
        $this->ca_cert = Util::get($options, 'ca_cert', $this->get_default_ca_cert());
        $this->verify_ssl = Util::get($options, 'verify_ssl', true);
        $this->curl_ssl_version = Util::get($options, 'curl_ssl_version');
        $this->trust_x_forwarded_proto = Util::get($options, 'trust_x_forwarded_proto');
        $this->transport = Util::get($options, 'transport', null);
        $this->mb_detect_order = Util::get($options, 'mb_detect_order', null);
        $this->error_types = Util::get($options, 'error_types', null);

        // app path is used to determine if code is part of your application
        $this->setAppPath(Util::get($options, 'app_path', null));
        $this->setExcludedAppPaths(Util::get($options, 'excluded_app_paths', null));
        // a list of prefixes used to coerce absolute paths into relative
        $this->setPrefixes(Util::get($options, 'prefixes', $this->getDefaultPrefixes()));
        $this->processors = $this->setProcessorsFromOptions($options);

        $this->_lasterror = null;
        $this->_last_event_id = null;
        $this->_user = null;
        $this->_pending_events = [];
        $this->context = new Context;
        $this->breadcrumbs = new Breadcrumbs;
        $this->sdk = Util::get($options, 'sdk', ['name' => 'mage2.pro', 'version' => df_core_version()]);
        $this->serializer = new Serializer($this->mb_detect_order);
        $this->reprSerializer = new ReprSerializer($this->mb_detect_order);

        if ($this->curl_method == 'async') {
            $this->_curl_handler = new CurlHandler($this->get_curl_options());
        }

        $this->transaction = new TransactionStack();
        if ($this->is_http_request() && isset($_SERVER['PATH_INFO'])) {
            $this->transaction->push($_SERVER['PATH_INFO']);
        }

        if (Util::get($options, 'install_default_breadcrumb_handlers', true)) {
            $this->registerDefaultBreadcrumbHandlers();
        }

        register_shutdown_function(array($this, 'onShutdown'));
    }

    /**
     * Installs any available automated hooks (such as error_reporting).
     */
    function install()
    {
        if ($this->error_handler) {
            throw new Exception(sprintf('%s->install() must only be called once', get_class($this)));
        }
        $this->error_handler = new ErrorHandler($this, false, $this->error_types);
        $this->error_handler->registerExceptionHandler();
        $this->error_handler->registerErrorHandler();
        $this->error_handler->registerShutdownFunction();
        return $this;
    }

    function getRelease()
    {
        return $this->release;
    }

    function setRelease($value)
    {
        $this->release = $value;
        return $this;
    }

    function getEnvironment()
    {
        return $this->environment;
    }

    function setEnvironment($value)
    {
        $this->environment = $value;
        return $this;
    }

	/**
	 * 2016-12-22
	 * http://php.net/manual/en/ini.core.php#ini.include-path
	 * https://github.com/getsentry/sentry-php/issues/393
	 * «The method Client::getDefaultPrefixes() works incorrectly on Windows.»
	 *
	 * Впрочем, этот метод мы теперь всё равно не используем,
	 * потому что он включает в префиксы весь @see get_include_path()
	 * в том числе и папки внутри Magento (например: lib\internal),
	 * и тогда, например, файл типа
	 * C:\work\mage2.pro\store\lib\internal\Magento\Framework\App\ErrorHandler.php
	 * будет обрезан как Magento\Framework\App\ErrorHandler.php
	 *
	 * @return string[]
	 */
    private function getDefaultPrefixes() {return
		explode(df_is_windows() ? ';' : ':', get_include_path())
	;}

    private function _convertPath($value)
    {
        $path = @realpath($value);
        if ($path === false) {
            $path = $value;
        }
        // we need app_path to have a trailing slash otherwise
        // base path detection becomes complex if the same
        // prefix is matched
		/**
		 * 2016-12-22
		 * https://github.com/getsentry/sentry-php/issues/392
		 * «The method Client::_convertPath() works incorrectly on Windows»
		 */
        if (
        	(substr($path, 0, 1) === '/' || (1 < strlen($path) && ':' === $path[1]))
			&& DIRECTORY_SEPARATOR !== substr($path, -1, 1)) {
            $path .= DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    function getAppPath()
    {
        return $this->app_path;
    }

    function setAppPath($value)
    {
        if ($value) {
            $this->app_path = $this->_convertPath($value);
        } else {
            $this->app_path = null;
        }
        return $this;
    }

    function getExcludedAppPaths()
    {
        return $this->excluded_app_paths;
    }

    function setExcludedAppPaths($value)
    {
        if ($value) {
            $this->excluded_app_paths = $value ? array_map(array($this, '_convertPath'), $value) : $value;
        } else {
            $this->excluded_app_paths = null;
        }
        return $this;
    }
    function getPrefixes()
    {
        return $this->prefixes;
    }

    function setPrefixes($value)
    {
        $this->prefixes = $value ? array_map(array($this, '_convertPath'), $value) : $value;
        return $this;
    }

    function getSendCallback()
    {
        return $this->send_callback;
    }

    function setSendCallback($value)
    {
        $this->send_callback = $value;
        return $this;
    }

    function getTransport()
    {
        return $this->transport;
    }

    function getServerEndpoint($value)
    {
        return $this->server;
    }

	/**
	 * 2016-12-23
	 * @return string
	 */
    function getUserAgent() {return 'mage2.pro/' . df_core_version();}

    /**
     * Set a custom transport to override how Sentry events are sent upstream.
     *
     * The bound function will be called with ``$client`` and ``$data`` arguments
     * and is responsible for encoding the data, authenticating, and sending
     * the data to the upstream Sentry server.
     *
     * @param function     $value       Function to be called
     */
    function setTransport($value)
    {
        $this->transport = $value;
        return $this;
    }

    static function getDefaultProcessors() {return [SanitizeDataProcessor::class];}

    /**
     * Sets the Processor sub-classes to be used when data is processed before being
     * sent to Sentry.
     *
     * @param $options
     * @return array
     */
    function setProcessorsFromOptions($options)
    {
        $processors = [];
        foreach (Util::get($options, 'processors', self::getDefaultProcessors()) as $processor) {
            $new_processor = new $processor($this);

            if (isset($options['processorOptions']) && is_array($options['processorOptions'])) {
                if (isset($options['processorOptions'][$processor]) && method_exists($processor, 'setProcessorOptions')) {
                    $new_processor->setProcessorOptions($options['processorOptions'][$processor]);
                }
            }
            $processors[] = $new_processor;
        }
        return $processors;
    }

    /**
     * Parses a Raven-compatible DSN and returns an array of its values.
     *
     * @param string    $dsn    Raven compatible DSN: http://raven.readthedocs.org/en/latest/config/#the-sentry-dsn
     * @return array            parsed DSN
     */
    static function parseDSN($dsn)
    {
        $url = parse_url($dsn);
        $scheme = (isset($url['scheme']) ? $url['scheme'] : '');
        if (!in_array($scheme, array('http', 'https'))) {
            throw new \InvalidArgumentException('Unsupported Sentry DSN scheme: ' . (!empty($scheme) ? $scheme : '<not set>'));
        }
        $netloc = (isset($url['host']) ? $url['host'] : null);
        $netloc.= (isset($url['port']) ? ':'.$url['port'] : null);
        $rawpath = (isset($url['path']) ? $url['path'] : null);
        if ($rawpath) {
            $pos = strrpos($rawpath, '/', 1);
            if ($pos !== false) {
                $path = substr($rawpath, 0, $pos);
                $project = substr($rawpath, $pos + 1);
            } else {
                $path = '';
                $project = substr($rawpath, 1);
            }
        } else {
            $project = null;
            $path = '';
        }
        $username = (isset($url['user']) ? $url['user'] : null);
        $password = (isset($url['pass']) ? $url['pass'] : null);
        if (empty($netloc) || empty($project) || empty($username) || empty($password)) {
            throw new \InvalidArgumentException('Invalid Sentry DSN: ' . $dsn);
        }

        return array(
            'server'     => sprintf('%s://%s%s/api/%s/store/', $scheme, $netloc, $path, $project),
            'project'    => $project,
            'public_key' => $username,
            'secret_key' => $password,
        );
    }

    function getLastError()
    {
        return $this->_lasterror;
    }

    /**
     * Given an identifier, returns a Sentry searchable string.
     */
    function getIdent($ident)
    {
        // XXX: We don't calculate checksums yet, so we only have the ident.
        return $ident;
    }

    /**
     * Deprecated
     */
    function message($message, $params=[], $level=self::INFO,
                            $stack=false, $vars = null)
    {
        return $this->captureMessage($message, $params, $level, $stack, $vars);
    }

    /**
     * Deprecated
     */
    function exception($exception)
    {
        return $this->captureException($exception);
    }

    /**
     * Log a message to sentry
     *
     * @param string $message The message (primary description) for the event.
     * @param array $params params to use when formatting the message.
     * @param array $data Additional attributes to pass with this event (see Sentry docs).
     */
    function captureMessage($message, $params=[], $data=[], $stack = false, $vars = null) {
        // Gracefully handle messages which contain formatting characters, but were not
        // intended to be used with formatting.
        if (!empty($params)) {
            $formatted_message = vsprintf($message, $params);
        } else {
            $formatted_message = $message;
        }
		// support legacy method of passing in a level name as the third arg
        $data = is_null($data) ? [] : (!is_array($data) ? ['level' => $data] : $data);
        $data['message'] = $formatted_message;
        $data['sentry.interfaces.Message'] = array(
            'message' => $message,
            'params' => $params,
            'formatted' => $formatted_message,
        );
        return $this->capture($data, $stack, $vars);
    }

    /**
     * Log an exception to sentry
     *
     * @param E|DFE $e
     * @param array $data
     */
    function captureException(E $e, $data=null, $logger=null, $vars=null) {
        if (in_array(get_class($e), $this->exclude)) {
            return null;
        }
        if ($data === null) {
            $data = [];
        }
        $eOriginal = $e; /** @var E $eOriginal */
        do {
			$isDFE = $e instanceof DFE;
            $exc_data = [
            	'type' => $isDFE ? $e->sentryType() : get_class($e)
				,'value' => $this->serializer->serialize($isDFE ? $e->messageSentry() : $e->getMessage())
			];
            /**'exception'
             * Exception::getTrace doesn't store the point at where the exception
             * was thrown, so we have to stuff it in ourselves. Ugh.
             */
            $trace = $e->getTrace();
            /**
			 * 2016-12-22
			 * Убираем @see \Magento\Framework\App\ErrorHandler
			 * 2016-12-23
			 * И @see Breadcrumbs\ErrorHandler тоже убираем.
			 */
            /** @var bool $needAddFaceFrame */
            $needAddFakeFrame = !self::needSkipFrame($trace[0]);
			while (self::needSkipFrame($trace[0])) {
				array_shift($trace);
			}
            if ($needAddFakeFrame) {
				$frame_where_exception_thrown = array(
					'file' => $e->getFile(),
					'line' => $e->getLine(),
				);
				array_unshift($trace, $frame_where_exception_thrown);
			}
            $exc_data['stacktrace'] = array(
                'frames' => Stacktrace::get_stack_info(
                    $trace, $this->trace, $vars, $this->message_limit, $this->prefixes,
                    $this->app_path, $this->excluded_app_paths, $this->serializer, $this->reprSerializer
                ),
            );
            $exceptions[] = $exc_data;
        } while ($e = $e->getPrevious());
        $data['exception'] = array('values' => array_reverse($exceptions),);
        if ($logger !== null) {
            $data['logger'] = $logger;
        }
        if (empty($data['level'])) {
            if (method_exists($eOriginal, 'getSeverity')) {
                $data['level'] = $this->translateSeverity($eOriginal->getSeverity());
            }
            else {
                $data['level'] = self::ERROR;
            }
        }
        return $this->capture($data, $trace, $vars);
    }


    /**
     * Capture the most recent error (obtained with ``error_get_last``).
     */
    function captureLastError()
    {
        if (null === $error = error_get_last()) {
            return;
        }

        $e = new \ErrorException(
            @$error['message'], 0, @$error['type'],
            @$error['file'], @$error['line']
        );

        return $this->captureException($e);
    }

    /**
     * Log an query to sentry
     */
    function captureQuery($query, $level=self::INFO, $engine = '')
    {
        $data = array(
            'message' => $query,
            'level' => $level,
            'sentry.interfaces.Query' => array(
                'query' => $query
            )
        );

        if ($engine !== '') {
            $data['sentry.interfaces.Query']['engine'] = $engine;
        }
        return $this->capture($data, false);
    }

    /**
     * Return the last captured event's ID or null if none available.
     */
    function getLastEventID()
    {
        return $this->_last_event_id;
    }

    protected function registerDefaultBreadcrumbHandlers()
    {
        $handler = new Breadcrumbs\ErrorHandler($this);
        $handler->install();
    }

    protected function is_http_request()
    {
        return isset($_SERVER['REQUEST_METHOD']) && PHP_SAPI !== 'cli';
    }

    protected function get_http_data()
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
            } elseif (in_array($key, array('CONTENT_TYPE', 'CONTENT_LENGTH')) && $value !== '') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))))] = $value;
            }
        }

        $result = array(
            'method' => $this->_server_variable('REQUEST_METHOD'),
            'url' => $this->get_current_url(),
            'query_string' => $this->_server_variable('QUERY_STRING'),
        );

        // dont set this as an empty array as PHP will treat it as a numeric array
        // instead of a mapping which goes against the defined Sentry spec
        if (!empty($post = df_request_o()->getPost()->toArray())) {
            $result['data'] = $post;
        }
        // 2017-01-03 Мне пока куки не нужны.
		if (false) {
			if (!empty($_COOKIE)) {
				$result['cookies'] = $_COOKIE;
			}
		}
		// 2017-01-03
		// Отсюда куки тоже нужно удалить, потому что Sentry пытается их отсюда взять.
		unset($headers['Cookie']);
        if (!empty($headers)) {
            $result['headers'] = $headers;
        }

        return array(
            'request' => $result,
        );
    }

    protected function get_user_data()
    {
        $user = $this->context->user;
        if ($user === null) {
            if (!function_exists('session_id') || !session_id()) {
                return [];
            }
            $user = array(
                'id' => session_id(),
            );
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
        }
        return array(
            'user' => $user,
        );
    }

    protected function get_extra_data()
    {
        return $this->extra_data;
    }

    function get_default_data()
    {
        return array(
            //'server_name' => $this->name,
            'project' => $this->project,
            'site' => $this->site,
            //'logger' => $this->logger,
            'tags' => $this->tags,
            'platform' => 'php',
            'sdk' => $this->sdk,
            'culprit' => $this->transaction->peek(),
        );
    }

	/**
	 * 2017-04-08
	 * @used-by captureException()
	 * @used-by captureMessage()
	 * @param mixed $data
	 * @param mixed $stack
	 * @param mixed $vars
	 * @return mixed
	 */
    function capture($data, $stack = null, $vars = null)
    {
        if (!isset($data['timestamp'])) {
            $data['timestamp'] = gmdate('Y-m-d\TH:i:s\Z');
        }
        if (!isset($data['level'])) {
            $data['level'] = self::ERROR;
        }
        if (!isset($data['tags'])) {
            $data['tags'] = [];
        }
        if (!isset($data['extra'])) {
            $data['extra'] = [];
        }
        if (!isset($data['event_id'])) {
            $data['event_id'] = $this->uuid4();
        }

        if (isset($data['message'])) {
            $data['message'] = substr($data['message'], 0, $this->message_limit);
        }

        $data = array_merge($this->get_default_data(), $data);

        if ($this->is_http_request()) {
            $data = array_merge($this->get_http_data(), $data);
        }

        $data = array_merge($this->get_user_data(), $data);

        if ($this->release) {
            $data['release'] = $this->release;
        }
        if ($this->environment) {
            $data['environment'] = $this->environment;
        }
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
        /** @var array(string => mixed) */
        $extra = $data['extra'] + $this->context->extra + $this->get_extra_data();
		// 2017-01-03
		// Этот полный JSON в конце массива может быть обрублен в интерфейсе Sentry
		// (и, соответственно, так же обрублен при просмотре события в формате JSON
		// по ссылке в шапке экрана события в Sentry),
		// однако всё равно удобно видеть данные в JSON, пусть даже в обрубленном виде.
        $data['extra'] = Extra::adjust($extra) + ['_json' => df_json_encode($extra)];
		$data = df_clean($data);

        if (!$this->breadcrumbs->is_empty()) {
            $data['breadcrumbs'] = $this->breadcrumbs->fetch();
        }

        if ((!$stack && $this->auto_log_stacks) || $stack === true) {
            $stack = debug_backtrace();

            // Drop last stack
            array_shift($stack);
        }

        if (!empty($stack)) {
            if (!isset($data['stacktrace']) && !isset($data['exception'])) {
                $data['stacktrace'] = array(
                    'frames' => Stacktrace::get_stack_info(
                        $stack, $this->trace, $vars, $this->message_limit, $this->prefixes,
                        $this->app_path, $this->excluded_app_paths, $this->serializer, $this->reprSerializer
                    ),
                );
            }
        }

        $this->sanitize($data);
        $this->process($data);

        if (!$this->store_errors_for_bulk_send) {
            $this->send($data);
        } else {
            $this->_pending_events[] = $data;
        }

        $this->_last_event_id = $data['event_id'];

        return $data['event_id'];
    }

    function sanitize(&$data)
    {
        // attempt to sanitize any user provided data
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

    /**
     * Process data through all defined Processor sub-classes
     *
     * @param array     $data       Associative array of data to log
     */
    function process(&$data)
    {
        foreach ($this->processors as $processor) {
            $processor->process($data);
        }
    }

    function sendUnsentErrors()
    {
        foreach ($this->_pending_events as $data) {
            $this->send($data);
        }
        $this->_pending_events = [];
        if ($this->store_errors_for_bulk_send) {
            //in case an error occurs after this is called, on shutdown, send any new errors.
            $this->store_errors_for_bulk_send = !defined('RAVEN_CLIENT_END_REACHED');
        }
    }

    function encode(&$data)
    {
        $message = Compat::json_encode($data);
        if ($message === false) {
            if (function_exists('json_last_error_msg')) {
                $this->_lasterror = json_last_error_msg();
            } else {
                $this->_lasterror = json_last_error();
            }
            return false;
        }

        if (function_exists("gzcompress")) {
            $message = gzcompress($message);
        }

        // PHP's builtin curl_* function are happy without this, but the exec method requires it
        $message = base64_encode($message);

        return $message;
    }

    /**
     * Wrapper to handle encoding and sending data to the Sentry API server.
     *
     * @param array     $data       Associative array of data to log
     */
    function send(&$data)
    {
        if (
        	is_callable($this->send_callback)
			&& false === call_user_func_array($this->send_callback, array(&$data))
		) {
            // if send_callback returns false, end native send
            return;
        }

        if (!$this->server) {
            return;
        }

        if ($this->transport) {
            return call_user_func($this->transport, $this, $data);
        }

        $message = $this->encode($data);

        $headers = array(
            'User-Agent' => $this->getUserAgent(),
            'X-Sentry-Auth' => $this->getAuthHeader(),
            'Content-Type' => 'application/octet-stream'
        );

        $this->send_remote($this->server, $message, $headers);
    }

    /**
     * Send data to Sentry
     *
     * @param string    $url        Full URL to Sentry
     * @param array     $data       Associative array of data to log
     * @param array     $headers    Associative array of headers
     */
    private function send_remote($url, $data, $headers=[])
    {
        $parts = parse_url($url);
        $parts['netloc'] = $parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : null);
        $this->send_http($url, $data, $headers);
    }

    protected function get_default_ca_cert()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cacert.pem';
    }

    protected function get_curl_options()
    {
        $options = array(
            CURLOPT_VERBOSE => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => $this->verify_ssl,
            CURLOPT_CAINFO => $this->ca_cert,
            CURLOPT_USERAGENT => $this->getUserAgent(),
        );
        if ($this->http_proxy) {
            $options[CURLOPT_PROXY] = $this->http_proxy;
        }
        if ($this->curl_ssl_version) {
            $options[CURLOPT_SSLVERSION] = $this->curl_ssl_version;
        }
        if ($this->curl_ipv4) {
            $options[CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
        }
        if (defined('CURLOPT_TIMEOUT_MS')) {
            // MS is available in curl >= 7.16.2
            $timeout = max(1, ceil(1000 * $this->timeout));

            // some versions of PHP 5.3 don't have this defined correctly
            if (!defined('CURLOPT_CONNECTTIMEOUT_MS')) {
                //see http://stackoverflow.com/questions/9062798/php-curl-timeout-is-not-working/9063006#9063006
                define('CURLOPT_CONNECTTIMEOUT_MS', 156);
            }

            $options[CURLOPT_CONNECTTIMEOUT_MS] = $timeout;
            $options[CURLOPT_TIMEOUT_MS] = $timeout;
        } else {
            // fall back to the lower-precision timeout.
            $timeout = max(1, ceil($this->timeout));
            $options[CURLOPT_CONNECTTIMEOUT] = $timeout;
            $options[CURLOPT_TIMEOUT] = $timeout;
        }
        return $options;
    }

    /**
     * Send the message over http to the sentry url given
     *
     * @param string $url       URL of the Sentry instance to log to
     * @param array $data       Associative array of data to log
     * @param array $headers    Associative array of headers
     */
    private function send_http($url, $data, $headers=[])
    {
        if ($this->curl_method == 'async') {
            $this->_curl_handler->enqueue($url, $data, $headers);
        } elseif ($this->curl_method == 'exec') {
            $this->send_http_asynchronous_curl_exec($url, $data, $headers);
        } else {
            $this->send_http_synchronous($url, $data, $headers);
        }
    }

    protected function buildCurlCommand($url, $data, $headers)
    {
        // TODO(dcramer): support ca_cert
        $cmd = $this->curl_path.' -X POST ';
        foreach ($headers as $key => $value) {
            $cmd .= '-H ' . escapeshellarg($key.': '.$value). ' ';
        }
        $cmd .= '-d ' . escapeshellarg($data) . ' ';
        $cmd .= escapeshellarg($url) . ' ';
        $cmd .= '-m 5 ';  // 5 second timeout for the whole process (connect + send)
        if (!$this->verify_ssl) {
            $cmd .= '-k ';
        }
        $cmd .= '> /dev/null 2>&1 &'; // ensure exec returns immediately while curl runs in the background

        return $cmd;
    }

    /**
     * Send the cURL to Sentry asynchronously. No errors will be returned from cURL
     *
     * @param string    $url        URL of the Sentry instance to log to
     * @param array     $data       Associative array of data to log
     * @param array     $headers    Associative array of headers
     * @return bool
     */
    private function send_http_asynchronous_curl_exec($url, $data, $headers)
    {
        exec($this->buildCurlCommand($url, $data, $headers));
        return true; // The exec method is just fire and forget, so just assume it always works
    }

    /**
     * Send a blocking cURL to Sentry and check for errors from cURL
     *
     * @param string    $url        URL of the Sentry instance to log to
     * @param array     $data       Associative array of data to log
     * @param array     $headers    Associative array of headers
     * @return bool
     */
    private function send_http_synchronous($url, $data, $headers)
    {
        $new_headers = [];
        foreach ($headers as $key => $value) {
            array_push($new_headers, $key .': '. $value);
        }
        // XXX(dcramer): Prevent 100-continue response form server (Fixes GH-216)
        $new_headers[] = 'Expect:';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $new_headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $options = $this->get_curl_options();
        $ca_cert = $options[CURLOPT_CAINFO];
        unset($options[CURLOPT_CAINFO]);
        curl_setopt_array($curl, $options);

        curl_exec($curl);

        $errno = curl_errno($curl);
        // CURLE_SSL_CACERT || CURLE_SSL_CACERT_BADFILE
        if ($errno == 60 || $errno == 77) {
            curl_setopt($curl, CURLOPT_CAINFO, $ca_cert);
            curl_exec($curl);
        }

        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $success = ($code == 200);
        if (!$success) {
            // It'd be nice just to raise an exception here, but it's not very PHP-like
            $this->_lasterror = curl_error($curl);
        } else {
            $this->_lasterror = null;
        }
        curl_close($curl);

        return $success;
    }

    /**
     * Generate a Sentry authorization header string
     *
     * @param string    $timestamp      Timestamp when the event occurred
     * @param string    $client         HTTP client name (not Client object)
     * @param string    $api_key        Sentry API key
     * @param string    $secret_key     Sentry API key
     * @return string
     */
    protected function get_auth_header($timestamp, $client, $api_key, $secret_key)
    {
        $header = array(
            sprintf('sentry_timestamp=%F', $timestamp),
            "sentry_client={$client}",
            sprintf('sentry_version=%s', self::PROTOCOL),
        );

        if ($api_key) {
            $header[] = "sentry_key={$api_key}";
        }

        if ($secret_key) {
            $header[] = "sentry_secret={$secret_key}";
        }


        return sprintf('Sentry %s', implode(', ', $header));
    }

    function getAuthHeader()
    {
        $timestamp = microtime(true);
        return $this->get_auth_header($timestamp, $this->getUserAgent(), $this->public_key, $this->secret_key);
    }

    /**
     * Generate an uuid4 value
     *
     * @return string
     */
    private function uuid4()
    {
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        return str_replace('-', '', $uuid);
    }

    /**
     * Return the URL for the current request
     *
     * @return string|null
     */
    protected function get_current_url()
    {
        // When running from commandline the REQUEST_URI is missing.
        if (!isset($_SERVER['REQUEST_URI'])) {
            return null;
        }

        // HTTP_HOST is a client-supplied header that is optional in HTTP 1.0
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
    protected function isHttps()
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
     * Get the value of a key from $_SERVER
     *
     * @param string $key       Key whose value you wish to obtain
     * @return string           Key's value
     */
    private function _server_variable($key)
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return '';
    }

    /**
     * Translate a PHP Error constant into a Sentry log level group
     *
     * @param string $severity  PHP E_$x error constant
     * @return string           Sentry log level group
     */
    function translateSeverity($severity)
    {
        if (is_array($this->severity_map) && isset($this->severity_map[$severity])) {
            return $this->severity_map[$severity];
        }
        switch ($severity) {
            case E_ERROR:              return Client::ERROR;
            case E_WARNING:            return Client::WARN;
            case E_PARSE:              return Client::ERROR;
            case E_NOTICE:             return Client::INFO;
            case E_CORE_ERROR:         return Client::ERROR;
            case E_CORE_WARNING:       return Client::WARN;
            case E_COMPILE_ERROR:      return Client::ERROR;
            case E_COMPILE_WARNING:    return Client::WARN;
            case E_USER_ERROR:         return Client::ERROR;
            case E_USER_WARNING:       return Client::WARN;
            case E_USER_NOTICE:        return Client::INFO;
            case E_STRICT:             return Client::INFO;
            case E_RECOVERABLE_ERROR:  return Client::ERROR;
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
     * Convenience function for setting a user's ID and Email
     *
     * @deprecated
     * @param string $id            User's ID
     * @param string|null $email    User's email
     * @param array $data           Additional user data
     */
    function set_user_data($id, $email=null, $data=[])
    {
        $user = array('id' => $id);
        if (isset($email)) {
            $user['email'] = $email;
        }
        $this->user_context(array_merge($user, $data));
    }

    function onShutdown()
    {
        if (!defined('RAVEN_CLIENT_END_REACHED')) {
            define('RAVEN_CLIENT_END_REACHED', true);
        }
        $this->sendUnsentErrors();
        if ($this->curl_method == 'async') {
            $this->_curl_handler->join();
        }
    }

    /**
     * Sets user context.
     *
     * @param array $data   Associative array of user data
     * @param bool $merge   Merge existing context with new context
     */
    function user_context($data, $merge=true)
    {
        if ($merge && $this->context->user !== null) {
            // bail if data is null
            if (!$data) {
                return;
            }
            $this->context->user = array_merge($this->context->user, $data);
        } else {
            $this->context->user = $data;
        }
    }

    /**
     * 2017-01-10
	 * К сожалению, использовать «/» в имени тега нельзя.
	 * 2017-02-09
	 * Иероглифы использовать тоже нельзя:
	 * попытка использовать тег «歐付寶 O'Pay (allPay)» приводит к сбою
	 * «Discarded invalid value for parameter 'tags'».
	 * @used-by df_sentry_tags()
	 * @uses df_translit_url()
     * @param array(string => string) $a
     */
    final function tags_context(array $a) {
    	$this->context->tags = dfa_key_transform($a, 'df_translit_url') + $this->context->tags;
    }

    /**
	 * 2017-01-10
	 * @used-by df_sentry_extra()
     * @param array(string => mixed) $a
     */
    final function extra_context(array $a) {$this->context->extra = $a + $this->context->extra;}

    /**
     * @param array $processors
     */
    function setProcessors(array $processors)
    {
        $this->processors = $processors;
    }

	/**
	 * 2016-12-23
	 * @param array(string => string|int|array) $frame
	 * @return bool
	 */
    private static function needSkipFrame(array $frame) {return
		\Magento\Framework\App\ErrorHandler::class === dfa($frame, 'class')
		|| df_ends_with(df_path_n(dfa($frame, 'file')), 'Sentry/Breadcrumbs/ErrorHandler.php')
	;}
}
