<?php
/**
 * 2017-12-13
 * @used-by \Df\Payment\Method::canUseForCountryP()
 */
function df_add_ds_right(string $p):string {return df_trim_ds_right($p) . '/';}