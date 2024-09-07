<?php
use Df\Core\Exception as DFE;

/**
 * PHP supports global constants since 5.3:
 * http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html#post1363452
 * @used-by df_find()
 * @used-by df_map()
 */
const DF_AFTER = 1;

/**
 * PHP supports global constants since 5.3:
 * http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html#post1363452
 * @used-by df_find()
 * @used-by df_map()
 * @used-by df_map_k()
 * @used-by df_map_kr()
 * @used-by \Df\Payment\Method::amountFactor()
 */
const DF_BEFORE = -1;

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
 * @used-by \Df\Payment\ConfigProvider::configOptions()
 * @used-by \Df\Qa\Dumper::dumpArrayElements()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @used-by \Df\Sentry\Client::send()
 * @used-by \Dfe\PostFinance\Signer::sign()
 * @used-by \Sharapov\Cabinetsbay\Setup\UpgradeData::p100() (https://github.com/cabinetsbay/site/issues/98)
 * @used-by \Wolf\Filter\Block\Navigation::hDropdowns()
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/l3/tabs/overview.phtml (https://github.com/cabinetsbay/catalog/issues/22)
 * @used-by vendor/mage2pro/color/view/frontend/templates/index.phtml
 * @param callable|iterable $a1
 * @param callable|iterable $a2
 * @return array(int|string => mixed)
 */
function df_map_k($a1, $a2):array {return df_map($a1, $a2, [], [], DF_BEFORE);}

/**
 * 2016-11-08 Функция принимает аргументы в любом порядке.
 * 2024-06-03
 * 1.1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 1.2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 1.3) https://php.net/manual/en/language.types.iterable.php
 * 2) We still can not use «Union Types» (e.g. `callable|iterable`) because they require PHP ≥ 8 (we need to support PHP ≥ 7.1):
 * 2.1) https://php.watch/versions/8.0/union-types
 * 2.2) https://3v4l.org/AOWmO
 * @used-by dfak_transform()
 * @used-by \Df\Config\Source\Block::map()
 * @used-by \Df\Core\Text\Regex::getErrorCodeMap()
 * @used-by \Dfe\Color\Image::labels()
 * @used-by \Dfe\Color\Plugin\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual::afterGetJsonConfig()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @param callable|iterable $a1
 * @param callable|iterable $a2
 * @return array(int|string => mixed)
 * @throws DFE
 */
function df_map_kr($a1, $a2):array {return df_map($a1, $a2, [], [], DF_BEFORE, true);}

/**
 * 2016-11-08 Функция принимает аргументы в любом порядке.
 * 2024-06-03
 * 1.1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 1.2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 1.3) https://php.net/manual/en/language.types.iterable.php
 * 2) We still can not use «Union Types» (e.g. `callable|iterable`) because they require PHP ≥ 8 (we need to support PHP ≥ 7.1):
 * 2.1) https://php.watch/versions/8.0/union-types
 * 2.2) https://3v4l.org/AOWmO
 * @used-by df_category_children_map()
 * @used-by df_modules_my()
 * @used-by df_parse_colon()
 * @used-by dfe_packages()
 * @used-by dfe_portal_stripe_customers()
 * @used-by \Df\Config\Source\Block::map()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFrom3To2()
 * @used-by \Df\Directory\Model\ResourceModel\Country\Collection::mapFromCodeToName()
 * @used-by \Df\Framework\Form\Element\Multiselect::getElementHtml()
 * @used-by \Dfe\Oro\Test\Basic::t02_orders_stripe()
 * @used-by \Dfe\ZohoBI\Source\Organization::fetch()
 * @used-by \Dfe\PostFinance\Source\Hash\Algorithm::map()
 * @used-by \Dfe\Spryng\Source\Account::fetch()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @used-by \TFC\GoogleShopping\Products::atts() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/1)
 * @param callable|iterable $a1
 * @param callable|iterable $a2
 * @return array(int|string => mixed)
 */
function df_map_r($a1, $a2):array {return df_map($a1, $a2, [], [], 0, true);}