<?php
namespace Df\Framework\Log\Handler;
use Magento\Framework\Logger\Handler\Base as LB;
use Monolog\Logger as L;
# 2024-02-11
# "Log the ≤ `Monolog\Logger::INFO`-level messages to module-level separate files (instead of `system.log`)":
# https://github.com/mage2pro/core/issues/348
/** @used-by \Df\Framework\Log\Dispatcher::handle() */
final class Info extends \Df\Framework\Log\Handler {
	/**
	 * 2024-02-11
	 * @override
	 * @see \Df\Framework\Log\Handler::_p()
	 * @used-by \Df\Framework\Log\Handler::p()
	 */
	protected function _p():bool {/** @var bool $r */
		if ($r = L::INFO >= $this->r()->level()) {
			self::lb()->handle($this->r()->source());
		}
		return $r;
	}

	/**
	 * 2024-02-11
	 * @used-by self::_p()
	 */
	private static function lb():LB {return dfcf(function(string $m):LB {
		$n = df_report_prefix($m); /** @var string $n */
		return df_new_om(LB::class, ['fileName' => "var/log/mage2.pro/info/$n.log"]);
	}, [df_caller_module()]);}
}