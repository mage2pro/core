<?php
namespace Df\Config\Source;
use Df\Config\Settings as S;
use Zend_Date as ZD;
# 2016-07-19
/** @method static WaitPeriodType s() */
final class WaitPeriodType extends \Df\Config\Source {
	/**
	 * 2016-07-19
	 * @override
	 * @see \Df\Config\Source::map()
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	protected function map():array {return ['calendar_days' => 'Calendar Days', self::$WORKING_DAYS => 'Working Days'];}

	/**
	 * 2017-07-30       
	 * @used-by \Dfe\AllPay\Settings::waitPeriodATM()
	 * @used-by \Dfe\Moip\Settings\Boleto::waitPeriod()
	 * @used-by \Dfe\Qiwi\Settings::waitPeriod()
	 */
	static function calculate(S $s, string $k = '', string $kType = 'waitPeriodType'):int {return dfcf(
		function(S $s, string $k, string $kType):int {
			$r = $s->nat($k); /** @var int $r */
			return self::$WORKING_DAYS === $s->v($kType) ? $r : df_num_calendar_days_by_num_working_days(
				ZD::now(), $r, $s->scope()
			);
		}, [$s, $k ?: df_caller_f(), $kType]
	);}
	
	/**
	 * 2017-07-30
	 * @used-by self::calculate()
	 * @used-by self::map()
	 * @var string
	 */
	private static $WORKING_DAYS = 'working_days';	
}