<?php
/**
 * 2017-09-01
 * @used-by \Df\StripeClone\CardFormatter::label()
 * @used-by \Dfe\PostFinance\W\Event::cardNumber()
 * @param string $last4
 * @param string $brand
 */
function dfp_card_format_last4($last4, $brand):string {return df_desc("···· $last4", $brand);}