<?php
namespace Df\Cron\Plugin\Console\Command;
use Magento\Cron\Console\Command\CronCommand as Sb;
use Symfony\Component\Console\Input\InputInterface as II;
use Symfony\Component\Console\Output\OutputInterface as IO;
# 2020-02-17
# https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Cron/Console/Command/CronCommand.php
# https://github.com/magento/magento2/blob/2.3.4/app/code/Magento/Cron/Console/Command/CronCommand.php
final class CronCommand {
	/**
	 * 2020-02-17
	 * "The Cron log (`magento.cron.log`) should contain a backtrace for every exception logged":
	 * https://github.com/tradefurniturecompany/site/issues/34
	 * @see df_error()
	 * @see \Symfony\Component\Console\Command\Command::run()
	 * https://github.com/symfony/console/blob/v4.3.10/Command/Command.php#L189-L259
	 */
	function aroundRun(Sb $sb, \Closure $f, II $i, IO $o):int {
		try {return $f($i, $o);}
		catch (\Exception $e) {
			df_log_e($e, $this);
			throw $e;
		}
	}
}