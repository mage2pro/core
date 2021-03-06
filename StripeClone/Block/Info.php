<?php
namespace Df\StripeClone\Block;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Card;
use Df\StripeClone\Facade\Charge as fCharge;
use Df\StripeClone\Method as M;
/**
 * 2017-01-13
 * @see \Dfe\Moip\Block\Info\Card
 * @see \Dfe\Square\Block\Info
 * @see \Dfe\Stripe\Block\Info
 * @see \Dfe\TBCBank\Block\Info
 * @see \Dfe\Vantiv\Block\Info
 * @method M m()
 */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2017-11-12
	 * @used-by card()
	 * @see \Dfe\Stripe\Block\Info::cardData()
	 * @see \Dfe\TBCBank\Block\Info::cardData()
	 * @return object|array(string => mixed)
	 */
	protected function cardData() {return $this->cardDataFromChargeResponse($this->tm()->res0());}

	/**
	 * 2018-11-12
	 * @used-by prepare()
	 * @see \Dfe\TBCBank\Block\Info::cardNumberLabel()
	 * @return string
	 */
	protected function cardNumberLabel() {return $this->extended('Card Number', 'Number');}

	/**
	 * 2018-12-19
	 * @used-by cf()
	 * @see \Dfe\Vantiv\Block\Info::card()
	 * @return Card
	 */
	protected function card() {return Card::create($this->m(), $this->cardData());}

	/**
	 * 2017-11-12
	 * @used-by cardData()
	 * @used-by \Dfe\Stripe\Block\Info::cardData()
	 * @param string|array(string => mixed) $r
	 * @return array(string => mixed)
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
	 * 2018-11-13
	 * @used-by prepare()
	 * @used-by \Dfe\TBCBank\Block\Info::prepare()
	 * @return CF
	 */
	final protected function cf() {return dfc($this, function() {return CF::s(
		$this->m(), $this->card()
	);});}

	/**
	 * 2017-01-13
	 * @override
	 * @see \Df\Payment\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::prepareToRendering()
	 * @see \Dfe\Moip\Block\Info\Card::prepare()
	 * @see \Dfe\Square\Block\Info::prepare()
	 * @see \Dfe\TBCBank\Block\Info::prepare()
	 */
	protected function prepare() {
		$cf = $this->cf(); /** @var CF $cf */
		$this->siID();
		$this->si($this->cardNumberLabel(), $cf->label());
		$cf->c()->owner() ? $this->siEx('Cardholder', $cf->c()->owner()) : null;
		$this->siEx(['Card Expiration' => $cf->exp(), 'Card Country' => $cf->country()]);
	}
}