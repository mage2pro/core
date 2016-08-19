<?php
namespace Df\Qa;
abstract class Message extends \Df\Core\O {
	/**
	 * @used-by report()
	 * @return string
	 */
	abstract protected function main();

	/**
	 * @used-by df_notify_exception()
	 * @used-by \Df\Qa\Message_Failure_Error::check()
	 * @return void
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
	 * @used-by report()
	 * @return string
	 */
	protected function postface() {return '';}

	/**
	 * @used-by report()
	 * @return string
	 */
	protected function preface() {return '';}

	/**
	 * 2016-08-20
	 * @used-by \Df\Qa\Message::log()
	 * @return string
	 */
	protected function reportName() {
		return 'mage2.pro/' . df_ccc('-', $this->reportNamePrefix(), '{date}--{time}.log');
	}

	/**
	 * 2016-08-20
	 * @used-by \Df\Qa\Message::reportName()
	 * @return string|string[]
	 */
	protected function reportNamePrefix() {return [];}

	/**
	 * @used-by \Df\Qa\Message_Failure::traceS()
	 * @used-by \Df\Qa\Message_Failure_Exception::preface()
	 * @used-by report()
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
	 * @used-by log()
	 * @used-by mail()
	 * @return string
	 */
	private function report() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->sections(
				Context::render(), $this->preface(), $this->main(), $this->postface()
			);
		}
		return $this->{__METHOD__};
	}
}