<?php
use Df\Core\Exception as E;
use Df\Xml\X;
use Magento\Framework\Simplexml\Element as MX;
use SimpleXMLElement as CX;

/**
 * @deprecated It is unused.
 * @return CX|null
 * @throws E
 */
function df_xml_child(CX $e, string $name, bool $req = false) {
	$childNodes = df_xml_children($e, $name, $req); /** @var CX[] $childNodes */
	if (is_null($childNodes)) { /** @var CX|null $r */
		$r = null;
	}
	else {
		/**
		 * Обратите внимание, что если мы имеем структуру:
		 *	<dictionary>
		 *		<rule/>
		 *		<rule/>
		 *		<rule/>
		 *	</dictionary>
		 * то $this->e()->{'rule'} вернёт не массив, а объект (!),
		 * но при этом @see count() для этого объекта работает как для массива (!),
		 * то есть реально возвращает количество детей типа rule.
		 * Далее, оператор [] также работает, как для массива (!)
		 * http://stackoverflow.com/a/16100099
		 * Класс @see \SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		df_assert_eq(1, count($childNodes));
		$r = $childNodes[0];
		df_assert($r instanceof CX);
	}
	return $r;
}

/**
 * @used-by df_xml_child()
 * @return CX|null
 * @throws E
 */
function df_xml_children(CX $e, string $name, bool $req = false) { /** @var CX|null $r */
	df_param_sne($name, 0);
	if (df_xml_exists_child($e, $name)) {
		/**
		 * Обратите внимание, что если мы имеем структуру:
		 *	<dictionary>
		 *		<rule/>
		 *		<rule/>
		 *		<rule/>
		 *	</dictionary>
		 * то $e->{'rule'} вернёт не массив, а объект (!),
		 * но при этом @see count() для этого объекта работает как для массива (!),
		 * то есть реально возвращает количество детей типа rule.
		 * Далее, оператор [] также работает, как для массива (!)
		 * http://stackoverflow.com/a/16100099
		 * Класс @see \SimpleXMLElement — вообще один из самых необычных классов PHP.
		 */
		$r = $e->{$name};
	}
	elseif (!$req) {
		$r = null;
	}
	else {
		df_error("Требуемый узел «{$name}» отсутствует в документе:\n{xml}", ['{xml}' => df_xml_report($e)]);
	}
	return $r;
}

/**
 * 2015-02-27
 * Алгоритм взят отсюда: http://stackoverflow.com/a/5344560
 * Проверил, что он работает: http://3v4l.org/tnEIJ
 * Обратите внимание, что isset() вместо empty() не сработает: http://3v4l.org/2P5o0
 * isset, однако, работает для проверки наличия дочерних листов: @see df_xml_exists_child()
 *
 * Обратите внимание, что оператор $e->{'тест'} всегда возвращает объект @see \SimpleXMLElement,
 * вне зависимости от наличия узла «тест», просто для отсутствующего узла данный объект будет пуст,
 * и empty() для него вернёт true.
 *
 * 2015-08-04
 * Заметил, что empty($e) для текстовых узлов всегда возвращает true,
 * даже если узел как строка приводится к true (например: «147»).
 * Например:
 * Например:
 *	<Остаток>
 *		<Склад>
 *			<Ид>6f87e83f-722c-11df-b336-0011955cba6b</Ид>
 *			<Количество>147</Количество>
 *		</Склад>
 *	</Остаток>
 * Если здесь сделать xpath Остаток/Склад/Количество,
 * то для узла «147» @see df_xml_exists($e) вернёт false.
 *
 * Обратите внимание, что эту особенность использует алгоритм @see df_check_leaf():
 * return !df_xml_exists($e) || !count($e->children());
 *
 * @used-by df_check_leaf()
 */
function df_xml_exists(CX $e = null):bool {return !empty($e);}

/**
 * http://stackoverflow.com/questions/1560827#comment20135428_1562158
 * @used-by df_xml_children()
 */
function df_xml_exists_child(CX $e, string $child):bool {return isset($e->{$child});}

/**
 * 2016-09-01
 * 2018-12-18 Single quotes are not supported by some external systems (e.g., Vantiv), so now I use double quotes.
 * @see df_xml_parse_header()
 * @used-by df_xml_g()
 */
function df_xml_header(string $enc = 'UTF-8', string $v = '1.0'):string {return "<?xml version=\"$v\" encoding=\"$enc\"?>";}

/**
 * @used-by \Dfe\SecurePay\Refund::process()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @used-by \Dfe\Vantiv\Test\CaseT\Charge::t04()
 * @param array(string => string) $attr [optional]
 * @param array(string => mixed) $contents [optional]
 */
function df_xml_node(string $tag, array $attr = [], array $contents = []):X {
	$r = df_xml_parse("<{$tag}/>"); /** @var X $r */
	$r->addAttributes($attr);
	$r->importArray($contents);
	return $r;
}

/**
 * 2016-09-01
 * @used-by \Dfe\SecurePay\Refund::process()
 * @uses \Df\Xml\X::asNiceXml() не сохраняет заголовок XML.
 * @param string|X $x
 */
function df_xml_prettify($x):string {return df_cc_n(df_xml_parse_header($x), df_xml_parse($x)->asNiceXml());}

/**
 * @used-by df_assert_leaf()
 * @used-by df_xml_children()
 * @param CX|MX|X $e
 */
function df_xml_report(CX $e):string {return $e instanceof MX ? $e->asNiceXml() : $e->asXML();}

/**
 * 2016-09-01
 * @see df_xml_x()
 * @used-by df_xml_parse_header()
 * @param string|X $x
 */
function df_xml_s($x):string {return is_string($x) ? $x : $x->asXML();}