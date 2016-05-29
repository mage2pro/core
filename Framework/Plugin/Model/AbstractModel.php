<?php
namespace Df\Framework\Plugin\Model;
use Df\Framework\Model\CallbackPool;
use Magento\Framework\Model\AbstractModel as Sb;
class AbstractModel {
	/**
	 * 2016-05-23
	 * Сделал по аналогии с @see \Magento\Framework\EntityManager\CallbackHandler::process()
	 * https://github.com/magento/magento2/blob/b366da/lib/internal/Magento/Framework/EntityManager/CallbackHandler.php#L41-L63
	 *
	 * Раньше пытался использовать так:
		$this->o()->getResource()->addCommitCallback(function() use($cm, $payment) {
			\Twocheckout_Sale::comment([
				'sale_id' => $payment->getAdditionalInformation(InfoBlock::SALE_ID)
				, 'sale_comment' => df_credit_memo_backend_url($cm->getId())
			]);
		});
	 * Но здесь хэш вычисляется по классу connection,
	 * и получались ложные срабатывания, когда сохранялся какой-то другой объект,
	 * использующий тот же connection.
	 *
	 * 2016-05-23
	 * Сначала пытался прицепить этот плагин к методу
	 * @see \Magento\Framework\Model\AbstractModel::save()
	 * однако это неверно, потому что многие модели, как ни странно,
	 * вызываются без вызова метода @see \Magento\Framework\Model\AbstractModel::save(),
	 * а вместо этого сразу вызывыается метод save() ресурсной модели.
	 * Например, так работает сохранение заказа при его размещении.
	 *
	 * 2016-05-29
	 * Из after-плагинов надо обязательно возвращать результат,
	 * иначем мы можем поломать последующие after-плагины:
	 * https://mail.google.com/mail/u/0/#inbox/154f9e0eb03982aa
	 * Recoverable Error: Argument 2 passed to
	 * @see \Magento\WebapiSecurity\Model\Plugin\CacheInvalidator::afterAfterSave()
	 * must be an instance of Magento\Framework\App\Config\Value,
	 * null given.
	 *
	 * @see df_on_save()
	 * @see \Magento\Framework\Model\AbstractModel::afterSave()
	 * @param Sb $sb
	 * @param Sb $result
	 * @return Sb
	 */
	public function afterAfterSave(Sb $sb, Sb $result) {
		/** @var string $hash */
		$hash = spl_object_hash($sb);
		/** @var callable[] $callbacks */
		$callbacks = CallbackPool::get($hash);
		foreach ($callbacks as $callback) {
			/** @var callable $callback */
			call_user_func($callback);
		}
		return $result;
	}
}
