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
 */
function df_action_create($c):IAction {
	$f = df_o(ActionFactory::class); /** @var ActionFactory $f */
	return $f->create($c);
}

/**
 * 2021-05-11
 * @used-by \Dfe\Portal\Router::match()
 * @used-by \Mageplaza\Blog\Controller\Router::forward() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/190)
 */
function df_action_c_forward():Forward {return df_action_create(Forward::class);}

/**
 * 2021-05-11
 * @used-by df_router_redirect()
 */
function df_action_c_redirect():Redirect {return df_action_create(Redirect::class);}

/**
 * 2021-06-27
 * @used-by \Mageplaza\Blog\Controller\Router::match() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/188)
 * @used-by \TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 * @param IRequest|Http $req
 * @param string $path
 */
function df_router_redirect(IRequest $req, $path):Redirect {
	df_response()->setRedirect(df_url_frontend($path), 301);
	$req->setDispatched(true);
	return df_action_c_redirect();
}