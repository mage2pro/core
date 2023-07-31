<?php
namespace Df\Framework\Config;
use Magento\Framework\Config\Dom as _P;
use \DOMDocument as Doc;
# 2023-07-31
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation */
class DomL extends _P {
	final static function init(_P $o, string $xml) {
		$defaultSchema = $o->schema; /** @var string $defaultSchema */
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
			$o->schema = $schema;
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
			$o->schema = $defaultSchema;
		}
		return $r;
	}
}