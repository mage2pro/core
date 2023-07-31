<?php
namespace Df\Framework\Config;
use Magento\Framework\Config\Dom as _P;
use \DOMDocument as Doc;
# 2023-08-01
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation */
final class DomL {
	/**
	 * 2023-08-01
	 * @see \Magento\Framework\Config\Dom::_initDom()
	 * @used-by \Df\Framework\Config\Dom::_initDom()
	 */
	static function init(_P $o, string $xml):Doc {
		$defaultSchema = dfr_prop_get($o, 'schema'); /** @var string $defaultSchema */
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
			dfr_prop_set($o, 'schema', $schema);
		}
		/** @var Doc $r */
		try {
			$r = df_call_parent($o, '_initDom', $xml);
		}
		finally {
			# 2023-07-17
			# 1) I have had a wrong code for 7 years:
			#		$r->schema = $defaultSchema;
			# https://github.com/mage2pro/core/blob/9.5.0/Framework/Config/Dom.php#L81
			# 2) I have noticed the mistake only with PHH 8.2:
			# "[PHP 8.2] «Creation of dynamic property DOMDocument::$schema is deprecated
			# in vendor/mage2pro/core/Framework/Config/Dom.php on line 81»":
			# https://github.com/mage2pro/core/issues/215
			dfr_prop_set($o, 'schema', $defaultSchema);
		}
		return $r;
	}

	/**
	 * 2015-11-15
	 * @see \Magento\Framework\Config\Dom::validate(
	 * @used-by \Df\Framework\Config\Dom::validate()
	 */
	static function validate(_P $o, string $schemaFileName, array &$errors = []):bool {
		df_call_parent($o, 'validate', $schemaFileName, $errors);
		return !array_filter($errors, function($m) {/** @var string $m */
			# 2015-11-15
			# Не отключаем валидацию составного файла полностью,
			# а лишь убираем их диагностического отчёта сообщения о сбоях в наших полях.
			return
				# 2015-11-15
				# «Element 'dfSample': This element is not expected. Line: 55»
				# https://github.com/mage2pro/core/tree/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L207
				!df_contains($m, 'Element \'df')
				# 2015-12-29
				# https://github.com/mage2pro/core/tree/57607cc23405c3dcde50999d063b2a7f49499260/Config/etc/system_file.xsd#L70
				# «Element 'field', attribute 'dfItemFormElement': The attribute 'dfItemFormElement' is not allowed.»
				&& !df_contains($m, 'attribute \'df')
			;
		});
	}
}