<?php
namespace Df\Framework\ViewA;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\View\Layout;
use Magento\Ui\Component\AbstractComponent;
class LayoutPlugin {
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
		/** @var \Magento\Ui\Component\Wrapper\UiComponent|null $wrapper */
		$wrapper = $subject->isUiComponent($name) ? $subject->getUiComponent($name) : null;
		/** @var string $result */
		if ($wrapper) {
			df_state()->componentSet($wrapper[\Df\Framework\ViewA\Element\BlockFactoryPlugin::COMPONENT]);
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