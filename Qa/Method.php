<?php
namespace Df\Qa;
use Df\Qa\Trace\Frame;
use Df\Zf\Validate\StringT\Iso2 as VIso2;
use Zend_Validate_Interface as Vd;
use \Exception as E;
use \ReflectionParameter as RP;
final class Method {
	/**
	 * @used-by df_param_iso2()
	 * @throws E
	 */
	static function assertParamIsIso2(string $v, int $ord, int $sl = 0):string {return self::vp(VIso2::s(), $v, $ord, ++$sl);}

	/**
	 * @used-by df_param_sne()
	 * @used-by self::vp()
	 * @throws E
	 */
	static function raiseErrorParam(string $method, array $messages, int $ord, int $sl = 1):void {
		$frame = self::caller($sl); /** @var Frame $frame */
		$name = 'unknown'; /** @var string $name */
		if ($frame->methodR()) {/** @var RP $param */
			$name = $frame->methodParameter($ord)->getName();
		}
		$messagesS = df_cc_n($messages); /** @var string $messagesS */
		self::throwException(
			"[{$frame->method()}]"
			."\nThe argument «{$name}» is rejected by the «{$method}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			,$sl
		);
	}

	/**
	 * @used-by df_result_s()
	 * @used-by df_result_sne()
	 * @throws E
	 */
	static function raiseErrorResult(string $vd, array $messages, int $sl = 1):void {
		$messagesS = df_cc_n($messages); /** @var string $messagesS */
		$method = self::caller($sl)->method(); /** @var string $method */
		self::throwException(
			"[{$method}]\nA result of this method is rejected by the «{$vd}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $sl
		);
	}

	/**
	 * @used-by df_assert_sne()
	 * @throws E
	 */
	static function raiseErrorVariable(string $vd, array $messages, int $sl = 1):void {
		$messagesS = df_cc_n($messages); /** @var string $messagesS */
		$method = self::caller($sl)->method(); /** @var string $method */
		self::throwException(
			"[{$method}]\nThe validator «{$vd}» has catched a variable with an invalid value."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			,$sl
		);
	}

	/**
	 * 2017-01-12
	 * @used-by df_assert_sne()
	 * @used-by df_param_sne()
	 * @used-by df_result_sne()
	 */
	const NES = 'A non-empty string is required, but got an empty one.';

	/**
	 * Объект @see Frame конструируется на основе $o + 2,
	 * потому что нам нужно вернуть название метода, который вызвал тот метод, который вызвал метод caller.
	 * @used-by self::raiseErrorParam()
	 * @used-by self::raiseErrorResult()
	 * @used-by self::raiseErrorVariable()
	 */
	private static function caller(int $o):Frame {return Frame::i(df_bt(0, 3 + $o)[2 + $o]);}

	/**
	 * 2015-01-28
	 * Раньше тут стояло throw $e, что приводило к отображению на экране диагностического сообщения в неверной кодировке.
	 * @uses df_error() точнее: эта функция в режиме разработчика отсылает браузеру заголовок HTTP о требуемой кодировке.
	 * @used-by self::raiseErrorParam()
	 * @used-by self::raiseErrorResult()
	 * @used-by self::raiseErrorVariable()
	 * @throws E
	 */
	private static function throwException(string $message, int $sl = 0):void {df_error(new E($message, ++$sl));}
	
	/**
	 * @used-by self::assertParamIsIso2()
	 * @param mixed $v
	 * @return mixed
	 * @throws E
	 */
	private static function vp(Vd $vd, $v, int $ord, int $sl = 1) {return $vd->isValid($v) ? $v : self::raiseErrorParam(
		get_class($vd), $vd->getMessages(), $ord, ++$sl
	);}
}