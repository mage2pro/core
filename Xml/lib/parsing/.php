<?php
use Closure as F;
use Df\Core\Exception as E;
use Df\Xml\G;
use SimpleXMLElement as X;
use Throwable as T;

/**
 * 2024-09-22
 * 1) It returns:
 * 		@see G if $x is a string
 * 		$x unchanged if $x is @see X
 * 2) "Refactor the `Df_Xml` module": https://github.com/mage2pro/core/issues/437
 * @used-by df_module_name_by_path()
 * @used-by df_xml_node()
 * @used-by df_xml_parse_a()
 * @used-by df_xml_prettify()
 * @used-by df_xml_x()
 * @used-by \Df\Xml\G2::__construct()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param X|string $x
 * @param F|T|bool|mixed $onE
 * @return G|X
 * @throws E
 */
function df_xml_x($x, $onE = true):X {return $x instanceof X ? $x : df_try(
	function() use($x) {return new G(df_assert_sne($x, 0));}
	, true !== $onE ? $onE : function(T $t) use($x):E {return df_error_create(
		"Failed to parse XML: «%s».\nXML:\n%s", df_xts($t), df_trim($x)
	);}
);}