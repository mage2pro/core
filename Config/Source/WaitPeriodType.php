<?php
namespace Df\Config\Source;
use Magento\Sales\Model\Order as O;
// 2016-07-19
/** @method static WaitPeriodType s() */
final class WaitPeriodType extends \Df\Config\Source {
	/**
	 * 2016-07-19
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map() {return [
		'calendar_days'=> 'Calendar Days', self::WORKING_DAYS => 'Working Days'
	];}
	
	/**
	 * 2017-03-19
	 * @used-by \Df\Config\Source\WaitPeriodType::map()
	 * @used-by \Dfe\AllPay\Settings::waitPeriodATM()
	 */
	const WORKING_DAYS = 'working_days';
}