<?php
namespace Df\Customer\Model;
/**
 * 2016-06-04
 * @method string|null getBeforeAuthUrl(bool $clear = false)
 * @method Session unsBeforeAuthUrl()
 *
 * 2016-06-04
 * @method int|null getLastCustomerId(bool $clear = false)
 * @method Session setLastCustomerId($v)
 *
 * 2018-04-13
 * @method bool|null getDfeFrugueCountry(bool $clear = false)
 * @method void setDfeFrugueCountry(string $v)
 *
 * @method bool|null getDfeFrugueRedirected(bool $clear = false)
 * @method void setDfeFrugueRedirected(bool $v)
 *
 * @method bool|null getDfeFrugueRedirectStarted(bool $clear = false)
 * @method void setDfeFrugueRedirectStarted(bool $v)
 * 
 * @method array|null getDfeTBCParams(bool $clear = false)
 * @method void setDfeTBCParams(array $v)
 */
class Session extends \Magento\Customer\Model\Session {}