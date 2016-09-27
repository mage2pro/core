<?php
// Sorting is disabled intentionally for performance improvement.
foreach (glob(dirname(__FILE__) . '/*/registration.php', GLOB_NOSORT) as $file) {
	include $file;
}