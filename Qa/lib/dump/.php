<?php
use Df\Qa\Dumper;
/**
 * We do not use @uses \Df\Qa\Dumper as a singleton
 * because @see \Df\Qa\Dumper::dumpObject()
 * uses the @see \Df\Qa\Dumper::$_dumped property to avoid a recursion.
 * @see df_kv()
 * @see df_string()
 * @see df_type()
 * @used-by df_assert_eq()
 * @used-by df_bool()
 * @used-by df_caller_m()
 * @used-by df_dump_ds()
 * @used-by df_kv()
 * @used-by df_kv_table()
 * @used-by df_sentry()
 * @used-by df_string()
 * @used-by df_type()
 * @used-by dfa_assert_keys()
 * @used-by dfc()
 * @used-by dfs_con()
 * @used-by \Df\API\Client::_p()
 * @used-by \Df\Framework\Form\Element\Text::getValue()
 * @used-by \Df\Sentry\Client::capture()
 * @used-by \Df\Sentry\Extra::adjust()
 * @used-by \Df\Zf\Validate::message()
 * @used-by \Dfe\BlackbaudNetCommunity\Customer::p()
 * @used-by \Dfe\Dynamics365\Test\OAuth::discovery()
 * @used-by \Dfe\Geo\Test\Basic::t01()
 * @used-by \Dfe\Portal\Test\Basic::t01()
 * @used-by \Dfe\Portal\Test\Basic::t02()
 * @used-by \Dfe\Robokassa\Test\Basic::t01()
 * @used-by \Hotlink\Brightpearl\Model\Api\Transport::_submit() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/site/issues/122)
 * @param mixed $v
 */
function df_dump($v):string {return Dumper::i()->dump($v);}

/**
 * 2023-08-04
 * @used-by df_log_l()
 * @used-by \Df\Qa\Failure\Exception::postface()
 */
function df_dump_ds($v):string {return df_json_dont_sort(function() use($v):string {return df_dump($v);});}