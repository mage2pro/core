<?php
use Df\Core\Exception as DFE;

/**
 * 2016-10-25 Оказалось, что в ядре нет такой функции.
 * 2022-11-26
 * @see array_search() looks only for a static value (does not support a comparison closure):
 * https://php.net/manual/function.array-search.php
 * 2023-07-26
 * 1) "Replace `array|Traversable` with `iterable`": https://github.com/mage2pro/core/issues/255
 * 2) https://php.net/manual/language.types.iterable.php
 * https://php.net/manual/en/migration82.other-changes.php#migration82.other-changes.core
 * 3) Using `iterable` as an argument type requires PHP ≥ 7.1: https://3v4l.org/SNUMI
 * 2024-06-03
 * 1.1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 1.2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 1.3) https://php.net/manual/en/language.types.iterable.php
 * 2) We still can not use «Union Types» (e.g. `callable|iterable`) because they require PHP ≥ 8 (we need to support PHP ≥ 7.1):
 * 2.1) https://php.watch/versions/8.0/union-types
 * 2.2) https://3v4l.org/AOWmO
 * @used-by df_bt_has()
 * @used-by df_ends_with()
 * @used-by df_find()
 * @used-by df_handle_prefix()
 * @used-by df_is()
 * @used-by df_modules_my()
 * @used-by df_oq_sa()
 * @used-by df_sales_email_sending()
 * @used-by df_starts_with()
 * @used-by dfa_has_objects()
 * @used-by ikf_oi_pid()
 * @used-by mnr_recurring()
 * @used-by Df\Framework\Plugin\View\Layout::afterIsCacheable()
 * @used-by Df\Payment\Info\Report::addAfter()
 * @used-by Df\Payment\Method::amountFactor()
 * @used-by Df\Payment\TM::confirmed()
 * @used-by Dfe\Stripe\Method::cardType()
 * @used-by Frugue\Core\Plugin\Sales\Model\Quote::afterGetAddressesCollection()
 * @used-by Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @used-by Inkifi\Mediaclip\Event::_areAllOIAvailableForDownload()
 * @used-by Inkifi\Mediaclip\Event::oi()
 * @used-by TFC\Core\Plugin\Catalog\Block\Product\View\GalleryOptions::afterGetOptionsJson()
 * @param callable|iterable $a1
 * @param callable|iterable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @return mixed|null
 * @throws DFE
 */
function df_find($a1, $a2, $pAppend = [], $pPrepend = [], int $keyPosition = 0, bool $nested = false) {
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
	[$a, $f] = dfaf($a1, $a2); /** @var iterable $a */ /** @var callable $f */
	$pAppend = df_array($pAppend); $pPrepend = df_array($pPrepend);
	$r = null; /** @var mixed|null $r */
	foreach ($a as $k => $v) {/** @var int|string $k */ /** @var mixed $v */ /** @var mixed[] $primaryArgument */
		switch ($keyPosition) {
			case DF_BEFORE:
				$primaryArgument = [$k, $v];
				break;
			case DF_AFTER:
				$primaryArgument = [$v, $k];
				break;
			default:
				$primaryArgument = [$v];
		}
		if ($fr = call_user_func_array($f, array_merge($pPrepend, $primaryArgument, $pAppend))) {
			$r = !is_bool($fr) ? $fr : $v;
			break;
		}
	}
	# 2023-07-25
	# 1) "Adapt `df_find` to the nested search": https://github.com/mage2pro/core/issues/251
	# 2) I implement the nested seach in a separate loop to minimize recursions.
	if (null === $r && $nested) {
		foreach ($a as $v) {/** @var int|string $k */ /** @var mixed $v */ /** @var mixed[] $primaryArgument */
			if (is_iterable($v)) {
				if ($r = df_find($v, $f, $pAppend, $pPrepend, $keyPosition, true)) {
					break;
				}
			}
		}
	}
	return $r;
}

/**
 * 2020-04-25
 * @used-by dfa_r()
 * @used-by VegAndTheCity\Core\Plugin\Mageplaza\Search\Helper\Data::afterGetProducts()
 * @param mixed $d [optional]
 * @return mixed|null
 */
function dfa_r(array $a, string $k, $d = null) {/** @var mixed|null $r */
	if (isset($a[$k])) {
		$r = $a[$k];
	}
	else foreach ($a as $ak => $av) {/** @var string $ak */ /** @var mixed $av */
		if (is_array($av) && !is_null($r = dfa_r($av, $k))) {
			break;
		}
	}
	return isset($r) ? $r : $d;
}