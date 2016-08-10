<?php
namespace Df\Core\Helper;
class Output {
	/**
	 * @param string $text
	 * @return string
	 */
	public function _($text) {return df_e($text);}

	/**
	 * @param string $xml
	 * @return string
	 */
	public function formatXml($xml) {
		df_param_string($xml, 0);
		/** @var \DOMDocument $domDocument */
		$domDocument = new \DOMDocument();
		/** @var bool $r */
		$r = $domDocument->loadXML($xml);
		df_assert(TRUE === $r);
		$domDocument->formatOutput = true;
		/** @var string $result */
		$result = $domDocument->saveXML();
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getXmlHeader() {return '<'.'?xml version="1.0" encoding="UTF-8"?'.'>'."\n";}

	/**
	 * @param mixed[] $data
	 * @return string
	 */
	public function json(array $data) {
		return \Zend_Json::encode($data, $this->getJsonEncoderOptions());
	}

	/**
	 * @param string|null $string
	 * @param string $delimiter [optional]
	 * @return string[]
	 */
	public function parseCsv($string, $delimiter = ',') {
		return !$string ? [] : df_trim(explode($delimiter, $string));
	}

	/** @return int */
	private function getJsonEncoderOptions() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = 0;
			/**
			 * Использование кавычек обязательно!
			 * http://php.net/manual/function.defined.php (пример 1)
			 * http://magento-forum.ru/topic/4190/
			 */
			if (defined('JSON_FORCE_OBJECT')) {
				$result |= JSON_FORCE_OBJECT;
			}
			if (defined('JSON_UNESCAPED_UNICODE')) {
				$result |= JSON_UNESCAPED_UNICODE;
			}
			if (defined('JSON_NUMERIC_CHECK')) {
				$result |= JSON_NUMERIC_CHECK;
			}
			if (defined('JSON_PRETTY_PRINT')) {
				$result |= JSON_PRETTY_PRINT;
			}
			if (defined('JSON_PRETTY_PRINT')) {
				$result |= JSON_PRETTY_PRINT;
			}
			if (defined('JSON_FORCE_OBJECT')) {
				$result |= JSON_FORCE_OBJECT;
			}
			if (defined('JSON_UNESCAPED_SLASHES')) {
				$result |= JSON_UNESCAPED_SLASHES;
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}