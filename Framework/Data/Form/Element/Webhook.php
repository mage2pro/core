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
			!$this->enabled()
			? 'The notifications are not available,'
			. ' because the store is running on <b>localhost</b>.'
			: (
				!$this->requireHttps() || df_uri_check_https($this->url())
				? $this->url()
				: ''
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
	public function getComment() {return !$this->enabled() ? null : parent::getComment();}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	private function enabled() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !df_is_localhost() || df_is_it_my_local_pc();
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-30
	 * @return bool
	 */
	private function requireHttps() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_fe_fc_b($this, 'dfWebhook_requireHTTPS');
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
	 * @return string
	 */
	private function url() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = df_url_frontend($this->routePath(), ['_secure' => $this->requireHttps()]);
			if (df_is_it_my_local_pc()) {
				$result = str_replace(
					'http://localhost.com:900/store/'
					, 'https://mage2.pro/sandbox/'
					, $result
				);
			}
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}