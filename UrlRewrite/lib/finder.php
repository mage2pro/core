<?php
use Magento\UrlRewrite\Model\Storage\DbStorage;
use Magento\UrlRewrite\Model\UrlFinderInterface as IFinder;
/**
 * 2020-01-18 It can return also @see \Magento\UrlRewrite\Model\CompositeUrlFinder in Magento 2.3.3.
 * @used-by Frugue\Store\Plugin\UrlRewrite\Model\StoreSwitcher\RewriteUrl::aroundSwitch()
 * @used-by Frugue\Store\Plugin\UrlRewrite\Model\StoreSwitcher\RewriteUrl::findCurrentRewrite()
 * @used-by TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 * @return IFinder|DbStorage
 */
function df_url_finder() {return df_o(IFinder::class);}