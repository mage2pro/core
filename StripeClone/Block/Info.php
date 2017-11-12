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
 * @see \Dfe\Square\Block\Info()
 * @method M m()
 */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2017-11-12
	 * @used-by prepare()
	 * @return array(string => mixed)
	 */
	protected function cardData() {
		$r = $this->tm()->res0(); /** @var string|array(string => mixed) $r */
		/**
		 * 2017-01-13
		 * Раньше я хранил ответ сервера в JSON, теперь же я храню его в виде массива.
		 * @see \Df\Payment\Method::iiaSetTRR()
		 * Формат JSON поддерживаю для корректного просмотра прежних транзакций.
		 */
		$pathToCard = FCharge::s($m = $this->m())->pathToCard(); /** @var string $pathToCard */ /** @var M $m */
		$r = is_array($r) ? $r : df_json_decode($r);
		/** @var array(string => mixed) $r */
		if (!($r = dfa_deep($r, FCharge::s($m)->pathToCard()))) {
			df_error("Unable to extract the bank card data by path «{$pathToCard}» from the charge:\n%s",
				df_json_encode($r)
			);
		}
		return $r;
	}

	/**
	 * 2017-01-13
	 * @override
	 * @see \Df\Payment\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 * @used-by \Dfe\Moip\Block\Info\Card::prepare()
	 * @see \Dfe\Moip\Block\Info\Card::prepare()
	 * @see \Dfe\Square\Block\Info::prepare()
	 */
	protected function prepare() {
		$m = $this->m(); /** @var M $m */
		$c = CF::s($m, (Card::create($m, $this->cardData()))); /** @var CF $c */
		$this->siID();
		$this->si($this->extended('Card Number', 'Number'), $c->label());
		$c->c()->owner() ? $this->siEx('Cardholder', $c->c()->owner()) : null;
		$this->siEx(['Card Expires' => $c->exp(), 'Card Country' => $c->country()]);
	}
}