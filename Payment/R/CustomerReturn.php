<?php
// 2016-08-27
namespace Df\Payment\R;
use Df\Payment\Settings as S;
use Df\Sales\Model\Order as DFO;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
abstract class CustomerReturn extends \Magento\Framework\App\Action\Action {
	/**
	 * 2016-08-27
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @return Redirect
	 */
	public function execute() {
		/** @var Redirect $result */
		if ($this->o() && !$this->o()->isCanceled()) {
			$result = $this->_redirect('checkout/onepage/success');
		}
		else {
			df_checkout_session()->restoreQuote();
			// 2016-05-06
			// «How to redirect a customer to the checkout payment step?» https://mage2.pro/t/1523
			$result = $this->_redirect('checkout', ['_fragment' => 'payment']);
			// 2016-07-14
			// Show an explanation message to the customer
			// when it returns to the store after an unsuccessful payment attempt.
			df_checkout_error(df_var($this->s()->messageFailure(), [
				'originalMessage' => $this->message()
			]));
		}
		return $result;
	}

	/**
	 * 2016-08-27
	 * @used-by \Df\Payment\R\CustomerReturn::execute()
	 * @return string
	 */
	protected function message() {return '';}

	/**
	 * 2016-08-27
	 * @used-by \Dfe\AllPay\Controller\CustomerReturn\Index::message()
	 * @param string $key
	 * @return string|null
	 */
	protected function transP($key) {return !$this->t() ? null : df_trans_raw_details($this->t(), $key);}

	/**
	 * 2016-08-27
	 * @return O|DFO|null
	 */
	private function o() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2016-08-17
			 * Для тестирования можно использовать:
			 * $this->{__METHOD__} = df_order_r()->get(257);
			 */
			$this->{__METHOD__} = df_n_set(df_checkout_session()->getLastRealOrder());
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-08-27
	 * @return OP|null
	 */
	private function p() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(!$this->o() ? null : $this->o()->getPayment());
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * 2016-08-27
	 * @return S
	 */
	private function s() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = S::convention($this);
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-27
	 * @return T|null
	 */
	private function t() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(df_trans_by_payment_last($this->p()));
		}
		return df_n_get($this->{__METHOD__});
	}
}


