<?php
namespace Df\Framework\Validator;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
use Magento\Framework\Phrase;
class Currency implements \Df\Framework\IValidator {
	/**
	 * 2016-06-30
	 * 2016-11-13
	 * Отныне в качестве $iso3 можно передавать список валют в виде строки,
	 * перечисляя их через запятую. Так, например, делает модуль «Omise»:
		<argument name='iso3' xsi:type='string'>THB,JPY</argument>
	 * https://github.com/mage2pro/omise/tree/0.0.7/etc/adminhtml/di.xml#L18
	 * @param string $iso3
	 */
	function __construct($iso3) {$this->_iso3 = df_csv_parse($iso3);}

	/**
	 * 2016-06-30
	 * @override
	 * @see \Df\Framework\IValidator::check()
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
	 * @used-by \Df\Framework\Validator\Composite::check()
	 * @param AE $e
	 * @return true|Phrase|Phrase[]
	 */
	function check(AE $e) {return
		// 2016-11-20
		// !! обязательно, потому что нам нужно вернуть именно true|Phrase|Phrase[]
		!!df_filter($this->_iso3, function($c) {return df_currency_has_rate($c);}) ?: $this->message()
	;}

	/**
	 * 2016-06-30
	 * @return Phrase
	 */
	private function message() {
		/** @var string $namesA */
		$namesA = df_quote_russian(df_html_b(df_currency_name($this->_iso3)));
		/** @var string $namesS */
		$namesS = implode(', ', $namesA);
		/** @var string $whatToEnable */
		/** @var string $whatToSet */
		/** @var string $object */
		list($whatToEnable, $whatToSet, $object) =
			1 === count($namesA)
			? ["the {$namesS} currency", 'an exchange rate', 'it']
			: ["at least one of the {$namesS} currencies", 'exchange rates', 'them']
		;
		/** @var string $urlEnable */
		$urlEnable = df_url_backend('admin/system_config/edit/section/currency');
		/** @var string $urlRate */
		$urlRate = df_url_backend('admin/system_currency');
		// 2016-11-20
		// @todo It should return a Phrase, not a string.
		return "Please <a href='{$urlEnable}' target='_blank'>enable</a> {$whatToEnable}"
	   	. " and <a href='{$urlRate}' target='_blank'>set {$whatToSet}</a> for {$object}.";
	}

	/**
	 * 2016-06-30
	 * @var string[]
	 */
	private $_iso3;
}