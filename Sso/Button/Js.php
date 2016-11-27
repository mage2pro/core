<?php
namespace Df\Sso\Button;
abstract class Js extends \Df\Sso\Button {
	/**
	 * 2016-11-26
	 * @override
	 * @see \Df\Sso\Button::htmlN()
	 * @used-by \Df\Sso\Button::html()
	 * @return string
	 */
	protected function htmlN() {return '';}

	/**
	 * 2016-11-26
	 * @override
	 * @see \Df\Sso\Button::htmlU()
	 * @used-by \Df\Sso\Button::html()
	 * @return string
	 */
	protected function htmlU() {return '';}

	/**
	 * 2016-11-26
	 * @used-by loggedOut()
	 * @return array(string => mixed)
	 */
	protected function jsOptions() {return [];}

	/**
	 * 2016-11-27
	 * @override
	 * @see \Df\Sso\Button::lHref()
	 * @used-by \Df\Sso\Button::htmlL()
	 * @return string
	 */
	final protected function lHref() {return 'javascript:void(0)';}

	/**
	 * 2016-11-26
	 * @override
	 * @see \Df\Sso\Button::loggedOut()
	 * @see \Dfe\FacebookLogin\Button::loggedOut()
	 * @used-by \Df\Sso\Button::_toHtml()
	 * @return string
	 */
	protected function loggedOut() {return
		df_x_magento_init($this, 'button', df_nta($this['dfJsOptions']) + $this->jsOptions() + [
			'domId' => $this->id()
			,'redirect' => $this->getUrl(df_route($this),
				 df_clean(['_secure' => $this->redirectShouldBeSecure()], false)
			)
			,'type' => $this->s()->type()
		])
		.parent::loggedOut()
	;}

	/**
	 * 2016-11-26
	 * @see \Dfe\AmazonLogin\Button::redirectShouldBeSecure()
	 * @used-by loggedOut()
	 * @return bool
	 */
	protected function redirectShouldBeSecure() {return false;}
}