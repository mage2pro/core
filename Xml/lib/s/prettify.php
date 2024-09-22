<?php
use Df\Xml\G;

/**
 * 2016-09-01
 * @used-by \Dfe\SecurePay\Refund::process()
 * @uses df_xml_s() не сохраняет заголовок XML.
 * @param G|string $x
 */
function df_xml_prettify($x):string {return df_cc_n(df_xml_parse_header($x), df_xml_s(df_xml_parse($x)));}