<?php
namespace Df\Framework\Config\Dom;
use \DOMDocument as Doc;
/**
 * 2023-08-01
 * @used-by \Df\Framework\Config\Dom
 * @used-by \Df\Variable\Model\Config\Structure\Dom()
 */
trait T {
	/**
	 * 2015-11-15
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Config\Dom::validate()
	 * @used-by \Magento\Config\Model\Config\Structure\Reader::_readFiles():
	 *	if ($this->validationState->isValidationRequired()) {
	 *		$errors = [];
	 *		if ($configMerger && !$configMerger->validate($this->_schemaFile, $errors)) {
	 *			$message = "Invalid Document \n";
	 *			throw new LocalizedException(
	 *				new \Magento\Framework\Phrase($message . implode("\n", $errors))
	 *			);
	 *		}
	 *	}
	 * https://github.com/magento/magento2/blob/2.4.7-beta1/app/code/Magento/Config/Model/Config/Structure/Reader.php#L111-L116
	 * @param string $schemaFileName
	 * @param array $errors
	 * @throws \Exception
	 */
	function validate($schemaFileName, &$errors = []):bool {return L::validate($this, $schemaFileName, $errors);}

	/**
	 * 2015-11-15
	 * @override
	 * @see \Magento\Framework\Config\Dom::_initDom()
	 * @used-by \Magento\Framework\Config\Dom::__construct():
	 *		$this->dom = $this->_initDom($xml);
	 * https://github.com/magento/magento2/blob/2.4.7-beta1/lib/internal/Magento/Framework/Config/Dom.php#L114
	 * @param string $xml
	 * @throws \Magento\Framework\Config\Dom\ValidationException
	 */
	protected function _initDom($xml):Doc {return L::init($this, $xml);}
}