<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element;
/**
 * 2016-05-30
 * @see \Df\Amazon\FE\JsOrigin
 * @see \Df\Framework\Form\Element\Webhook 
 * @see \Df\Payment\FE\CustomerReturn
 * @see \Df\Sso\FE\CustomerReturn
 */
abstract class Url extends Element {
	/**
	 * 2016-05-30
	 * @override
	 * @see \Df\Framework\Form\Element::getComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/a5fa3af3/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
	 *	if ((string)$element->getComment()) {
	 *		$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
	 *	}
	 */
	function getComment() {return $this->thirdPartyLocalhost() ? null : parent::getComment();}

	/**
	 * 2016-05-30
	 * 2016-06-07
	 * 'id' => $this->getId() нужно для совместимости с 2.0.6,
	 * иначе там сбой в выражении inputs = $(idTo).up(this._config.levels_up)
	 * https://mail.google.com/mail/u/0/#search/maged%40wrapco.com.au/15510135c446afdb
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @return string
	 */
	function getElementHtml() {return df_tag('div', ['class' => 'df-url', 'id' => $this->getId()],
		$this->thirdPartyLocalhost() ? $this->messageForThirdPartyLocalhost() : $this->messageForOthers()
	);}

	/**
	 * 2016-05-31
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	protected function messageForOthers() {$url = $this->url(); return
		!$this->requireHttps() || df_check_https_strict($url) ? $url :
			'Looks like your <a href="https://mage2.pro/t/1723" target="_blank">'
			.'«<b>General</b>» → «<b>Web</b>» → «<b>Base URLs (Secure)</b>'
			.' → «<b>Secure Base URL</b>»</a>'
			.' option is misconfigured (does not start with «<b>https</b>»).'
	;}
	
	/**
	 * 2016-05-31
	 * @used-by getElementHtml()  
	 * @see \Df\Framework\Form\Element\Webhook::messageForThirdPartyLocalhost()
	 * @return string
	 */
	protected function messageForThirdPartyLocalhost() {return $this->messageForOthers();}

	/**
	 * 2016-05-30
	 * 2016-05-31
	 * https://mage2.pro/tags/secure-url
	 * @see \Magento\Framework\Url::getBaseUrl()
	 * https://github.com/magento/magento2/blob/a5fa3af3/lib/internal/Magento/Framework/Url.php#L437-L439
	 *	if (isset($params['_secure'])) {
	 *		$this->getRouteParamsResolver()->setSecure($params['_secure']);
	 *	}
	 * @used-by messageForOthers()
	 * @see \Df\Amazon\FE\JsOrigin::url() 
	 * @see \Df\Payment\FE\CustomerReturn::url()
	 * @see \Df\Sso\FE\CustomerReturn::url()
	 * @return string
	 */
	protected function url() {return df_webhook($this->m(), '', $this->requireHttps());}

	/**
	 * 2016-05-30
	 * @used-by messageForOthers()
	 * @used-by url()
	 * @used-by \Df\Amazon\FE\JsOrigin::url()
	 * @return bool
	 */
	final protected function requireHttps() {return dfc($this, function() {return
		!df_is_localhost() && df_fe_fc_b($this, 'dfWebhook_requireHTTPS')
	;});}

	/**
	 * 2017-04-12
	 * @used-by \Df\Payment\FE\CustomerReturn::routePath()
	 * @used-by \Df\Sso\FE\CustomerReturn::url()
	 * @return string
	 */
	final protected function m() {return dfc($this, function() {return df_fe_m($this);});}

	/**
	 * 2016-05-30
	 * @used-by getComment()
	 * @used-by getElementHtml()
	 * @return bool
	 */
	private function thirdPartyLocalhost() {return dfc($this, function() {return
		df_is_localhost() && !df_my()
	;});}
}