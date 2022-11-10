<?php
namespace Df\Payment\W;
use Df\Framework\Request as Req;
use Df\Payment\Method as M;
use Df\Payment\W\Exception\Critical;
/**
 * 2017-03-10
 * 2017-03-13 
 * Каждый модуль может иметь не больше одного Reader, и Reader должен быть расположен по пути W\Reader.
 * A payment module can have a single Reader, and it should be located at the `W\Reader` path
 * @see \Df\Payment\W\Reader\Json
 * @see \Dfe\AllPay\W\Reader
 * @see \Dfe\AlphaCommerceHub\W\Reader
 * @see \Dfe\Qiwi\W\Reader
 * @see \Dfe\TBCBank\W\Reader
 * @see \Dfe\YandexKassa\W\Reader
 */
class Reader implements IEvent {
	/**
	 * 2017-03-10
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @used-by \Df\Payment\W\F::__construct()
	 * @param M $m
	 * @param array(string => mixed)|null $req [optional]
	 * *) null в качестве значения $req означает, что $req должен быть взят из запроса HTTP,
	 * *) массив в качестве значения $req означает прямую инициализацию $req:
	 * это сценарий @see \Df\Payment\TM::responses()
	 */
	final function __construct(M $m, $req = null) {
		$this->_m = $m;
		$this->_test = is_null($req) ? Req::extra() : [];
		/**
		 * 2017-12-08
		 * We should not filter a ready $req.
		 * @see \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
		 */
		$this->_req = $req ?: $this->reqFilter($this->_test ? $this->testData() : $this->http());
	}

	/**
	 * 2017-03-17
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 1) Определить, к какой транзакции Magento относится данное событие.
	 * 2) Загрузить эту транзакцию из БД.
	 * 3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @used-by \Df\Payment\W\Event::m()
	 */
	function m():M {return $this->_m;}

	/**
	 * 2017-03-10
	 * @override
	 * @see \Df\Payment\W\IEvent::r()
	 * @used-by self::error()
	 * @used-by self::rr()
	 * @used-by self::t()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final function r($k = null, $d = null) {return dfa($this->_req, $k, $d);}

	/**
	 * 2017-01-12
	 * @used-by self::t()
	 * @used-by \Df\Payment\W\Event::rr()
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed
	 * @throws Critical
	 */
	final function rr($k = null, $d = null) {return !is_null($r = $this->r($k, $d)) ? $r : $this->errorP($k);}

	/**
	 * 2017-03-10
	 * Some PSP send only one type of notifications.
	 * In such case, a notification does not denote its own type, and this method returns null.
	 * 2017-03-13 The result is in our internal format, not in the PSP format.
	 * @used-by self::tl()
	 * @used-by \Df\Payment\W\Event::t()
	 * @used-by \Df\Payment\W\F::c()
	 * @used-by \Dfe\AllPay\W\Reader::isOffline()
	 * @return string|null
	 */
	final function t() {return dfc($this, function() {return is_null($r = $this->tRaw()) ? null : $this->te2i($r);});}

	/**
	 * 2017-03-12 Type label.
	 * @override
	 * @see \Df\Payment\W\IEvent::tl()
	 * @used-by \Df\Payment\W\Action::ignoredLog()
	 * @used-by \Df\Payment\W\Handler::log()
	 */
	final function tl():string {return dfc($this, function() {return $this->tl_($this->t());});}

	/**
	 * 2017-03-10
	 * @used-by self::tl()
	 * @used-by \Df\Payment\W\Event::tl_()
	 * @param string|null $t
	 */
	final function tl_($t):string {return !is_null($t) ? $t : 'Confirmation';}

	/**
	 * 2017-03-13 Returns a value in the PSP format.
	 * 2017-03-23 Использую именно @uses array_key_exists(), чтобы для ПС с единственным типом оповещений писать ?df-type=
	 * @used-by self::t()
	 * @used-by \Df\Payment\W\Event::tl()
	 * @return string|null
	 */
	final function tRaw() {return array_key_exists('type', $this->_test) ? $this->_test['type'] : (
		is_null($kt = $this->kt()) ? null : $this->rr($kt)
	);}

