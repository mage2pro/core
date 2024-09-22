<?php
/**
 * 2019-01-11
 * 2019-11-15 https://stackoverflow.com/a/1253417
 * @used-by \Inkifi\Consolidation\Controller\Adminhtml\Index\Index::execute()
 */
function df_is_guid(string $s):bool {return 36 === strlen($s) && preg_match(
	'#^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$#', $s
);}