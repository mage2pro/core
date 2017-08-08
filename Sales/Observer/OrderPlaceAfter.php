<?php
namespace Df\Sales\Observer;
use Magento\Framework\Event\Observer as O;
use Magento\Framework\Event\ObserverInterface;
// 2017-04-01
final class OrderPlaceAfter implements ObserverInterface {
	/**
	 * 2017-04-01
	 * 2017-05-09
	 * Сегодня заметил в магазине plazasport.no сбой «The locale '' is no known locale»:
	 * https://sentry.io/dmitry-fedyuk/mage2pro/issues/268632312/
	 * Я так понимаю, причиной этого сбоя является попытка использования несуществующей локали.
	 * Этот сбой можно воспроизвести вручную так: \Zend_Locale::findLocale('non existent')
	 * Или так: https://bionest-tech.com/payment/?lang=**otherlanguage**
	 * Способа автоматического устранения такого сбоя я не придумал,
	 * поэтому просто подавляю эту исключительную ситуацию именно для данного сценария.
	 * @see \Zend_Locale::findLocale()
	 * https://github.com/zendframework/zf1/blob/release-1.12.20/library/Zend/Locale.php#L1759
	 * 2017-08-08 `Zend Framework 1 bug: «The locale '' is no known locale»`: https://mage2.pro/t/4255
	 * @override
	 * @see ObserverInterface::execute()
	 * What events are triggered on an order placement? https://mage2.pro/t/3573
	 * @param O $o
	 */
	function execute(O $o) {
		try {
			/** @var string $k */
			/** @var string|false $v */
			if (!($v = df_cache_load($k = md5(__METHOD__))) || df_num_days(df_date_parse($v, false))) {
				dfe_modules_log();
			}
			df_cache_save(df_dts(), $k);
		}
		catch (\Zend_Locale_Exception $e) {}
	}
}