<?php
/**
 * 2015-02-07
 * Эта функция аналогична методу @see \Magento\Framework\Data\Collection::walk(),
 * и даже может использоваться вместо @see \Magento\Framework\Data\Collection::walk(),
 * однако, в отличие от @see \Magento\Framework\Data\Collection::walk(),
 * она способна работать не только с коллекцией, но также с массивом объектов и объектом,
 * поддерживающим интерфейс @see Traversable
 * 2016-07-31 При вызове с 2-мя параметрами эта функция идентична функции @see df_column()
 * 2017-07-09
 * Now the function accepts an array as $object.
 * Even in this case it differs from @see array_column(): array_column() misses the keys: https://3v4l.org/llMrL
 * df_column() preserves the keys.
 * 2024-06-03
 * 1.1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 1.2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 1.3) https://php.net/manual/en/language.types.iterable.php
 * 2) We still can not use «Union Types» (e.g. `callable|iterable`) because they require PHP ≥ 8 (we need to support PHP ≥ 7.1):
 * 2.1) https://php.watch/versions/8.0/union-types
 * 2.2) https://3v4l.org/AOWmO
 * @used-by df_category_names()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Core\GlobalSingletonDestructor::process()
 * @param Traversable|array(int|string => _DO|array(string => mixed)) $c
 * @param string|callable $f
 * @param mixed ...$p
 */
function df_each(iterable $c, $f, ...$p):array {return df_map(function($v) use($f, $p) {return df_call($v, $f, $p);}, $c);}