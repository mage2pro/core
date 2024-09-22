<?php
use Df\Core\Exception as E;
use SimpleXMLElement as X;
use Throwable as T;

/**
 * @used-by df_module_name_by_path()
 * @used-by df_xml_g()
 * @used-by df_xml_node()
 * @used-by df_xml_parse_a()
 * @used-by df_xml_prettify()
 * @used-by df_xml_x()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param X|string $x
 * @throws E
 */
function df_xml_x($x, bool $throw = true):?X {/** @var ?X $r */
	if ($x instanceof X) {
		$r = $x;
	}
	else {
		df_param_sne($x, 0);
		$r = null;
		try {$r = new X($x);}
		catch (T $t) {
			if ($throw) {
				df_error("Failed to parse XML: «%s».\nXML:\n%s", df_xts($t), df_trim($x));
			}
		}
	}
	return $r;
}