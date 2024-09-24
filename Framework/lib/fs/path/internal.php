<?php
/**
 * 2017-05-08
 * 2024-06-09 It returns `true` if:
 * 		1) $p is empty
 * 		2) $p is absolute (not relative) and inside the Magento installation directory.
 * @used-by Df\Framework\Plugin\Session\SessionManager::beforeStart()
 * @used-by Df\Sentry\Trace::info()
 */
function df_path_is_internal(string $p):bool {return df_es($p) || df_starts_with(df_path_n($p), df_path_n(BP));}