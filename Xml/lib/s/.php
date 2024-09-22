<?php
use Magento\Framework\Simplexml\Element as MX;
use SimpleXMLElement as CX;

/**
 * 2016-09-01 The result does not include the XML header. @see df_xml_header()
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
		# 2024-09-22 https://www.php.net/manual/en/simplexmlelement.attributes.php
		if ($aa = $x->attributes('xsi', true)) { /** @var ?CX $aa */
			foreach ($aa as $k => $v) {/** @var string $k */ /** @var mixed $v */
				$v = str_replace('"', '\"', (string)$v);
				$r .= " xsi:$k=\"$v\"";
			}
		};
		$xs = trim((string)$x); /** @var string $xs */
		$isEmpty = df_es($xs); /** @var bool $isEmpty */
		if (!$x->hasChildren()) {
			$r .= $isEmpty
				? '/>' . $nl
				/**
				 * 2021-12-16
				 * The previous code was: `$this->xmlentities($xs)`
				 * @see \Magento\Framework\Simplexml\Element::xmlentities()
				 */
				: '>' . df_cdata_raw_if_needed($xs) . "</{$x->getName()}>$nl"
			;
		}
		else {
			$r .= '>';
			if (!$isEmpty) {
				/**
				 * 2021-12-16
				 * The previous code was: `$this->xmlentities($xs)`
				 * @see \Magento\Framework\Simplexml\Element::xmlentities()
				 */
				$r .= df_cdata_raw_if_needed($xs);
			}
			$r .= $nl;
			foreach ($x->children() as $child) {/** @var CX $child */
				$r .= df_xml_s($child, ++$level);
			}
			$r .= "$pad</{$x->getName()}>$nl";
		}
	}
	return $r;
}