<?php
use Df\Xml\X;
use Magento\Framework\Simplexml\Element as MX;
use SimpleXMLElement as CX;

/**
 * 2016-09-01
 * 2018-12-18 Single quotes are not supported by some external systems (e.g., Vantiv), so now I use double quotes.
 * @see df_xml_parse_header()
 * @used-by df_xml_g()
 */
function df_xml_header(string $enc = 'UTF-8', string $v = '1.0'):string {return "<?xml version=\"$v\" encoding=\"$enc\"?>";}

/**
 * 2016-09-01
 * @used-by \Dfe\SecurePay\Refund::process()
 * @uses \Df\Xml\X::asNiceXml() не сохраняет заголовок XML.
 * @param string|X $x
 */
function df_xml_prettify($x):string {return df_cc_n(df_xml_parse_header($x), df_xml_parse($x)->asNiceXml());}

/**
 * @used-by df_assert_leaf()
 * @used-by df_xml_children()
 * @param CX|MX|X $e
 */
function df_xml_report(CX $e):string {return $e instanceof MX ? $e->asNiceXml() : $e->asXML();}

/**
 * 2016-09-01
 * @see df_xml_x()
 * @used-by df_xml_parse_header()
 * @param string|X $x
 */
function df_xml_s($x):string {return is_string($x) ? $x : $x->asXML();}