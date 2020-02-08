<?php
namespace Df\Cron\Plugin\Model;
use Magento\Cron\Model\VirtualLogger as Sb;
// 2020-02-08
final class VirtualLogger {
	/**
	 * 2020-02-08
	 * @see \Magento\Framework\Logger\Handler\Base::write()
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param array $record
	 * @return void
	 */
	function aroundWrite(Sb $sb, \Closure $f, array $record) {$f($record);}
}