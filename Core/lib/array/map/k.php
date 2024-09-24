<?php
/**
 * 2016-08-09 Функция принимает аргументы в любом порядке.
 * 2024-06-03
 * 1.1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 1.2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 1.3) https://php.net/manual/en/language.types.iterable.php
 * 2) We still can not use «Union Types» (e.g. `callable|iterable`) because they require PHP ≥ 8 (we need to support PHP ≥ 7.1):
 * 2.1) https://php.watch/versions/8.0/union-types
 * 2.2) https://3v4l.org/AOWmO
 * @used-by df_kv()
 * @used-by df_kv_table()
 * @used-by df_modules_my()
 * @used-by dfe_modules_log()
 * @used-by dfp_methods()
 * @used-by Df\Payment\ConfigProvider::configOptions()
 * @used-by Df\Qa\Dumper::dumpArrayElements()
 * @used-by Df\Qa\Trace\Formatter::p()
 * @used-by Df\Sentry\Client::send()
 * @used-by Dfe\PostFinance\Signer::sign()
 * @used-by Sharapov\Cabinetsbay\Setup\UpgradeData::p100() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l3/tabs/overview.phtml (https://github.com/cabinetsbay/catalog/issues/22)
 * @used-by vendor/mage2pro/color/view/frontend/templates/index.phtml
 * @param callable|iterable $a1
 * @param callable|iterable $a2
 * @return array(int|string => mixed)
 */
function df_map_k($a1, $a2):array {return df_map($a1, $a2, [], [], DF_BEFORE);}