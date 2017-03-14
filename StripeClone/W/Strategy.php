<?php
namespace Df\StripeClone\W;
use Df\Sales\Model\Order as DfOrder;
use Df\StripeClone\Method as M;
use Magento\Framework\Controller\AbstractResult as Result;
use Magento\Framework\Phrase;
use Magento\Sales\Api\Data\OrderPaymentInterface as IOP;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment as OP;
// 2017-01-06
/** @see \Df\StripeClone\W\Strategy\Charge */
abstract class Strategy {
	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\W\Handler::_handle()
	 * @return void
	 */
	abstract function handle();

	/**
	 * 2017-01-06
	 * @used-by \Df\StripeClone\W\Handler::_handle()
	 * @param Handler $w
	 */
	final function __construct(Handler $w) {$this->_h = $w;}

	/**
	 * 2017-01-15
	 * @override
	 * @see \Df\StripeClone\W\Handler::currentTransactionType()
	 * @used-by \Df\StripeClone\W\Handler::id()
	 * @return string
	 */
	final protected function currentTransactionType() {return $this->_h->currentTransactionType();}

	/**
	 * 2017-01-17
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Refunded::handle()
	 * @return Handler
	 */
	final protected function h() {return $this->_h;}

	/**
	 * 2017-01-07
	 * @return IOP|OP|null
	 */
	final protected function ii() {return $this->_h->ii();}

	/**
	 * 2017-01-15
	 * @return M
	 */
	final protected function m() {return dfc($this, function() {return df_ar($this->_h->m(), M::class);});}

	/**
	 * 2017-01-06
	 * @return Order|DfOrder
	 */
	final protected function o() {return $this->_h->o();}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Refunded::parentId()
	 * @return string
	 */
	final protected function parentId() {return $this->_h->parentId();}

	/**
	 * 2017-01-07
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Captured::handle()
	 * @param Result|Phrase|string $v
	 * @return void
	 */
	final protected function resultSet($v) {$this->_h->resultSet($v);}

	/**
	 * 2017-01-07
	 * @param string|string[]|null $k [optional]
	 * @param mixed|null $d [optional]
	 * @used-by \Df\StripeClone\W\Strategy\Charge\Refunded::handle()
	 * @return array(string => mixed)|mixed|null
	 */
	final protected function ro($k = null, $d = null) {return $this->_h->ro($k, $d);}

	/**
	 * 2017-01-06
	 * @used-by __construct()
	 * @used-by m()
	 * @used-by o()
	 * @used-by resultSet()
	 * @var Handler
	 */
	private $_h;
}