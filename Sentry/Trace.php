<?php
namespace Df\Sentry;
use ReflectionFunction as RF;
use ReflectionMethod as RM;
final class Trace {
	/**
	 * 2020-06-27
	 * @used-by \Df\Sentry\Client::capture()
	 * @used-by \Df\Sentry\Client::captureException()
	 * @param $frames
	 */
	static function info($frames):array {
		$serializer = new Serializer;
		$reprSerializer = new ReprSerializer;
		$r = [];
		$count = count($frames); /** @var int $count */
		for ($i = 0; $i < $count; $i++) { /** @var int $i */
			$frame = $frames[$i];  /** @var array(string => mixed) $frame */
			$next = $count === $i + 1 ? [] : $frames[$i + 1]; /** @var array(string => mixed) $next */
			/** @var string $file */
			if (array_key_exists('file', $frame)) {
				$context = self::code($frame['file'], $frame['line']);
				$file = $frame['file'];
			}
			else {
				$file = '';
				$context = [
					'filename' => '[Anonymous function]'
					,'line' => empty($frame['class'])
						? sprintf('%s(anonymous)', $frame['function'])
						: sprintf('%s%s%s', $frame['class'], $frame['type'], $frame['function'])
					,'lineno' => 0
					,'prefix' => ''
					,'suffix' => ''
				];
			}
			# 2020-07-08
			# «Argument 1 passed to Df\Sentry\Trace::get_frame_context()
			# must be of the type array, null given,
			# called in vendor/mage2pro/core/Sentry/Trace.php on line 38
			# and defined in vendor/mage2pro/core/Sentry/Trace.php:156»:
			# https://github.com/mage2pro/core/issues/103
			$vars = !$next ? [] : self::get_frame_context($next);
			$data = [
				'context_line' => $serializer->serialize($context['line'])
				,'filename' => df_path_relative($context['filename'])
				,'function' => dfa($next, 'function')
				,'in_app' => df_path_is_internal($file)
				,'lineno' => (int) $context['lineno']
				,'post_context' => $serializer->serialize($context['suffix'])
				,'pre_context' => $serializer->serialize($context['prefix'])
			];
			# dont set this as an empty array as PHP will treat it as a numeric array
			# instead of a mapping which goes against the defined Sentry spec
			if (!empty($vars)) {
				$cleanVars = [];
				foreach ($vars as $key => $value) {
					$value = $reprSerializer->serialize($value);
					if (is_string($value) || is_numeric($value)) {
						$cleanVars[(string)$key] = substr($value, 0, Client::MESSAGE_LIMIT);
					}
					else {
						$cleanVars[(string)$key] = $value;
					}
				}
				$data['vars'] = $cleanVars;
			}
			$r[] = $data;
		}
		return array_reverse($r);
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
	 * @used-by self::info()
	 * @param $file
	 * @param $line
	 * @return array(string => mixed)
	 */
	private static function code($file, $line):array {
		$context_lines = 5; /** @const int $context_lines */
		/** @var array(string => mixed) $r */
		$r = ['filename' => $file, 'line' => '', 'lineno' => $line, 'prefix' => [], 'suffix' => []];
		if (!is_null($file) && !is_null($line)) {
			# Code which is eval'ed have a modified filename.. Extract the
			# correct filename + linenumber from the string.
			$matches = [];
			if ($matched = preg_match("/^(.*?)\((\d+)\) : eval\(\)'d code$/", $file, $matches)) {
				$r = ['filename' => $file = $matches[1], 'lineno' => $line = $matches[2]] + $r;
			}
			# In the case of an anonymous function, the filename is sent as:
			# "</path/to/filename>(<lineno>) : runtime-created function"
			# Extract the correct filename + linenumber from the string.
			$matches = [];
			if ($matched = preg_match("/^(.*?)\((\d+)\) : runtime-created function$/", $file, $matches)) {
				$r = ['filename' => $file = $matches[1], 'lineno' => $line = $matches[2]] + $r;
			}
			try {
				$fileO = new \SplFileObject($file);  /** @var \SplFileObject $fileO */
				$target = max(0, ($line - ($context_lines + 1)));
				$fileO->seek($target);
				$cur_lineno = $target+1;
				while (!$fileO->eof()) {
					$lineS = rtrim($fileO->current(), "\r\n"); /** @var string $lineS */
					if ($cur_lineno == $line) {
						$r['line'] = $lineS;
					}
					elseif ($cur_lineno < $line) {
						$r['prefix'][] = $lineS;
					}
					elseif ($cur_lineno > $line) {
						$r['suffix'][] = $lineS;
					}
					$cur_lineno++;
					if ($cur_lineno > $line + $context_lines) {
						break;
					}
					$fileO->next();
				}
			}
			catch (\RuntimeException $e) {}
		}
		return $r;
	}

	/**
	 * 2020-06-28
	 * @used-by self::get_frame_context()
	 * @param array(string => mixed) $frame
	 * @return array(string => mixed)
	 */
	private static function get_default_context($frame):array {
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
	 * @used-by self::info()
	 * @param array(string => mixed) $frame
	 * @return array(string => mixed)
	 */
	private static function get_frame_context(array $frame):array {
		$r = []; /** @var array(string => mixed) $r */
		if (isset($frame['args'])) {
			$args = dfa($frame, 'args'); /** @var array $args */
			$c = dfa($frame, 'class'); /** @var string|null $c */
			$f = dfa($frame, 'function'); /** @var string|null $f */
			# The reflection API seems more appropriate if we associate it with the frame
			# where the function is actually called (since we're treating them as function context)
			if (!$f || df_contains($f, '__lambda_func') || 'Closure' === $c || df_ends_with($f, '{closure}')) {
				$r = self::get_default_context($frame);
			}
			elseif (in_array($f, ['include', 'include_once', 'require', 'require_once'])) {
				$r = empty($args) ? [] : ['param1' => $args[0]];
			}
			else {
				try {
					$ref = !$c
						? new RF($f)
						: new RM($c, method_exists($c, $f) ? $f : ('::' === $frame['type'] ? '__callStatic' : '__call'))
					; /** @var RF|RM $ref */
					$params = $ref->getParameters();
					foreach ($args as $i => $arg) {
						if (!isset($params[$i])) {
							$r["param$i"] = $arg;
						}
						else {
							# Assign the argument by the parameter name
							if (is_array($arg)) {
								foreach ($arg as $key => $value) {
									if (is_string($value) || is_numeric($value)) {
										$arg[$key] = substr($value, 0, Client::MESSAGE_LIMIT);
									}
								}
							}
							$r[$params[$i]->name] = $arg;
						}
					}
				}
				catch (\ReflectionException $e) {
					$r = self::get_default_context($frame);
				}
			}
		}
		return $r;
	}
}