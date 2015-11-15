<?php
namespace Df\Config;
use Magento\Framework\Option\ArrayInterface;
/**
 * 2015-11-14
 * Благодаря @see \Df\Config\Model\Config\SourceFactoryPlugin
 * потомки этого класса не являются объектами-одиночками.
 */
abstract class Source extends \Df\Core\O implements ArrayInterface {
	/**
	 * 2015-11-14
	 * @used-by \Df\Config\Source::toOptionArray()
	 * @return array(string => string)
	 */
	abstract protected function map();

	/**
	 * 2015-11-14
	 * @override
	 * @see \Magento\Framework\Option\ArrayInterface::toOptionArray()
	 * @return array(array(string => string))
	 */
	public function toOptionArray() {return df_map_to_options_t($this->map());}

	/**
	 * 2015-11-14
	 * Возаращает по имени атрибут или содержимое дочернего тега для настроечного поля.
	 * Например, пусть есть поле:
			<field
				id='visibility'
				translate='label'
				type='select'
				sortOrder='1'
				showInDefault='1'
				showInWebsite='1'
				showInStore='1'
			>
				<label>Visibility</label>
				<source_model>Dfe\Sku\ConfigSource\Visibility</source_model>
				<comment><![CDATA[<a href='https://mage2.pro/t/197'>Documentation.</a>]]></comment>
			</field>
	 * Тогда
	 * $this->field()->getData() вернёт такой массив:
		array(
			[_elementType] => field
			[comment] => <a href='https://mage2.pro/t/197'>Documentation.</a>
			[id] => visibility
			[label] => Visibility
			[path] => dfe_sku/frontend
			[showInDefault] => 1
			[showInStore] => 1
			[showInWebsite] => 1
			[sortOrder] => 1
			[source_model] => Dfe\Sku\ConfigSource\Visibility
			[translate] => label
			[type] => select
		)
	 * Обратите внимание: массив содержит и атрибуты, и детей.
	 *
	 * @param string $key
	 * @return string|null
	 */
	protected function f($key) {return $this->field()->getAttribute($key);}

	/** @return string */
	private function codeField() {return $this->pathA()[2];}

	/** @return string */
	private function codeGroup() {return $this->pathA()[1];}

	/** @return string */
	private function codeSection() {return $this->pathA()[0];}

	/**
	 * 2015-11-14
	 * Очень красивое и неожиданное решение.
	 * Оказывается, Magento 2 использует для настроечных полей
	 * шаблон проектирования «Приспособленец»:
	 * https://ru.wikipedia.org/wiki/Приспособленец_(шаблон_проектирования)
	 * Поэтому настроечное поле является объектом-одиночкой,
	 * и мы можем получить его из реестра.
	 *
	 * https://mage2.pro/t/212
	 *
	 * 1)
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator\Field::__construct()
		public function __construct(
			\Magento\Config\Model\Config\Structure\Element\Group $groupFlyweight,
			\Magento\Config\Model\Config\Structure\Element\Field $fieldFlyweight
		) {
			$this->_groupFlyweight = $groupFlyweight;
			$this->_fieldFlyweight = $fieldFlyweight;
		}
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Iterator/Field.php#L30
	 *
	 * 2)
	 * @see \Magento\Config\Model\Config\Structure\Element\Group::__construct()
		public function __construct(
			(...)
			\Magento\Config\Model\Config\Structure\Element\Iterator\Field $childrenIterator,
			(...)
		) {
			parent::__construct($storeManager, $moduleManager, $childrenIterator);
			(...)
		}
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Group.php#L36
	 *
	 * 3)
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator::current()
		public function current()
		{
		return $this->_flyweight;
		}
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Iterator.php#L68-L71
	 *
	 * @see \Magento\Config\Model\Config\Structure\Element\Iterator::next()
		public function next()
		{
			next($this->_elements);
			if (current($this->_elements)) {
				$this->_initFlyweight(current($this->_elements));
				if (!$this->current()->isVisible()) {
					$this->next();
				}
			}
		 }
	 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Iterator.php#L78-L87
	 *
	 *
	 * @return \Magento\Config\Model\Config\Structure\Element\Field
	 */
	private function field() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_o('Magento\Config\Model\Config\Structure\Element\Field');
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2015-11-14
	 * Путь вида «dfe_sku/frontend/visibility».
	 * @return string
	 */
	private function path() {return $this[self::$P__PATH];}

	/** @return string[] */
	private function pathA() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_explode_xpath($this->path());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @see https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L426-L428
		if ($sourceModel instanceof \Magento\Framework\DataObject) {
			$sourceModel->setPath($this->getPath());
		}
	 * @var string
	 */
	private static $P__PATH = 'path';
}