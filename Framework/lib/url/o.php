<?php
use Df\Backend\Model\Url as UrlBackend;
use Magento\Backend\Model\Url as UrlBackendM;
use Magento\Backend\Model\UrlInterface as IUrlBackend;
use Magento\Framework\App\Route\Config as RouteConfig;
use Magento\Framework\App\Route\ConfigInterface as IRouteConfig;
use Magento\Framework\HTTP\PhpEnvironment\Request as Req;
use Magento\Framework\Url;
use Magento\Framework\UrlInterface as IUrl;
use Magento\Framework\Url\Helper\Data as H;

/**
 * 2020-01-18
 * @used-by df_url_path()
 */
function df_request_i(string $url):Req {return df_new_om(Req::class, ['uri' => $url]);}

/**
 * 2016-08-27 
 * @used-by df_route()
 * @return IRouteConfig|RouteConfig
 */
function df_route_config() {return df_o(IRouteConfig::class);}

/**
 * 2017-06-28
 * @used-by df_url_backend()
 */
function df_url_backend_new():UrlBackend {return df_new_om(UrlBackend::class);}

/**
 * 2020-01-19
 * @used-by df_url_param_redirect()
 */
function df_url_h():H {return df_o(H::class);}

/** @used-by df_url_frontend() */
function df_url_frontend_o():Url {return df_o(Url::class);}

/**
 * @used-by df_current_url()
 * @return IUrl|Url|IUrlBackend|UrlBackendM
 */
function df_url_o() {return df_o(IUrl::class);}