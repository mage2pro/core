<?php
// 2016-08-27
namespace Df\Payment\R;
use Df\Payment\Settings as S;
use Df\Sales\Model\Order as DFO;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2016-12-25
 * @see \Dfe\AllPay\Controller\CustomerReturn\Index
 * @see \Dfe\SecurePay\Controller\CustomerReturn\Index
 */
abstract class CustomerReturn extends \Df\Payment\R\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return Redirect
	 */
	public function execute() {
		if ($this->needLog()) {
			dfp_report($this, $_REQUEST, 'customerReturn');
		}
		/** @var Redirect $result */
		if ($this->valid()) {
			$this->onValid();
			$result = $this->redirect('checkout/onepage/success');
		}
		else {
			/** @var O|DFO|null $o */
			$o = $this->o();
			if ($o && $o->canCancel()) {
				$o->cancel();
				$o->save();
			}
			df_checkout_session()->restoreQuote();
			// 2016-07-14
			// Show an explanation message to the customer
			// when it returns to the store after an unsuccessful payment attempt.
			df_checkout_error($this->messageFailure());
			// 2016-05-06
			// «How to redirect a customer to the checkout payment step?» https://mage2.pro/t/1523
			$result = $this->redirect('checkout', ['_fragment' => 'payment']);
		}
		return $result;
	}

	/**
	 * 2016-08-27
	 * @used-by execute()
	 * @return string
	 */
	protected function message() {return '';}

	/**
	 * 2016-12-25
	 * @used-by execute()
	 * @return string
	 */
	protected function messageFailure() {return
		df_var($this->s()->messageFailure(), ['originalMessage' => $this->message()])
	;}

	/**
	 * 2016-12-25
	 * @used-by execute()
	 * @return void
	 */
	protected function onValid() {}

	/**
	 * 2016-08-27
	 * @used-by \Dfe\AllPay\Controller\CustomerReturn\Index::message()
	 * @param string $key
	 * @return string|null
	 */
	protected function transP($key) {return !$this->t() ? null : df_trans_raw_details($this->t(), $key);}

	/**
	 * 2016-12-25
	 * @used-by execute()
	 * @return bool
	 */
	protected function valid() {return $this->o() && !$this->o()->isCanceled();}

	/**
	 * 2016-08-27
	 * Для тестирования можно использовать: df_order_r()->get(257);
	 * @return O|DFO|null
	 */
	private function o() {return df_checkout_session()->getLastRealOrder();}

	/**
	 * 2016-08-27
	 * @return OP|null
	 */
	private function p() {return dfc($this, function() {return
		!$this->o() ? null : $this->o()->getPayment()
	;});}

	/**
	 * 2016-08-27
	 * @return T|null
	 */
	private function t() {return dfc($this, function() {return df_trans_by_payment_last($this->p());});}
}