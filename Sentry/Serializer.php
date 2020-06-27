<?php
namespace Df\Sentry;
/**
 * 2020-06-28
 * @see \Df\Sentry\ReprSerializer
 */
class Serializer {
	/**
	 * 2020-06-28
	 * @used-by \Df\Sentry\Stacktrace::get_stack_info()
	 */
	function serialize($value, $max_depth=3, $_depth=0) {
		$className = is_object($value) ? get_class($value) : null;
		$toArray = is_array($value) || $className === 'stdClass';
		if ($toArray && $_depth < $max_depth) {
			$new = [];
			foreach ($value as $k => $v) {
				$new[$this->serializeValue($k)] = $this->serialize($v, $max_depth, $_depth + 1);
			}

			return $new;
		}
		return $this->serializeValue($value);
	}

	/**
	 * 2020-06-28
	 * @used-by serializeValue()
	 * @used-by \Df\Sentry\ReprSerializer::serializeValue()
	 * @param string|mixed $v
	 * @return false|string|string[]|null
	 */
	final protected function serializeString($v) {
		$v = (string)$v;
		if (function_exists('mb_detect_encoding') && function_exists('mb_convert_encoding')) {
			/**
			 * 2020-06-28
			 * «"auto" is expanded according to `mbstring.language`»
			 * https://www.php.net/manual/function.mb-detect-encoding.php#example-3317
			 */
			if ($currentEncoding = mb_detect_encoding($v, 'auto')) {
				$v = mb_convert_encoding($v, 'UTF-8', $currentEncoding);
			} else {
				$v = mb_convert_encoding($v, 'UTF-8');
			}
		}
		if (strlen($v) > 1024) {
			$v = substr($v, 0, 1014) . ' {clipped}';
		}
		return $v;
	}

	protected function serializeValue($value)
	{
		if (is_null($value) || is_bool($value) || is_float($value) || is_integer($value)) {
			return $value;
		} elseif (is_object($value) || gettype($value) == 'object') {
			return 'Object '.get_class($value);
		} elseif (is_resource($value)) {
			return 'Resource '.get_resource_type($value);
		} elseif (is_array($value)) {
			return 'Array of length ' . count($value);
		} else {
			return $this->serializeString($value);
		}
	}
}
