<?php
namespace Df\Core\Helper;
class Path {
	/**
	 * @used-by Df_Admin_Model_Notifier_ClassRewriteConflicts::getModulesFromCodePool()
	 * @used-by \Df\Core\Lib::includeScripts()
	 * 2015-02-06
	 * Этот метод возвращает массив непосредственных (дочерних) папок и файлов
	 * внутри заданной папки $path.
	 * Текущий, быстрый алгоритм взят отсюда: http://php.net/manual/function.scandir.php#107215
	 * Текущий, быстрый алгоритм просто отсекает 2 первых элемента результата вызова @uses scandir(),
	 * заведомо зная, что эти элементы — «.» и «..».
	 * Мы это заведомо знаем, потому что при вызове @uses scandir()
	 * без указания значения второго (опционального) параметра $sorting_order
	 * функция @uses scandir() считает этот параметр равным «0»
	 * (начиная с PHP 5.4 для этого значения появилась константа SCANDIR_SORT_ASCENDING:
	 * http://php.net/manual/function.scandir.php
	 * «So for all PHP versions, use 0 for ascending order, and 1 for descending order.»
	 *
	 * Раньше использовался корректный, но более медленный алгоритм отсюда:
	 * http://php.net/manual/function.scandir.php#115871
		return array_diff(scandir($path), array('..', '.'));
	 * @param string $path
	 * @return string[]
	 */
	public function children($path) {return array_slice(scandir($path), 2);}

	/**
	 * @param string $path
	 * @param bool $isDir [optional]
	 * @return void
	 */
	public function createAndMakeWritable($path, $isDir = false) {
		df_param_string_not_empty($path, 0);
		if (!isset($this->{__METHOD__}[$path])) {
			if (file_exists($path)) {
				df_assert(is_dir($path) === $isDir);
				$this->chmod($path);
			}
			else {
				/** @var string $dir */
				$dir = $isDir ? $path : dirname($path);
				if (!file_exists($dir)) {
					$this->mkdir($dir);
				}
				else {
					$this->chmod($dir);
				}
			}
			$this->{__METHOD__}[$path] = true;
		}
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function delete($path) {
		df_param_string_not_empty($path, 0);
		\Magento\Framework\Filesystem\Io\File::rmdirRecursive($path);
	}

	/**
	 * @param string $path
	 * @throws \Df\Core\Exception
	 */
	private function chmod($path) {
		try {
			$r = chmod($path, 0777);
			df_throw_last_error($r);
		}
		catch (\Exception $e) {
			/** @var bool $isPermissionDenied */
			$isPermissionDenied = df_contains($e->getMessage(), 'Permission denied');
			df_error(
				$isPermissionDenied
				? "Операционная система запретила интерпретатору PHP {operation} «{path}»."
				:
					"Не удалась {operation} «{path}»."
					."\nДиагностическое сообщение интерпретатора PHP: «{message}»."
				,[
					'{operation}' => is_dir($path) ? 'запись в папку' : 'запись файла'
					,'{path}' => $path
					,'{message}' => $e->getMessage()
				]
			);
		}
	}

	/**
	 * @param string $dir
	 * @throws \Df\Core\Exception
	 */
	private function mkdir($dir) {
		try {
			$r = mkdir($dir, 0777, $recursive = true);
			df_throw_last_error($r);
		}
		catch (\Exception $e) {
			/** @var bool $isPermissionDenied */
			$isPermissionDenied = df_contains($e->getMessage(), 'Permission denied');
			df_error(
				$isPermissionDenied
				? "Операционная система запретила интерпретатору PHP создание папки «{$dir}»."
				: "Не удалось создать папку «{$dir}»."
				."\nДиагностическое сообщение интерпретатора PHP: «{$e->getMessage()}»."
			);
		}
	}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}