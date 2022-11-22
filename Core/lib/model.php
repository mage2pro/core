<?php
use Df\Core\Exception as DFE;
use Df\Framework\Model\CallbackPool;
use Magento\Framework\Model\AbstractModel as M;

/**
 * 2016-03-26
 * @param string|M $m
 * Идентификатор необязательно является целым числом,
 * потому что объект может загружаться по нестандартному ключу
 * (с указанием этого ключа параметром $field).
 * Так же, и первичный ключ может не быть целым числом (например, при загрузке валют).
 * @used-by df_transx()
 * @used-by \Df\Payment\PlaceOrderInternal::qid()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @param string|int|null $id
 * @param string|null $field [optional]
 * @return M|null
 * @throws DFE
 */
function df_load($m, $id, bool $throw = true, $field = null) {/** @var M|null $r */
	$c = df_cts($m); /** @var string $c */
	if (!$id) {
		$r = !$throw ? null : df_error("You are trying to load a model of the class «{$c}» with an empty ID.");
	}
	else {
		$r = df_ar(is_object($m) ? $m : df_new_om($m), M::class);
		$r->load($id, $field);
		if (!$r->getId()) {
			$r = !$throw ? null : df_error("A model of the class «{$c}» with ID «{$id}» is absent.");
		}
	}
	return $r;
}

/**
 * 2016-05-23
 * @see \Df\Framework\Plugin\Model\AbstractModel::afterSave()
 * @used-by \Dfe\TwoCheckout\Method::_refund()
 * @used-by \Dfe\TwoCheckout\Method::charge()
 * @param callable|array $callback
 */
function df_on_save(M $m, $callback):void {CallbackPool::attach(spl_object_hash($m), $callback);}