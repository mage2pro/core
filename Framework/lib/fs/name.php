<?php

/**
 * Возвращает неиспользуемое имя файла в заданной папке $directory по заданному шаблону $template.
 * Результатом всегда является непустая строка.
 * @used-by df_report()
 * @param string $directory
 * @param string $template
 * @param string $ds [optional]
 */
function df_file_name($directory, $template, $ds = '-'):string { /** @var string $r */
	# 2016-11-09 If $template contains the file's path, when it will be removed from $template and added to $directory.
	$directory = df_path_n($directory);
	$template = df_path_n($template);
	if (df_contains($template, '/')) {
		$templateA = explode('/', $template); /** @var string[] $templateA */
		$template = array_pop($templateA);
		$directory = df_cc_path($directory, $templateA);
	}
	$counter = 1; /** @var int $counter */
	$hasOrderingPosition = df_contains($template, '{ordering}');/** @var bool $hasOrderingPosition */
	$now = \Zend_Date::now()->setTimezone('Europe/Moscow'); /** @var \Zend_Date $now */
	/** @var array(string => string) $vars */
	$vars = df_map_k(function($k, $v) use($ds, $now) {return
		df_dts($now, implode($ds, $v))
	;}, ['date' => ['y', 'MM', 'dd'], 'time' => ['HH', 'mm'], 'time-full' => ['HH', 'mm', 'ss']]);
	/**
	 * 2016-11-09
	 * @see \Zend_Date неправильно работает с миллисекундами:
	 * всегда возвращает 0 вместо реального количества миллисекунд.
	 * Так происходит из-за дефекта в методах
	 * @see \Zend_Date::addMilliSecond()
	 * @see \Zend_Date::setMilliSecond()
	 * Там такой код:
	 *		list($milli, $time) = explode(" ", microtime());
	 *		$milli = intval($milli);
	 * https://github.com/OpenMage/magento-mirror/blob/1.9.3.0/lib/Zend/Date.php#L4490-L4491
	 * Этот код ошибочен, потому что после первой операции
	 * $milli содержит дробное значение меньше 1, например: 0.653...
	 * А вторая операция тупо делает из этого значения 0.
	 */
	$vars['time-full-ms'] = implode($ds, [$vars['time-full'], sprintf(
		'%02d', round(100 * df_first(explode(' ', microtime())))
	)]);
	while (true) {
		/** @var string $fileName */
		$fileName = df_var($template, ['ordering' => sprintf('%03d', $counter)] + $vars);
		$fileFullPath = $directory . DS . $fileName; /** @var string $fileFullPath */
		if (!file_exists($fileFullPath)) {
			/**
			 * Раньше здесь стояло @see file_put_contents, и иногда почему-то возникал сбой:
			 * failed to open stream: No such file or directory.
			 * Может быть, такой сбой возникает, если папка не существует?
			 */
			$r = $fileFullPath;
			break;
		}
		elseif ($counter > 999) {
			df_error("The counter has exceeded the $counter limit.");
		}
		else {
			$counter++;
			# Если в шаблоне имени файла нет переменной «{ordering}» — значит, надо добавить её,
			# чтобы в следующей интерации имя файла стало уникальным.
			# Вставляем «{ordering}» непосредственно перед расширением файла.
			# Например, rm.shipping.log преобразуем в rm.shipping-{ordering}.log
			if (!$hasOrderingPosition && (2 === $counter)) {
				$fileNameTemplateExploded = explode('.', $template); /** @var string[] $fileNameTemplateExploded */
				/** @var int $secondFromLastPartIndex*/
				$secondFromLastPartIndex =  max(0, count($fileNameTemplateExploded) - 2);
				/** @var string $secondFromLastPart */
				$secondFromLastPart = dfa($fileNameTemplateExploded, $secondFromLastPartIndex);
				df_assert_sne($secondFromLastPart);
				$fileNameTemplateExploded[$secondFromLastPartIndex] = implode('--', [$secondFromLastPart, '{ordering}']);
				$template = df_assert_ne($template, implode('.', $fileNameTemplateExploded));
			}
		}
	}
	return df_path_n($r);
}

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
 * @param string $n
 * @param string $spaceSubstitute [optional]
 */
function df_fs_name($n, $spaceSubstitute = '-'):string {
	$n = str_replace(' ', $spaceSubstitute, $n);
	# «Remove anything which isn't a word, whitespace, number or any of the following caracters -_~,;:[]().
	# If you don't need to handle multi-byte characters you can use preg_replace rather than mb_ereg_replace
	# Thanks @Łukasz Rysiak!»
	# http://stackoverflow.com/a/2021729
	$n = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $n);
	# «Remove any runs of periods (thanks falstro!)» http://stackoverflow.com/a/2021729
	return mb_ereg_replace("([\.]{2,})", '', $n);
}