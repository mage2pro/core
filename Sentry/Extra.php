<?php
# 2017-01-03
# https://forum.sentry.io/t/694
# https://docs.sentry.io/learn/quotas/#attributes-limits
namespace Df\Sentry;
final class Extra {
	/**
	 * 2017-01-03
	 * 2017-04-16 Ключи могут содержать пробелы, например: «Redirect Params».
	 * @used-by self::adjust() Recursion.
	 * @used-by \Df\Sentry\Client::capture()
	 * @param array(string => mixed) $a
	 * @return array(string => string)
	 */
	static function adjust(array $a):array {/** @var array(string => string) $r */
		$r = [];
		foreach ($a as $k => $v)  {/** @var string $k */ /** @var mixed $v */
			$vs = df_dump($v); /** @var string $vs */
			# 2017-01-03 We need the length in bytes, not in characters:
			# https://docs.sentry.io/learn/quotas/#attributes-limits
			$l = strlen($vs); /** @var int $l */
			/** @var iterable|null $vi */
			if ($l <= 512 || is_null($vi = is_iterable($v) ? $v : (df_has_gd($v) ? df_gd($v) : null))) {
				$r[$k] = $vs;
			}
			else {
				# 2017-01-03
				# $vs requires > 512 bytes,
				# so I transfer the elements of the array $v on a level higher ($a), and add $k prefix to its keys.
				$r = array_merge($r, self::adjust(dfak_transform($vi, function($vk) use($k) {return "$k/$vk";})));
			}
		}
		return $r;
	}
}