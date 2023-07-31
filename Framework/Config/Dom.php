<?php
namespace Df\Framework\Config;
/**
 * 2015-11-15
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * Цель перекрытия — устранение дефекта
 * https://github.com/magento/magento2/issues/2372
 * «Magento 2 ignores XML schema location in the etc/adminhtml/system.xml documents
 * and always uses the Magento/Config/etc/system_file.xsd schema instead».
 *
 * Перекрытие происходит только для @used-by \Magento\Config\Model\Config\Structure\Reader
 * https://mage2.pro/t/215
 *	<type name='Magento\Config\Model\Config\Structure\Reader'>
 *		<arguments>
 *			<argument name='domDocumentClass' xsi:type='string'>Df\Framework\Config\Dom</argument>
 *		</arguments>
 *	</type>
 */
class Dom extends \Magento\Framework\Config\Dom {use Dom\T;}