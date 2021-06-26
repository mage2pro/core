<?php
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface as IAction;
use Magento\Framework\App\Action\Forward as Forward;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\RequestInterface as IRequest;
use Magento\Framework\App\Request\Http;

/**
 * 2017-05-04
 * @used-by df_action_c_forward()
 * @used-by df_action_c_redirect()
 * @param string $c
 * @return IAction
 */
function df_action_create($c) {
	$f = df_o(ActionFactory::class); /** @var ActionFactory $f */
	return $f->create($c);
}

/**
 * 2021-05-11
 * @used-by \Dfe\Portal\Router::match()
 * @used-by \Mageplaza\Blog\Controller\Router::forward() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/190)
 * @return Forward
 */
function df_action_c_forward() {return df_action_create(Forward::class);}

/**
 * 2021-05-11
 * @used-by df_router_redirect()
 * @return Redirect
 */
function df_action_c_redirect() {return df_action_create(Redirect::class);}

/**
 * 2021-06-27
 * @used-by \Mageplaza\Blog\Controller\Router::match() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/188)
 * @used-by \TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 * @param IRequest|Http $req
 * @param string $path
 * @return Redirect
 */
function df_router_redirect(IRequest $req, $path) {
	df_response()->setRedirect(df_url_frontend($path), 301);
	$req->setDispatched(true);
	return df_action_c_redirect();
}