<?php
namespace Df\Framework\App\Config;
/**
 * @method mixed|null getValue()
 */
abstract class Value extends \Magento\Framework\App\Config\Value {
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
	 * @see \Df\Framework\App\Config\Value::save()
	 * @return $this
	 */
	public function save() {
		try {
			$this->dfSaveBefore();
			parent::save();
		}
		finally {
			$this->dfSaveAfter();
		}
		return $this;
	}

	/**
	 * 2015-12-07
	 * @used-by \Df\Framework\App\Config\Value::save()
	 * @return void
	 */
	protected function dfSaveAfter() {}

	/**
	 * 2015-12-07
	 * @used-by \Df\Framework\App\Config\Value::save()
	 * @return void
	 */
	protected function dfSaveBefore() {}

	/**
	 * 2015-12-07
	 * @return array(string => mixed)
	 */
	protected function value() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_a_deep($this->_data, $this->valuePathA());
			df_result_array($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-07
	 * @return string[]
	 */
	private function valuePathA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = ['groups', $this->pathA(1), 'fields', $this->pathA(2)];
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-12-06
	 * @param int $index [optional]
	 * @return string[]
	 */
	private function pathA($index = null) {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = explode('/', $this->getPath());
		}
		return is_null($index) ? $this->{__METHOD__} : $this->{__METHOD__}[$index];
	}
}