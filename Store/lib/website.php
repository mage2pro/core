<?php
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store as S;
use Magento\Store\Model\Website as W;
/**
 * 2019-11-22
 * The $v argument could be:
 * 		*) `null` or absert: the current website
 * 		*) `true`: the default website
 * @used-by df_website_code()
 * @param W|O|S|int|string|null|bool $v [optional]
 * @throws NSE|Exception
 */
function df_website($v = null):W {return $v instanceof S ? $v->getWebsite() : (
	df_is_o($v) ? $v->getStore()->getWebsite() : df_store_m()->getWebsite($v)
);}

/**
 * 2019-11-22
 * The $v argument could be:
 * 		*) `null` or absent: the current website
 * 		*) `true`: the default website
 * @used-by df_msi_website2stockId()
 * @param W|O|S|int|string|null|bool $v [optional]
 * @throws Exception
 * @throws NSE
 */
function df_website_code($v = null):string {return df_website($v)->getCode();}

/**
 * 2024-06-02
 * The $v argument could be:
 * 		*) `null` or absent: the current website
 * 		*) `true`: the default website
 * @used-by df_subscriber()
 * @param W|O|S|int|string|null|bool $v [optional]
 * @throws Exception
 * @throws NSE
 */
function df_website_id($v = null):int {return df_website($v)->getId();}