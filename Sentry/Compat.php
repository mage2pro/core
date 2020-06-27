<?php
namespace Df\Sentry;
final class Compat {
	static function gethostname() {
		if (function_exists('gethostname')) {
			return gethostname();
		}
		return php_uname('n');
	}
	
	static function hash_hmac($algo, $data, $key, $raw_output=false)
	{
		if (function_exists('hash_hmac')) {
			return hash_hmac($algo, $data, $key, $raw_output);
		}

		return self::_hash_hmac($algo, $data, $key, $raw_output);
	}

	/**
	 * Implementation from 'KC Cloyd'.
	 * See http://nl2.php.net/manual/en/function.hash-hmac.php
	 */
	static function _hash_hmac($algo, $data, $key, $raw_output=false)
	{
		$algo = strtolower($algo);
		$pack = 'H'.strlen($algo('test'));
		$size = 64;
		$opad = str_repeat(chr(0x5C), $size);
		$ipad = str_repeat(chr(0x36), $size);

		if (strlen($key) > $size) {
			$key = str_pad(pack($pack, $algo($key)), $size, chr(0x00));
		} else {
			$key = str_pad($key, $size, chr(0x00));
		}

		$keyLastPos = strlen($key) - 1;
		for ($i = 0; $i < $keyLastPos; $i++) {
			$opad[$i] = $opad[$i] ^ $key[$i];
			$ipad[$i] = $ipad[$i] ^ $key[$i];
		}

		$output = $algo($opad.pack($pack, $algo($ipad.$data)));

		return ($raw_output) ? pack($pack, $output) : $output;
	}
}
