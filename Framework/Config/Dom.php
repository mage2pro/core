<?php
namespace Df\Framework\Config;
/**
 * 2015-11-15
 * Цель перекрытия — устранение дефекта
 * https://github.com/magento/magento2/issues/2372
 * «Magento 2 ignores XML schema location in the etc/adminhtml/system.xml documents
 * and always uses the Magento/Config/etc/system_file.xsd schema instead».
 *
 * Перекрытие происходит только для @used-by \Magento\Config\Model\Config\Structure\Reader
 * https://mage2.pro/t/215
	<type name='Magento\Config\Model\Config\Structure\Reader'>
		<arguments>
			<argument name='domDocumentClass' xsi:type='string'>Df\Framework\Config\Dom</argument>
		</arguments>
	</type>
 */
class Dom extends \Magento\Framework\Config\Dom {
	/**
	 * 2015-11-15
	 * @override
	 * @see \Magento\Framework\Config\Dom::validate()
	 * @param string $schemaFileName
	 * @param array $errors
	 * @return bool
	 * @throws \Exception
	 */
	function validate($schemaFileName, &$errors = []) {
		parent::validate($schemaFileName, $errors);
		$errors = array_filter($errors, function($message) {
			/** @var string $message */
			// 2015-11-15
			// Не отключаем валидацию составного файла полностью,
			// а лишь убираем их диагностического отчёта сообщения о сбоях в наших полях.
			return
				// 2015-11-15
				// «Element 'dfSample': This element is not expected. Line: 55»
				// https://github.com/mage2pro/core/tree/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L207
				!df_contains($message, 'Element \'df')
				// 2015-12-29
				// https://github.com/mage2pro/core/tree/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L70
				// «Element 'field', attribute 'dfItemFormElement': The attribute 'dfItemFormElement' is not allowed.»
				&& !df_contains($message, 'attribute \'df')
			;
		});
		return !$errors;
	}

	/**
	 * @override
	 * @see \Magento\Framework\Config\Dom::_initDom()
	 * @param string $xml
	 * @return \DOMDocument
	 * @throws \Magento\Framework\Config\Dom\ValidationException
	 */
	protected function _initDom($xml) {
		/** @var string $defaultSchema */
		$defaultSchema = $this->schema;
		/** @var \DOMDocument $dom */
		$dom = new \DOMDocument;
		$dom->loadXML($xml);
		// Возвращает строку вида: «urn:magento:module:Magento_Config:etc/system_file.xsd»
		/** @var string $schema */
		$schema = $dom->documentElement->getAttributeNS(
			$dom->lookupNamespaceUri('xsi'), 'noNamespaceSchemaLocation'
		);
		/**
		 * Используем df_starts_with($customSchema, 'urn:')
		 * для совместимости с устаревшим и нигде в ядре не используемым форматом
		 * с обратными файловыми путями: ../
		 *
		 * 2016-06-07
		 * Раньше тут стояло:
		 * if ($schema && && df_starts_with($schema, 'urn:')
		 * Однако сторонние модули используют хуёвые невалидные схемы типа
		 * urn:magento:framework:Backend/etc/system_file.xsd
		 * что приводило к сбоям.
		 */
		if ($schema && 'urn:magento:module:Df_Config:etc/system_file.xsd' === $schema) {
			/**
			 * Переводить схему в формат файлового пути необязательно:
			 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Config/Dom/UrnResolver.php#L69-L71
			 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Config/Dom/UrnResolver.php#L26-L55
			 */
			$this->schema = $schema;
		}
		/** @var \DOMDocument $result */
		try {
			$result = parent::_initDom($xml);
		}
		finally {
			$this->schema = $defaultSchema;
		}
		return $result;
	}
}