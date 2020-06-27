<?php
namespace Df\Sentry;
/**
 * Serializes a value into a representation that should reasonably suggest
 * both the type and value, and be serializable into JSON.
 * @package raven
 */
final class ReprSerializer extends Serializer {
	/**
	 * 2020-06-28
	 * @override
	 * @see \Df\Sentry\Serializer::serializeValue()
	 * @used-by \Df\Sentry\Serializer::serialize()
	 * @param mixed $v
	 * @return bool|false|float|int|string|string[]|null
	 */
	protected function serializeValue($v) {
		if ($v === null) {
			return 'null';
		} elseif ($v === false) {
			return 'false';
		} elseif ($v === true) {
			return 'true';
		} elseif (is_float($v) && (int) $v == $v) {
			return $v.'.0';
		} elseif (is_integer($v) || is_float($v)) {
			return (string) $v;
		} elseif (is_object($v) || gettype($v) == 'object') {
			return 'Object '.get_class($v);
		} elseif (is_resource($v)) {
			return 'Resource '.get_resource_type($v);
		} elseif (is_array($v)) {
			return 'Array of length ' . count($v);
		} else {
			return $this->serializeString($v);
		}
	}
}
