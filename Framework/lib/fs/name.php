<?php
/**
 * 2015-11-29
 * Преобразует строку таким образом, чтобы её было безопасно и удобно использовать в качестве имени файла или папки.
 * http://stackoverflow.com/a/2021729
 * 2017-02-09
 * Сегодня заметил, что эта функция удаляет пробелы, но сохраняет символы Unicode: '歐付寶 all/Pay' => '歐付寶-allPay'
 * Example #1: '歐付寶 all/Pay':
 * 		@see df_fs_name => 歐付寶-allPay
 * 		@see df_translit =>  all/Pay
 * 		@see df_translit_url => all-Pay
 * 		@see df_translit_url_lc => all-pay
 * Example #2: '歐付寶 O'Pay (allPay)':
 * 		@see df_fs_name => 歐付寶-allPay
 * 		@see df_translit =>  allPay
 * 		@see df_translit_url => allPay
 * 		@see df_translit_url_lc => allpay
 * @see df_file_name()
 */
function df_fs_name(string $n, string $spaceSubstitute = '-'):string {
	$n = str_replace(' ', $spaceSubstitute, $n);
	# «Remove anything which isn't a word, whitespace, number or any of the following caracters -_~,;:[]().
	# If you don't need to handle multi-byte characters you can use preg_replace rather than mb_ereg_replace
	# Thanks @Łukasz Rysiak!»
	# http://stackoverflow.com/a/2021729
	$n = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $n);
	# «Remove any runs of periods (thanks falstro!)» http://stackoverflow.com/a/2021729
	return mb_ereg_replace("([\.]{2,})", '', $n);
}