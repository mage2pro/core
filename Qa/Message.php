<?php
namespace Df\Qa;
/**
 * 2020-08-15 "Get rid of the `Df\Core\OLegacy` inheritance for `Df\Qa\Message`" https://github.com/mage2pro/core/issues/109
 * @see \Df\Qa\Message\Failure
 */
abstract class Message extends \Df\Core\O {
	/**
	 * @used-by report()
	 * @see \Df\Qa\Message\Failure\Error::main()
	 * @see \Df\Qa\Message\Failure\Exception::main()
	 * @return string
	 */
	abstract protected function main();

	/**
	 * @used-by df_notify_exception()
	 * @used-by \Df\Qa\Message\Failure\Error::check()
	 * @throws \Exception
	 */
	public final function log() {
		/**
		 * 2015-04-04
		 * Нам нужно правильно обработать ситуацию,
		 * когда при формировании диагностического отчёта о сбое происходит новый сбой.
		 * 1) Статическая переменная $inProcess предотвращает нас от бесконечной рекурсии.
		 * 2) try... catch позволяет нам перехватить внутренний сбой,
		 * сформировать диагностическое сообщение о нём,
		 * а затем перевозбудить его снова, чтобы вывести на экран.
		 * Обратите внимание, что внутренний сбой не будет виден на экране при асинхронном запросе
		 * (много таких запросов делает, например, страница оформления заказа),
		 * поэтому try... catch с целью записи отчёта крайне важно:
		 * без этого при сбое асинхроноого запроса диагностичекское сообщение о сбое
		 * окажется утраченным.
		 */
		static $inProcess;
		if (!$inProcess) {
			$inProcess = true;
			try {
				df_report($this->reportName(), $this->report());
				$inProcess = false;
			}
			catch (\Exception $e) {
				df_log(df_ets($e));
				throw $e;
			}
		}
	}

	/**
	 * @used-by log()
	 * @used-by mail()
	 * @used-by df_log_l()
	 * @return string
	 */
	final function report() {return dfc($this, function() {return $this->sections(
		Context::render(), $this->preface(), $this->main(), $this->postface()
	);});}

	/**
	 * @used-by report()
	 * @see \Df\Qa\Message\Failure::postface()
	 * @return string
	 */
	protected function postface() {return '';}

	/**
	 * @used-by report()
	 * @see \Df\Qa\Message\Failure\Error::preface()
	 * @return string
	 */
	protected function preface() {return '';}

	/**
	 * 2016-08-20
	 * @used-by \Df\Qa\Message::log()
	 * @return string
	 */
	protected function reportName() {return 'mage2.pro/' . df_ccc('-', $this->reportNamePrefix(), '{date}--{time}.log');}

	/**
	 * 2016-08-20
	 * @used-by \Df\Qa\Message::reportName()
	 * @see \Df\Qa\Message\Failure\Exception::reportNamePrefix()
	 * @return string|string[]
	 */
	protected function reportNamePrefix() {return [];}

	/**
	 * @used-by report()
	 * @used-by \Df\Qa\Message\Failure\Exception::postface()
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