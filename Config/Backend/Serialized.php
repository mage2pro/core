<?php
namespace Df\Config\Backend;
use Df\Config\Backend;
/**
 * 2015-12-07
 * Вообще, в ядре есть свои схожие классы
 * @see \Magento\Config\Model\Config\Backend\Serialized
 * @see \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
 * Однако я решил разработать свой, более мощный и заточенный под мои задачи.
 */
class Serialized extends Backend {
	/**
	 * 2015-12-07
	 * @override
	 *
	 * Не забывайте о дефекте https://mage2.pro/t/285
	 * «@see \Magento\Framework\App\Config\Value::afterLoad() method
	 * breaks specification of the overriden parent method
	 * @see \Magento\Framework\Model\AbstractModel::afterLoad()
	 * by not calling and ignoring its logic»
	 *
	 * @see \Magento\Framework\Model\AbstractModel::_afterLoad()
	 * @used-by \Magento\Framework\Model\AbstractModel::load()
	 * @return void
	 */
	protected function _afterLoad() {
		$this->valueUnserialize();
		parent::_afterLoad();
	}

	/**
	 * 2015-12-07
	 * @override
	 * @see \Df\Config\Backend::dfSaveAfter()
	 * @used-by \Df\Config\Backend::save()
	 * @return void
	 */
	protected function dfSaveAfter() {
		$this->valueUnserialize();
		parent::dfSaveAfter();
	}

	/**
	 * 2015-12-07
	 * @override
	 * @see \Df\Config\Backend::dfSaveBefore()
	 * @used-by \Df\Config\Backend::save()
	 * @return void
	 */
	protected function dfSaveBefore() {
		parent::dfSaveBefore();
		$this->valueSerialize();
	}

	/**
	 * 2015-12-07
	 * @used-by \Df\Framework\Form\Element\FieldsetBackend::dfSaveBefore()
	 * @return void
	 */
	protected function valueSerialize() {
		$this->setValue(df_json_encode_pretty($this->value()));
	}

	/**
	 * 2015-12-07
	 * Сначала пробовал здесь код:
	 		$value = json_decode($this->getValue(), $assoc = true);
			dfa_deep_set($this->_data, $this->valuePathA(), $value);
			$this->setValue(null);
	 * Однако этот код неверен,
	 * потому что нам нужно установить данные именно в поле value:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L344
	 * $data = $backendModel->getValue();
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L362
	 * 'value' => $data
	 * @used-by \Df\Framework\Form\Element\FieldsetBackend::_afterLoad()
	 * @used-by \Df\Framework\Form\Element\FieldsetBackend::dfSaveAfter()
	 * @return void
	 */
	protected function valueUnserialize() {$this->setValue(df_json_decode($this->getValue()));}
}