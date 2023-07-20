<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element;
/**
 * 2016-05-30
 * @see \Dfe\Amazon\FE\JsOrigin
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
	function getComment():string {return $this->thirdPartyLocalhost() ? '' : parent::getComment();}

	/**
	 * 2016-05-30
	 * 2016-06-07
	 * 'id' => $this->getId() нужно для совместимости с 2.0.6,
	 * иначе там сбой в выражении inputs = $(idTo).up(this._config.levels_up)
	 * https://mail.google.com/mail/u/0/#search/maged%40wrapco.com.au/15510135c446afdb
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
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
	 */
	function getElementHtml():string {return df_tag('div', ['class' => 'df-url', 'id' => $this->getId()],
		$this->thirdPartyLocalhost() ? $this->messageForThirdPartyLocalhost() : $this->messageForOthers()
	);}
	
	/**
	 * 2016-05-31
	 * @used-by self::getElementHtml()
	 * @see \Df\Framework\Form\Element\Webhook::messageForThirdPartyLocalhost()
	 */
	protected function messageForThirdPartyLocalhost():string {return $this->messageForOthers();}

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
	 * @used-by self::messageForOthers()
	 * @see \Dfe\Amazon\FE\JsOrigin::url() 
	 * @see \Df\Payment\FE\CustomerReturn::url()
	 * @see \Df\Sso\FE\CustomerReturn::url()
	 */
	protected function url():string {return df_webhook(
		# 2023-07-20
		# «df_webhook(): Argument #2 ($suffix) must be of type string, null given,
		# called in vendor/mage2pro/core/Framework/Form/Element/Url.php on line 94»: https://github.com/mage2pro/core/issues/236
		$this->m(), df_fe_fc($this, 'dfWebhook_suffix', ''), $this->requireHttps()
	);}

	/**
	 * 2016-05-30
	 * @used-by self::messageForOthers()
	 * @used-by self::url()
	 * @used-by \Dfe\Amazon\FE\JsOrigin::url()
	 */
	final protected function requireHttps():bool {return dfc($this, function() {return
		!df_is_localhost() && df_fe_fc_b($this, 'dfWebhook_requireHTTPS')
	;});}

	/**
	 * 2017-04-12
	 * @used-by \Df\Payment\FE\CustomerReturn::routePath()
	 * @used-by \Df\Sso\FE\CustomerReturn::url()
	 */
	final protected function m():string {return dfc($this, function() {return df_fe_m($this);});}

	/**
	 * 2016-05-31
	 * @used-by self::getElementHtml()
	 * @used-by self::messageForThirdPartyLocalhost()
	 */
	private function messageForOthers():string {$url = $this->url(); return
		!$this->requireHttps() || df_check_https_strict($url) ? $url :
			'Looks like your <a href="https://mage2.pro/t/1723" target="_blank">'
			.'«<b>General</b>» → «<b>Web</b>» → «<b>Base URLs (Secure)</b>'
			.' → «<b>Secure Base URL</b>»</a>'
			.' option is misconfigured (does not start with «<b>https</b>»).'
	;}

	/**
	 * 2016-05-30
	 * @used-by self::getComment()
	 * @used-by self::getElementHtml()
	 */
	private function thirdPartyLocalhost():bool {return dfc($this, function() {return df_is_localhost() && !df_my();});}
}