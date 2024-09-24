<?php
use Magento\Framework\Simplexml\Element as MX;
use SimpleXMLElement as X;

/**
 * 2016-09-01 The result does not include the XML header. @see df_xml_header()
 * @used-by df_xml_assert_leaf()
 * @used-by df_xml_children()
 * @used-by df_xml_parse_header()
 * @used-by df_xml_prettify()
 * @used-by df_xml_s()
 * @used-by Df\Xml\G::__toString()
 * @param X|MX|string $x
 */
function df_xml_s($x, int $level = 0):string {/** @var string $r */
	if (is_string($x)) {
		$r = $x;
	}
	else {
		/**
		 * 2015-02-27
		 * Для конвертации объекта класса @see SimpleXMLElement в строку
		 * надо использовать именно метод @uses SimpleXMLElement::asXML(),
		 * а не @see SimpleXMLElement::__toString() или оператор (string)$this.
		 * @see SimpleXMLElement::__toString() и (string)$this
		 * возвращают непустую строку только для концевых узлов (листьев дерева XML).
		 * Пример:
		 *	<?xml version='1.0' encoding='utf-8'?>
		 *		<menu>
		 *			<product>
		 *				<cms>
		 *					<class>aaa</class>
		 *					<weight>1</weight>
		 *				</cms>
		 *				<test>
		 *					<class>bbb</class>
		 *					<weight>2</weight>
		 *				</test>
		 *			</product>
		 *		</menu>
		 * Здесь для $e1 = $xml->{'product'}->{'cms'}->{'class'}
		 * мы можем использовать $e1->__toString() и (string)$e1.
		 * http://3v4l.org/rAq3F
		 * Однако для $e2 = $xml->{'product'}->{'cms'}
		 * мы не можем использовать $e2->__toString() и (string)$e2,
		 * потому что узел «cms» не является концевым узлом (листом дерева XML): http://3v4l.org/Pkj37
		 * Более того, метод @see SimpleXMLElement::__toString() отсутствует в PHP версий 5.2.17 и ниже:
		 * http://3v4l.org/Wiia2#v500
		 * @see \Magento\Framework\Simplexml\Element::asNiceXml()
		 */
		/** @var string $nl */ /** @var string $pad */
		[$nl, $pad] = !$level ? ['', ''] : ["\n", str_pad('', $level * 1, "\t", STR_PAD_LEFT)];
		$r = "$pad<{$x->getName()}";
		# 2024-09-22 https://www.php.net/manual/en/simplexmlelement.attributes.php
		if ($aa = $x->attributes('xsi', true)) { /** @var ?X $aa */
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
			foreach ($x->children() as $child) {/** @var X $child */
				$r .= df_xml_s($child, ++$level);
			}
			$r .= "$pad</{$x->getName()}>$nl";
		}
	}
	return $r;
}

/**
 * 2015-02-27
 * Возвращает документ XML в виде текста без заголовка XML.
 * Раньше алгоритм был таким:
 * 		str_replace('<?xml version="1.0"?>', '', $this->asXML());
 * Однако этот алгоритм неверен: ведь в заголовке XML может присутствовать указание кодировки, например:
 * 		<?xml version='1.0' encoding='utf-8'?>
 * Новый алгоритм взят отсюда: http://stackoverflow.com/a/5947858
 * 2024-09-22 @deprecated
 */
function df_xml_s_simple(X $x):string {
	$dom = dom_import_simplexml($x); /** @var \DOMElement $dom */
	/**
	 * 2021-12-13
	 * @uses \DOMDocument::saveXML() can return `false`:
	 * https://php.net/manual/domdocument.savexml.php#refsect1-domdocument.savexml-returnvalues
	 */
	return df_assert_nef($dom->ownerDocument->saveXML($dom->ownerDocument->documentElement));
}