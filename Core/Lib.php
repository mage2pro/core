<?php
namespace Df\Core;
use Df\Core\Helper\Path;
class Lib {
	/**
	 * 2016-11-22
	 * В отличие от РСМ мы ничего не возвращаем из этого метода.
	 * https://github.com/magento-russia/3/blob/2016-11-22/app/code/local/Df/Core/Lib.php?ts=4#L178-L188
	 * @used-by \Df\Core\Boot::init()
	 * @used-by \Df\Core\Boot::run()
	 * @param string $m
	 */
	public static function load($m) {
		/** @var array(string => true) */
		static $done;
		if (!isset($done[$m])) {
			$done[$m] = true;
			/** @var string $libDir */
			$libDir = dirname(__DIR__) . "/{$m}/lib";
			// Нельзя писать df_path()->children(),
			// потому что библиотеки Российской сборки ещё не загружены
			foreach (Path::s()->children($libDir) as $c) {
				/** @var string $c */
				/** @var string $path */
				$path = "{$libDir}/{$c}";
				// 2016-11-22
				// «include returns FALSE on failure and raises a warning.
				// Successful includes, unless overridden by the included file, return 1.»
				// require_once ведёт себя так же.
				is_file($path) ? require_once $path : null;
			}
		}
	}
}