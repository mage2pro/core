<?php
namespace Df\StripeClone\Block;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Card;
use Df\StripeClone\Facade\Charge as FCharge;
use Df\StripeClone\Method as M;
use Magento\Sales\Model\Order\Payment\Transaction as T;
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
		/** @var M $m */
		$m = $this->m();
		/** @var T $t */
		$t = df_tm($m)->tReq();
		/** @var string|array(string => mixed) $r */
		$r = df_trd(df_tm($m)->tReq(), M::IIA_TR_RESPONSE);
		/**
		 * 2017-01-13
		 * Раньше я хранил ответ сервера в JSON, теперь же я храню его в виде массива.
		 * @see \Df\Payment\Method::iiaSetTRR()
		 * Формат JSON поддерживаю для корректного просмотра прежних транзакций.
		 */
		/** @var CF $c */
		$c = new CF(Card::create($m, dfa_deep(
			is_array($r) ? $r : df_json_decode($r), FCharge::s($m)->pathToCard()
		)));
		$this->siB("{$this->titleB()} ID", $m->tidFormat($t));
		$this->si($this->isBackend() ? 'Card Number' : 'Number', $c->label());
		$c->c()->owner() ? $this->siB('Cardholder', $c->c()->owner()) : null;
		$this->siB(['Card Expires' => $c->exp(), 'Card Country' => $c->country()]);
	}
}