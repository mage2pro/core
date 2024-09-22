<?php
/**
 * 2016-03-08 It adds the $tail suffix to the $s string if the suffix is absent in $s.
 * @used-by df_cc_path_t()
 * @used-by df_file_ext_add()
 */
function df_append(string $s, string $tail):string {return df_ends_with($s, $tail) ? $s : $s . $tail;}