<?php
use Magento\Framework\Simplexml\Element as MX;
use SimpleXMLElement as CX;

/**
 * 2016-09-01
 * @see \Magento\Framework\Simplexml\Element::asNiceXml()
 * @used-by df_assert_leaf()
 * @used-by df_xml_children()
 * @used-by df_xml_g()
 * @used-by df_xml_parse_header()
 * @used-by df_xml_prettify()
 * @used-by df_xml_s()
 * @param CX|MX|string $x
 */
function df_xml_s($x, int $level = 0):string {/** @var string $r */
	if (is_string($x)) {
		$r = $x;
	}
	else {
		/** @var string $nl */ /** @var string $pad */
		[$nl, $pad] = !$level ? ['', ''] : ["\n", str_pad('', $level * 1, "\t", STR_PAD_LEFT)];
		$r = "$pad<{$x->getName()}";
		$attributes = $x->attributes();
		if ($attributes) {
			foreach ($attributes as $key => $value) {
				$r .= ' ' . $key . '="' . str_replace('"', '\"', (string)$value) . '"';
			}
		}
		$attributes = $x->attributes('xsi', true);
		if ($attributes) {
			foreach ($attributes as $key => $value) {
				$r .= ' xsi:' . $key . '="' . str_replace('"', '\"', (string)$value) . '"';
			}
		}
		if ($x->hasChildren()) {
			$r .= '>';
			$value = trim((string)$x);
			if (strlen($value)) {
				/**
				 * 2021-12-16
				 * The previous code was: `$this->xmlentities($value)`
				 * @see \Magento\Framework\Simplexml\Element::xmlentities()
				 */
				$r .= df_cdata_raw_if_needed($value);
			}
			$r .= $nl;
			foreach ($x->children() as $child) {/** @var CX $child */
				$r .= $child->asNiceXml('', is_numeric($level) ? $level + 1 : true);
			}
			$r .= $pad . '</' . $x->getName() . '>' . $nl;
		}
		else {
			$value = (string)$x;
			if (strlen($value)) {
				/**
				 * 2021-12-16
				 * The previous code was: `$this->xmlentities($value)`
				 * @see \Magento\Framework\Simplexml\Element::xmlentities()
				 */
				$r .= '>' . df_cdata_raw_if_needed($value) . '</' . $x->getName() . '>' . $nl;
			}
			else {
				$r .= '/>' . $nl;
			}
		}
		if ((0 === $level || false === $level) && !empty($filename)) {
			file_put_contents($filename, $r);
		}
	}
	return $r;
}