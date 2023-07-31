<?php
use ReflectionObject as RO;
use ReflectionProperty as RP;
/**
 * 2023-08-01
 * @used-by dfr_prop_get()
 * @used-by dfr_prop_set()
 * @param object $o
 */
function dfr_prop($o, string $n):RP {
	$ro = new RO($o); /** @var RO $ro */
	df_assert($ro->hasProperty($n), "The `{$ro->getName()}` class does not have the `{$n}` property.");
	$r = $ro->getProperty($n); /** @var RP $r */
	/**
	 * 2023-08-01
	 * 1) https://stackoverflow.com/a/17174448
	 * 2) «As of PHP 8.1.0, calling this method has no effect; all methods are invokable by default»:
	 * https://www.php.net/manual/reflectionmethod.setaccessible.php#refsect1-reflectionmethod.setaccessible-description
	 * That is why I use @noinspection PhpExpressionResultUnusedInspection
	 */
	$r->setAccessible(true);
	return $r;
}

/**
 * 2023-08-01
 * 1) "Implement `dfr_prop_get()`": https://github.com/mage2pro/core/issues/299
 * 2) https://stackoverflow.com/a/11604967
 * @see df_call_parent()
 * @see dfr_prop_set()
 * @used-by \Df\Framework\Config\DomL::init()
 * @param object $o
 * @return mixed
 */
function dfr_prop_get($o, string $n) {return dfr_prop($o, $n)->getValue($o);}

/**
 * 2023-08-01 "Implement `dfr_prop_set()`": https://github.com/mage2pro/core/issues/300
 * @see df_call_parent()
 * @see dfr_prop_get()
 * @used-by \Df\Framework\Config\DomL::init()
 * @param object $o
 * @param mixed $v
 */
function dfr_prop_set($o, string $n, $v):void {dfr_prop($o, $n)->setValue($o, $v);}