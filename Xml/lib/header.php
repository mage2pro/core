<?php
use SimpleXMLElement as X;

/**
 * 2016-09-01
 * 2018-12-18 Single quotes are not supported by some external systems (e.g., Vantiv), so now I use double quotes.
 * @see df_xml_parse_header()
 * @used-by df_xml_g()
 */
function df_xml_header(string $enc = 'UTF-8', string $v = '1.0'):string {return "<?xml version=\"$v\" encoding=\"$enc\"?>";}

/**
 * 2016-09-01
 * Если XML не отформатирован, то после его заголовка перенос строки идти не обязан: http://stackoverflow.com/a/8384602
 * @used-by df_xml_prettify()
 * @param X|string $x
 * @return string|null
 */
function df_xml_parse_header($x) {return df_preg_match('#^<\?xml.*\?>#', df_xml_s($x));}