<?php
namespace Df\StripeClone\Block;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Card;
use Df\StripeClone\Facade\Charge as FCharge;
use Df\StripeClone\Method as M;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-13
 * @see \Dfe\Moip\Block\Info\Card
 * @method M m()
 */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2017-01-13
	 * @override
	 * @see \Df\Payment\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 * @used-by \Dfe\Moip\Block\Info\Card::prepare()
	 * @see \Dfe\Moip\Block\Info\Card::prepare()
	 */
	protected function prepare() {
		$m = $this->m(); /** @var M $m */
		$r = $this->tm()->res0(); /** @var string|array(string => mixed) $r */
		/**
		 * 2017-01-13
		 * Раньше я хранил ответ сервера в JSON, теперь же я храню его в виде массива.
		 * @see \Df\Payment\Method::iiaSetTRR()
		 * Формат JSON поддерживаю для корректного просмотра прежних транзакций.
		 */
		/** @var CF $c */
		$c = CF::s($m, (Card::create($m, dfa_deep(
			is_array($r) ? $r : df_json_decode($r), FCharge::s($m)->pathToCard()
		))));
		$this->siID();
		$this->si($this->extended('Card Number', 'Number'), $c->label());
		$c->c()->owner() ? $this->siEx('Cardholder', $c->c()->owner()) : null;
		$this->siEx(['Card Expires' => $c->exp(), 'Card Country' => $c->country()]);
	}
}