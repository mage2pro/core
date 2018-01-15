<?php
namespace Df\Payment\W;
use Df\Core\Exception as DFE;
use Df\Framework\W\Result as wResult;
use Df\Payment\W\Exception\Ignored;
/**
 * 2016-08-27
 * 2017-03-19
 * The class is not abstract anymore: it is used as the base for the following virtual types:
 * 1) AllPay:
 * 1a) https://github.com/mage2pro/allpay/blob/1.10.0/etc/di.xml#L6
 * 1b) https://github.com/mage2pro/allpay/blob/1.10.0/etc/di.xml#L8
 * 2) AlphaCommerceHub: https://github.com/mage2pro/alphacommercehub/blob/1c759e8/etc/frontend/di.xml#L13
 * 3) Dragonpay: https://github.com/mage2pro/dragonpay/blob/0.1.2/etc/di.xml#L7
 * 4) Ginger Payments: https://github.com/mage2pro/ginger-payments/blob/0.4.1/etc/di.xml#L6
 * 5) iPay88: https://github.com/mage2pro/ipay88/blob/0.0.9/etc/di.xml#L13
 * 6) Iyzico: https://github.com/mage2pro/iyzico/blob/0.2.3/etc/di.xml#L6
 * 7) Kassa Compleet: https://github.com/mage2pro/kassa-compleet/blob/0.4.1/etc/di.xml#L6
 * 8) Moip: https://github.com/mage2pro/moip/blob/0.0.1/etc/di.xml#L6
 * 8) Omise: https://github.com/mage2pro/omise/blob/1.7.1/etc/di.xml#L7
 * 10) Paymill: https://github.com/mage2pro/paymill/blob/1.3.1/etc/di.xml#L6
 * 11) PostFinance: https://github.com/mage2pro/postfinance/blob/0.1.2/etc/di.xml#L7
 * 12) QIWI Wallet: https://github.com/mage2pro/qiwi/blob/0.3.0/etc/di.xml#L7
 * 13) Robokassa: https://github.com/mage2pro/robokassa/blob/0.0.4/etc/di.xml#L6
 * 14) SecurePay: https://github.com/mage2pro/securepay/blob/1.4.1/etc/di.xml#L6
 * 15) Stripe: https://github.com/mage2pro/stripe/blob/1.9.1/etc/di.xml#L6
 * 16) Yandex.Kassa: https://github.com/mage2pro/yandex-kassa/blob/0.2.1/etc/di.xml#L7
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class Action extends \Df\Payment\Action {
	/**
	 * 2016-08-27
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return wResult|null
	 * @throws DFE
	 */
	function execute() {
		$m = $this->module(); /** @var string $m */
		$f = null; /** @var F|null $f */
		$responder = null; /** @var Responder|null $responder */
		$r = null; /** @var wResult|null $r */
		try {
			$f = F::s($m);
			$responder = $f->responder();
			$ev = $f->e(); /** @var Event $ev */
			if ($type = $ev->checkIgnored()) { /** @var string $type */
				throw new Ignored($f->m(), $ev->rd(), $type);
			}
			$f->handler()->handle();
		}
		catch (Ignored $e) {
			$this->ignoredLog($e);
			$responder->setIgnored($e);
		}
		catch (\Exception $e) {
			df_log_e($e);
			df_sentry($m, $e);
			if ($e instanceof IEvent && $e->r()) {
				df_log_l($m, $e->r());
			}
			if ($responder) {
				$responder->setError($e);
			}
			else {
				$r = Responder::defaultError($e);
			}
		}
		$r = $r ?: $responder->get();
		/**
		 * 2017-11-18
		 * "Implement a function to distinguish between a customer return from a PSP payment page
		 * and a PSP webhook notification": https://github.com/mage2pro/core/issues/53
		 * 2017-11-21
		 * "AlphaCommerceHub with POLi Payments: a blank page is shown to the buyer on a error"
		 * https://github.com/mage2pro/core/issues/59
		 */
		$isRedirect = df_is_redirect(); /** @var bool $isRedirect */
		/**
		 * 2017-11-18
		 * «Call to a member function isSuccess() on null in mage2pro/core/Payment/W/Action.php:78»
		 * https://github.com/mage2pro/core/issues/55
		 */
		if (!$responder || !$responder->isSuccess()) {
			df_log_l($m, $r->__toString(), 'response');
			if ($isRedirect) {
				// 2016-07-14
				// Show an explanation message to the customer
				// when it returns to the store after an unsuccessful payment attempt.
				df_checkout_error($r->__toString());
			}
		}
		if ($isRedirect) {
			$r = null;
		}
		else {
			df_response_sign($r);
			if (!$r instanceof wResult) {
				/**
				 * 2017-01-07
				 * Иначе мы можем получить сложнодиагностируемый сбой «Invalid return type».
				 * @see \Magento\Framework\App\Http::launch()
				 * https://github.com/magento/magento2/blob/2.1.3/lib/internal/Magento/Framework/App/Http.php#L137-L145
				 */
				df_error('Invalid result class: %s.', get_class($r));
			}
		}
		return $r;
	}

	/**
	 * 2017-01-17
	 * 2017-02-01
	 * Отныне игнорируемые операции логирую только на своих серверах.
	 * Аналогично поступаю и с @see \Df\Payment\Method::action():
	 * @see \Df\StripeClone\Method::needLogActions()
	 * @used-by execute()
	 * @param Ignored $e
	 */
	private function ignoredLog(Ignored $e) {
		if (df_my()) {
			dfp_sentry_tags($m = $e->m()); /** @var string $m */
			$req = $e->event()->r(); /** @var array(string => mixed) $req */
			$ev = $e->event(); /** @var Event $ev */
			$label = $ev->tl(); /** @var string $label */
			df_sentry($m, "[{$e->mTitle()}] {$label}: ignored", ['extra' => $req]);
			df_log_l($m, $req, $ev->t());
		}
	}
}