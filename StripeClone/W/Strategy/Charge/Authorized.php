<?php
// 2017-01-15
namespace Df\StripeClone\W\Strategy\Charge;
final class Authorized extends \Df\StripeClone\W\Strategy\Charge {
	/**
	 * 2017-01-15
	 * @override
	 * @see \Df\StripeClone\W\Strategy::handle()
	 * @used-by \Df\StripeClone\W\Handler::_handle()
	 * @return void
	 */
	function handle() {
		$this->action();
		df_order_send_email($this->o());
		$this->resultSet($this->ii()->getId());
	}
}