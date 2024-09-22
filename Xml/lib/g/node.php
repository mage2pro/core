<?php
use Df\Xml\G;

/**
 * @used-by df_xml_g()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::t04()
 * @param array(string => string) $attr [optional]
 * @param array(string => mixed) $contents [optional]
 */
function df_xml_g_node(string $tag, array $attr = [], array $contents = []):G {
	$r = new G(df_xml_x("<{$tag}/>")); /** @var G $r */
	$r->addAttributes($attr);
	$r->importArray($contents);
	return $r;
}