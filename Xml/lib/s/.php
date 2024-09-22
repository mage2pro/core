<?php
use Magento\Framework\Simplexml\Element as MX;
use SimpleXMLElement as CX;

/**
 * 2016-09-01
 * @see df_xml_x()
 * @used-by df_assert_leaf()
 * @used-by df_xml_children()
 * @used-by df_xml_parse_header()
 * @param CX|MX|string $x
 */
function df_xml_s($x):string {return is_string($x) ? $x : ($x instanceof MX ? $x->asNiceXml() : $x->asXML());}