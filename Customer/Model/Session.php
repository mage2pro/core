<?php
namespace Df\Customer\Model;
/**
 * 2016-06-04
 * @method string|null getBeforeAuthUrl(bool $clear = false)
 * @method Session unsBeforeAuthUrl()
 *
 * 2016-06-04
 * @method int|null getLastCustomerId(bool $clear = false)
 * @method Session setLastCustomerId($value)
 *
 * 2016-12-03
 * @method array|null getDfSsoRegistrationData(bool $clear = false)
 * @used-by \Df\Customer\Plugin\Block\Form\Register::afterGetFormData()
 * @method void setDfSsoRegistrationData(array $value)
 * @method Session unsDfSsoRegistrationData()
 *
 * 2016-12-03
 * @method string|null getDfSsoId(bool $clear = false)
 * @method void setDfSsoId(string $value)
 * @method Session unsDfSsoId()
 *
 * 2016-12-02
 * @method string|null getDfSsoProvider(bool $clear = false)
 * @method void setDfSsoProvider(string $value)
 * @method Session unsDfSsoProvider()
 *
 * 2016-12-04
 * @method bool|null getDfNeedConfirm(bool $clear = false)
 * @method void setDfNeedConfirm(bool $value)
 *
 * 2018-04-13
 * @method bool|null getDfeFrugueCountry(bool $clear = false)
 * @method void setDfeFrugueCountry(string $value)
 *
 * @method bool|null getDfeFrugueRedirected(bool $clear = false)
 * @method void setDfeFrugueRedirected(bool $value)
 *
 * @method bool|null getDfeFrugueRedirectStarted(bool $clear = false)
 * @method void setDfeFrugueRedirectStarted(bool $value)
 */
class Session extends \Magento\Customer\Model\Session {}