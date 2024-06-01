<?php
namespace Df\Widget\Plugin\Block\Adminhtml\Widget;
use Magento\Widget\Block\Adminhtml\Widget\Options as Sb;
use Df\Framework\Log\Latest;
use \Throwable As T;
# 2024-06-01
final class Options {
	/**
	 * 2024-06-01 "Log widget creation errors": https://github.com/mage2pro/core/issues/397
	 * @see \Magento\Widget\Block\Adminhtml\Widget\Options::addFields()
	 */
	function aroundAddFields(Sb $sb, \Closure $f):void {
		try {$f();}
		catch (T $t) {
			df_log($t);
			/** 2024-06-01 Similar to @see \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun() */
			Latest::register($t);
			/**
			 * 2024-06-01
			 * 1) It will be caught by @see \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions::execute():
			 * 		catch (\Magento\Framework\Exception\LocalizedException $e) {
			 * 			$result = ['error' => true, 'message' => $e->getMessage()];
			 * 			$this->getResponse()->representJson(
			 * 				$this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($result)
			 * 			);
			 * 		}
			 * https://github.com/magento/magento2/blob/2.4.7/app/code/Magento/Widget/Controller/Adminhtml/Widget/LoadOptions.php#L53-L58
			 * 2) As you can see,
			 * @see \Magento\Widget\Controller\Adminhtml\Widget\LoadOptions::execute() does not log the exception,
			 * that is why we need the plugin.
			 */
			throw $t;
		}
	}
}