	/**
	 * 2017-03-10
	 * @used-by self::__construct()
	 * @see \Df\Payment\W\Reader\Json::http()
	 * @see \Dfe\AlphaCommerceHub\W\Reader::http()
	 * @see \Dfe\Qiwi\W\Reader::http()
	 * @return array(string => mixed)
	 */
	protected function http():array {return Req::clean();}

	/**
	 * 2017-03-10
	 * @used-by self::tRaw()
	 * @see \Df\GingerPaymentsBase\W\Reader::kt()
	 * @see \Dfe\AllPay\W\Reader::kt()
	 * @see \Dfe\Omise\W\Reader::kt()
	 * @see \Dfe\Paymill\W\Reader::kt()
	 * @see \Dfe\Stripe\W\Reader::kt()
	 * @see \Dfe\YandexKassa\W\Reader::kt()
	 * @return string|null
	 */
	protected function kt() {return null;}

	/**
	 * 2017-12-08
	 * @used-by self::__construct()
	 * @see \Dfe\AlphaCommerceHub\W\Reader::reqFilter()
	 * @see \Dfe\TBCBank\W\Reader::reqFilter()
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	protected function reqFilter(array $r):array {return $r;}

	/**
	 * 2017-03-12 Converts an event type from the PSP format to our internal format.
	 * @used-by self::t()
	 * @see \Dfe\AllPay\W\Reader::te2i()
	 * @param string $t
	 */
	protected function te2i($t):string {return $t;}

	/**
	 * 2017-03-10
	 * @used-by self::errorP()
	 * @param string $reason
	 * @throws Critical
	 */
	private function error($reason):void {
		($r = $this->r()) ? df_sentry_extra($this, 'Request', $r) : null;
		throw new Critical($this->_m, $this, "The request is invalid because $reason.");
	}

	/**
	 * 2017-03-15
	 * @used-by self::rr()
	 * @param $k
	 * @throws Critical
	 */
	private function errorP($k):void {$this->error("the required parameter `{$k}` is absent");}

	/**
	 * 2017-03-10
	 * @used-by self::testData()
	 * @param string|null $k
	 * @param string|null $d
	 * @return array(string => mixed)|mixed|null
	 */
	private function test($k = null, $d = null) {return dfa($this->_test, $k, $d);}

	/**
	 * 2017-03-11
	 * @used-by self::__construct()
	 * @return array(string => mixed)
	 * @throws Critical
	 */
	private function testData():array {
		if (!array_key_exists('type', $this->_test)) {
			$this->errorP('df-type');
		}
		$baseName = df_ccc('-', $this->_test['type'], $this->test('case')) ?: 'default'; /** @var string $baseName */
		$m = df_module_name_short($this->_m); /** @var string $m */
		/** @var string $f */
		if (!file_exists($f = BP . df_path_n_real("/_my/test/{$m}/{$baseName}.json"))) {
			df_error("Place your test data to the «{$f}» file.");
		}
		return df_json_file_read($f);
	}

	/**
	 * 2017-03-11
	 * $m здесь НЕ СОДЕРЖИТ корректного II.
	 * Для вычисления корректного II нам ещё предстоит провести кучу операций:
	 * 		1) Определить, к какой транзакции Magento относится данное событие.
	 * 		2) Загрузить эту транзакцию из БД.
	 * 		3) По транзакции получить II.
	 * Это всё нам ещё предстоит!
	 * @used-by self::__construct()
	 * @used-by self::error()
	 * @used-by self::m()
	 * @used-by self::testData()
	 * @var M
	 */
	private $_m;

	/**
	 * 2017-03-10
	 * @used-by self::__construct()
	 * @used-by self::r()
	 * @var array(string => mixed)
	 */
	private $_req;

	/**
	 * 2017-03-10
	 * @used-by self::__construct()
	 * @used-by self::test()
	 * @used-by self::testData()
	 * @used-by self::tRaw()
	 * @var array(string => mixed)
	 */
	private $_test;
}