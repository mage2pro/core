<?php
use Df\Core\Exception as DFE;

/**
 * 2017-03-16
 * 2022-02-23
 * 1) Sometimes @see df_action_has() does not work because the following methods are not yet called by Magento:
 * @see \Magento\Framework\App\Request\Http::setRouteName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setActionName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setControllerName()
 * In this case, use @see df_rp_has().
 * @see df_rp_has()
 * @used-by \Dfe\AllPay\W\Event\Offline::ttCurrent()
 */
function df_action_has(string $s):bool {return df_contains(df_action_name(), $s);}

/**
 * 2016-01-07
 * @see df_rp_has()
 * @used-by df_config_field()
 * @used-by \Dfe\Markdown\Modifier::modifyData()
 * @used-by \Inkifi\Consolidation\Plugin\Backend\Block\Widget\Button\Toolbar::beforePushButtons()
 * @used-by \SayItWithAGift\Core\Plugin\Newsletter\Model\Subscriber::beforePrepare()
 * @used-by \Wolf\Filter\Observer\ControllerActionPredispatch::execute()
 * @used-by vendor/wolfautoparts.com/filter/view/frontend/templates/sidebar.phtml
 */
function df_action_is(string ...$names):bool {return ($a = df_action_name()) && in_array($a, dfa_flatten($names));}

/**
 * 2015-09-02
 * 2017-03-15 @uses \Magento\Framework\App\Request\Http::getFullActionName() returns «__» in the CLI case.
 * 2022-02-23
 * The function returns «__» is the  following methods are not yet called by Magento:
 * @see \Magento\Framework\App\Request\Http::setRouteName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setActionName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setControllerName()
 * In this case, use `df_request_o()->getPathInfo()`: @see df_rp_has()
 * @used-by df_action_has()
 * @used-by df_action_is()
 * @used-by df_sentry()
 * @used-by \Dfe\Markdown\CatalogAction::entityType()
 * @used-by \Dfe\Markdown\FormElement::config()
 * @return string|null
 * @throws DFE
 */
function df_action_name() {return df_is_cli() ? null : df_assert_ne('__', df_request_o()->getFullActionName(),
	'`Magento\Framework\App\Request\Http::getFullActionName()` is called too early'
	. ' (the underlying object is not yet initialized).'
);}

/**
 * 2017-08-28
 * @used-by df_is_checkout()
 * @used-by df_is_checkout_multishipping()
 * @used-by df_is_system_config()
 * @used-by \DxMoto\Core\Plugin\Amasty\Finder\Observer\LayoutRender::aroundExecute()
 * @param string|string[] $p
 */
function df_action_prefix($p):bool {return df_starts_with(df_action_name(), $p);}

/**
 * 2019-12-26
 * @see \Magento\Store\App\Response\Redirect::getRefererUrl():
 * 		df_response_redirect()->getRefererUrl()
 * @used-by df_context()
 * @used-by df_referer_ends_with()
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Framework\App\Http::aroundLaunch() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/72)
 * @used-by https://github.com/royalwholesalecandy/core/issues/58#issuecomment-569049731
 */
function df_referer():string {return dfa($_SERVER, 'HTTP_REFERER');}

/**
 * 2019-11-04
 * @see df_redirect_back()
 * @used-by \PPCs\Core\Plugin\Amazon\Payment\Observer\AddAmazonButton::aroundExecute()
 * @used-by \PPCs\Core\Plugin\Quote\Model\QuoteRepository::aroundGetActiveForCustomer()
 * @param string $s
 */
function df_referer_ends_with($s):bool {return df_ends_with(df_referer(), $s);}