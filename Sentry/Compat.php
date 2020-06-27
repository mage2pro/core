<?php
namespace Df\Sentry;
final class Compat {
	static function gethostname() {
		if (function_exists('gethostname')) {
			return gethostname();
		}
		return php_uname('n');
	}
}
