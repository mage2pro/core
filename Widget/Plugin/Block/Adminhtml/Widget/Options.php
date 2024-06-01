<?php
namespace Df\Widget\Plugin\Block\Adminhtml\Widget;
use Magento\Widget\Block\Adminhtml\Widget\Options as Sb;
# 2024-06-01 "Log widget creation errors": https://github.com/mage2pro/core/issues/397
final class Options {
	/**
	 * 2024-06-01 "Log widget creation errors": https://github.com/mage2pro/core/issues/397
	 * @see \Magento\Widget\Block\Adminhtml\Widget\Options::addFields()
	 */
	function aroundAddFields(Sb $sb, \Closure $f):void {try {$f();} catch (\Exception $e) {df_log($e); throw $e;}}
}