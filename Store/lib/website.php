<?php
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website as W;
/**
 * 2019-11-22
 * The $v argument could be one of:
 * 		*) a website: W
 * 		*) a store: Store
 * 		*) a website's ID: int
 * 		*) a website's code: string
 * 		*) `null` or absert: the current website
 * 		*) `true`: the default website
 * @used-by df_website_code()
 * @param W|Store|int|string|null|bool $v [optional]
 * @throws NSE|Exception
 */
function df_website($v = null):W {return $v instanceof Store ? $v->getWebsite() : df_store_m()->getWebsite($v);}

/**
 * 2019-11-22
 * The $v argument could be one of:
 * 		*) a website: W
 * 		*) a store: Store
 * 		*) a website's ID: int
 * 		*) a website's code: string
 * 		*) `null` or absent: the current website
 * 		*) `true`: the default website
 * @used-by df_msi_website2stockId()
 * @param W|Store|int|string|null|bool $v [optional]
 * @throws Exception
 * @throws NSE
 */
function df_website_code($v = null):string {return df_website($v)->getCode();}