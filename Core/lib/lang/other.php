<?php
/**
 * 2017-02-07
 * @used-by df_lang_zh()
 * @used-by df_lang_ru()
 * @used-by \Df\Payment\Block\Info::extended()
 * @used-by \Df\Payment\Method::test()
 * @used-by \Df\Payment\Source\API\Key\Testable::test()
 * @used-by \Dfe\Moip\FE\Webhooks::onFormInitialized()
 * @used-by \Dfe\Paymill\Settings::test3DS()
 * $args — массив либо пустой, либо из 2 элементов с целочисленными индексами 0 и 1.
 * Если массив $args пуст, то функция возвращает $r.
 * Если массив $args непуст, то функция возвращает:
 * 		$args[0] при истинности $r
 *		$args[1] при ложности $r
 * @return mixed
 */
function df_b(array $args, bool $r) {return !$args ? $r : $args[intval(!$r)];}

/**
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @param mixed $v
 * @return mixed
 */
function df_nop($v) {return $v;}