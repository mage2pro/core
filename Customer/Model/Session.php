<?php
namespace Df\Customer\Model;
/**
 * 2016-06-04
 * @method string|null getBeforeAuthUrl(bool $value = false)
 * @method int|null getLastCustomerId()
 * @method Session setLastCustomerId($value)
 * @method Session unsBeforeAuthUrl()
 *
 * 2016-12-02
 * @method array|null getDfSsoData()
 * @method string|null getDfSsoProvider()
 *
 * 2016-12-02
 * @method void setDfSsoData(array $value)
 * @method void setDfSsoProvider(string $value)
 * @used-by \Df\Sso\CustomerReturn::execute()
 */
class Session extends \Magento\Customer\Model\Session {}