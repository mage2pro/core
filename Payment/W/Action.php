<?php
namespace Df\Payment\W;
use Df\Framework\Controller\Result\Text;
use Df\Payment\W\Exception\Ignored;
/**
 * 2016-08-27
 * @see \Dfe\AllPay\Controller\Confirm\Index
 *
 * 2017-03-19
 * The class is not abstract anymore: it is used as the base for the following virtual types:
 * 1) Dragonpay: https://github.com/mage2pro/dragonpay/blob/0.1.1/etc/di.xml#L6
 * 2) Ginger Payments: https://github.com/mage2pro/ginger-payments/blob/0.4.1/etc/di.xml#L6
 * 3) iPay88: https://github.com/mage2pro/ipay88/blob/0.0.9/etc/di.xml#L13
 * 4) Iyzico: https://github.com/mage2pro/iyzico/blob/0.2.3/etc/di.xml#L6
 * 5) Kassa Compleet: https://github.com/mage2pro/kassa-compleet/blob/0.4.1/etc/di.xml#L6
 * 6) Moip: https://github.com/mage2pro/moip/blob/0.0.1/etc/di.xml#L6
 * 7) Omise: https://github.com/mage2pro/omise/blob/1.7.1/etc/di.xml#L7
 * 8) Paymill: https://github.com/mage2pro/paymill/blob/1.3.1/etc/di.xml#L6
 * 9) Robokassa: https://github.com/mage2pro/robokassa/blob/0.0.4/etc/di.xml#L6
 * 10) SecurePay: https://github.com/mage2pro/securepay/blob/1.4.1/etc/di.xml#L6
 * 11) Stripe: https://github.com/mage2pro/stripe/blob/1.9.1/etc/di.xml#L6
 */
class Action extends \Df\Payment\Action {
	/**
	 * 2016-08-27
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\App\Action\Action::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Text
	 */
	function execute() {
		$m = $this->m(); /** @var string $m */
		/** @var Text $result */
		try {
			$result = F::s($m)->handler()->handle();
		}
		catch (Ignored $e) {
			$result = $this->ignored($e);
		}
		catch (\Exception $e) {
			df_log_e($e);
			df_sentry($m, $e);
			if ($e instanceof IEvent && $e->r()) {
				df_log_l($m, $e->r());
			}
			$result = $this->error($e);
		}
		if (df_my()) {
			df_log_l($m, $result->__toString(), 'response');
		}
		/**
		 * 2017-01-07
		 * Иначе мы можем получить сложнодиагностируемый сбой «Invalid return type».
		 * @see \Magento\Framework\App\Http::launch()
		 * https://github.com/magento/magento2/blob/2.1.3/lib/internal/Magento/Framework/App/Http.php#L137-L145
		 */
		return df_ar(df_response_sign($result), Text::class);
	}

	/**
	 * 2017-01-02
	 * @used-by execute()
	 * @see \Dfe\AllPay\Controller\Confirm\Index::error()
	 * @param \Exception $e
	 * @return $this
	 */
	protected function error(\Exception $e) {return Handler::resultError($e);}

	/**
	 * 2017-01-17
	 * @used-by execute()
	 * @param Ignored $e
	 * @return Text
	 */
	private function ignored(Ignored $e) {
		/**
		 * 2017-02-01 
		 * Отныне игнорируемые операции логирую только на своих серверах.
		 * Аналогично поступаю и с @see \Df\Payment\Method::action():
		 * @see \Df\StripeClone\Method::needLogActions()
		 */
		if (df_my()) {
			/** @var string $m */
			dfp_sentry_tags($m = $e->m());
			/** @var array(string => mixed) $req */
			$req = $e->event()->r();
			/** @var Event $ev */
			$ev = $e->event();			
			/** @var string $label */
			$label = $ev->tl();
			df_sentry($m, "[{$e->mTitle()}] {$label}: ignored", [
				'extra' => ['Payment Data' => df_json_encode($req)]
			]);
			df_log_l($m, $req, $ev->t());
		}
		return Text::i($e->message());
	}
}