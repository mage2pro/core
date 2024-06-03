<?php
use ReflectionClass as RC;
use ReflectionMethod as RM;
/**
 * 2023-08-01
 * 1) "Implement `df_call_parent()`": https://github.com/mage2pro/core/issues/298
 * 2) https://stackoverflow.com/a/50469869
 * 3) https://3v4l.org/usLci
 * 2024-06-03
 * 1) The `object` type requires PHP ≥ 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2) We need to support PHP ≥ 7.1.
 * @see dfr_prop_get()
 * @see dfr_prop_set()
 * @used-by \Df\Framework\Config\Dom\L::init()
 * @used-by \Df\Framework\Config\Dom\L::validate()
 * @param object $o
 * @return mixed
 */
function df_call_parent($o, string $m, array $a = []) {
	$rc = new RC($c = get_class($o)); /** @var RC $rc */ /** @var string $c */
	$rc2 = df_assert($rc->getParentClass(), "`{$c}` does not have a parent."); /** @var RC $rc2 */
	df_assert($rc2->hasMethod($m), "The `{$rc2->getName()}` class does not have the `{$m}` method.");
	$rm = $rc2->getMethod($m);	/** @var RM $rm */
	/**
	 * 2023-08-01
	 * 1) https://stackoverflow.com/a/17174448
	 * 2) «As of PHP 8.1.0, calling this method has no effect; all methods are invokable by default»:
	 * https://php.net/manual/reflectionmethod.setaccessible.php#refsect1-reflectionmethod.setaccessible-description
	 * That is why I use @noinspection PhpExpressionResultUnusedInspection
	 */
	$rm->setAccessible(true);
	/**
	 * 2023-08-01
	 * 1) «Magento\Framework\Config\Dom::validate():
	 * Argument #2 ($errors) must be passed by reference, value given
	 * in vendor/mage2pro/core/Core/lib/reflection\parent.php on line 30»: https://github.com/mage2pro/core/issues/301
	 * 2) «@see ReflectionMethod::invoke() cannot be used when reference parameters are expected.
	 * @uses ReflectionMethod::invokeArgs() has to be used instead (passing references in the argument list).»:
	 * https://php.net/manual/reflectionmethod.invoke.php#refsect1-reflectionmethod.invoke-notes
	 */
	return $rm->invokeArgs($o, $a);
}