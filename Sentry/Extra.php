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
	static function adjust(array $a) {/** @var array(string => string) $r */
		$r = [];
		foreach ($a as $k => $v)  {/** @var string $k */ /** @var mixed $v */
			if (!is_array($v)) {
				$r[$k] = $v;
			}
			else {
				$json = df_json_encode($v); /** @var string $json */
				// 2017-01-03 We need the length in bytes, not in characters:
				// https://docs.sentry.io/learn/quotas/#attributes-limits
				$l = strlen($json); /** @var int $l */
				if ($l <= 512) {
					$r[$k] = $json;
				}
				else {
					// 2017-01-03
					// JSON не укладывается в 512 байтов,
					// поэтому переносим элементы массива $v на уровень выше (на уровень $a),
					// прибавляя к их ключам приставку $k.
					$r = array_merge($r, self::adjust(dfak_transform($v, function($vk) use($k) {return "$k/$vk";})));
				}
			}
		}
		return $r;
	}
}