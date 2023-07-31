<?php
use ReflectionObject as RO;
use ReflectionProperty as RP;
/**
 * 2023-08-01
 * 1) "Implement `dfr_prop_get()`": https://github.com/mage2pro/core/issues/299
 * 2) https://stackoverflow.com/a/11604967
 * @param object $o
 * @return mixed
 */
function dfr_prop_get($o, string $n) {
	$ro = new RO($o); /** @var RO $ro */
	df_assert($ro->hasProperty($n), "The `{$ro->getName()}` class does not have the `{$n}` property.");
	$rp = $ro->getProperty($n); /** @var RP $rp */
	/**
	 * 2023-08-01
	 * 1) https://stackoverflow.com/a/17174448
	 * 2) «As of PHP 8.1.0, calling this method has no effect; all methods are invokable by default»:
	 * https://www.php.net/manual/reflectionmethod.setaccessible.php#refsect1-reflectionmethod.setaccessible-description
	 * That is why I use @noinspection PhpExpressionResultUnusedInspection
	 */
	$rp->setAccessible(true);
	return $rp->getValue($o);
}
