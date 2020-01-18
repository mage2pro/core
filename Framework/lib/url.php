<?php
use Magento\Framework\HTTP\PhpEnvironment\Request as Req;
/**
 * 2020-01-18
 * @used-by \Frugue\Store\Plugin\UrlRewrite\Model\StoreSwitcher\RewriteUrl::aroundSwitch()
 * @param string $url
 * @return Req
 */
function df_request_i($url) {return df_new_om(Req::class, ['uri' => $url]);}