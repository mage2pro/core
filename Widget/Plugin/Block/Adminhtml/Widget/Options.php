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
			throw $t;
		}
	}
}