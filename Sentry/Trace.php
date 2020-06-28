<?php
namespace Df\Sentry;
final class Trace {
	/**
	 * 2020-06-27
	 * @used-by \Df\Sentry\Client::capture()
	 * @used-by \Df\Sentry\Client::captureException()
	 * @param $frames
	 * @return array
	 */
	static function info($frames) {
		/**
		 * 2016-12-22
		 * «The method Client::_convertPath() works incorrectly on Windows»:
		 * https://github.com/getsentry/sentry-php/issues/392
		 * 2020-06-28
		 * @uses realpath() removes the trailing slash:  «Trailing delimiters, such as \ and /, are also removed»
		 * https://www.php.net/manual/function.realpath.php#refsect1-function.realpath-returnvalues
		 */
		$base = @realpath(BP) . DS;
		$serializer = new \Df\Sentry\Serializer;
		$reprSerializer = new \Df\Sentry\ReprSerializer;
		/**
		 * PHP stores calls in the stacktrace, rather than executing context. Sentry
		 * wants to know "when Im calling this code, where am I", and PHP says "I'm
		 * calling this function" not "I'm in this function". Due to that, we shift
		 * the context for a frame up one, meaning the variables (which are the calling
		 * args) come from the previous frame.
		 */
		$result = [];
		for ($i = 0; $i < count($frames); $i++) {
			$frame = isset($frames[$i]) ? $frames[$i] : null;
			$nextframe = isset($frames[$i + 1]) ? $frames[$i + 1] : null;

			if (!array_key_exists('file', $frame)) {
				if (!empty($frame['class'])) {
					$context['line'] = sprintf('%s%s%s',
						$frame['class'], $frame['type'], $frame['function']);
				} else {
					$context['line'] = sprintf('%s(anonymous)', $frame['function']);
				}
				$abs_path = '';
				$context['prefix'] = '';
				$context['suffix'] = '';
				$context['filename'] = $filename = '[Anonymous function]';
				$context['lineno'] = 0;
			} else {
				$context = self::read_source_file($frame['file'], $frame['line']);
				$abs_path = $frame['file'];
			}
			$context['filename'] = df_trim_text_left($context['filename'], $base);
			$vars = self::get_frame_context($nextframe);
			$data = [
				'context_line' => $serializer->serialize($context['line'])
				,'filename' => $context['filename']
				,'function' => isset($nextframe['function']) ? $nextframe['function'] : null
				,'in_app' => df_starts_with($abs_path, $base)
				,'lineno' => (int) $context['lineno']
				,'post_context' => $serializer->serialize($context['suffix'])
				,'pre_context' => $serializer->serialize($context['prefix'])
			];
			// dont set this as an empty array as PHP will treat it as a numeric array
			// instead of a mapping which goes against the defined Sentry spec
			if (!empty($vars)) {
				$cleanVars = [];
				foreach ($vars as $key => $value) {
					$value = $reprSerializer->serialize($value);
					if (is_string($value) || is_numeric($value)) {
						$cleanVars[(string)$key] = substr($value, 0, Client::MESSAGE_LIMIT);
					} else {
						$cleanVars[(string)$key] = $value;
					}
				}
				$data['vars'] = $cleanVars;
			}

			$result[] = $data;
		}

		return array_reverse($result);
	}

	/**
	 * 2020-06-28
	 * @used-by get_frame_context()
	 * @param array(string => mixed) $frame
	 * @return array(string => mixed)
	 */
	private static function get_default_context($frame) {
		$r = []; /** @var array(string => mixed) $r */
		$i = 1; /** @var int $i */
		foreach (dfa($frame, 'args', []) as $arg) {
			if (is_string($arg) || is_numeric($arg)) {
				$arg = substr($arg, 0, Client::MESSAGE_LIMIT);
			}
			$r["param$i"] = $arg;
			$i++;
		}
		return $r;
	}

