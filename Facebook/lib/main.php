<?php
use Df\Facebook\Settings as S;
/**
 * 2016-01-09
 * @return string
 */
function df_facebook_init() {
	static $r; return $r || !S::s()->appId() ? '' : $r = df_block_r(null, [], 'Df_Facebook::init');
}
