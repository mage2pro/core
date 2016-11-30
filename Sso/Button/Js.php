<?php
namespace Df\Sso\Button;
abstract class Js extends \Df\Sso\Button {
	/**
	 * 2016-11-28
	 * @override
	 * @see \Df\Sso\Button::attributes()
	 * @see \Dfe\FacebookLogin\Button::attributes()
	 * @used-by \Df\Sso\Button::loggedOut()
	 * @return array(string => string)
	 */
	protected function attributes() {return df_x_magento_init_att(
		$this, 'button', $this->jsOptions() + [
			'redirect' => $this->getUrl(df_route($this),
				 df_clean(['_secure' => $this->redirectShouldBeSecure()], false)
			)
			,'type' => $this->s()->type()
		]
	) + parent::attributes();}

	/**
	 * 2016-11-26
	 * @used-by loggedOut()
	 * @used-by \Dfe\AmazonLogin\Button::jsOptions()
	 * @see \Dfe\AmazonLogin\Button::jsOptions()
	 * @return array(string => mixed)
	 */
	protected function jsOptions() {return ['selector' => ".{$this->cssClass()}"];}

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
	 * @see \Dfe\AmazonLogin\Button::redirectShouldBeSecure()
	 * @used-by attributes()
	 * @return bool
	 */
	protected function redirectShouldBeSecure() {return false;}
}