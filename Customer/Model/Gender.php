<?php
namespace Df\Customer\Model;
/**
 * 2016-06-04
 * Magento использует значения:
 * 1: «Male»
 * 2: «Female»
 * 3: не определился :-)
 */
interface Gender{
	const FEMALE = 2;
	const MALE = 1;
	const UNKNOWN = 3;
}