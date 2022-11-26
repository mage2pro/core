<?php
use Df\Payment\Method as M;
use Df\Payment\Settings as S;
use Magento\Payment\Model\InfoInterface as II;
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Payment as QP;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Payment as OP;
use Magento\Sales\Model\Order\Payment\Transaction as T;
/**              
 * 2017-03-27       
 * @used-by dfe_stripe_source()
 * @used-by dfpex_from_doc()   
 * @used-by \Df\Payment\Action::s()
 * @used-by \Df\Payment\Source\API\Key::ss()
 * @used-by \Df\Payment\Source\Identification::id()
 * @used-by \Df\Payment\TestCase::s()
 * @used-by \Df\PaypalClone\Signer::s()
 * @used-by \Dfe\AllPay\Total\Quote::collect()
 * @used-by \Dfe\AlphaCommerceHub\API\Client::commonParams()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::ss()
 * @used-by \Dfe\CheckoutCom\Handler\CustomerReturn::p()
 * @used-by \Dfe\CheckoutCom\Response::__construct()
 * @used-by \Dfe\CheckoutCom\Response::getCaptureCharge()
 * @used-by \Dfe\Klarna\Api\Checkout::html()
 * @used-by \Dfe\Klarna\Observer\ShortcutButtonsContainer::execute()
 * @used-by \Dfe\Moip\Test\Data::ga()
 * @used-by \Dfe\Omise\Test\TestCase()
 * @used-by \Dfe\Qiwi\API\Client::s()
 * @used-by \Dfe\Square\API\Client::headers()
 * @used-by \Dfe\Square\API\Facade\LocationBased::prefix()
 * @used-by \Dfe\Stripe\Block\Js::_toHtml()
 * @used-by \Dfe\Stripe\FE\Currency::s()
 * @used-by \Dfe\TBCBank\API\Client::proxy()
 * @used-by \Dfe\TBCBank\API\Client::zfConfig()
 * @used-by \Dfe\TwoCheckout\Handler::p()
 * @used-by \Dfe\Vantiv\API\Client::_construct()
 * @used-by \Dfe\Vantiv\API\Client::proxy()
 * @param M|II|OP|QP|O|Q|T|object|string|null $m
 * @return S|mixed
 */
function dfps($m, string $k = '') {return dfp_my($m = dfpm($m)) ? $m->s($k) : $m->getConfigData($k);}