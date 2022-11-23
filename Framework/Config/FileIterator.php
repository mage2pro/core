<?php
namespace Df\Framework\Config;
use Magento\Framework\Config\FileIterator as I;
# 2017-07-26
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class FileIterator extends I {
	/**
	 * 2017-07-26
	 * @used-by \Df\Framework\Module\Dir\Reader::getComposerJsonFiles()
	 * @return string[]
	 */
	final static function pathsGet(I $i):array {return $i->paths;}

	/**
	 * 2017-07-26
	 * @used-by \Df\Framework\Module\Dir\Reader::getComposerJsonFiles()
	 * @param I $i
	 * @param string[] $v
	 */
	final static function pathsSet(I $i, array $v):I {$i->paths = $v; return $i;}
}