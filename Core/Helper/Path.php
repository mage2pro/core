<?php
namespace Df\Core\Helper;
use Df\Core\Exception as DFE;
final class Path {
	/**
	 * @used-by df_file_put_contents()
	 * @param string $path
	 * @param bool $isDir [optional]
	 */
	static function createAndMakeWritable($path, $isDir = false) {dfcf(function($path, $isDir = false) {
		if (file_exists(df_param_sne($path, 0))) {
			df_assert_eq(!!$isDir, is_dir($path));
			self::chmod($path);
		}
		else {
			/** @var string $d */
			file_exists($d = $isDir ? $path : dirname($path)) ? self::chmod($d) : self::mkdir($d);
		}
	;}, func_get_args());}

	/**
	 * @used-by createAndMakeWritable()
	 * @param string $path
	 * @throws \Df\Core\Exception
	 */
	private static function chmod($path) {
		try {
			$r = chmod($path, 0777);
			df_throw_last_error($r);
		}
		catch (\Exception $e) {
			/** @var string $m */
			$m = $e->getMessage();
			df_error(df_contains($m, 'Permission denied') || df_contains($m, 'Operation not permitted')
				? "The operating system forbids the PHP interpreter to {operation} «{$path}»."
				: "Unable to {operation} «{$path}».\nThe PHP interpreter's message: «{$m}»."
				,['{operation}' => is_dir($path) ? 'write to' : 'read from']
			);
		}
	}

	/**
	 * @param string $dir
	 * @throws \Df\Core\Exception
	 */
	private static function mkdir($dir) {
		try {
			df_throw_last_error(mkdir($dir, 0777, true));
		}
		catch (\Exception $e) {
			/** @var string $m */
			$m = $e->getMessage();
			df_error(df_contains($m, 'Permission denied') || df_contains($m, 'Operation not permitted')
				? "The operating system forbids the PHP interpreter to create the folder «{$dir}»."
				: "Unable to create the folder «{$dir}»."
				."\nThe PHP interpreter's message: «{$m}»."
			);
		}
	}
}