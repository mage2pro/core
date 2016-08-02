<?php
namespace Df\Config\Plugin\Model\Config\Structure\Element;
use Df\Config\Backend;
use Magento\Framework\App\Config\ValueInterface as IBackend;
use Magento\Config\Model\Config\Structure\Element\Field as Sb;
class Field {
	/**
	 * 2016-08-02
	 * The plugin purpose is
	 * to pass the @see \Magento\Config\Model\Config\Structure\Element\Field instance
	 * to a backend model instance on the backend model creation:
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Block/System/Config/Form.php#L314
	 * https://github.com/magento/magento2/blob/2.1.0/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L214-L222
	 * It allow us to use the field's configuration
	 * when a config value is loaded from DB.
	 *
	 * @see \Magento\Config\Model\Config\Structure\Element\Field::getBackendModel()
	 * @param Sb $sb
	 * @param IBackend|Backend $result
	 * @return string
	 */
	public function afterGetBackendModel(Sb $sb, IBackend $result) {
		if ($result instanceof Backend) {
			$result->dfSetField($sb);
		}
		return $result;
	}
}