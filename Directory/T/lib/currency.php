<?php
// 2017-01-29
namespace Df\Directory\T\lib;
use Magento\Framework\Locale\Bundle\CurrencyBundle;
class currency extends \Df\Core\TestCase {
	/**
	 * 2017-01-29
	 * http://stackoverflow.com/a/31755693
	 * @test
	 */
	public function t01() {
		/** @var CurrencyBundle $b */
		//$b = (new CurrencyBundle());
		//df_currency_name('SEK');
		/** @var $locale */
		$locale = df_locale_by_country('FI');
		echo $locale . "\n";
		/** @var \NumberFormatter $formatter */
		$formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
		echo $formatter->getTextAttribute(\NumberFormatter::CURRENCY_CODE);
		//xdebug_break();
	}
}