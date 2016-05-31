<?php
namespace Df\Framework\Data\Form\Element;
use Df\Framework\Data\Form\Element;
// 2016-05-30
class Webhook extends Element {
	/**
	 * 2016-05-30
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @return string
	 */
	public function getElementHtml() {
		return df_tag('div', 'df-webhook',
			$this->thirdPartyLocalhost()
			? 'The notifications are not available,'
			. ' because the store is running on <b>localhost</b>.'
			: (
				!$this->requireHttps() || df_uri_check_https($this->url())
				? $this->url()
				: 'Looks like your <a href="https://mage2.pro/t/topic/1723" target="_blank">'
				 . '«<b>General</b>» → «<b>Web</b>» → «<b>Base URLs (Secure)</b> '
				 . ' → «<b>Secure Base URL</b>»</a>'
				 . ' option is misconfigured (does not start with «https»).'
			)
		);
	}

	/**
	 * 2016-05-30
	 * @override
	 * @see \Df\Framework\Data\Form\Element::getComment()
	 * @used-by \Magento\Config\Block\System\Config\Form\Field::_renderValue()
	 * https://github.com/magento/magento2/blob/a5fa3af3/app/code/Magento/Config/Block/System/Config/Form/Field.php#L82-L84
		if ((string)$element->getComment()) {
			$html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
		}
	 */
	public function getComment() {return $this->thirdPartyLocalhost() ? null : parent::getComment();}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	private function requireHttps() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_fe_fc_b($this, 'dfWebhook_requireHTTPS') && !$this->thirdPartyLocalhost()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-30
	 * @return string
	 */
	private function routePath() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_fe_fc($this, 'dfWebhook_routePath');
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	private function thirdPartyLocalhost() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_is_localhost() && !df_is_it_my_local_pc();
		}
		return $this->{__METHOD__};
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
	private function url() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_is_it_my_local_pc()
				? "https://mage2.pro/sandbox/{$this->routePath()}/"
				: df_url_frontend($this->routePath(), [
					'_secure' => $this->requireHttps() ? true : null
				])
			;
		}
		return $this->{__METHOD__};
	}
}