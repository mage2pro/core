<?php
namespace Df\StripeClone\Block;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Method as M;
use Df\StripeClone\ResponseRecord as RR;
// 2017-01-13
/** @method M m() */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2017-01-13
	 * @override
	 * @see \Df\Payment\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 */
	final protected function prepare() {
		/** @var CF $c */
		$c = RR::s($this, $this->transF())->card();
		$this->siB("{$this->titleB()} ID", $this->m()->formatTransactionId($this->transF()));
		$this->si($this->isBackend() ? 'Card Number' : 'Number', $c->label());
		$this->siB(['Card Expires' => $c->exp(), 'Card Country' => $c->country()]);
	}
}