<?php
namespace Df\PaypalClone;
use Df\Payment\W\Event;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-08-27
 * @see \Df\GingerPaymentsBase\Method
 * @see \Df\PaypalClone\Method\Normal
 * @see \Dfe\Klarna\Method
 */
abstract class Method extends \Df\Payment\Method {
	/**
	 * 2016-07-10
	 * 2017-01-05
	 * Преобразует в глобальный внутренний идентификатор транзакции:
	 * 1) Внешний идентификатор транзакции.
	 * Это случай, когда идентификатор формируется платёжной системой.
	 * 2) Локальный внутренний идентификатор транзакции.
	 * Это случай, когда мы сами сформировали идентификатор запроса к платёжной системе.
	 * Мы намеренно передавали идентификатор локальным (без приставки с именем модуля)
	 * для удобства работы с этими идентификаторами в интерфейсе платёжной системы:
	 * ведь там все идентификаторы имели бы одинаковую приставку.
	 * Такой идентификатор формируется в методах:
	 * @see \Df\PaypalClone\Charge::requestId()
	 * @see \Dfe\AllPay\Charge::requestId()
	 *
	 * Глобальный внутренний идентификатор отличается наличием приставки «<имя модуля>-».
	 *
	 * @used-by \Df\GingerPaymentsBase\Method::getConfigPaymentAction()
	 * @used-by \Df\PaypalClone\Method::addTransaction()
	 * @used-by \Df\PaypalClone\W\Nav::e2i()
	 * @used-by \Dfe\SecurePay\Method::_refund()
	 * @param string $externalId
	 * @return string
	 */
	final function e2i($externalId) {return "{$this->getCode()}-$externalId";}

	/**
	 * 2016-07-18  
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\PaypalClone\BlockInfo::responseF()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	function responseF($k = null) {return $this->tm()->responseF($k);}

	/**
	 * 2016-07-18
	 * @final I do not use the PHP «final» keyword here to allow refine the return type using PHPDoc.
	 * @used-by \Df\PaypalClone\BlockInfo::responseL()
	 * @param string|null $k [optional]
	 * @return Event|string|null
	 */
	function responseL($k = null) {return $this->tm()->responseL($k);}

	/**
	 * 2017-03-05
	 * @used-by responseF()
	 * @used-by responseL()
	 * @used-by \Df\PaypalClone\Refund::tm()
	 * @return TM
	 */
	final function tm() {return dfc($this, function() {return new TM($this);});}

	/**
	 * 2016-07-10
	 * @used-by \Df\PaypalClone\Method\Normal::getConfigPaymentAction()
	 * @param string $id
	 * @param array(string => mixed) $data
	 */
	final protected function addTransaction($id, array $data) {
		$this->ii()->setTransactionId($this->e2i($id));
		$this->iiaSetTR($data);
		//$this->ii()->setIsTransactionClosed(false);
		/**
		 * 2016-07-10
		 * @uses \Magento\Sales\Model\Order\Payment\Transaction::TYPE_PAYMENT —
		 * это единственный транзакция без специального назначения,
		 * и поэтому мы можем безопасно его использовать
		 * для сохранения информации о нашем запросе к платёжной системе.
		 */
		$this->ii()->addTransaction(T::TYPE_PAYMENT);
	}
}