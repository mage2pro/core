<?php
use SimpleXMLElement as X;

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
function df_xml_exists(?X $e = null):bool {return !empty($e);}

/**
 * http://stackoverflow.com/questions/1560827#comment20135428_1562158
 * @used-by df_xml_children()
 */
function df_xml_exists_child(X $e, string $child):bool {return isset($e->{$child});}