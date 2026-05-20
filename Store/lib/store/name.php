<?php
use Magento\Store\Api\Data\StoreInterface as IStore;
/**
 * 2026-05-20
 * @used-by df_store_names()
 * @param int|string|null|bool|IStore $store [optional]
 */
function df_store_name($s = null):string {return df_store($s)->getName();}

/**
 * 2016-01-11
 * @see df_store_codes()  
 * @see df_category_names()
 * @used-by Dfe\SalesSequence\Config\Next\Element::rows()
 * @return string[]
 */
function df_store_names(bool $withDefault = false, bool $codeKey = false):array {return array_map(
	/** @uses df_store_name() */
	'df_store_name', df_stores($withDefault, $codeKey)
);}