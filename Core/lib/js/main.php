<?php
/**
 * 2015-10-26 https://mage2.pro/t/145
 * 2016-11-28
 * An example:
 * https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Theme/view/frontend/templates/js/cookie.phtml#L16-L26
 * Such syntax (unlike @see df_widget() ) does not allow us to pass a DOM element as the second argument:
 * https://github.com/magento/magento2/blob/2.1.2/lib/web/mage/apply/main.js#L69-L70
 * 2017-04-21
 * 1) This function does not associate the JavaScript code with any DOM element.
 * If you want such association, then use @see df_widget() instead.
 * 2) $m could be:
 * 2.1) A module name: «A_B»
 * 2.2) A class name: «A\B\C».
 * 2.3) An object: it comes down to the case 2 via @see get_class()
 * 2.4) 2017-10-16: `null`, if $script is an absolute URL.
 * @used-by df_fe_init()
 * @used-by df_js_c()
 * @used-by \CanadaSatellite\Amelia\Block::_toHtml() (canadasatellite.ca, https://github.com/canadasatellite-ca/amelia/issues/1)
 * @used-by \Df\Checkout\B\Messages::_toHtml()
 * @used-by \Df\Checkout\B\Payment::_toHtml()
 * @used-by \Df\Intl\Js::_toHtml()
 * @used-by \Dfe\Phone\Js::_toHtml()
 * @used-by \Df\Sso\Css::_toHtml()
 * @used-by \Dfe\AmazonLogin\Button::loggedIn()
 * @used-by \Dfe\Customer\Block::_toHtml()
 * @used-by \Dfe\Markdown\FormElement::getAfterElementHtml()
 * @used-by \Dfe\Sift\Js::_toHtml()
 * @used-by \Dfe\Stripe\Block\Js::_toHtml()
 * @used-by \KingPalm\B2B\Block\RegionJS\Backend::_toHtml()
 * @used-by \RWCandy\Captcha\Js()
 * @used-by \TFC\Core\B\Category::_toHtml() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/30)
 * @used-by \TFC\Core\B\Checkout\Success::_toHtml() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/42)
 * @param string|object|null $m
 * @param array(string => mixed) $p [optional]
 */
function df_js($m, string $s = '', array $p = []):string {$s = $s ?: 'main'; return df_js_x(
	'*', df_is_url_absolute($s) ? null : $m, $s, $p
);}