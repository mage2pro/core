<?php
namespace Df\Config\Backend;
use Df\Config\Backend;
use Df\Config\O as E;
use \Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2015-12-07
 * Вообще, в ядре есть свои схожие классы
 * @see \Magento\Config\Model\Config\Backend\Serialized
 * @see \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
 * Однако я решил разработать свой, более мощный и заточенный под мои задачи.
 * 2017-06-29
 * This class is not abstract, because it is used directly in some places
 * (currently, only by the m2e/frontend project):
 * https://code.dmitry-fedyuk.com/m2e/frontend/blob/1.0.5/etc/adminhtml/system.xml#L104
 *
 * @see \Df\Config\Backend\ArrayT
 */
class Serialized extends Backend {
	/**
	 * 2015-12-07
	 * Не забывайте о дефекте https://mage2.pro/t/285
	 * «@see \Magento\Framework\App\Config\Value::afterLoad() method
	 * breaks specification of the overriden parent method
	 * @see \Magento\Framework\Model\AbstractModel::afterLoad()
	 * by not calling and ignoring its logic»
	 * @override
	 * @see \Df\Config\Backend::_afterLoad()
	 * @used-by \Magento\Framework\Model\AbstractModel::load()
	 */
	protected function _afterLoad():void {
		$this->valueUnserialize();
		parent::_afterLoad();
	}

	/**
	 * 2015-12-07
	 * @override
	 * @see \Df\Config\Backend::dfSaveAfter()
	 * @used-by \Df\Config\Backend::save()
	 */
	protected function dfSaveAfter():void {
		$this->valueUnserialize();
		parent::dfSaveAfter();
	}

	/**
	 * 2015-12-07
	 * @override
	 * @see \Df\Config\Backend::dfSaveBefore()
	 * @used-by \Df\Config\Backend::save()
	 */
	protected function dfSaveBefore():void {
		parent::dfSaveBefore();
		$this->valueSerialize();
	}

	/**
	 * 2016-08-03   
	 * @used-by self::processI()
	 * @used-by \Df\Config\Backend\ArrayT::processI()
	 */
	final protected function entityC():string {return dfc($this, function():string {return df_assert_class_exists($this->fc(
		'dfEntity'
	));});}

	/**
	 * 2016-08-07     
	 * @used-by self::processA()
	 * @see \Df\Config\Backend\ArrayT::processI()
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	protected function processI(array $r):array {
		$e = df_ic($this->entityC(), E::class, $r); /** @var E $e */
		$e->validate();
		return $r;
	}

	/**
	 * 2015-12-07
	 * @used-by \Df\Framework\Form\Element\FieldsetBackend::dfSaveBefore()
	 */
	final protected function valueSerialize():void {$this->setValue(df_json_encode($this->processA(
		$this->value()
	)));}

	/**
	 * 2015-12-07
	 * Сначала пробовал здесь код:
	 *		$value = json_decode($this->getValue(), $assoc = true);
	 *		dfa_deep_set($this->_data, $this->valuePathA(), $value);
	 *		$this->setValue(null);
	 * Однако этот код неверен,
	 * потому что нам нужно установить данные именно в поле value:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L344
	 * $data = $backendModel->getValue();
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L362
	 * 'value' => $data
	 * @used-by \Df\Framework\Form\Element\FieldsetBackend::_afterLoad()
	 * @used-by \Df\Framework\Form\Element\FieldsetBackend::dfSaveAfter()
	 */
	final protected function valueUnserialize():void {
		/**
		 * 2016-07-31
		 * Добавил проверку !is_array($this->getValue()),
		 * потому что родительский метод @see \Df\Config\Backend::save()
		 * будет вызывать нас даже если при сериализации произошёл сбой
		 * (и она не была завершена успешно):
		 * https://github.com/mage2pro/core/blob/1.5.7/Config/Backend.php?ts=4#L29-L35
		 */
		if (!is_array($this->getValue())) {
			$this->setValue($this->processA(df_eta(df_json_decode($this->getValue()))));
		}
	}

	/**
	 * 2016-07-30
	 * @used-by self::valueSerialize()
	 * @used-by self::valueUnserialize()
	 * @param array(string => mixed) $result
	 * @return array(string => mixed)
	 * @throws \Exception
	 */
	private function processA(array $r):array {
		try {
			$r = $this->processI($r);
		}
		/**
		 * 2016-08-02
		 * Исключительная ситуация может быть не только типа @see \Df\Core\Exception,
		 * но и типа @see \Exception,
		 * потому что чтение некорректных данных может приводить к побочным сбоям.
		 * В частности, @uses \Df\Config\A::get()
		 * вызывает у объектов метод @see \Df\Config\ArrayItem::getId(),
		 * который может приводить к сбою при некорректности данных.
		 * 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
		 */
		catch (Th $th) {
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
				throw $th;
			}
			$r = [];
			//df_cfg_delete($this->getPath());
			//df_cfg_save($this->getPath(), null, );
			df_log($th);
			df_message_error(__(
				"The store's database contains incorrect data for the «<b>%1</b>» option."
				."<br/>The data for this options are reset.", $this->label()
			));
		}
		return $r;
	}
}