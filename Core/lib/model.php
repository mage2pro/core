<?php
use Df\Framework\Model\CallbackPool;
use Magento\Framework\Model\AbstractModel as M;

/**
 * 2016-03-26
 * @param string|M $model
 * Идентификатор необязательно является целым числом,
 * потому что объект может загружаться по нестандартному ключу
 * (с указанием этого ключа параметром $field).
 * Так же, и первичный ключ может не быть целым числом (например, при загрузке валют).
 * @param string|int $id
 * @param bool $throwOnAbsence [optional]
 * @param string|null $field [optional]
 * @return M|null
 */
function df_load($model, $id, $throwOnAbsence = true, $field = null) {
	/** @var M|null $result */
	$result = is_string($model) ? df_om()->create($model) : $model;
	df_assert($result instanceof M);
	$result->load(df_assert($id), $field);
	if (!$result->getId()) {
		if (!$throwOnAbsence) {
			$result = null;
		}
		else {
			df_error("The model of class «%s» with ID «{$id}» is absent.", get_class($result));
		}
	}
	return $result;
}

/**
 * 2016-05-23
 * @see \Df\Framework\Plugin\Model\AbstractModel::afterSave()
 * @param M $m
 * @param callable|array $callback
 */
function df_on_save(M $m, $callback) {CallbackPool::attach(spl_object_hash($m), $callback);}