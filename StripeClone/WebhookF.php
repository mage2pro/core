<?php
//
namespace Df\StripeClone;
use Df\Core\Exception as DFE;
use Df\Payment\Exception\Webhook\NotImplemented;
use Df\StripeClone\Settings as S;
/**
 * 2017-01-03
 * @see \Dfe\Omise\WebhookF
 * @see \Dfe\Stripe\WebhookF
 */
abstract class WebhookF extends \Df\Payment\WebhookF {
	/**
	 * 2017-01-04
	 * @used-by type()
	 * @see \Dfe\Omise\WebhookF::typeKey()
	 * @see \Dfe\Stripe\WebhookF::typeKey()
	 * @return string
	 */
	abstract protected function typeKey();

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::__construct()
	 * @param string|object $module
	 * @param array(string => mixed)|null $req [optional]
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 */
	public function __construct($module, $req = null) {
		$this->ss()->init();
		parent::__construct($module, $req);
	}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::i()
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @return Webhook
	 */
	final public function i() {
		/** @var Webhook $result */
		$result = parent::i();
		$result->typeSet($this->type());
		return $result;
	}

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
		$s = df_cc_class_uc(df_explode_multiple(['.', '_'], $type));
		if (!$s) {
			$this->eRequestIsInvalid("there is no class for the type «{$type}»");
		}
		/** @var string|null $result */
		$result = df_con($this->module(), df_cc_class('Webhook', $s), null, false);
		if (!$result) {
			throw new NotImplemented($this->req(), $this->module(), $type);
		}
		return $result;
	}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::reqFromHttp()
	 * @used-by \Df\Payment\WebhookF::__construct()
	 * @return array(string => mixed)
	 */
	final protected function reqFromHttp() {
		/** @var string $json */
		$json = file_get_contents('php://input');
		// 2017-01-07
		// На localhost $json будет равен пустой строке.
		return !$json ? [] : df_json_decode($json);
	}

	/**
	 * 2016-12-25
	 * @return S
	 */
	final protected function ss() {return dfc($this, function() {return S::conventionB(static::class);});}

	/**
	 * 2017-01-07
	 * @used-by i()
	 * @return string
	 */
	private function type() {return dfc($this, function() {return
		$this->assertType($this->extra(self::KEY_TYPE, $this->req($this->typeKey())))
	;});}

	/**
	 * 2017-01-08
	 * @used-by type()
	 */
	const KEY_TYPE = 'type';
}