<?php
namespace Df\Core;
class Reflection extends \Df\Core\O {
	/**
	 * «Df_1C_Cml2_Action_Catalog_Export_Process» => «cml2.action.catalog.export.process»
	 * «Df_1C_Cml2_Export_Document_Catalog» => «export.document.catalog»
	 * @param \Magento\Framework\Model\AbstractModel $model
	 * @param string $separator
	 * @param int $offsetLeft [optional]
	 * @return string
	 */
	public function getModelId(\Magento\Framework\Model\AbstractModel $model, $separator, $offsetLeft = 0) {
		/** @var string $className */
		$className = get_class($model);
		if (!isset($this->{__METHOD__}[$className][$separator][$offsetLeft])) {
			$this->{__METHOD__}[$className][$separator][$offsetLeft] =
				implode('.', array_slice(df_t()->lcfirst(rm_explode_class($className)), 3 + $offsetLeft))
			;
		}
		return $this->{__METHOD__}[$className][$separator][$offsetLeft];
	}

	/**
	 * @used-by rm_module_name()
	 * «Df_SalesRule_Model_Event_Validator_Process» => «Df_SalesRule»
	 * @param string $className
	 * @return string
	 */
	public function getModuleName($className) {
		if (!isset($this->{__METHOD__}[$className])) {
			$this->{__METHOD__}[$className] = implode('_',
				array_slice(rm_explode_class($className), 0, 2)
			);
		}
		return $this->{__METHOD__}[$className];
	}

	/**
	 * «Df_Checkout_OnepageController» => «Df_Checkout»
	 * «Mage_Adminhtml_Customer_Wishlist_Product_Composite_WishlistController» => «Mage_Adminhtml»
	 * @param $controllerClassName string
	 * @return string
	 */
	public function getModuleNameByControllerClassName($controllerClassName) {
		/**
		 * Например:
		 * [«Df», «Checkout», «OnepageController»]
		 * [«Mage», «Adminhtml», «Customer», «Wishlist», «Product», «Composite», «WishlistController»]
		 * @var string[] $classNameParts
		 */
		$classNameParts = rm_explode_class($controllerClassName);
		/**
		 * Например:
		 * [«Df», «Checkout»]
		 * [«Mage», «Adminhtml»]
		 * @var string[] $moduleNameParts
		 */
		$moduleNameParts = array_slice($classNameParts, 0, 2);
		/**
		 * Например:
		 * «Df_Checkout»
		 * «Mage_Adminhtml»
		 */
		return implode('_', $moduleNameParts);
	}

	/** @return Reflection */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}