<?php
namespace Df\Sales\Plugin\Helper;
use Magento\Sales\Helper\Admin as Sb;
// 2016-08-20
class Admin {
	/**
	 * 2016-08-20
	 * Цель плагина — сохранение в ссылках атрибута «target = "blank"» на страницах транзакций.
	 * @see \Df\Payment\Method::tidFormat()
	 *
	 * @see \Magento\Sales\Helper\Admin::escapeHtmlWithLinks()
	 * @used-by \Magento\Sales\Block\Adminhtml\Transactions\Detail::_toHtml()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param string $data
	 * @param string[]|null $allowedTags [optional]
	 * @return string
	 */
	function aroundEscapeHtmlWithLinks(Sb $sb, \Closure $f, $data, $allowedTags = null) {
		return df_trans_is_my() ? $data : $f($data, $allowedTags);
	}
}