<?php
use Magento\Framework\Component\ComponentRegistrar as R;
// 2017-11-13
// Today I have added the subdirectories support inside the `lib` folders,
// because some lib/*.php files became too big, and I want to split them.
$requireFiles = function($libDir) use(&$requireFiles) {
	// 2015-02-06
	// array_slice removes «.» and «..».
	// http://php.net/manual/function.scandir.php#107215
	foreach (array_slice(scandir($libDir), 2) as $c) {  /** @var string $resource */
		is_dir($resource = "{$libDir}/{$c}") ? $requireFiles($resource) : require_once "{$libDir}/{$c}";
	}
};
// 2017-06-18 The strange array_diff / array_merge combination makes the Df_Core module to be loaded first.
/** @var string $base */
foreach (array_merge(['Core'], array_diff(scandir($base = dirname(__FILE__) . '/'), ['Core'])) as $m) {
	// 2016-11-23
	// It gets rid of the ['..', '.'] and the root files (non-directories).
	/** @var string $baseM */
	if (ctype_upper($m[0]) && is_dir($baseM = $base . $m)) {
		R::register(R::MODULE, "Df_{$m}", $baseM);
		/** @var string $libDir */
		if (is_dir($libDir = "{$baseM}/lib")) {
			$requireFiles($libDir);
		}
	}
}
register_shutdown_function(function() {\Df\Qa\Message\Failure\Error::check();});