<?php
namespace Df\Framework\Validator;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Phrase;
# 2016-06-30
class Currency implements \Df\Framework\IValidator {
	/**
	 * 2016-06-30
	 * 2016-11-13
	 * Отныне в качестве $iso3 можно передавать список валют в виде строки, перечисляя их через запятую.
	 * Так, например, делает модуль «Omise»:
	 * 		<argument name='iso3' xsi:type='string'>THB,JPY</argument>
	 * https://github.com/mage2pro/omise/tree/0.0.7/etc/adminhtml/di.xml#L18
	 */
	function __construct(string $iso3) {$this->_iso3 = df_csv_parse($iso3);}

	/**
	 * 2016-06-30
	 * @override
	 * @see \Df\Framework\IValidator::check()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @return true|string|string[]
	 */
	function check(AE $e) {return
		# 2016-11-20 !! обязательно, потому что нам нужно вернуть именно true|string|string[]
		!!df_filter($this->_iso3, function($c) {return df_currency_has_rate($c);}) ?: $this->message()
	;}

	/**
	 * 2016-06-30
	 * @used-by self::check()
	 */
	private function message():string {
		$namesA = df_quote_russian(df_html_b(df_currency_name($this->_iso3))); /** @var string $namesA */
		$namesS = df_csv_pretty($namesA); /** @var string $namesS */
		/** @var string $whatToEnable */ /** @var string $whatToSet */ /** @var string $object */
		# 2020-03-02, 2022-10-31
		# 1) Symmetric array destructuring requires PHP ≥ 7.1:
		#		[$a, $b] = [1, 2];
		# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
		# We should support PHP 7.0.
		# https://3v4l.org/3O92j
		# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
		# https://stackoverflow.com/a/28233499
		# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
		[$whatToEnable, $whatToSet, $object] =
			1 === count($namesA)
			? ["the {$namesS} currency", 'an exchange rate', 'it']
			: ["at least one of the {$namesS} currencies", 'exchange rates', 'them']
		;
		$urlEnable = df_url_backend('admin/system_config/edit/section/currency'); /** @var string $urlEnable */
		$urlRate = df_url_backend('admin/system_currency'); /** @var string $urlRate */
		return "Please <a href='{$urlEnable}' target='_blank'>enable</a> {$whatToEnable}"
	   	. " and <a href='{$urlRate}' target='_blank'>set {$whatToSet}</a> for {$object}.";
	}

	/**
	 * 2016-06-30
	 * @var string[]
	 */
	private $_iso3;
}