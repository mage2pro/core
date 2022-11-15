<?php
use Magento\Webapi\Controller\Rest\InputParamsResolver as IPR;
use Magento\Webapi\Controller\Rest\Router\Route;
/**
 * 2017-03-15
 * @used-by df_sentry()
 */
function df_rest_action():string {
	$r = df_rest_route(); /** @var Route $r */
	return df_cc_method($r->getServiceClass(), $r->getServiceMethod());
}

/**
 * 2017-03-15
 * @used-by df_rest_route()
 * @return IPR
 */
function df_rest_ipr() {return df_o(IPR::class);}

/**
 * 2017-03-15
 * @used-by df_rest_action()
 * @return Route
 */
function df_rest_route() {return df_rest_ipr()->getRoute();}