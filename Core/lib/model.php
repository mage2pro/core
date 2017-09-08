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
 * @param string|int|null $id
 * @param bool $throw [optional]
 * @param string|null $field [optional]
 * @return M|null
 * @throws DFE
 */
function df_load($m, $id, $throw = true, $field = null) {
	/** @var M|null $r */
	$c = df_cts($m); /** @var string $c */
	if (!$id) {
		$r = !$throw ? null : df_error("You are trying to load a model of class «{$c}» with an empty ID.");
	}
	else {
		$r = df_ar(is_object($m) ? $m : df_new_om($m), M::class);
		$r->load($id, $field);
		if (!$r->getId()) {
			$r = !$throw ? null : df_error("The model of class «{$c}» with ID «{$id}» is absent.");
		}
	}
	return $r;
}

/**
 * 2016-05-23
 * @see \Df\Framework\Plugin\Model\AbstractModel::afterSave()
 * @param M $m
 * @param callable|array $callback
 */
function df_on_save(M $m, $callback) {CallbackPool::attach(spl_object_hash($m), $callback);}