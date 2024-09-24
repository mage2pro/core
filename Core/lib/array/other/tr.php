<?php
/**
 * 2016-09-05
 * 2022-11-27 Added the @uses df_nes() check.
 * @see dfa_strict()
 * @used-by df_cfg_save()
 * @used-by df_url_bp()
 * @used-by ikf_pw_country()
 * @used-by Df\Directory\FE\Currency::v()
 * @used-by Dfe\GingerPaymentsBase\Block\Info::prepareCommon()
 * @used-by Dfe\GingerPaymentsBase\Choice::title()
 * @used-by Dfe\GingerPaymentsBase\Method::optionE()
 * @used-by Dfe\GingerPaymentsBase\Method::optionI()
 * @used-by Df\Payment\BankCardNetworkDetector::label()
 * @used-by Df\PaypalClone\W\Event::statusT()
 * @used-by Dfe\AllPay\W\Reader::te2i()
 * @used-by Dfe\IPay88\W\Event::optionTitle()
 * @used-by Dfe\Moip\Facade\Card::brand()
 * @used-by Dfe\Moip\Facade\Card::logoId()
 * @used-by Dfe\Moip\Facade\Card::numberLength()
 * @used-by Dfe\Paymill\Facade\Card::brand()
 * @used-by Dfe\PostFinance\W\Event::optionTitle()
 * @used-by Dfe\Robokassa\W\Event::optionTitle()
 * @used-by Dfe\Square\Facade\Card::brand()
 * @used-by Dfe\Stripe\FE\Currency::getComment()
 * @used-by Dfe\Stripe\Init\Action::redirectUrl()
 * @used-by Dfe\Vantiv\Facade\Card::brandCodeE()
 * @used-by Frugue\Store\Block\Switcher::map()
 * @used-by Frugue\Store\Block\Switcher::name()
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function dftr($v, array $map) {return df_nes($v) ? $v : dfa($map, $v, $v);}