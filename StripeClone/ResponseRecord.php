<?php
namespace Df\StripeClone;
use Df\StripeClone\CardFormatter as CF;
use Df\StripeClone\Facade\Card;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**
 * 2017-01-13
 * @see \Dfe\Omise\ResponseRecord
 * @see \Dfe\Paymill\ResponseRecord
 * @see \Dfe\Stripe\ResponseRecord
 */
abstract class ResponseRecord extends \Df\Core\A {
	/**
	 * 2017-01-13
	 * Returns the path to the bank card information in the payment system response.
	 * @used-by _card()
	 * @see \Dfe\Omise\ResponseRecord::keyCard()
	 * @see \Dfe\Paymill\ResponseRecord::keyCard()
	 * @see \Dfe\Stripe\ResponseRecord::keyCard()
	 * @return string
	 */
	abstract protected function keyCard();

	/**
	 * 2017-01-13
	 * @return CF
	 */
	final function card() {return dfc($this, function() {return new CF(
		Card::create($this, $this->a(df_cc_path($this->keyCard())))
	);});}

	/**
	 * 2017-01-13
	 * @return string
	 */
	final function id() {return $this['id'];}

	/**
	 * 2017-02-12
	 * @used-by \Df\StripeClone\Block\Info::prepare()
	 * @param string|object $m
	 * @param T $t
	 * @return self
	 */
	final static function s($m, T $t) {return dfcf(function($m, T $t) {
		/** @var string|array(string => mixed) $r */
		$r = df_trans_raw_details($t, Method::IIA_TR_RESPONSE);
		/**
		 * 2017-01-13
		 * Раньше я хранил ответ сервера в JSON, теперь же я храню его в виде массива.
		 * @see \Df\Payment\Method::iiaSetTRR()
		 * Формат JSON поддерживаю для корректного просмотра прежних транзакций.
		 */
		return df_new(df_con_heir($m, __CLASS__), is_array($r) ? $r : df_json_decode($r));
	}, [df_module_name($m), $t]);}
}