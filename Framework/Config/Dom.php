<?php
namespace Df\Framework\Config;
use \DOMDocument as Doc;
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
	<type name='Magento\Config\Model\Config\Structure\Reader'>
		<arguments>
			<argument name='domDocumentClass' xsi:type='string'>Df\Framework\Config\Dom</argument>
		</arguments>
	</type>
 */
class Dom extends \Magento\Framework\Config\Dom {
	/**
	 * 2015-11-15
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Config\Dom::validate()
	 * @param string $schemaFileName
	 * @param array $errors
	 * @throws \Exception
	 */
	function validate($schemaFileName, &$errors = []):bool {
		parent::validate($schemaFileName, $errors);
		$errors = array_filter($errors, function($message) {
			/** @var string $message */
			# 2015-11-15
			# Не отключаем валидацию составного файла полностью,
			# а лишь убираем их диагностического отчёта сообщения о сбоях в наших полях.
			return
				# 2015-11-15
				# «Element 'dfSample': This element is not expected. Line: 55»
				# https://github.com/mage2pro/core/tree/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L207
				!df_contains($message, 'Element \'df')
				# 2015-12-29
				# https://github.com/mage2pro/core/tree/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L70
				# «Element 'field', attribute 'dfItemFormElement': The attribute 'dfItemFormElement' is not allowed.»
				&& !df_contains($message, 'attribute \'df')
			;
		});
		return !$errors;
	}

	/**
	 * @override
	 * @see \Magento\Framework\Config\Dom::_initDom()
	 * @param string $xml
	 * @throws \Magento\Framework\Config\Dom\ValidationException
	 */
	protected function _initDom($xml):Doc {
		$defaultSchema = $this->schema; /** @var string $defaultSchema */
		$doc = new Doc; /** @var Doc $doc */
		$doc->loadXML($xml);
		# Возвращает строку вида: «urn:magento:module:Magento_Config:etc/system_file.xsd»
		/** @var string $schema */
		$schema = $doc->documentElement->getAttributeNS($doc->lookupNamespaceUri('xsi'), 'noNamespaceSchemaLocation');
		# Используем df_starts_with($customSchema, 'urn:')
		# для совместимости с устаревшим и нигде в ядре не используемым форматом с обратными файловыми путями: ../
		# 2016-06-07
		# Раньше тут стояло:
		# 		if ($schema && df_starts_with($schema, 'urn:')
		# Однако сторонние модули используют хуёвые невалидные схемы типа
		# «urn:magento:framework:Backend/etc/system_file.xsd», что приводило к сбоям.
		if ('urn:magento:module:Df_Config:etc/system_file.xsd' === $schema) {
			/**
			 * Переводить схему в формат файлового пути необязательно:
			 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Config/Dom/UrnResolver.php#L69-L71
			 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/Config/Dom/UrnResolver.php#L26-L55
			 */
			$this->schema = $schema;
		}
		/** @var Doc $r */
		try {$r = parent::_initDom($xml);}
		finally {
			# 2023-07-17
			# 1) I have had a wrong code for 7 years:
			#		$r->schema = $defaultSchema;
			# https://github.com/mage2pro/core/blob/9.5.0/Framework/Config/Dom.php#L81
			# 2) I have noticed the mistake only with PHH 8.2:
			# "[PHP 8.2] «Creation of dynamic property DOMDocument::$schema is deprecated
			# in vendor/mage2pro/core/Framework/Config/Dom.php on line 81»":
			# https://github.com/mage2pro/core/issues/215
			$this->schema = $defaultSchema;
		}
		return $r;
	}
}