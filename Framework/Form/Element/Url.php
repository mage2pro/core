<?php
namespace Df\Framework\Form\Element;
use Df\Framework\Form\Element;
// 2016-05-30
abstract class Url extends Element {
	/**
	 * 2016-05-31
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	abstract protected function messageForThirdPartyLocalhost();
	/**
	 * 2016-05-30
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @return string
	 */
	public function getElementHtml() {
		/**
		 * 2016-06-07
		 * 'id' => $this->getId() нужно для совместимости с 2.0.6,
		 * иначе там сбой в выражении inputs = $(idTo).up(this._config.levels_up)
		 * https://mail.google.com/mail/u/0/#search/maged%40wrapco.com.au/15510135c446afdb
		 */
		return df_tag('div', ['class' => 'df-url', 'id' => $this->getId()],
			$this->thirdPartyLocalhost()
			? $this->messageForThirdPartyLocalhost()
			: $this->messageForOthers()
		);
	}

	/**
	 * 2016-05-30
	 * @override
	 * @see \Df\Framework\Form\Element::getComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/a5fa3af3/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
		if ((string)$element->getComment()) {
			$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
		}
	 */
	public function getComment() {return $this->thirdPartyLocalhost() ? null : parent::getComment();}

	/**
	 * 2016-05-31
	 * @used-by \Df\Framework\Form\Element\Url::getElementHtml()
	 * @return string
	 */
	protected function messageForOthers() {
		return !$this->requireHttps() || df_check_https($this->url())
			? $this->url()
			: 'Looks like your <a href="https://mage2.pro/t/topic/1723" target="_blank">'
			 . '«<b>General</b>» → «<b>Web</b>» → «<b>Base URLs (Secure)</b> '
			 . ' → «<b>Secure Base URL</b>»</a>'
			 . ' option is misconfigured (does not start with «https»).'
		;
	}

	/**
	 * 2016-05-30
	 * @return string|null
	 */
	protected function routePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(df_fe_fc($this, 'dfWebhook_routePath'));
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-05-30
	 * 2016-05-31
	 * https://mage2.pro/tags/secure-url
	 * @see \Magento\Framework\Url::getBaseUrl()
	 * https://github.com/magento/magento2/blob/a5fa3af3/lib/internal/Magento/Framework/Url.php#L437-L439
		if (isset($params['_secure'])) {
			$this->getRouteParamsResolver()->setSecure($params['_secure']);
		}
	 * @return string
	 */
	protected function url() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_my_local() ? $this->urlForMyLocalPc() : $this->urlForOthers()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-31
	 * @return string
	 */
	protected function urlForMyLocalPc() {
		return df_cc_url_t('https://mage2.pro/sandbox', $this->routePath());
	}

	/**
	 * 2016-05-31
	 * @return string
	 */
	protected function urlForOthers() {
		return df_url_frontend($this->routePath(), ['_secure' => $this->requireHttps() ? true : null]);
	}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	private function requireHttps() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_fe_fc_b($this, 'dfWebhook_requireHTTPS') && !df_is_localhost();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	private function thirdPartyLocalhost() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_is_localhost() && !df_my_local();
		}
		return $this->{__METHOD__};
	}
}