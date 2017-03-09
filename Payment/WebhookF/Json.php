<?php
namespace Df\Payment\WebhookF;
use Df\Core\Exception as DFE;
use Df\Payment\Exception\Webhook\NotImplemented;
use Df\Payment\Settings as S;
use Df\Payment\Webhook as W;
/**
 * 2017-03-09
 * @see \Df\StripeClone\WebhookF
 */
abstract class Json extends \Df\Payment\WebhookF {
	/**
	 * 2017-01-04
	 * @used-by type()
	 * @see \Dfe\Omise\WebhookF::typeKey()
	 * @see \Dfe\Paymill\WebhookF::typeKey()
	 * @see \Dfe\Stripe\WebhookF::typeKey()
	 * @return string
	 */
	abstract protected function typeKey();

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::__construct()
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @param string|object $module
	 * @param array(string => mixed)|null $req [optional]
	 */
	function __construct($module, $req = null) {
		$this->ss()->init();
		parent::__construct($module, $req);
	}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::i()
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @return W
	 */
	final function i() {return parent::i()->typeSet($this->type());}

	/**
	 * 2017-01-03
	 * @override
	 * @see \Df\Payment\WebhookF::_class()
	 * @used-by \Df\Payment\WebhookF::i()
	 * @return string
	 * @throws DFE|NotImplemented
	 */
	final protected function _class() {
		// 2017-01-07
		// В первую очередь смотрим тип запроса в $extra, а затем — в $req.
		/** @var string $type */
		$type = $this->type();
		// 2016-03-18
		// https://stripe.com/docs/api#event_object-type
		// Пример события с обоими разделителями: «charge.dispute.funds_reinstated»
		/** @var string $s */
		if (!($s = df_cc_class_uc(df_explode_multiple(['.', '_'], $type)))) {
			$this->eRequestIsInvalid("there is no class for the type «{$type}»");
		}
		/** @var string|null $result */
		if (!($result = df_con($this->module(), df_cc_class('Webhook', $s), null, false))) {
			throw new NotImplemented($this->req(), $this->module(), $type);
		}
		return $result;
	}

	/**
	 * 2017-01-04
	 * 2017-01-07
	 * На localhost результатом будет пустой массив.
	 * @override
	 * @see \Df\Payment\WebhookF::reqFromHttp()
	 * @used-by \Df\Payment\WebhookF::__construct()
	 * @return array(string => mixed)
	 */
	final protected function reqFromHttp() {return df_request_body_json();}

	/**
	 * 2016-12-25
	 * @return S
	 */
	final protected function ss() {return dfc($this, function() {return
		S::conventionB($this->module())
	;});}

	/**
	 * 2017-01-07
	 * @used-by i()
	 * @return string
	 */
	private function type() {return dfc($this, function() {return $this->assertType(
		$this->extra(self::KEY_TYPE, $this->req($this->typeKey()))
	);});}

	/**
	 * 2017-01-08
	 * @used-by type()  
	 * @used-by \Df\StripeClone\Webhook::testDataFile()
	 */
	const KEY_TYPE = 'type';
}