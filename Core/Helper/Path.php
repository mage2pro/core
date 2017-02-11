<?php
namespace Df\Core\Helper;
class Path {
	/**
	 * @param string $path
	 * @param bool $isDir [optional]
	 * @return void
	 */
	function createAndMakeWritable($path, $isDir = false) {
		df_param_sne($path, 0);
		if (!isset($this->{__METHOD__}[$path])) {
			if (file_exists($path)) {
				df_assert_eq(!!$isDir, is_dir($path));
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
	function delete($path) {
		df_param_sne($path, 0);
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