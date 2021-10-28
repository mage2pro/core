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
 * @method bool|null getDfeFrugueRedirectStarted(bool $clear = false)
 * @method void setDfeFrugueRedirectStarted(bool $v)
 */
class Session extends \Magento\Customer\Model\Session {}