<?php
/**
 * 2016-03-08 It adds the $head prefix to the $s string if the prefix is absent in $s.
 * @used-by df_sys_path_abs()
 * @used-by ikf_ite()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
 */
function df_prepend(string $s, string $head):string {return df_starts_with($s, $head) ? $s : $head . $s;}