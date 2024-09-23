<?php
use SimpleXMLElement as X;

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
 * @used-by df_xml_assert_leaf()
 */
function df_xml_is_leaf(X $x):bool {return empty($x) || !$x->children()->count();}