<?php

/**
 * 2016-09-01
 * Вообще говоря, заголовок у XML необязателен, но моя функция @see df_xml_prettify() его добавляет,
 * поэтому меня пока данный алгоритм устраивает.
 * Более качественный алгоритм будет более ресурсоёмким: нам надо будет разбирать весь XML.
 * @used-by Df\Backend\Block\Widget\Grid\Column\Renderer\Text::render()
 * @param mixed $v
 */
function df_is_xml($v):bool {return is_string($v) && df_starts_with($v, '<?xml');}