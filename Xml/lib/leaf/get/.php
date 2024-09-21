<?php
use SimpleXMLElement as CX;

/**
 * 2015-02-27
 * Обратите внимание на разницу между @see \SimpleXMLElement::asXML()
 * и @see \SimpleXMLElement::__toString() / оператор (string)$this.
 *
 * @see \SimpleXMLElement::__toString() и (string)$this
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
 * потому что узел «cms» не является концевым узлом (листом дерева XML).
 * http://3v4l.org/Pkj37
 * Более того, метод @see \SimpleXMLElement::__toString()
 * отсутствует в PHP версий 5.2.17 и ниже:
 * http://3v4l.org/Wiia2#v500
 *
 * 2015-03-02
 * Обратите внимание,
 * то мы специально допускаем возможность для первого параметра $e принимать значение null:
 * это даёт нам возможность писать код типа:
 * @used-by Df_Page_Helper_Head::needSkipAsStandardCss()
 *	df_leaf_b(df_config_node(
 *		'df/page/skip_standard_css/', df_state()->getController()->getFullActionName()
 *	))
 * без дополнительных проверок, имеется ли в наличии запрашиваемый лист дерева XML
 * (если лист отсутствует, то @see df_config_node() вернёт null)
 *
 * @used-by df_leaf_b()
 * @used-by df_leaf_child()
 * @used-by df_leaf_f()
 * @used-by df_leaf_i()
 * @used-by df_leaf_s()
 * @param string|null|callable $d [optional]
 */
function df_leaf(CX $e = null, $d = null):?string {/** @var ?string $r */
	/**
	 * 2015-08-04
	 * Нельзя здесь использовать !$e,
	 * потому что для концевых текстовых узлов с ненулевым целым значением (например: «147»)
	 * такое выражение довольно-таки неожиданно возвращает true.
	 * @see \SimpleXMLElement вообще необычный класс с нестандартным поведением.
	 * Чтобы понять, почему в данном случае !$e равно true, посморите функцию @see df_xml_exists()
	 *
	 * Так вот, @see df_xml_exists() для текстового узла всегда возвращает false,
	 * даже если текстовое значение не приводится к false (то же «147»).
	 *
	 * Почему так происходит — видно из реализации @see df_xml_exists(): !empty($e)
	 * То есть, empty($e) для текстовых узлов возвращает true.
	 *
	 * Например:
	 *	<Остаток>
	 *		<Склад>
	 *			<Ид>6f87e83f-722c-11df-b336-0011955cba6b</Ид>
	 *			<Количество>147</Количество>
	 *		</Склад>
	 *	</Остаток>
	 * Если здесь сделать xpath Остаток/Склад/Количество,
	 * то для узла «147» !$e почему-то вернёт true,
	 * хотя в данном случае $e является полноценным объектом @see \SimpleXMLElement
	 * и (string)$e возвращает «147».
	 */
	if (is_null($e)) {
		$r = df_call_if($d);
	}
	elseif (df_es($r = (string)df_assert_leaf($e))) {
		/**
		 * 2015-09-25
		 * Добавил данное условие, чтобы различать случай пустого узла и отсутствия узла.
		 * Пример пустого узла ru_RU:
		 * <term>
		 * 		<en_US>Order Total</en_US>
		 * 		<ru_RU></ru_RU>
		 * </term>
		 * Так вот, для пустого узла empty($e) вернёт false,
		 * а для отсутствующего узла — true.
		 */
		$r = df_if1(empty($e), $d, '');
	}
	return $r;
}