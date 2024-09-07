<?php
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