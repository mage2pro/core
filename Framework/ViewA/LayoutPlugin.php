<?php
namespace Df\Framework\ViewA;
use Magento\Framework\View\Layout;
use Magento\Ui\Component\Wrapper\UiComponent;
/**
 * 2016-01-01
 * Наследуемся от класса @uses \Magento\Ui\Component\Wrapper\UiComponent,
 * чтобы получить доступ к его свойству @uses \Magento\Ui\Component\Wrapper\UiComponent::$component
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Ui/Component/Wrapper/UiComponent.php#L25
 */
class LayoutPlugin extends UiComponent {
	/**
	 * 2016-01-01
	 * Потрясающая техника, которую я изобрёл только что.
	 */
	public function __construct() {}

	/**
	 * 2015-09-19
	 * Цель метода — получение информации о формируемом в данный момент компоненте
	 * с целью индивидуального языкового перевода его интерфейса.
	 * @see Layout::renderNonCachedElement()
	 * @param Layout $subject
	 * @param \Closure $proceed
	 * @param string $name
	 * @return string
	 */
	public function aroundRenderNonCachedElement(Layout $subject, \Closure $proceed, $name) {
		/** @var UiComponent|null $wrapper */
		$wrapper = $subject->isUiComponent($name) ? $subject->getUiComponent($name) : null;
		/** @var string $result */
		if ($wrapper) {
			df_state()->componentSet($wrapper->component);
		}
		try {
			$result = $proceed($name);
		}
		finally {
			if ($wrapper) {
				df_state()->componentSetPrev();
			}
		}
		return $result;
	}
}