<?php
/**
 * @see df_bts_yn()
 * @used-by \Df\Qa\Dumper::dump()
 * @used-by \Dfe\AlphaCommerceHub\Method::charge()
 * @used-by \Dfe\Vantiv\Charge::pCharge()
 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t02()
 */
function df_bts(bool $v):string {return $v ? 'true' : 'false';}

/**
 * 2017-11-08
 * @used-by \Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
 * @see df_bts()
 */
function df_bts_yn(bool $v):string {return $v ? 'yes' : 'no';}