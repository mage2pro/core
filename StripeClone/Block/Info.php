<?php
namespace Df\StripeClone\Block;
use Df\StripeClone\Card;
use Df\StripeClone\Method as M;
use Df\StripeClone\ResponseRecord as RR;
/**
 * 2017-01-13
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
		/** @var Card $c */
		$c = $this->responseRecord()->card();
		$this->siB("{$this->titleB()} ID", $this->m()->formatTransactionId($this->transF()));
		$this->si($this->isBackend() ? 'Card Number' : 'Number', (string)$c);
		$this->siB(['Card Expires' => $c->expires(), 'Card Country' => $c->country()]);
	}

	/**
	 * 2017-01-13
	 * @return RR
	 */
	private function responseRecord() {return dfc($this, function() {
		/** @var string|array(string => mixed) $r */
		$r = df_trans_raw_details($this->transF(), M::IIA_TR_RESPONSE);
		/**
		 * 2017-01-13
		 * Раньше я хранил ответ сервера в JSON, теперь же я храню его в виде массива.
		 * @see \Df\Payment\Method::iiaSetTRR()
		 * Формат JSON поддерживаю для корректного просмотра прежних транзакций.
		 */
		return df_new(df_con_hier($this->m(), RR::class), is_array($r) ? $r : df_json_decode($r));
	});}
}