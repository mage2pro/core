<?php
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
 * 2016-09-01
 * Вообще говоря, заголовок у XML необязателен, но моя функция @see df_xml_prettify() его добавляет,
 * поэтому меня пока данный алгоритм устраивает.
 * Более качественный алгоритм будет более ресурсоёмким: нам надо будет разбирать весь XML.
 * @used-by \Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @param mixed $v
 */
function df_check_xml($v):bool {return is_string($v) && df_starts_with($v, '<?xml');}