<?php
use ReflectionClass as RC;
use ReflectionMethod as RM;
/**
 * 2023-08-01
 * 1) "Implement `df_call_parent()`": https://github.com/mage2pro/core/issues/298
 * 2) https://stackoverflow.com/a/50469869
 * 3) https://3v4l.org/usLci
 * @see dfr_prop_get()
 * @see dfr_prop_set()
 * @param object $o
 * @param mixed ...$a [optional]
 * @return mixed
 */
function df_call_parent($o, string $m, ...$a) {
	$rc = new RC($c = get_class($o)); /** @var RC $rc */ /** @var string $c */
	$rc2 = df_assert($rc->getParentClass(), "`{$c}` does not have a parent."); /** @var RC $rc2 */
	df_assert($rc2->hasMethod($m), "The `{$rc2->getName()}` class does not have the `{$m}` method.");
	$rm = $rc2->getMethod($m);	/** @var RM $rm */
	/**
	 * 2023-08-01
	 * 1) https://stackoverflow.com/a/17174448
	 * 2) «As of PHP 8.1.0, calling this method has no effect; all methods are invokable by default»:
	 * https://www.php.net/manual/reflectionmethod.setaccessible.php#refsect1-reflectionmethod.setaccessible-description
	 * That is why I use @noinspection PhpExpressionResultUnusedInspection
	 */
	$rm->setAccessible(true);
	return $rm->invoke($o, ...$a);
}