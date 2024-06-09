<?php

/**
 * 2024-05-08 "Implement `df_assert_module_enabled()`": https://github.com/mage2pro/core/issues/367
 * @used-by df_module_name_by_path()
 * @used-by \Sharapov\Cabinetsbay\Setup\InstallData::install() (https://github.com/cabinetsbay/site/issues/98)
 */
function df_assert_module_enabled(string $m):string {return df_assert(df_module_enabled($m), "The `{$m}` module must be enabled.");}

/**
 * 2019-11-21
 * @used-by df_caller_entry_m()
 * @used-by df_log_l()
 * @used-by df_msi()
 */
function df_module_enabled(string $m):bool {return df_module_m()->isEnabled(df_module_name($m));}