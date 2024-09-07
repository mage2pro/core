<?php
use Closure as F;

/**
 * 2020-01-29
 * 2022-11-17
 * `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2024-06-03 We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @see dfac()
 * @used-by \Df\Config\A::get()
 * @used-by \Df\Core\Visitor::r()
 * @used-by \Df\Framework\Form\Element\Fieldset::v()
 * @used-by \Df\Payment\TM::req()
 * @used-by \Df\Payment\TM::res0()
 * @used-by \Dfe\CheckoutCom\Response::a()
 * @used-by \Dfe\FacebookLogin\Customer::r()
 * @param object $o
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return mixed
 */
function dfaoc($o, F $f, $k = '', $d = null) {return dfa(dfc($o, $f, [], false, 1), $k, $d);}