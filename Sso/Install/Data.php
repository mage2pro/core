<?php
namespace Df\Sso\Install;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
abstract class Data implements InstallDataInterface {
	/**
	 * 2016-06-05
	 * @used-by \Df\Sso\Install\Data::attribute()
	 * @return string
	 */
	abstract protected function labelPrefix();

	/**             
	 * 2016-06-04
	 * @return string
	 */
	abstract protected function schemaClass();
	
	/**
	 * 2016-06-04
	 * @override
	 * @see \Magento\Framework\Setup\InstallDataInterface::install()
	 * @param ModuleDataSetupInterface $setup
	 * @param ModuleContextInterface $context
	 * @return void
	 */
	final public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
		$this->attribute($this->schema()->fName(), 'User Full Name');
		$this->attribute($this->schema()->fId(), 'User ID');
		$this->_install();
	}

	/**
	 * 2016-06-05
	 * 2016-08-21
	 * Этот метод намеренно не объявлен абстрактным, потому что, например,
	 * потомок @see \Dfe\AmazonLogin\Setup\InstallData не перекрывает его.
	 * @used-by \Df\Sso\Install\Data::install()
	 * @see \Dfe\FacebookLogin\Setup\InstallData::_install()
	 * @return void
	 */
	protected function _install() {}

	/**
	 * 2015-10-10
	 * @param string $name
	 * @param string $label
	 * @return void
	 */
	final protected function attribute($name, $label) {
		/** @var int $ordering */
		static $ordering = 1000;
		df_eav_setup()->addAttribute('customer', $name, [
			'type' => 'static',
			'label' => df_cc_s($this->labelPrefix(), $label),
			'input' => 'text',
			'sort_order' => $ordering,
			'position' => $ordering++,
			'visible' => false,
			'system' => false,
			'required' => false
		]);
		/** @var int $attributeId */
		$attributeId = df_first(df_fetch_col('eav_attribute', 'attribute_id', 'attribute_code', $name));
		df_conn()->insert(df_table('customer_form_attribute'), [
			'form_code' => 'adminhtml_customer', 'attribute_id' => $attributeId
		]);
	}

	/**
	 * 2016-06-04
	 * @return Schema
	 */
	private function schema() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_create($this->schemaClass());
		}
		return $this->{__METHOD__};
	}
}