<?php
# 2016-08-31  Портировал из Российской сборки Magento
namespace Df\Xml;
final class G extends \Df\Core\O {
	/**
	 * @see \Magento\Framework\Simplexml\Element::asNiceXml() не добавляет к документу заголовок XML: его надо добавить вручную.
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
	 * 2016-08-31
	 * @used-by df_xml_g()
	 * @param array(string => mixed) $contents
	 * @param array(string => mixed) $p [optional]
	 */
	static function p(string $tag, array $contents, array $p = []):string {
		$h = dfa($p, self::P__SKIP_HEADER, df_xml_header()); /** @var string $h */
		$x = df_xml_parse("$h\n<{$tag}/>"); /** @var X $x */
		$x->addAttributes(dfa($p, self::P__ATTRIBUTES, []));
		$x->importArray($contents);
		# Символ 0xB (вертикальная табуляция) допустим в UTF-8, но недопустим в XML: http://stackoverflow.com/a/10095901
		return str_replace("\x0B", "&#x0B;", dfa($p, self::P__SKIP_HEADER)? $x->asXMLPart() : df_cc_n($h, $x->asNiceXml()));
	}

	/**
	 * @used-by \Df\Framework\W\Result\Xml::__toString()
	 * @used-by \Dfe\Vantiv\API\Client::_construct()
	 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::t04()
	 */
	const P__ATTRIBUTES = 'attributes';

	/** @used-by self::_p() */
	const P__SKIP_HEADER = 'skip_header';
}