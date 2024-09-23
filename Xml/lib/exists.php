<?php
use SimpleXMLElement as X;

/**
 * http://stackoverflow.com/questions/1560827#comment20135428_1562158
 * @used-by df_xml_children()
 */
function df_xml_exists(X $e, string $child):bool {return isset($e->{$child});}