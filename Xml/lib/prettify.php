<?php
use SimpleXMLElement as X;

/**
 * 2016-09-01
 * @used-by \Dfe\SecurePay\Refund::process()
 * The output of @uses df_xml_s() does not include the XML header.
 * @param X|string $x
 */
function df_xml_prettify($x):string {return df_cc_n(df_xml_parse_header($x), df_xml_s(df_xml_x($x)));}