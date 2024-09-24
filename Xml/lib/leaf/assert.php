<?php
use Df\Core\Exception as E;
use SimpleXMLElement as X;

/**
 * @throws E
 */
function df_xml_assert_leaf(X $x):X {return df_xml_is_leaf($x) ? $x : df_error(
	"Требуется лист XML, однако получена ветка XML:\n%s.", df_xml_s($x)
);}