	/**
	 * 2020-06-28
	 * @used-by info()
	 * @param $frame
	 * @return array
	 */
	private static function get_frame_context($frame) {
		$frame_arg_limit = Client::MESSAGE_LIMIT;
		if (!isset($frame['args'])) {
			return [];
		}
		// The reflection API seems more appropriate if we associate it with the frame
		// where the function is actually called (since we're treating them as function context)
		if (!isset($frame['function'])) {
			return self::get_default_context($frame);
		}
		if (strpos($frame['function'], '__lambda_func') !== false) {
			return self::get_default_context($frame);
		}
		if (isset($frame['class']) && $frame['class'] == 'Closure') {
			return self::get_default_context($frame);
		}
		if (strpos($frame['function'], '{closure}') !== false) {
			return self::get_default_context($frame);
		}
		if (in_array($frame['function'], ['include', 'include_once', 'require', 'require_once'])) {
			if (empty($frame['args'])) {
				// No arguments
				return [];
			} else {
				// Sanitize the file path
				return array('param1' => $frame['args'][0]);
			}
		}
		try {
			if (isset($frame['class'])) {
				if (method_exists($frame['class'], $frame['function'])) {
					$reflection = new \ReflectionMethod($frame['class'], $frame['function']);
				} elseif ($frame['type'] === '::') {
					$reflection = new \ReflectionMethod($frame['class'], '__callStatic');
				} else {
					$reflection = new \ReflectionMethod($frame['class'], '__call');
				}
			} else {
				$reflection = new \ReflectionFunction($frame['function']);
			}
		} catch (\ReflectionException $e) {
			return self::get_default_context($frame);
		}

		$params = $reflection->getParameters();

		$args = [];
		foreach ($frame['args'] as $i => $arg) {
			if (isset($params[$i])) {
				// Assign the argument by the parameter name
				if (is_array($arg)) {
					foreach ($arg as $key => $value) {
						if (is_string($value) || is_numeric($value)) {
							$arg[$key] = substr($value, 0, Client::MESSAGE_LIMIT);
						}
					}
				}
				$args[$params[$i]->name] = $arg;
			} else {
				$args['param'.$i] = $arg;
			}
		}

		return $args;
	}

	private static function read_source_file($filename, $lineno, $context_lines = 5)
	{
		$frame = array(
			'prefix' => [],
			'line' => '',
			'suffix' => [],
			'filename' => $filename,
			'lineno' => $lineno,
		);

		if ($filename === null || $lineno === null) {
			return $frame;
		}

		// Code which is eval'ed have a modified filename.. Extract the
		// correct filename + linenumber from the string.
		$matches = [];
		$matched = preg_match("/^(.*?)\((\d+)\) : eval\(\)'d code$/",
			$filename, $matches);
		if ($matched) {
			$frame['filename'] = $filename = $matches[1];
			$frame['lineno'] = $lineno = $matches[2];
		}

		// In the case of an anonymous function, the filename is sent as:
		// "</path/to/filename>(<lineno>) : runtime-created function"
		// Extract the correct filename + linenumber from the string.
		$matches = [];
		$matched = preg_match("/^(.*?)\((\d+)\) : runtime-created function$/",
			$filename, $matches);
		if ($matched) {
			$frame['filename'] = $filename = $matches[1];
			$frame['lineno'] = $lineno = $matches[2];
		}

		try {
			$file = new \SplFileObject($filename);
			$target = max(0, ($lineno - ($context_lines + 1)));
			$file->seek($target);
			$cur_lineno = $target+1;
			while (!$file->eof()) {
				$line = rtrim($file->current(), "\r\n");
				if ($cur_lineno == $lineno) {
					$frame['line'] = $line;
				} elseif ($cur_lineno < $lineno) {
					$frame['prefix'][] = $line;
				} elseif ($cur_lineno > $lineno) {
					$frame['suffix'][] = $line;
				}
				$cur_lineno++;
				if ($cur_lineno > $lineno + $context_lines) {
					break;
				}
				$file->next();
			}
		} catch (\RuntimeException $exc) {
			return $frame;
		}

		return $frame;
	}
}
