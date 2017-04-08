<?php
namespace Df\Sales\Observer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
// 2017-04-01
final class OrderPlaceAfter implements ObserverInterface {
	/**
	 * 2017-04-01
	 * @override
	 * @see ObserverInterface::execute()
	 * What events are triggered on an order placement? https://mage2.pro/t/3573
	 * @param O $o
	 */
	function execute(O $o) {
		/** @var string $key */
		$key = md5(__METHOD__);
		/** @var string|false $v */
		if (!($v = df_cache_load($key)) || df_num_days(df_date(df_date_parse($v, false)))) {
			df_modules_log();
		}
		df_cache_save(df_dts(), $key);
	}
}