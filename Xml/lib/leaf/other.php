<?php
use Df\Core\Exception as E;
use Df\Xml\X;
use SimpleXMLElement as CX;

/**
 * 2015-02-27
 * 1) Метод @see \SimpleXMLElement::count() появился только в PHP 5.3,
 * поэтому мы его не используем: https://php.net/manual/simplexmlelement.count.php
 * 2) `count($e->children())` некорректно возвращает 1 для листов в PHP 5.1: http://3v4l.org/PT6Pt
 * Однако нам не нужно поддерживать PHP 5.1.
 * 3) Для несуществующего узла попытка вызова @uses count() приведёт к сбою: «Warning: count(): Node no longer exists»
 * http://3v4l.org/PsIPe#v512
 * 4) Текущий алгоритм проверен на работоспособность здесь: http://3v4l.org/VldTN
 * 2015-08-15
 * Нельзя здесь использовать `count($e->children())`,
 * потому что класс @see SimpleXmlElement не реализует интерфейс @see Iterator,
 * а реализует только интерфейс @see Traversable.
 * https://php.net/manual/class.iterator.php
 * https://php.net/manual/class.traversable.php
 * https://php.net/manual/simplexmlelement.count.php
 * 2015-08-16
 * 1) Как ни странно, написанное выше действительно верно: http://3v4l.org/covo1
 * 2) Класс @see \SimpleXMLElement не реализует интерфейс @see Iterator,
 * а реализует только интерфейс @see Traversable.
 * https://php.net/manual/class.iterator.php
 * https://php.net/manual/class.traversable.php
 * https://php.net/manual/simplexmlelement.count.php
 * Однако @uses count() почему-то работает для него.
 * @see \SimpleXMLElement — самый загадочный класс PHP.
 * @used-by df_assert_leaf()
 */
function df_check_leaf(CX $e):bool {return !df_xml_exists($e) || !$e->children()->count();}

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
 * @return string|null
 */
function df_leaf(CX $e = null, $d = null) {/** @var string $r */
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

/**
 * @deprecated It is unused.
 * @param bool|callable $default [optional]
 */
function df_leaf_b(CX $e = null, $default = false):bool {return df_bool(df_leaf($e, $default));}

/**
 * 2022-11-15 @deprecated It is unused.
 * @param string|mixed|null|callable $d [optional]
 * @return string|mixed|null
 */
function df_leaf_child(CX $e, string $child, $d = null) {return df_leaf($e->{$child}, $d);}

/**
 * 2015-08-16 Намеренно убрал параметр $default.
 * 2022-11-15 @deprecated It is unused.
 */
function df_leaf_f(CX $e = null):float {return df_float(df_leaf($e));}

/**
 * 2015-08-16 Намеренно убрал параметр $default.
 * 2022-11-15 @deprecated It is unused.
 */
function df_leaf_i(CX $e = null):int {return df_int(df_leaf($e));}

/**
 * @used-by df_leaf_sne()
 * @used-by \Df\Xml\X::map()
 * @used-by \Df\Xml\X::xpathMap()
 * @used-by \Dfe\Robokassa\Api\Options::p()
 * @param CX|null $e [optional]
 * @param string|callable $d [optional]
 */
function df_leaf_s(CX $e = null, $d = ''):string {return (string)df_leaf($e, $d);}

/**
 * @used-by \Df\Xml\X::map()
 * @used-by \Df\Xml\X::xpathMap()
 * @used-by \Dfe\SecurePay\Refund::process()
 * @param string|callable $d [optional]
 */
function df_leaf_sne(CX $e = null, $d = ''):string {/** @var string $r */
	if (df_es($r = df_leaf_s($e, $d))) {
		df_error('Лист дерева XML должен быть непуст, однако он пуст.');
	}
	return $r;
}