<?php
namespace Df\StripeClone\Block;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Card;
use Df\StripeClone\Facade\Charge as fCharge;
use Df\StripeClone\Method as M;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-13
 * @see \Dfe\Moip\Block\Info\Card
 * @see \Dfe\Square\Block\Info
 * @see \Dfe\Stripe\Block\Info
 * @method M m()
 */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2017-11-12
	 * @used-by prepare()
	 * @see \Dfe\Stripe\Block\Info::cardData()
	 * @return object|array(string => mixed)
	 */
	protected function cardData() {return $this->cardDataFromChargeResponse($this->tm()->res0());}

	/**
	 * 2017-11-12
	 * @used-by cardData()
	 * @used-by \Dfe\Stripe\Block\Info::cardData()
	 * @param string|array(string => mixed) $r
	 * @return object|array(string => mixed)
	 */
	final protected function cardDataFromChargeResponse($r) {
		/**
		 * 2017-01-13
		 * Раньше я хранил ответ сервера в JSON, теперь же я храню его в виде массива.
		 * @see \Df\Payment\Method::iiaSetTRR()
		 * Формат JSON поддерживаю для корректного просмотра прежних транзакций.
		 */
		$r = is_array($r) ? $r : df_json_decode($r);
		/** @var array(string => mixed) $r */ /** @var string $pathToCard */ /** @var M $m */
		if (!($r = dfa_deep($r, $pathToCard = fCharge::s($m = $this->m())->pathToCard()))) {
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
	 * @used-by \Df\Payment\Block\Info::prepareToRendering()
	 * @used-by \Dfe\Moip\Block\Info\Card::prepare()
	 * @see \Dfe\Moip\Block\Info\Card::prepare()
	 * @see \Dfe\Square\Block\Info::prepare()
	 */
	protected function prepare() {
		$c = CF::s($m = $this->m(), Card::create($m, $this->cardData())); /** @var CF $c */ /** @var M $m */
		$this->siID();
		$this->si($this->extended('Card Number', 'Number'), $c->label());
		$c->c()->owner() ? $this->siEx('Cardholder', $c->c()->owner()) : null;
		$this->siEx(['Card Expires' => $c->exp(), 'Card Country' => $c->country()]);
	}
}