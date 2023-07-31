<?php
namespace Df\Variable\Model\Config\Structure;
/**
 * 2023-07-31
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation
 * [`/admin/catalog/category/` / `Magento_Variable`]:
 * «The XML in file "vendor/mage2pro/<…>/etc/adminhtml/system.xml" is invalid» /
 * «Element 'dfExtension': This element is not expected»: https://github.com/mage2pro/core/issues/297
 * 2) The falure is caused by the `Magento_Variable` module:
 * 		<virtualType
 * 			name="Magento\Variable\Model\Config\Structure\ReaderVirtual"
 * 			type="Magento\Config\Model\Config\Structure\Reader"
 * 		>
 * 			<arguments>
 * 				<argument name="domDocumentClass" xsi:type="string">Magento\Variable\Model\Config\Structure\Dom</argument>
 * 			</arguments>
 * 		</virtualType>
 * https://github.com/magento/magento2/blob/2.4.7-beta1/app/code/Magento/Variable/etc/di.xml#L56-L60
 * 3) The `Magento_Variable` module exists in Magento since 2.0.0:
 * https://github.com/magento/magento2/tree/2.0.0/app/code/Magento/Variable
 * So I can implement a fix similar to https://github.com/mage2pro/core/blob/10.1.6/Config/etc/di.xml#L14-L28
 */
class Dom extends \Magento\Variable\Model\Config\Structure\Dom {}