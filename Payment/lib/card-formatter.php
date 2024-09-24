<?php
/**
 * 2017-09-01
 * @used-by Df\StripeClone\CardFormatter::label()
 * @used-by Dfe\PostFinance\W\Event::cardNumber()
 */
function dfp_card_format_last4(string $last4, string $brand):string {return df_desc("···· $last4", $brand);}