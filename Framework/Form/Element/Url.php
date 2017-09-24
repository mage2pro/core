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
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Df\Framework\Form\Element::getComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Block/System/Config/Form/Field.php#L79-L81
	 *	if ((string)$element->getComment()) {
	 *		$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
	 *	}
	 */
	function getComment() {return $this->thirdPartyLocalhost() ? null : parent::getComment();}

	/**
	 * 2016-05-30
	 * 2016-06-07
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * 'id' => $this->getId() нужно для совместимости с 2.0.6,
	 * иначе там сбой в выражении inputs = $(idTo).up(this._config.levels_up)
	 * https://mail.google.com/mail/u/0/#search/maged%40wrapco.com.au/15510135c446afdb
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getDefaultHtml():
	 *		public function getDefaultHtml() {
	 *			$html = $this->getData('default_html');
	 *			if ($html === null) {
	 *				$html = $this->getNoSpan() === true ? '' : '<div class="admin__field">' . "\n";
	 *				$html .= $this->getLabelHtml();
	 *				$html .= $this->getElementHtml();
	 *				$html .= $this->getNoSpan() === true ? '' : '</div>' . "\n";
	 *			}
	 *			return $html;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L426-L441
	 * @return string
	 */
	function getElementHtml() {return df_tag('div', ['class' => 'df-url', 'id' => $this->getId()],
		$this->thirdPartyLocalhost() ? $this->messageForThirdPartyLocalhost() : $this->messageForOthers()
	);}
	
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
	 * 2017-09-24
	 * Currently, «dfWebhook_suffix» is never used.
	 * Previously, it was used by the Yandex.Kassa extension:
	 *	<field
	 *		dfWebhook_requireHTTPS='true'
	 *		dfWebhook_suffix='check'
 	 *		<...>
	 *		type='Df\Framework\Form\Element\Webhook'
	 *	>
	 *		<label>checkUrl</label>
	 *		<...>
	 *	</field>
	 *	<field
	 *		dfWebhook_requireHTTPS='true'
	 *		dfWebhook_suffix='confirm'
	 *		<...>
	 *		type='Df\Framework\Form\Element\Webhook'
	 *	>
	 *		<label>avisoUrl</label>
	 *		<...>
	 *	</field>
	 * https://github.com/mage2pro/yandex-kassa/blob/0.1.9/etc/adminhtml/system.xml#L106-L131
	 * @used-by messageForOthers()
	 * @see \Df\Amazon\FE\JsOrigin::url() 
	 * @see \Df\Payment\FE\CustomerReturn::url()
	 * @see \Df\Sso\FE\CustomerReturn::url()
	 * @return string
	 */
	protected function url() {return df_webhook(
		$this->m(), df_fe_fc($this, 'dfWebhook_suffix'), $this->requireHttps()
	);}

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
	 * 2016-05-31
	 * @used-by getElementHtml()
	 * @used-by messageForThirdPartyLocalhost()
	 * @return string
	 */
	private function messageForOthers() {$url = $this->url(); return
		!$this->requireHttps() || df_check_https_strict($url) ? $url :
			'Looks like your <a href="https://mage2.pro/t/1723" target="_blank">'
			.'«<b>General</b>» → «<b>Web</b>» → «<b>Base URLs (Secure)</b>'
			.' → «<b>Secure Base URL</b>»</a>'
			.' option is misconfigured (does not start with «<b>https</b>»).'
	;}

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