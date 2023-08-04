<?php
namespace Df\Core;
# 2023-08-04
final class Json {
	/**
	 * 2023-08-04 "Implement `df_json_dont_sort()`": https://github.com/mage2pro/core/issues/313
	 * @used-by df_json_dont_sort()
	 * @used-by df_json_sort()
	 * @param bool|null|string $v [optional]
	 * @return self|bool
	 */
	static function bSort($v = DF_N) {return df_prop(null, $v, true);}
}