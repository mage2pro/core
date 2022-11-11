<?php
namespace Df\Sso;
use Df\Customer\Session as Sess;
use Magento\Framework\View\Element\AbstractBlock as _P;
/**
 * 2016-12-04
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by https://github.com/mage2pro/core/blob/2.3.3/Sso/view/frontend/layout/customer_account_create.xml#L18
Sso/view/frontend/layout/customer_account_login.xml#L18
 * @used-by https://github.com/mage2pro/core/blob/2.3.3/Sso/view/frontend/layout/customer_account_login.xml#L18
 */
class Css extends _P {
	/**
	 * 2016-12-04
	 * @override
	 * @see _P::_toHtml()
	 * @used-by _P::toHtml():
	 *		$html = $this->_loadCache();
	 *		if ($html === false) {
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->suspend($this->getData('translate_inline'));
	 *			}
	 *			$this->_beforeToHtml();
	 *			$html = $this->_toHtml();
	 *			$this->_saveCache($html);
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->resume();
	 *			}
	 *		}
	 *		$html = $this->_afterToHtml($html);
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L643-L689
	 */
	final protected function _toHtml():string {
		$hhl = df_style_inline_hide('.header.links', '#switcher-currency'); /** @var string $hhl */
		return self::isAccConfirmation() ? $hhl . df_style_inline_hide('.login-container') :
			(self::isRegCompletion() ? $hhl . df_js(__CLASS__, 'reg-completion') : '')
	;}

	/**
	 * 2016-12-04 The result should be cached because the method resets the `needConfirm` value.
	 * @used-by self::_toHtml()
	 * @used-by \Df\Sso\Button::_toHtml()
	 */
	final static function isAccConfirmation():bool {return dfcf(function() {
		$s = Sess::s(); /** @var Sess $s */
		if ($r = df_is_login() && $s->needConfirm()) { /** @var bool $r */
			$s->needConfirm(false);
		}
		return $r;
	});}

	/**
	 * 2016-12-02
	 * Случай, когда покупатель авторизовался в провайдере SSO,
	 * но информации провайдера SSO недостаточно для автоматической регистрации покупателя в Magento.
	 * В этом случае:
	 * 1) метод @see \Df\Sso\CustomerReturn::execute() перенаправляет покупателя на страницу регистрации.
	 * 2) мы не показываем наши кнопки SSO,
	 * а также скрываем из шапки стандартные ссылки «Sign In», «Create an Account» и блок выбора валюты.
	 */
	final static function isRegCompletion():bool {return dfcf(function() {return df_is_reg() && Sess::s()->ssoProvider();});}
}