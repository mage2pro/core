<?php

/**
 * Функция возвращает null, если массив пуст.
 * Обратите внимание, что неверен код
 *	$result = reset($a);
 *	return (false === $result) ? null : $result;
 * потому что если @uses reset() вернуло false, это не всегда означает сбой метода:
 * ведь первый элемент массива может быть равен false.
 * @see df_last()
 * @see df_tail()
 * @used-by df_caller_c()
 * @used-by df_store_code_from_url()
 * @used-by dfa_group()
 * @used-by dfe_alphacommercehub_fix_amount_bug()
 * @used-by \CanadaSatellite\Core\Plugin\Magento\Catalog\Model\Product::afterGetPreconfiguredValues() (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/83)
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \Df\GoogleFont\Exception::message()
 * @used-by \Df\Payment\TM::response()
 * @used-by \Dfe\Color\Image::dist()
 * @used-by \Inkifi\Consolidation\Processor::pid()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::writeLocal()
 * @used-by \Inkifi\Mediaclip\T\CaseT\Order\Item::t01()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @used-by \TFC\GoogleShopping\Att\Brand::v() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/google-shopping/issues/8)
 * @used-by \VegAndTheCity\Core\Plugin\Mageplaza\Search\Helper\Data::afterGetProducts()
 * @used-by frugue/core/view/frontend/templates/wishlist/item/column/image.phtml
 * @return mixed|null
 */
function df_first(array $a) {return !$a ? null : reset($a);}

/**
 * 2019-08-21 https://www.php.net/manual/en/function.array-key-first.php
 * @used-by \Dfe\Color\Observer\ProductSaveBefore::execute()
 * @param array(int|string => mixed) $a
 * @return string|int|null
 */
function df_first_key(array $a) {
	$r = null; /** @var int|string|null $r */
	foreach($a as $k => $v) { /** @var int|string $k */
		$r = $k;
		break;
	}
	return $r;
}

/**
 * Функция возвращает null, если массив пуст.
 * Если использовать @see end() вместо @see df_last(),
 * то указатель массива после вызова end сместится к последнему элементу.
 * При использовании @see df_last() смещения указателя не происходит,
 * потому что в @see df_last() попадает лишь копия массива.
 *
 * Обратите внимание, что неверен код
 *	$result = end($array);
 *	return (false === $result) ? null : $result;
 * потому что если @uses end() вернуло false, это не всегда означает сбой метода:
 * ведь последний элемент массива может быть равен false.
 * http://www.php.net/manual/en/function.end.php#107733
 * @see df_first()
 * @see df_tail()
 * @used-by df_class_l()
 * @used-by df_fe_name_short()
 * @used-by df_package_name_l()
 * @used-by df_url_path()
 * @used-by df_url_staged()
 * @used-by ikf_eti()
 * @used-by \Df\Config\Backend::value()
 * @used-by \Df\Core\Text\Regex::match()
 * @used-by \Df\Customer\Settings\BillingAddress::disabled()
 * @used-by \Df\Framework\Form\Element::uidSt()
 * @used-by \Df\Payment\Operation::customerNameL()
 * @used-by \Df\Payment\Source\API\Key\Testable::_test()
 * @used-by \Df\Payment\TM::response()
 * @used-by \Df\PaypalClone\Init\Action::redirectParams()
 * @used-by \Df\StripeClone\Payer::cardId()
 * @used-by \Dfe\AlphaCommerceHub\W\Event::providerRespL()
 * @used-by \Dfe\AmazonLogin\Customer::nameLast()
 * @used-by \Dfe\Omise\Facade\Customer::cardAdd()
 * @used-by \Dfe\Salesforce\Test\Basic::t02_the_latest_version()
 * @used-by \Dfe\Stripe\W\Handler\Charge\Refunded::amount()
 * @used-by \Dfe\Stripe\W\Handler\Charge\Refunded::eTransId()
 * @used-by \KingPalm\Core\Plugin\Aitoc\OrdersExportImport\Model\Processor\Config\ExportConfigMapper::aroundToConfig()
 * @used-by \TFC\Core\Router::match() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/40)
 * @return mixed|null
 */
function df_last(array $array) {return !$array ? null : end($array);}