<?php
use Df\Qa\Message\Failure\Error;
use Magento\Framework\Component\ComponentRegistrar as R;
// https://github.com/magento-russia/3/blob/2016-11-22/app/code/local/Df/Core/Boot.php?ts=4#L155-L188
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
/** @var string $base */
$base = dirname(__FILE__) . '/';
/** @var string[] $modules */
$modules = array_diff(scandir($base), ['..', '.', '.git', 'composer.json', 'etc', 'registration.php']);
foreach (array_merge(['Core'], array_diff($modules, ['Core'])) as $m) {
	/** @var string $baseM */
	$baseM = $base . $m;
	R::register(R::MODULE, "Df_{$m}", $baseM);
	/** @var string $libDir */
	$libDir = "{$baseM}/lib";
	if (is_dir($libDir)) {
		// 2015-02-06
		// array_slice отсекает «.» и «..».
		// http://php.net/manual/function.scandir.php#107215
		foreach (array_slice(scandir($libDir), 2) as $c) {
			/** @var string $c */
			require_once $libDir . '/' . $c;
		}
	}
}
register_shutdown_function(function() {Error::check();});