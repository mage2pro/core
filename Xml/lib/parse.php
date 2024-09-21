<?php
use Df\Core\Exception as E;
use Df\Xml\X;

/**
 * @used-by df_module_name_by_path()
 * @used-by df_xml_g()
 * @used-by df_xml_node()
 * @used-by df_xml_parse_a()
 * @used-by df_xml_prettify()
 * @used-by df_xml_x()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param string|X $x
 * @throws E
 */
function df_xml_parse($x, bool $throw = true):?X {/** @var ?X $r */
	if ($x instanceof X) {
		$r = $x;
	}
	else {
		df_param_sne($x, 0);
		$r = null;
		try {$r = new X($x);}
		catch (\Throwable $th) {
			if ($throw) {
				df_error(
					"Failed to parse an XML document:\n"
					. "«%s»\n"
					. "********************\n"
					. "%s\n"
					. "********************\n"
					, df_xts($th)
					, df_trim($x)
				);
			}
		}
	}
	return $r;
}

/**
 * 2018-12-19
 * @uses \Magento\Framework\Simplexml\Element::asArray() returns XML tag's attributes
 * inside an `@` key, e.g:
 *	<authorizationResponse reportGroup="1272532" customerId="admin@mage2.pro">
 *		<litleTxnId>82924701437133501</litleTxnId>
 *		<orderId>f838868475</orderId>
 *		<response>000</response>
 *		<...>
 *	</authorizationResponse>
 * will be converted to:
 * 	{
 *		"@": {
 *			"customerId": "admin@mage2.pro",
 *			"reportGroup": "1272532"
 *		},
 *		"litleTxnId": "82924701437133501",
 *		"orderId": "f838868475",
 *		"response": "000",
 * 		<...>
 *	}
 * @used-by \Dfe\Vantiv\API\Client::_construct()
 * @param string|X $x
 * @return array(string => mixed)
 * @throws E
 */
function df_xml_parse_a($x):array {return df_xml_parse($x)->asArray();}

/**
 * 2016-09-01
 * Если XML не отформатирован, то после его заголовка перенос строки идти не обязан: http://stackoverflow.com/a/8384602
 * @used-by df_xml_prettify()
 * @param string|X $x
 * @return string|null
 */
function df_xml_parse_header($x) {return df_preg_match('#^<\?xml.*\?>#', df_xml_s($x));}

/**
 * 2016-09-01
 * 2021-12-02 @deprecated It is unused.
 * @see df_xml_s()
 * @param string|X $x
 */
function df_xml_x($x):X {return $x instanceof X ? $x : df_xml_parse($x);}