<?php
namespace Df\Sales\Plugin\Helper;
use Magento\Sales\Helper\Admin as Sb;
# 2016-08-20
final class Admin {
	/**
	 * 2016-08-20 Цель плагина — сохранение в ссылках атрибута «target = "blank"» на страницах транзакций.
	 * 2023-01-30
	 * «Argument 3 passed to Df\Sales\Plugin\Helper\Admin::aroundEscapeHtmlWithLinks()
	 * must be of the type string, null given, called in vendor/magento/framework/Interception/Interceptor.php on line 135»:
	 * https://github.com/mage2pro/core/issues/201
	 * @see \Df\Payment\Method::tidFormat()
	 * @see \Magento\Sales\Helper\Admin::escapeHtmlWithLinks()
	 * @used-by \Magento\Sales\Block\Adminhtml\Transactions\Detail::_toHtml()
	 * @param string[]|null $allowedTags [optional]
	 */
	function aroundEscapeHtmlWithLinks(Sb $sb, \Closure $f, string $data = null, $allowedTags = null):string {return
		df_trans_is_my() ? $data : $f($data, $allowedTags)
	;}
}