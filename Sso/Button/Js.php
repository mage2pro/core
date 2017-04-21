<?php
namespace Df\Sso\Button;
/**
 * 2016-11-28
 * @see \Dfe\AmazonLogin\Button
 * @see \Dfe\FacebookLogin\Button
 */
abstract class Js extends \Df\Sso\Button {
	/**
	 * 2016-11-28
	 * @override
	 * @see \Df\Sso\Button::attributes()
	 * @used-by \Df\Sso\Button::loggedOut()
	 * @see \Dfe\FacebookLogin\Button::attributes()
	 * @return array(string => string)
	 */
	protected function attributes() {return df_widget(
		$this, 'button', $this->jsOptions() + [
			'redirect' => $this->getUrl(df_route($this), df_clean([
				'_secure' => $this->redirectShouldBeSecure()], false
			))
			,'type' => $this->s()->type()
		]
	) + parent::attributes();}

	/**
	 * 2016-11-30
	 * Чтобы кнопка native при загрузке елозила по экрану,
	 * мы в разметке изначально указываем ['style' => 'display:none'],
	 * а затем уже после загрузки JavaScript удаляем это значение атрибута «style».
	 * @override
	 * @see \Df\Sso\Button::attributesN()
	 * @used-by \Df\Sso\Button::attributes()
	 * @return array(string => string)
	 */
	protected function attributesN() {return ['style' => 'display:none'];}

	/**
	 * 2016-11-26
	 * @used-by attributes()
	 * @see \Dfe\AmazonLogin\Button::jsOptions()
	 * @return array(string => mixed)
	 */
	protected function jsOptions() {return ['selector' => ".{$this->cssClass()}"];}

	/**
	 * 2016-11-27
	 * @override
	 * @see \Df\Sso\Button::lHref()
	 * @used-by \Df\Sso\Button::attributes()
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