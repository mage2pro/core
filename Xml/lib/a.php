<?php
use Df\Core\Exception as E;
use SimpleXMLElement as X;

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
 * @param X|string $x
 * @return array(string => mixed)
 * @throws E
 */
function df_xml2a($x):array {
	$x = df_xml_x($x);
	$r = [];
	foreach ($x->attributes() as $k => $v) {/** @var string $k */ /** @var mixed $v */
		if ($v) {
			$r['@'][$k] = (string)$v;
		}
	}
	// add children values
	if ($x->hasChildren()) {
		foreach ($x->children() as $childName => $child) {
			$r[$childName] = df_xml2a($child);
		}
	}
	else {
		if (empty($r)) {
			// return as string, if nothing was found
			$r = (string)$x;
		} else {
			// value has zero key element
			$r[0] = (string)$x;
		}
	}
	return $r;
}