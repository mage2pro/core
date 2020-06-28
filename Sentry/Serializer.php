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
	 * @param mixed $v
	 * @param int $max_depth
	 * @param int $_depth
	 * @return array|bool|false|float|int|string|string[]|null
	 */
	function serialize($v, $max_depth=3, $_depth=0) {
		$className = is_object($v) ? get_class($v) : null;
		$toArray = is_array($v) || $className === 'stdClass';
		if ($toArray && $_depth < $max_depth) {
			$new = [];
			foreach ($v as $k => $iv) {
				$new[$this->serializeValue($k)] = $this->serialize($iv, $max_depth, $_depth + 1);
			}
			return $new;
		}
		return $this->serializeValue($v);
	}

	/**
	 * 2020-06-28
	 * @used-by serializeValue()
	 * @used-by \Df\Sentry\ReprSerializer::serializeValue()
	 * @param string|mixed $r
	 * @return false|string|string[]|null
	 */
	final protected function serializeString($r) {return
		// 2020-06-28
		// «"auto" is expanded according to `mbstring.language`»
		// https://www.php.net/manual/function.mb-detect-encoding.php#example-3317
		1024 > strlen($r = mb_convert_encoding($r, 'UTF-8', mb_detect_encoding($r, 'auto') ?: mb_internal_encoding()))
			? $r
			: substr($r, 0, 1014) . ' {clipped}'
	;}

	/**
	 * 2020-06-28
	 * @used-by serialize()
	 * @see \Df\Sentry\ReprSerializer::serializeValue()
	 * @param mixed $v
	 * @return bool|false|float|int|string|string[]|null
	 */
	protected function serializeValue($v) { /** @var string $r */
		if (is_null($v) || is_bool($v) || is_float($v) || is_integer($v)) {
			$r = $v;
		}
		elseif (is_object($v) || gettype($v) == 'object') {
			$r = 'Object ' . get_class($v);
		}
		elseif (is_resource($v)) {
			$r = 'Resource '. get_resource_type($v);
		}
		elseif (is_array($v)) {
			$r = 'Array of length ' . count($v);
		}
		else {
			$r = $this->serializeString($v);
		}
		return $r;
	}
}
