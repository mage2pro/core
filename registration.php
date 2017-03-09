<?php
use Magento\Framework\Component\ComponentRegistrar as R;
// https://github.com/magento-russia/3/blob/2016-11-22/app/code/local/Df/Core/Boot.php?ts=4#L155-L188
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
/** @var string $base */
foreach (array_merge(['Core'], array_diff(scandir($base = dirname(__FILE__) . '/'), ['Core'])) as $m) {
	// 2016-11-23
	// It gets rid of the ['..', '.'] and the root files (non-directories).
	/** @var string $baseM */
	if (ctype_upper($m[0]) && is_dir($baseM = $base . $m)) {
		R::register(R::MODULE, "Df_{$m}", $baseM);
		/** @var string $libDir */
		if (is_dir($libDir = "{$baseM}/lib")) {
			// 2015-02-06
			// array_slice removes «.» and «..».
			// http://php.net/manual/function.scandir.php#107215
			foreach (array_slice(scandir($libDir), 2) as $c) {require_once "{$libDir}/{$c}";}
		}
	}
}
register_shutdown_function(function() {\Df\Qa\Message\Failure\Error::check();});