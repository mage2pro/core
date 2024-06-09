<?php
/**
 * Возвращает неиспользуемое имя файла в заданной папке $directory по заданному шаблону $template.
 * Результатом всегда является непустая строка.
 * @see df_fs_name()
 * @used-by df_report()
 */
function df_file_name(string $directory, string $template, string $ds = '-'):string { /** @var string $r */
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
		'%02d', round(100 * df_first(df_explode_space(microtime())))
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