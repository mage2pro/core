<?php
namespace Df\Config\Backend;
use Df\Config\Backend;
use Df\Config\O;
use Df\Core\Exception as DFE;
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
	 * @see \Df\Config\Backend::_afterLoad()
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
	 * 2016-08-03
	 * @return string
	 */
	protected function entityC() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->fc('dfEntity');
			df_assert_class_exists($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-07-30
	 * @used-by \Df\Config\Backend\Serialized::valueSerialize()
	 * @used-by \Df\Config\Backend\Serialized::valueUnserialize()
	 * @param array(string => mixed) $array
	 * @return array(string => mixed)
	 */
	protected function processA(array $array) {return $this->validate($array);}

	/**
	 * 2016-08-03
	 * @see \Df\Config\Backend\Serialized::processA()
	 * @used-by \Df\Config\Backend\Serialized::valueSerialize()
	 * @used-by \Df\Config\Backend\Serialized::valueUnserialize()
	 * @param array(string => mixed) $result
	 * @return array(string => mixed)
	 * @throws \Exception
	 */
	final protected function validate(array $result) {
		try {
			$this->validateI($result);
		}
		/**
		 * 2016-08-02
		 * Исключительная ситуация может быть не только типа @see \Df\Core\Exception,
		 * но и типа @see \Exception,
		 * потому что чтение некорректных данных может приводить к побочным сбоям.
		 * В частности, @uses \Df\Config\A::get()
		 * вызывает у объектов метод @see \Df\Config\ArrayItem::getId(),
		 * который может приводить к сбою при некорректности данных.
		 */
		catch (\Exception $e) {
			/**
			 * 2016-08-02
			 * Если некорректость данных обнаружена при их сохранении,
			 * то там удобнее возбудить исключительную ситуацию,
			 * чтобы администратор магазина увидел диагностическое сообщение на экране.
			 * Далее администратор может скорректировать данные посредством интерфейса.
			 *
			 * Если же некорректость данных обнаружена при их загрузке,
			 * то это значит, что некорректные данные находятся в базе данных,
			 * и администратор всё равно не сможет скорректировать их посредством интерфейса.
			 * Поэтому вместо возбуждения исключительной ситуации просто сбрасываем данные.
			 *
			 * Некорректость данных при их загрузке возможна, например,
			 * если поменялся формат данных в ещё разрабатываемом модуле:
			 * тогда нам вместо конквертации данных проще их сбросить,
			 * чтобы не сопровождать код по такой конвертации,
			 * который с релизом модуля больше никогда не понадобится.
			 */
			if ($this->isSaving()) {
				throw $e;
			}
			$result = [];
			//df_cfg_delete($this->getPath());
			//df_cfg_save($this->getPath(), null, );
			df_log($e);
			df_message_error(__(
				"The store's database contains incorrect data for the «<b>%1</b>» option."
				."<br/>The data for this options are reset.", $this->label()
			));
		}
		return $result;
	}

	/**
	 * 2016-08-03
	 * @used-by \Df\Config\Backend\Serialized::validate()
	 * @param array(string => mixed) $array
	 * @return void
	 * @throws DFE
	 */
	protected function validateI(array $array) {
		/** @var O $entity */
		$entity = df_ic($this->entityC(), O::class, $array);
		$entity->validate();
	}

	/**
	 * 2015-12-07
	 * @used-by \Df\Framework\Form\Element\FieldsetBackend::dfSaveBefore()
	 * @return void
	 */
	protected function valueSerialize() {
		$this->setValue(df_json_encode_pretty($this->processA($this->value())));
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
	protected function valueUnserialize() {
		/**
		 * 2016-07-31
		 * Добавил проверку !is_array($this->getValue()),
		 * потому что родительский метод @see \Df\Config\Backend::save()
		 * будет вызывать нас даже если при сериализации произошёл сбой
		 * (и она не была завершена успешно):
		 * https://github.com/mage2pro/core/blob/1.5.7/Config/Backend.php?ts=4#L29-L35
		 */
		if (!is_array($this->getValue())) {
			$this->setValue($this->processA(df_nta(df_json_decode($this->getValue()))));
		}
	}
}