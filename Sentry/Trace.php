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
		$serializer = new Serializer;
		$reprSerializer = new ReprSerializer;
		$result = [];
		$count = count($frames); /** @var int $count */
		for ($i = 0; $i < $count; $i++) { /** @var int $i */
			$frame = $frames[$i];  /** @var array(string => mixed) $frame */
			$next = $count === $i + 1 ? null : $frames[$i + 1]; /** @var null|array(string => mixed) $next */
			if (array_key_exists('file', $frame)) {
				$context = self::code($frame['file'], $frame['line']);
				$abs_path = $frame['file'];
			}
			else {
				$abs_path = '';
				$context = [
					'filename' => $filename = '[Anonymous function]'
					,'line' => empty($frame['class'])
						? sprintf('%s(anonymous)', $frame['function'])
						: sprintf('%s%s%s', $frame['class'], $frame['type'], $frame['function'])
					,'lineno' => 0
					,'prefix' => ''
					,'suffix' => ''
				];
			}
			$context['filename'] = df_trim_text_left($context['filename'], $base);
			$vars = self::get_frame_context($next);
			$data = [
				'context_line' => $serializer->serialize($context['line'])
				,'filename' => $context['filename']
				,'function' => isset($next['function']) ? $next['function'] : null
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

	/**
	 * 2020-06-29
	 * A result:
	 * 	{
	 *		"filename": "C:\\work\\clients\\justuno\\m2\\code\\vendor\\justuno.com\\m2\\Controller\\Response\\Catalog.php",
	 *		"line": "<the current line of the source code>",
	 *		"lineno": 29,
	 *		"prefix": [<5 previous lines of the source code>],
	 *		"suffix": [<5 next lines of the source code>]
	 *	}
	 * @used-by info()
	 * @param $filename
	 * @param $lineno
	 * @return array(string => mixed)
	 */
	private static function code($filename, $lineno) {
		$context_lines = 5; /** @const int $context_lines */
		/** @var array(string => mixed) $r */
		$r = ['filename' => $filename, 'line' => '', 'lineno' => $lineno, 'prefix' => [], 'suffix' => []];
		if (!is_null($filename) && !is_null($lineno)) {
			// Code which is eval'ed have a modified filename.. Extract the
			// correct filename + linenumber from the string.
			$matches = [];
			if ($matched = preg_match("/^(.*?)\((\d+)\) : eval\(\)'d code$/", $filename, $matches)) {
				$r = ['filename' => $filename = $matches[1], 'lineno' => $lineno = $matches[2]] + $r;
			}
			// In the case of an anonymous function, the filename is sent as:
			// "</path/to/filename>(<lineno>) : runtime-created function"
			// Extract the correct filename + linenumber from the string.
			$matches = [];
			if ($matched = preg_match("/^(.*?)\((\d+)\) : runtime-created function$/", $filename, $matches)) {
				$r = ['filename' => $filename = $matches[1], 'lineno' => $lineno = $matches[2]] + $r;
			}
			try {
				$file = new \SplFileObject($filename);
				$target = max(0, ($lineno - ($context_lines + 1)));
				$file->seek($target);
				$cur_lineno = $target+1;
				while (!$file->eof()) {
					$line = rtrim($file->current(), "\r\n");
					if ($cur_lineno == $lineno) {
						$r['line'] = $line;
					}
					elseif ($cur_lineno < $lineno) {
						$r['prefix'][] = $line;
					}
					elseif ($cur_lineno > $lineno) {
						$r['suffix'][] = $line;
					}
					$cur_lineno++;
					if ($cur_lineno > $lineno + $context_lines) {
						break;
					}
					$file->next();
				}
			}
			catch (\RuntimeException $e) {}
		}
		return $r;
	}
}
