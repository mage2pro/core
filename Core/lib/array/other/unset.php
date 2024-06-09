<?php
/**
 * 2016-09-02
 * @see dfa_deep_unset()
 * @uses array_flip() correctly handles empty arrays.
 * 2019-11-15
 * Previously, it was used as:
 * 		$this->_data = dfa_unset($this->_data, 'can_use_default_value', 'can_use_website_value', 'scope');
 * I replaced it with:
 * 		$this->unsetData(['can_use_default_value', 'can_use_website_value', 'scope']);
 * 2022-11-26
 * We can not declare the argument $k as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by \Df\Config\Backend::value()
 * @used-by \Df\Config\Backend\ArrayT::processI()
 * @used-by \Df\Framework\Request::clean()
 * @used-by \Dfe\Markdown\Observer\Catalog\ControllerAction::processPost()
 * @param array(string => mixed) $a
 * @param string|string[] ...$k
 * @return array(string => mixed)
 */
function dfa_unset(array $a, ...$k):array {return array_diff_key($a, array_flip(df_args($k)));}
