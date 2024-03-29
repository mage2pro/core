<?php
namespace Df\Customer\Model;
/**
 * 2016-06-04
 * Magento использует значения:
 * 1: «Male»
 * 2: «Female»
 * 3: не определился :-)
 */
interface Gender {
	/**
	 * @used-by \Dfe\FacebookLogin\Customer::gender()
	 */
	const FEMALE = 2;
	/**
	 * @used-by \Dfe\FacebookLogin\Customer::gender()
	 */
	const MALE = 1;
	/**
	 * @used-by \Df\Sso\Customer::gender()
	 * @used-by \Dfe\FacebookLogin\Customer::gender()
	 */
	const UNKNOWN = 3;
}