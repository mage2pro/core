<?php
namespace Df\Config\Backend\Unusial;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\Model\AbstractModel;
/**
 * 2016-01-11
 * *) https://mage2.pro/t/523
 * A backend model should always be a @see \Magento\Framework\Model\AbstractModel
 * *) https://mage2.pro/t/525
 * None of the @see \Magento\Framework\App\Config\ValueInterface methods are used by the core
 * outside of the particular implementation @see \Magento\Framework\App\Config\Value
 *
 * В сценарии загрузки страницы нам доступны лишь следующие магические методы get...
 * из перечисленных ниже: getPath(), getValue(), getStore(), getWebsite().
 *
 * @method string|int|null getConfigId()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Model/Config.php#L284
 * $backendModel->setConfigId($oldConfig[$path]['config_id']);
 *
 * @method string getPath()
 * Пример: «df_sales/documents_numeration/next»
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L332-L334
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Model/Config.php#L281
 * $backendModel->setPath($path)
 *
 * @method string getStore()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L338-L340
 * $backendModel->setStore($this->setStoreCode())
 * В сценарии отображения страницы при глобальной области действия настроек
 * возвращает пустую строку, а не null.
 *
 * @method mixed|null getValue()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L334-L336
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Model/Config.php#L281
 * $backendModel->setValue($data)
 * В сценарии отображения страницы всегда возвращает null,
 * потому что система не знает о нашем источнике значений
 * и передаёт в качестве значения то, что она пыталась загрузить из таблицы core_config_data,
 * а у нас там ничего нет:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L309-L316
 *
 * @method string getWebsite()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L336-L338
 * $backendModel->setWebsite($this->getWebsiteCode())
 * В сценарии отображения страницы при глобальной области действия настроек
 * возвращает пустую строку, а не null.
 *
 * @method $this setValue(mixed $value)
 *
 * @see \Dfe\SalesSequence\Config\Next\Backend
 */
abstract class Model extends AbstractModel implements ValueInterface {
	/**
	 * 2016-01-14
	 * @override
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L340
	 * @see \Magento\Framework\Model\AbstractModel::afterLoad()     
	 * @used-by \Magento\Config\Block\System\Config\Form::getFieldData()
	 *		if ($field->hasBackendModel()) {
	 *			$backendModel = $field->getBackendModel();
	 *			$backendModel->setPath($path)
	 *				->setValue($data)
	 *				->setWebsite($this->getWebsiteCode())
	 *				->setStore($this->getStoreCode())
	 *				->afterLoad();
	 *			$data = $backendModel->getValue();
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Block/System/Config/Form.php#L436-L444
	 * @see \Dfe\SalesSequence\Config\Next\Backend::afterLoad()
	 * @return $this
	 */
	function afterLoad() {df_abstract($this); return $this;}

	/**
	 * 2016-01-14    
	 * 2017-08-09 It looks like it is never used.
	 * @override
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/DB/Transaction.php#L166-L166
	 * @see \Magento\Framework\Model\AbstractModel::delete() 
	 * @see \Dfe\SalesSequence\Config\Next\Backend::delete()
	 * @return $this
	 */
	function delete() {df_abstract($this); return $this;}

	/**
	 * 2015-01-14
	 * https://mage2.pro/t/520
	 * «The method @see \Magento\Framework\App\Config\ValueInterface::getFieldsetDataValue()
	 * should be removed from the interface because it is used only internally
	 * by a particular interface implementation: @see \Magento\Framework\App\Config\Value »
	 * @override
	 * @see \Magento\Framework\App\Config\ValueInterface::getFieldsetDataValue()
	 * @param string $key
	 * @return string
	 */
	function getFieldsetDataValue($key) {df_abstract($this);}

	/**
	 * 2015-01-14
	 * https://mage2.pro/t/522
	 * «The method @see \Magento\Framework\App\Config\ValueInterface::getOldValue()
	 * should be removed from the interface because it is used only internally
	 * by a particular interface implementation: @see \Magento\Framework\App\Config\Value »
	 * @override
	 * @see \Magento\Framework\App\Config\ValueInterface::getOldValue()
	 * @return string
	 */
	function getOldValue() {df_abstract($this);}

	/**
	 * 2016-01-26
	 * @override
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Config/Block/System/Config/Form.php#L340
	 * @see \Magento\Framework\Model\AbstractModel::getResource()
	 * @used-by \Magento\Framework\DB\Transaction::_startTransaction()
	 * https://github.com/magento/magento2/blob/720667e/lib/internal/Magento/Framework/DB/Transaction.php#L44
	 * @used-by \Magento\Framework\DB\Transaction::_commitTransaction()
	 * https://github.com/magento/magento2/blob/720667e/lib/internal/Magento/Framework/DB/Transaction.php#L57
	 * @used-by \Magento\Framework\DB\Transaction::_rollbackTransaction()
	 * https://github.com/magento/magento2/blob/720667e/lib/internal/Magento/Framework/DB/Transaction.php#L70
	 * @return ResourceModel
	 */
	function getResource() {return ResourceModel::s();}

	/**
	 * 2015-01-14
	 * https://mage2.pro/t/521
	 * «The method @see \Magento\Framework\App\Config\ValueInterface::isValueChanged()
	 * should be removed from the interface because it is used only internally
	 * by a particular interface implementation: @see \Magento\Framework\App\Config\Value »
	 * @override
	 * @see \Magento\Framework\App\Config\ValueInterface::isValueChanged()
	 * @return bool
	 */
	function isValueChanged() {df_abstract($this); return null;}

	/**
	 * 2016-01-14
	 * @override
	 * @see \Magento\Framework\Model\AbstractModel::save()
	 * @used-by \Magento\Framework\DB\Transaction::save():
	 *		foreach ($this->_objects as $object) {
	 *			$object->save();
	 *		}
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/DB/Transaction.php#L127-L133
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/DB/Transaction.php#L127-L133
	 * 2017-12-04
	 * I call @uses \Magento\Framework\Model\ResourceModel\AbstractResource::addCommitCallback() manually,
	 * because I use a very basic resource model @see getResource() for this class:
	 * @see \Df\Config\Backend\Unusial\ResourceModel
	 * which directly inherits from @see \Magento\Framework\Model\ResourceModel\AbstractResource,
	 * and automatic `afterCommitCallback` call is only implemented in the following classes:
	 * 1) \Magento\Framework\Model\ResourceModel\Db\AbstractDb::save():
	 * 		$this->addCommitCallback([$object, 'afterCommitCallback'])->commit();
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Model/ResourceModel/Db/AbstractDb.php#L388-L416
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/Model/ResourceModel/Db/AbstractDb.php#L398-L430
	 * @return $this
	 */
	function save() {
		/**
		 * 2017-12-04
		 * @uses \Magento\Framework\Model\AbstractModel::afterCommitCallback()
		 * @used-by \Magento\Framework\Model\ResourceModel\AbstractResource::commit():
		 *		$this->getConnection()->commit();
		 *		if ($this->getConnection()->getTransactionLevel() === 0) {
		 *			$callbacks = CallbackPool::get(spl_object_hash($this->getConnection()));
		 *			try {
		 *				foreach ($callbacks as $callback) {
		 *					call_user_func($callback);
		 *				}
		 *			}
		 *			catch (\Exception $e) {
		 *				$this->getLogger()->critical($e);
		 *			}
		 *		}
		 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/Model/ResourceModel/AbstractResource.php#L81-L103
		 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/Model/ResourceModel/AbstractResource.php#L82-L105
		 */
		$this->getResource()->addCommitCallback([$this, 'afterCommitCallback']);
		return $this;
	}
}


