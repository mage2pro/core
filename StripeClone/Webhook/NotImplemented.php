<?php
// 2017-01-04
namespace Df\StripeClone\Webhook;
use Df\Framework\Controller\Result\Text;
class NotImplemented extends \Df\StripeClone\Webhook {
	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::_handle()
	 * @used-by \Df\Payment\Webhook::handle()
	 * @return void
	 */
	final protected function _handle() {}

	/**
	 * 2017-01-06
	 * @override
	 * @see \Df\StripeClone\Webhook::currentTransactionType()
	 * @used-by \Df\StripeClone\Webhook::id()
	 * @return string
	 */
	final protected function currentTransactionType() {return '';}

	/**
	 * 2017-01-06
	 * @override
	 * @see \Df\StripeClone\Webhook::parentTransactionType()
	 * @used-by \Df\StripeClone\Webhook::adaptParentId()
	 * @return string
	 */
	final protected function parentTransactionType() {return '';}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\Webhook::resultSuccess()
	 * @used-by \Df\Payment\Webhook::handle()
	 * @return Text
	 */
	final protected function resultSuccess() {return Text::i('The event handler is not implemented.');}

	/**
	 * 2017-01-06
	 * @override
	 * @see \Df\StripeClone\Webhook::roPath()
	 * @used-by \Df\StripeClone\Webhook::ro()
	 * @return string
	 */
	final protected function roPath() {return '';}
}