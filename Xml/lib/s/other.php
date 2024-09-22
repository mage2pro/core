<?php
use Magento\Framework\Simplexml\Element as MX;
use SimpleXMLElement as CX;

/**
 * @used-by df_assert_leaf()
 * @used-by df_xml_children()
 * @param CX|MX $e
 */
function df_xml_report(CX $e):string {return $e instanceof MX ? $e->asNiceXml() : $e->asXML();}

/**
 * 2016-09-01
 * @see df_xml_x()
 * @used-by df_xml_parse_header()
 * @param CX|string $x
 */
function df_xml_s($x):string {return is_string($x) ? $x : $x->asXML();}