<?php
// 2016-12-26
namespace Df\StripeClone;
abstract class Webhook extends \Df\Payment\Webhook {
	/**
	 * 2017-01-06
	 * @used-by id()
	 * @see \Dfe\Stripe\Webhook\Charge\Captured::currentTransactionType()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::currentTransactionType()
	 * @return string
	 */
	abstract protected function currentTransactionType();

	/**
	 * 2017-01-06
	 * @used-by adaptParentId()
	 * @see \Dfe\Stripe\Webhook\Charge\Captured::parentTransactionType()
	 * @see \Dfe\Stripe\Webhook\Charge\Refunded::parentTransactionType()
	 * @return string
	 */
	abstract protected function parentTransactionType();

	/**
	 * 2017-01-04
	 * @used-by \Df\StripeClone\WebhookF::i()
	 * @param string $v
	 * @return void
	 */
	final public function typeSet($v) {$this->_type = $v;}

	/**
	 * 2017-01-04
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	final public function ro($k = null, $d = null) {return $this->req(df_cc_path($this->roPath(), $k), $d);}

	/**
	 * 2017-01-06
	 * Stripe-подобные платёжные системы, в отличие от PayPal-подобных,
	 * отличаются богатством типов оповещений.
	 *
	 * PayPal-подобные платёжные системы присылают, как правило, только один тип оповещений:
	 * оповещение о факте успешности (или неуспешности) оплаты покупателем заказа.
	 *
	 * У Stripe-подобных платёжных систем типов оповещений множество,
	 * причём они порой образуют целые иерархии.
	 * Например, у Stripe оповещения об изменении статуса платежа объединяются в группу «charge»,
	 * которой принадлежат такие типы оповещений, как «charge.captured» и «charge.refunded».
	 *
	 * Разные Stripe-подобные платёжные системы обладают схожими типами платежей.
	 * Пример — те же самые «charge.captured» и «charge.refunded».
	 * По этой причине разумно выделять не только общие черты,
	 * свойственные конкретной Stripe-подобной платёжной системе
	 * и отличащие её от других Stripe-подобных платёжных систем,
	 * но и общие черты типов платежей: обработка того же «charge.captured»
	 * должна иметь общую реализацию для всех Stripe-подобных платёжных модулей.
	 *
	 * Для реализации такой системы из двух параллельных иерархий
	 * я вынес данный метод _handle() на верхний уровень иерархии Stripe-подобных платёжных модулей
	 * и сделал его конечным (final), а иерархию обработчиков разных типов платежей
	 * вынес в стратегию.
	 * Для данного Webhook система ищет стратегию с тем же именеми суффиксом «Strategy» на конце.
	 *
	 * Стратегии имеют суффикс Strategy
	 *
	 * @override
	 * @see \Df\Payment\Webhook::_handle()
	 * @used-by \Df\Payment\Webhook::handle()
	 * @return void
	 */
	final protected function _handle() {
		/** @var $d */
		$d = df_cc_class(df_module_name_c(__CLASS__), df_class_suffix($this)) . 'Strategy';
		/** @var string $strategyC */
		$strategyC = df_con_heir($this, $d);
		/** @var WebhookStrategy $strategy */
		$strategy = new $strategyC($this);
		$strategy->handle();
	}

	/**
	 * 2017-01-06
	 * Преобразует идентификатор платежа в платёжной системе
	 * в глобальный внутренний идентификатор родительской транзакции.
	 * @override
	 * @see \Df\Payment\Webhook::adaptParentId()
	 * @used-by \Df\Payment\Webhook::parentId()
	 * @param string $id
	 * @return string
	 */
	final protected function adaptParentId($id) {return $this->e2i($id, $this->parentTransactionType());}

	/**
	 * 2017-01-06
	 * Внутренний полный идентификатор текущей транзакции.
	 * Он используется лишь для присвоения его транзакции
	 * (чтобы в будущем мы смогли найти эту транзакцию по её идентификатору).
	 * @override
	 * @see \Df\Payment\Webhook::id()
	 * @used-by \Df\Payment\Webhook::addTransaction()
	 * @return string
	 */
	final protected function id() {return
		$this->e2i($this->parentIdRaw(), $this->currentTransactionType())
	;}

	/**
	 * 2017-01-04
	 * Для Stripe-подобные платёжных систем
	 * наш внутренний идентификатор транзакции основывается на внешнем:
	 * <имя модуля>-<внешний идентификатор платежа>-<окончание типа события>.
	 * 2017-01-07
	 * Ключ должен быть именно «data/object/id».
	 * Ключ «id» у события тоже присутствует, но его значением является не идентификатор платежа
	 * («ch_*»), а идентификатор события («evt_*»).
	 * @override
	 * @see \Df\Payment\Webhook::parentIdRawKey()
	 * @used-by \Df\Payment\Webhook::parentIdRaw()
	 * @return string
	 */
	final protected function parentIdRawKey() {return 'data/object/id';}

	/**
	 * 2017-01-04
	 * 2017-01-06
	 * Сообщение от платёжной системы — это иерархический JSON.
	 * На верхнем уровне иерархии расположены метаданные:
	 * *) тип сообщения (например: «charge.captured»).
	 * *) идентификатор платежа в платёжной системе
	 * *) тестовый ли платёж или промышленный
	 * *) версия API
	 * *) и.т.п.
	 * Конкретные данные сообщения расположены внутри иерархии по некоему пути.
	 * Этот путь и возвращает наш метод.
	 *
	 * 2017-01-09
	 * Для Omise конкретные данные сообщения расположены прямо на верхнем уровне иерархии,
	 * поэтому отныне roPath() может возвращать null и по умолчанию так и возвращает.
	 *
	 * @used-by ro()
	 * @see \Dfe\Stripe\Webhook::roPath()
	 * @return string|null
	 */
	protected function roPath() {return null;}

	/**
	 * 2017-01-04
	 * 2017-01-08
	 * Здесь ещё нельзя использовать @see \Df\StripeClone\Webhook::type(),
	 * потому что сюда мы попадаем из @used-by \Df\StripeClone\Webhook::__construct(),
	 * а тип устанавливается уже после, вызовом @see \Df\StripeClone\Webhook::typeSet()
	 * из @see \Df\StripeClone\WebhookF::i()
	 * @override
	 * @see \Df\Payment\Webhook::testDataFile()
	 * @used-by \Df\Payment\Webhook::testData()
	 * @return string
	 */
	final protected function testDataFile() {return $this->extra(WebhookF::KEY_TYPE);}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::type()
	 * @used-by \Df\Payment\Webhook::typeLabel()
	 * @used-by \Dfe\AllPay\Webhook::classSuffix()
	 * @used-by \Dfe\AllPay\Webhook::typeLabel()
	 * @return string
	 */
	final protected function type() {return $this->_type;}

	/**
	 * 2017-01-04
	 * Преобразует внешний идентификатор транзакции во внутренний.
	 * Внутренний идентификатор отличается от внешнего наличием окончания «-<тип транзакции>».
	 * @used-by id()
	 * @uses \Df\StripeClone\Method::e2i()
	 * @param string $id
	 * @param string $txnType
	 * @return string
	 */
	private function e2i($id, $txnType) {return dfp_method_call_s($this, 'e2i', $id, $txnType);}

	/**
	 * 2017-01-04
	 * @used-by type()
	 * @used-by typeSet()
	 * @var string
	 */
	private $_type;
}