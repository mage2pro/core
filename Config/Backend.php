<?php
namespace Df\Config;
use Magento\Config\Model\Config\Structure\AbstractElement as ConfigElement;
use Magento\Config\Model\Config\Structure\Element\Field;
use Magento\Config\Model\Config\Structure\Element\Group;
use Magento\Config\Model\Config\Structure\Element\Section;
use Magento\Config\Model\Config\Structure\ElementInterface as IConfigElement;
use Magento\Framework\Phrase;
/**
 * @method mixed|null getValue()
 */
class Backend extends \Magento\Framework\App\Config\Value {
	/**
	 * 2015-12-07
	 * Конечно, хотелось бы задействовать стандартные методы
	 * @see \Magento\Framework\Model\AbstractModel::beforeSave() и
	 * @see \Magento\Framework\Model\AbstractModel::afterSave()
	 * или же
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_beforeSave() и
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_afterSave()
	 * или же
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::_serializeFields() и
	 * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::unserializeFields()
	 * однако меня смутило, что в случае исключительной ситуации
	 * модель может остаться в несогласованном состоянии:
	 * https://mage2.pro/t/283
	 * https://mage2.pro/t/284
	 * Поэтому разработал свои аналогичные методы.
	 *
	 * @override
	 * @see \Magento\Framework\App\Config\Value::save()
	 * @return $this
	 * @throws \Exception
	 */
	public function save() {
		try {
			$this->dfSaveBefore();
			parent::save();
		}
		catch (\Exception $e) {
			df_log($e);
			throw df_le($e);
		}
		finally {
			$this->dfSaveAfter();
		}
		return $this;
	}

	/**
	 * 2016-08-02
	 * @return string
	 */
	protected function label() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $path */
			$path = $this->fc('path');
			/** @var string[] $pathA */
			$pathA = explode('/', $path);
			/** @var Phrase[] $resultA */
			$resultA = [];
			/** @var IConfigElement|ConfigElement|Section|null $e */
			while ($pathA && ($e = df_config_structure()->getElementByPathParts($pathA))) {
				$resultA[]= $e->getLabel();
				array_pop($pathA);
			}
			$resultA[]= df_config_tab_label($e);
			$resultA = array_reverse($resultA);
			$resultA[]= $this->labelShort();
			$this->{__METHOD__} = implode(' → ', df_quote_russian($resultA));
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-02
	 * @return Phrase
	 */
	protected function labelShort() {return __($this->fc('label'));}

	/**
	 * 2015-12-07
	 * @used-by \Df\Config\Backend::save()
	 * @return void
	 */
	protected function dfSaveAfter() {}

	/**
	 * 2015-12-07
	 * @used-by \Df\Config\Backend::save()
	 * @return void
	 */
	protected function dfSaveBefore() {}

	/**
	 * 2016-08-02
	 * @used-by \Df\Config\Plugin\Model\Config\Structure\Element\Field::afterGetBackendModel()
	 * @param Field $field
	 * @return void
	 */
	public function dfSetField(Field $field) {$this->_field = $field;}

	/**
	 * 2016-07-31
	 * @see \Df\Config\Backend::isSaving()
	 * @param string|null $key [optional]
	 * @param string|null|callable $default [optional]
	 * @return string|null|array(string => mixed)
	 */
	protected function fc($key = null, $default = null) {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => mixed) $result */
			$result = $this->_field ? $this->_field->getData() : $this['field_config'];
			df_assert($result);
			$this->{__METHOD__} = $result;
		}
		return dfa($this->{__METHOD__}, $key, $default);
	}

	/**
	 * 2016-07-31
	 * @see \Df\Config\Backend::fc()
	 * @return bool
	 */
	protected function isSaving() {return isset($this->_data['field_config']);}

	/**
	 * 2015-12-07
	 * 2016-01-01
	 * Сегодня заметил, что Magento 2, в отличие от Magento 1.x,
	 * допускает ирерархическую вложенность групп настроек большую, чем 3, например:
	 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Cron/etc/adminhtml/system.xml#L14
	 * В Magento 1.x вложенность всегда такова: section / group / field.
	 * В Magento 2 вложенность может быть такой: section / group / group / field.
	 * @return array(string => mixed)
	 */
	protected function value() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $pathA */
			$pathA = array_slice(df_explode_xpath($this->getPath()), 1);
			/** @var string $fieldName */
			$fieldName = array_pop($pathA);
			/** @var string $path */
			$path = 'groups/' . implode('/groups/', $pathA) . '/fields/' . $fieldName;
			$this->{__METHOD__} = dfa_deep($this->_data, $path);
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-08-02
	 * @used-by \Df\Config\Backend::dfSetField()
	 * @used-by \Df\Config\Backend::fc()
	 * @var Field|null
	 */
	private $_field;
}