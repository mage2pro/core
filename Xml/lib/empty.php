<?php
use SimpleXMLElement as X;

/**
 * 2015-02-27
 * 1) Алгоритм взят отсюда: http://stackoverflow.com/a/5344560
 * 2) Проверил, что он работает: http://3v4l.org/tnEIJ
 * 3) isset() вместо empty() не сработает: http://3v4l.org/2P5o0
 * 4) isset, однако, работает для проверки наличия дочерних листов: @see df_xml_exists_child()
 * 5) Оператор $e->{'тест'} всегда возвращает объект @see \SimpleXMLElement, вне зависимости от наличия узла «тест»,
 * просто для отсутствующего узла данный объект будет пуст, и empty() для него вернёт true.
 * 2015-08-04
 * Заметил, что empty($e) для текстовых узлов всегда возвращает `true`,
 * даже если узел как строка приводится к `true` (например: «147»). Например:
 *		<Остаток>
 *			<Склад>
 *				<Ид>6f87e83f-722c-11df-b336-0011955cba6b</Ид>
 *				<Количество>147</Количество>
 *			</Склад>
 *		</Остаток>
 * Если здесь сделать xpath `Остаток/Склад/Количество`, то для узла «147» @see df_xml_empty($e) вернёт `true`.
 * 6) Эту особенность использует алгоритм @see df_xml_is_leaf():
 * 		return df_xml_empty($e) || !count($e->children());
 * 2024-09-24
 * 1) «A variable is considered empty if it does not exist or if its value equals `false`.»
 * https://www.php.net/manual/en/function.empty.php
 * https://archive.is/8HRC5#selection-995.60-1001.5
 * 2) «When converting to `bool`, the following values are considered `false`:
 * 		*) Internal objects that overload their casting behaviour to `bool`.
 * 		For example: `SimpleXML` objects created from empty elements without attributes.»
 * https://www.php.net/manual/en/language.types.boolean.php#language.types.boolean.casting
 * https://archive.is/FcCfj#selection-1353.0-1355.60
 * 3) Even if a node has attributes, but does not have a content, `empty($x)` returns `true` for it:
 * 3.1) https://3v4l.org/h7hRH
 * 3.2) https://3v4l.org/YM3I8
 * 3.3) https://3v4l.org/2vaHf
 * 3.4) https://stackoverflow.com/questions/1560827#comment74422321_5344560
 * @used-by \Df\Xml\A::p()
 */
function df_xml_empty(?X $x):bool {return empty($x);}

/**
 * http://stackoverflow.com/questions/1560827#comment20135428_1562158
 * @used-by df_xml_children()
 */
function df_xml_exists_child(X $e, string $child):bool {return isset($e->{$child});}