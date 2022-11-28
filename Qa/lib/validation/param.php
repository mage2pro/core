<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;

/**
 * @used-by df_country_ctn()
 * @param string $v
 * @param int $ord	zero-based
 * @param int $sl [optional]
 * @throws DFE
 */
function df_param_iso2($v, $ord, $sl = 0):string {return Q::assertParamIsIso2($v, $ord, ++$sl);}

/**
 * @used-by df_assert_class_exists()
 * @used-by df_class_check_abstract()
 * @used-by df_date_from_db()
 * @used-by df_eav_update()
 * @used-by df_fs_delete()
 * @used-by df_xml_children()
 * @used-by df_xml_parse()
 * @used-by dfa_deep()
 * @used-by dfa_deep_unset()
 * @used-by \Df\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Df\Payment\W\Nav::e2i()
 * @used-by \Df\Xml\X::importString()
 * @used-by \Df\Xml\X::xpath()
 * @used-by \Df\Xml\X::xpathA()
 * @used-by \Dfe\AllPay\W\Event::tl_()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @throws DFE
 */
function df_param_sne(string $v, int $ord, int $sl = 0):string {$sl++;
	/**
	 * Раньше тут стояло `$method->assertParamIsString($v, $ord, $sl)`
	 * При второй попытке тут стояло `if (!$v)`, что тоже неправильно, ибо непустая строка '0' не проходит такую валидацию.
	 * 2022-11-10 @see df_result_sne()
	 */
	return !df_es($v) ? $v : Q::raiseErrorParam(__FUNCTION__, [Q::NES], $ord, $sl);
}