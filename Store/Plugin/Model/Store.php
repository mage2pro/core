<?php
namespace Df\Store\Plugin\Model;
use Magento\Store\Model\Store as Sb;
class Store {
	/**
	 * 2015-10-01
	 * «Admin» store title is untranslatable on admin attribute edit page neither by a dictionary nor manually:
	 * https://mage2.pro/t/99
	 * @see AbstractFrontend::getName()
	 * @param Sb $sb
	 * @param string $r
	 */
	function afterGetName(Sb $sb, $r):string {return 'Admin' === $r ? __($r) : $r;}
}