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
	 * @param T $t
	 * @return string
	 */
	static function p(T $t) {return dfcf(function($t) {
		$count = count($t); /** @var int $count */
		return implode(df_map_k($t, function($index, F $frame) use($count) {
			$index++;
			$r = self::frame($frame); /** @var string $r */
			if ($index !== $count) {
				$indexS = (string)$index; /** @var string $indexS */
				$indexLength = strlen($indexS); /** @var int $indexLength */
				$delimiterLength = 36; /** @var int $delimiterLength */
				$fillerLength = $delimiterLength - $indexLength; /** @var int $fillerLength */
				$fillerLengthL = floor($fillerLength / 2); /** @var int $fillerLengthL */
				$fillerLengthR = $fillerLength - $fillerLengthL; /** @var int $fillerLengthR */
				$r .= "\n" . str_repeat('*', $fillerLengthL) . $indexS . str_repeat('*', $fillerLengthR) . "\n";
			}
			return $r;
		}));
	}, [$t]);}

	/**     
	 * 2020-02-27          
	 * @used-by self::p()
	 * @param Frame $f 
	 * @return string
	 */
	private static function frame(F $f) {/** @var string $r */
		try {
			$resultA = array_filter(array_map([__CLASS__, 'param'], [
				['Location', df_cc(':', df_path_relative($f->filePath()), $f->line())], ['Callee', $f->method()]
			])); /** @var string[] $resultA */ /** @uses self::param() */
			$r = df_cc_n($resultA);
		}
		catch (\Exception $e) {
			$r = df_ets($e);
			/**
			 * 2020-02-20
			 * 1) «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
			 * 2) It is be dangerous to call @see df_log_e() here, because it will inderectly return us here,
			 * and it could be an infinite loop.
			 */
			static $loop = false;
			if ($loop) {
				df_log_l(__CLASS__, "$r\n{$e->getTraceAsString()}", df_class_l(__CLASS__));
			}
			else {
				$loop = true;
				df_log_e($e, __CLASS__);
				$loop = false;
			}
		}
		return $r;		
	}
	
	/**
	 * Этот метод может быть приватным, несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by self::p()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP: http://3v4l.org/OipEQ
	 * @param array $p
	 * @return string|null
	 */
	private static function param(array $p) {/** @var string|null $r */ /** @var string|null $v */
		if (!($v = $p[1])) {
			$r = null;
		}
		else {
			$label = $p[0]; /** @var string $label */
			$pad = df_pad(' ', 12 - mb_strlen($label)); /** @var string $pad */
			$r = "{$label}:{$pad}{$v}";
		}
		return $r;
	}	
}