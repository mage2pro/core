<?php
use Df\Backend\Model\Url as UrlBackend;
use Magento\Backend\Model\UrlInterface as IUrlBackend;
use Magento\Framework\App\Route\Config as RouteConfig;
use Magento\Framework\App\Route\ConfigInterface as IRouteConfig;
use Magento\Framework\HTTP\PhpEnvironment\Request as Req;
use Magento\Framework\Url;
use Magento\Framework\Url\Helper\Data as H;
use Magento\Framework\UrlInterface as IUrl;

/**
 * 2020-01-18
 * @used-by df_url_path()
 * @param string $url
 * @return Req
 */
function df_request_i($url) {return df_new_om(Req::class, ['uri' => $url]);}

/**
 * 2016-08-27 
 * @used-by df_route()
 * @return IRouteConfig|RouteConfig
 */
function df_route_config() {return df_o(IRouteConfig::class);}

/**
 * 2017-06-28
 * @return UrlBackend
 */
function df_url_backend_new() {return df_new_om(UrlBackend::class);}

/** @return UrlBackend */
function df_url_backend_o() {return df_o(UrlBackend::class);}

/**
 * 2020-01-19
 * @used-by df_url_param_redirect()
 * @return H
 */
function df_url_h() {return df_o(H::class);}

/** @return Url */
function df_url_frontend_o() {return df_o(Url::class);}

/** @return IUrl|Url|IUrlBackend|UrlBackend */
function df_url_o() {return df_o(IUrl::class);}