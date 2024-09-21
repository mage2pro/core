<?php
use Df\Xml\X;

/**
 * @see df_needs_cdata()
 * @used-by df_clean_xml()
 * @used-by \Dfe\SecurePay\Refund::process()
 */
function df_cdata(string $s):string {return X::markAsCData($s);}

/**
 * 2021-12-16
 * @used-by \Df\Xml\X::asNiceXml()
 */
function df_cdata_raw_if_needed(string $s):string {return !df_needs_cdata($s) ? $s : "<![CDATA[$s]]>";}

/**
 * 2021-12-12 https://3v4l.org/3SDsT
 * @see df_cdata()
 * @used-by df_cdata_raw_if_needed()
 * @used-by \Df\Xml\X::importString()
 */
function df_needs_cdata(string $s):bool {
	$s1 = htmlspecialchars_decode($s, ENT_XML1 | ENT_QUOTES);
    $s2 = htmlspecialchars($s1, ENT_XML1 | ENT_NOQUOTES);
	return $s !== $s2 && $s1 !== $s2;
}