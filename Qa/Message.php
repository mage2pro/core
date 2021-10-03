<?php
namespace Df\Qa;
/**
 * 2020-08-15 "Get rid of the `Df\Core\OLegacy` inheritance for `Df\Qa\Message`" https://github.com/mage2pro/core/issues/109
 * @see \Df\Qa\Failure
 */
abstract class Message extends \Df\Core\O {
	/**
	 * @used-by report()
	 * @see \Df\Qa\Failure\Error::main()
	 * @see \Df\Qa\Failure\Exception::main()
	 * @return string
	 */
	abstract protected function main();

	/**
	 * @used-by mail()
	 * @used-by df_log_l()
	 * @used-by \Df\Qa\Failure\Error::log()
	 * @return string
	 */
	final function report() {return dfc($this, function() {return $this->sections(
		Context::render(), $this->preface(), $this->main(), $this->postface()
	);});}

	/**
	 * @used-by report()
	 * @see \Df\Qa\Failure::postface()
	 * @return string
	 */
	protected function postface() {return '';}

	/**
	 * @used-by report()
	 * @see \Df\Qa\Failure\Error::preface()
	 * @return string
	 */
	protected function preface() {return '';}

	/**
	 * 2016-08-20
	 * @used-by \Df\Qa\Failure\Error::log()
	 * @return string
	 */
	protected function reportName() {return 'mage2.pro/' . df_ccc('-', $this->reportNamePrefix(), '{date}--{time}.log');}

	/**
	 * 2016-08-20
	 * @used-by \Df\Qa\Message::reportName()
	 * @see \Df\Qa\Failure\Exception::reportNamePrefix()
	 * @return string|string[]
	 */
	protected function reportNamePrefix() {return [];}

	/**
	 * @used-by report()
	 * @used-by \Df\Qa\Failure\Exception::postface()
	 * @param string|string[] $items
	 * @return string
	 */
	protected function sections($items) {
		if (!is_array($items)) {
			$items = func_get_args();
		}
		/** @var string $s */
		static $s; if (!$s) {$s = "\n" . str_repeat('*', 36) . "\n";};
		return implode($s, array_filter(df_trim(df_xml_output_plain($items))));
	}
}