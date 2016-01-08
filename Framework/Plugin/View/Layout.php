<?php
namespace Df\Framework\Plugin\View;
use Magento\Framework\View\Layout as Sb;
use Magento\Ui\Component\Wrapper\UiComponent;
/**
 * 2016-01-01
 * Наследуемся от класса @uses \Magento\Ui\Component\Wrapper\UiComponent,
 * чтобы получить доступ к его свойству @uses \Magento\Ui\Component\Wrapper\UiComponent::$component
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Ui/Component/Wrapper/UiComponent.php#L25
 */
class Layout extends UiComponent {
	/**
	 * 2016-01-01
	 * Потрясающая техника, которую я изобрёл только что.
	 */
	public function __construct() {}

	/**
	 * 2015-09-19
	 * Цель метода — получение информации о формируемом в данный момент компоненте
	 * с целью индивидуального языкового перевода его интерфейса.
	 * @see \Magento\Framework\View\Layout::renderNonCachedElement()
	 * @param Sb $sb
	 * @param \Closure $proceed
	 * @param string $name
	 * @return string
	 */
	public function aroundRenderNonCachedElement(Sb $sb, \Closure $proceed, $name) {
		/** @var UiComponent|null $wrapper */
		$wrapper = $sb->isUiComponent($name) ? $sb->getUiComponent($name) : null;
		/** @var string $result */
		$wrapper ? df_state()->componentSet($wrapper->component) : null;
		try {$result = $proceed($name);}
		finally {$wrapper ? df_state()->componentSetPrev() : null;}
		return $result;
	}
}