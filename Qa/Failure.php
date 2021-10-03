<?php
namespace Df\Qa;
use Df\Qa\Trace\Formatter;
/**
 * @see \Df\Qa\Failure\Error
 * @see \Df\Qa\Failure\Exception
 */
abstract class Failure extends \Df\Core\O {
	/**
	 * @used-by report()
	 * @see \Df\Qa\Failure\Error::main()
	 * @see \Df\Qa\Failure\Exception::main()
	 * @return string
	 */
	abstract protected function main();

	/**
	 * @abstract
	 * @used-by postface()
	 * @see \Df\Qa\Failure\Error::trace()
	 * @see \Df\Qa\Failure\Exception::trace()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace();

	/**
	 * @used-by df_log_l()
	 * @used-by \Df\Qa\Failure\Error::log()
	 * @return string
	 */
	final function report() {return dfc($this, function() {return $this->sections(
		Context::render(), $this->preface(), $this->main(), $this->postface()
	);});}

	/**
	 * @override
	 * @see \Df\Qa\Failure::postface()
	 * @used-by \Df\Qa\Failure::report()
	 * @used-by \Df\Qa\Failure\Exception::postface()
	 * @see \Df\Qa\Failure\Exception::postface()
	 * @return string
	 */
	protected function postface() {return Formatter::p(
		new Trace(array_slice($this->trace(), $this->stackLevel())), $this->a(self::P__SHOW_CODE_CONTEXT, true)
	);}

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
	 * @used-by \Df\Qa\Failure::reportName()
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

	/**
	 * @used-by postface()
	 * @see \Df\Qa\Failure\Exception::stackLevel()
	 * @see \Df\Qa\Failure\Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}

	/**
	 * @used-by df_log_l()
	 * @used-by postface()
	 */
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}