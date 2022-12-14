<?php
namespace Df\Rss\Plugin\Model;
use Magento\Framework\Phrase as Ph;
use Magento\Rss\Model\Rss as Sb;
# 2020-10-04
# "«Invalid parameter: parameter must be a non-empty string» at `vendor/magento/framework/App/Feed.php:36`":
# https://github.com/dxmoto/site/issues/113
final class Rss {
	/**
	 * 2020-10-04
	 * @see \Magento\Rss\Model\Rss::getFeeds()
	 * @param array(string => mixed) $r
	 */
	function afterGetFeeds(Sb $sb, array $r):string {return df_map($r, function($v) {return
		!$v  instanceof Ph ? $v : (string)$v
	;});}
}