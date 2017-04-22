<?php
use libphonenumber\NumberParseException as lE;
use libphonenumber\PhoneNumberUtil as lParser;
use libphonenumber\PhoneNumber as lPhone;
use Magento\Customer\Model\Address as CA;
use Magento\Quote\Model\Quote\Address as QA;
use Magento\Sales\Model\Order\Address as OA;
/**
 * 2017-04-22 https://github.com/giggsey/libphonenumber-for-php#quick-examples
 * @used-by \Dfe\CheckoutCom\Charge::cPhone()
 * @param string[]|OA|QA|CA $n
 * @param bool $throw [optional]
 * @return lPhone|null
 * @throws lE
 */
function df_phone($n, $throw = true) {return df_try(function() use($n) {return
	lParser::getInstance()->parse(...(!df_is_address($n) ? $n : [$n->getTelephone(), $n->getCountryId()]))
;}, $throw);}