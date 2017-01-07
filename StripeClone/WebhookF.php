<?php
// 2017-01-03
namespace Df\StripeClone;
use Df\Core\Exception as DFE;
use Df\Framework\Request as Req;
use Df\Payment\Exception\Webhook\NotImplemented;
use Df\StripeClone\Settings as S;
abstract class WebhookF extends \Df\Payment\WebhookF {
	/**
	 * 2017-01-04
	 * @used-by _class()
	 * @return string
	 */
	abstract protected function typeKey();

	/**
	 * 2017-01-04
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 */
	public function __construct() {$this->ss()->init();}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::i()
	 * @used-by \Df\Payment\Action\Webhook::execute()
	 * @param string|object $module
	 * @param array(string => mixed)|null $req [optional]
	 * @return Webhook
	 */
	final public function i($module, $req = null) {
		/** @var Webhook $result */
		$result = parent::i($module, $req);
		$result->typeSet(is_null($req) ? Req::extra('type') : $req[$this->typeKey()]);
		return $result;
	}

	/**
	 * 2017-01-03
	 * @override
	 * @see \Df\Payment\WebhookF::_class()
	 * @used-by \Df\Payment\WebhookF::i()
	 * @param string|object $module
	 * @param array(string => mixed) $req
	 * @param array(string => mixed) $extra [optional]
	 * @return string
	 * @throws DFE|NotImplemented
	 */
	final protected function _class($module, array $req, array $extra = []) {
		/** @var string $typeKey */
		$typeKey = $this->typeKey();
		// 2017-01-07
		// В первую очередь смотрим тип запроса в $extra, а затем — в $req.
		/** @var string $type */
		$type = dfa($extra, $typeKey, dfa($req, $typeKey));
		if (!$type) {
			df_error('The request is invalid because it does not specify its type.');
		}
		// 2016-03-18
		// https://stripe.com/docs/api#event_object-type
		// Пример события с обоими разделителями: «charge.dispute.funds_reinstated»
		/** @var string $s */
		$s = df_cc_class_uc(df_explode_multiple(['.', '_'], $type));
		if (!$s) {
			df_error('The request is invalid.');
		}
		/** @var string|null $result */
		$result = df_con($module, df_cc_class('Webhook', $s), null, false);
		if (!$result) {
			throw new NotImplemented($module, $type);
		}
		return $result;
	}

	/**
	 * 2017-01-04
	 * @override
	 * @see \Df\Payment\WebhookF::req()
	 * @used-by \Df\Payment\WebhookF::i()
	 * @return array(string => mixed)
	 */
	final protected function req() {
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
}

