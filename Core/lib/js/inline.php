<?php
/**
 * 2017-04-21
 * Эта функция обладает 2-мя преимуществами перед @see df_js_inline_url():
 * 1) Скрипт кэшируется посредством RequireJS.
 * Это важно в том случае, когда скрипт загружается не только в сценарии этой функции,
 * но и из другого скрипта JavaScript: применение RequireJS позволяет нам не загружать скрипт повторно.
 * 2) Загрузка скрипта не блокирует рисование страницы браузером
 * (аналогично для этого можно было бы использовать атрибут async тега script).
 */
function df_js_inline_r(string $n):string {return df_tag('script', ['type' => 'text/javascript'], "require(['$n']);");}

/**
 * 2017-04-21
 * @see df_js_inline_r()
 * @used-by vendor/tradefurniturecompany/core/view/frontend/templates/js.phtml
 */
function df_js_inline_url(string $res, bool $async = false):string {return df_resource_inline(
	$res, function(string $url) use($async):string {return df_tag(
		'script', ['src' => $url, 'type' => 'text/javascript'] + (!$async ? [] : ['async' => 'async']), '', false
	);}
);}