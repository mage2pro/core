<?php
// 2017-01-03
// https://forum.sentry.io/t/694
// https://docs.sentry.io/learn/quotas/#attributes-limits
namespace Df\Sentry;
class Extra {
	/**
	 * 2017-01-03
	 * @param array(string => mixed) $a
	 * @return array(string => string)
	 */
	public static function adjust(array $a) {
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
				$json = df_json_encode_pretty($v);
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
					/**
					 * 2017-01-03
					 * JSON не укладывается в 512 байтов,
					 * поэтому переносим элементы массива $v на уровень выше (на уровень $a),
					 * прибавляя к их ключам приставку $k.
					 */
					$result = array_merge($result, self::adjust(
						df_map_kr($v, function($vk, $vv) use($k) {return ["$k/$vk", $vv];})
					));
				}
			}
		}
		return $result;
	}
}