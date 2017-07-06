<?php
// 2017-01-03
// https://forum.sentry.io/t/694
// https://docs.sentry.io/learn/quotas/#attributes-limits
namespace Df\Sentry;
final class Extra {
	/**
	 * 2017-01-03
	 * 2017-04-16 Ключи могут содержать пробелы, например: «Redirect Params».
	 * @used-by adjust() Recursion.
	 * @used-by \Df\Sentry\Client::capture()
	 * @param array(string => mixed) $a
	 * @return array(string => string)
	 */
	static function adjust(array $a) {
		/** @var array(string => string) $result */
		$result = [];
		foreach ($a as $k => $v)  {
			/** @var string $k */
			/** @var mixed $v */
			if (!is_array($v)) {
				$result[$k] = $v;
			}
			else {
				/** @var string $json */
				$json = df_json_encode($v);
				/**
				 * 2017-01-03
				 * Нам нужна длина именно в байтах, а не в символах:
				 * https://docs.sentry.io/learn/quotas/#attributes-limits
				 */
				/** @var int $l */
				$l = strlen($json);
				if ($l <= 512) {
					$result[$k] = $json;
				}
				else {
					// 2017-01-03
					// JSON не укладывается в 512 байтов,
					// поэтому переносим элементы массива $v на уровень выше (на уровень $a),
					// прибавляя к их ключам приставку $k.
					$result = array_merge($result, self::adjust(
						dfa_key_transform($v, function($vk) use($k) {return "$k/$vk";})
					));
				}
			}
		}
		return $result;
	}
}