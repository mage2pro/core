<?php
namespace Df\Sso\Button;
abstract class Js extends \Df\Sso\Button {
	/**
	 * 2016-11-26
	 * @override
	 * @see \Df\Sso\Button::htmlL()
	 * @used-by \Df\Sso\Button::html()
	 * @return string
	 */
	protected function htmlL() {return '';}

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
	 * 2016-11-26
	 * @override
	 * @see \Df\Sso\Button::loggedOut()
	 * @used-by \Df\Sso\Button::_toHtml()
	 * @return string
	 */
	protected function loggedOut() {return
		df_x_magento_init($this, 'button', df_nta($this['dfJsOptions']) + $this->jsOptions() + [
			'domId' => $this->id()
			,'redirect' => $this->getUrl(df_route($this),
				 df_clean(['_secure' => $this->redirectShouldBeSecure()], false)
			)
		])
		.parent::loggedOut()
	;}

	/**
	 * 2016-11-26
	 * @used-by loggedOut()
	 * @return bool
	 */
	protected function redirectShouldBeSecure() {return false;}
}