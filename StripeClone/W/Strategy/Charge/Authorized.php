<?php
namespace Df\StripeClone\W\Strategy\Charge;
// 2017-01-15
final class Authorized extends \Df\StripeClone\W\Strategy\Charge {
	/**
	 * 2017-01-15
	 * @override
	 * @see \Df\StripeClone\W\Strategy::_handle()
	 * @used-by \Df\StripeClone\W\Strategy::::handle()
	 * @return void
	 */
	protected function _handle() {
		$this->action();
		df_order_send_email($this->o());
		$this->resultSet($this->op()->getId());
	}
}