<?php
/**
 * 2024-09-22
 * @used-by \Df\Core\Text\Regex::isSubjectMultiline()
 * @used-by \Df\Qa\Dumper::dumpObject()
 */
function df_is_multiline(string $s):bool {return df_contains(df_normalize($s), "\n");}