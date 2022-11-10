<?php
namespace Df\PaypalClone\W;
# 2016-07-12
final class Handler extends \Df\Payment\W\Handler {
	/**
	 * 2017-08-15
	 * @override
	 * @see \Df\Payment\W\Handler::strategyC()
	 * @used-by \Df\Payment\W\Handler::handle()
	 */
	protected function strategyC():string {return \Df\Payment\W\Strategy\ConfirmPending::class;}
}