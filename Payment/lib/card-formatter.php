<?php
/**
 * 2017-09-01
 * @used-by \Df\StripeClone\CardFormatter::label()
 * @used-by \Dfe\PostFinance\W\Event::cardNumber()
 * @param string $last4
 * @param string $brand
 * @return string
 */
function dfp_card_format_last4($last4, $brand) {return "···· $last4 ($brand)";}