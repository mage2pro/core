<?php
namespace Df\StripeClone\Block;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Card;
use Df\StripeClone\Facade\Charge as FCharge;
use Df\StripeClone\Method as M;
/**
 * 2017-01-13
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @method M m()
 */
class Info extends \Df\Payment\Block\Info {
	/**
	 * 2017-01-13
	 * @override
	 * @see \Df\Payment\Block\Info::prepare()
	 * @used-by \Df\Payment\Block\Info::_prepareSpecificInformation()
	 */
	final protected function prepare() {
		/** @var string|array(string => mixed) $r */
		$r = df_trd($this->transF(), M::IIA_TR_RESPONSE);
		/**
		 * 2017-01-13
		 * Раньше я хранил ответ сервера в JSON, теперь же я храню его в виде массива.
		 * @see \Df\Payment\Method::iiaSetTRR()
		 * Формат JSON поддерживаю для корректного просмотра прежних транзакций.
		 */
		/** @var CF $c */
		$c = new CF(Card::create($this->m(),
			dfa_deep(is_array($r) ? $r : df_json_decode($r), FCharge::s($this->m())->pathToCard())
		));
		$this->siB("{$this->titleB()} ID", $this->m()->tidFormat($this->transF()));
		$this->si($this->isBackend() ? 'Card Number' : 'Number', $c->label());
		$c->c()->owner() ? $this->siB('Cardholder', $c->c()->owner()) : null;
		$this->siB(['Card Expires' => $c->exp(), 'Card Country' => $c->country()]);
	}
}