<?php
namespace Df\Qa\Trace;
use Df\Qa\Trace as T;
use Df\Qa\Trace\Frame as F;
# 2020-02-27
final class Formatter {
	/**
	 * 2020-02-27
	 * @used-by df_bt_s()
	 * @used-by \Df\Qa\Failure::postface()
	 */
	static function p(T $t):string {return dfcf(function(T $t):string {return df_try(
		function() use($t) {return df_cc_n(df_map_k($t,
			function(int $i, F $f):string {
				$i++; return "$i\t{$f->method()}\n\t" . df_cc(':', df_path_relative($f->filePath()), $f->line());
			}
		));}
		,function(\Exception $e) {
			$r = df_xts($e);
			/**
			 * 2020-02-20
			 * 1) «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
			 * 2) It is be dangerous to call @see df_log() here, because it will inderectly return us here,
			 * and it could be an infinite loop.
			 */
			static $loop = false;
			if ($loop) {
				df_log_l(__CLASS__, "$r\n{$e->getTraceAsString()}", df_class_l(__CLASS__));
			}
			else {
				$loop = true;
				df_log($e, __CLASS__);
				$loop = false;
			}
		}
	);}, [$t]);}
}