<?php
/**
 * 2016-06-05
 * 2016-08-22
 * Помимо добавления поля в таблицу «customer_entity» надо ещё добавить атрибут
 * что мы делаем методом @see \Df\Sso\Upgrade\Data::attribute()
 * иначе данные не будут сохраняться: https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Eav/Model/Entity/AbstractEntity.php#L1262-L1265
 * 2019-06-04 @todo Support df_call_a()
 * @used-by \Df\Customer\Setup\UpgradeSchema::_process()
 * @used-by \Df\Sso\Upgrade\Schema::_process()
 * @used-by \Dfe\FacebookLogin\Setup\UpgradeSchema::_process()
 * @param string|null|array(string => mixed) $dfn [optional]
 */
function df_dbc_c(string $name, $dfn = null) {df_db_column_add('customer_entity', ...func_get_args());}

/**
 * 2019-06-04 @todo Support df_call_a()
 * @used-by \Verdepieno\Core\Setup\UpgradeSchema::_process()
 * @param string $name
 * @param string|null|array(string => mixed) $dfn [optional]
 */
function df_dbc_ca($name, $dfn = null) {df_db_column_add('customer_address_entity', ...func_get_args());}

/**
 * 2019-06-04 @todo Support df_call_a()
 * @used-by \Df\Sales\Setup\UpgradeSchema::_process()
 * @param string $name
 * @param string|null|array(string => mixed) $dfn [optional]
 */
function df_dbc_o($name, $dfn = null) {df_db_column_add('sales_order', ...func_get_args());}

/**
 * 2019-06-04 @todo Support df_call_a()
 * @used-by \Verdepieno\Core\Setup\UpgradeSchema::_process()
 * @param string $name
 * @param string|null|array(string => mixed) $dfn [optional]
 */
function df_dbc_oa($name, $dfn = null) {df_db_column_add('sales_order_address', ...func_get_args());}

/**
 * 2019-06-04 @todo Support df_call_a()
 * @used-by \Verdepieno\Core\Setup\UpgradeSchema::_process()
 * @param string $name
 * @param string|null|array(string => mixed) $dfn [optional]
 */
function df_dbc_qa($name, $dfn = null) {df_db_column_add('quote_address', ...func_get_args());}