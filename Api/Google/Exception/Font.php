<?php
namespace Df\Api\Google\Exception;
/**
 * 2015-11-17
 * В документации об этом ни слова не сказано,
 * однако в случае сбоя Google API возвращает JSON следующией структуры:
	{
		error: {
			errors: [
				{
					domain: "usageLimits",
					reason: "accessNotConfigured",
					message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
					extendedHelp: "https://console.developers.google.com"
				}
			],
			code: 403,
			message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration."
		}
	}
 * https://developers.google.com/fonts/docs/developer_api
 */
class Font extends \Df\Core\Exception {
	/**
	 * 2015-11-27
	 * @override
	 * @see \Df\Core\Exception::getMessageRm()
	 * @return string
	 */
	public function getMessageRm() {
		/** @var string[] $resultA */
		$resultA[]= "Google Fonts API error: «{$this->messageI()}».";
		if ($this->isAccessNotConfigured()) {
			$resultA[] = 'You need to setup Google Fonts API using the instruction https://mage2.pro/t/269';
		}
		return df_cc_n($resultA);
	}

	/** @return bool */
	private function isAccessNotConfigured() {return 'accessNotConfigured' === $this->reason();}

	/**
	 * 2015-11-28
		{
			domain: "usageLimits",
			reason: "accessNotConfigured",
			message: "Access Not Configured. The API (Google Fonts Developer API) is not enabled for your project. Please use the Google Developers Console to update your configuration.",
			extendedHelp: "https://console.developers.google.com"
		}
	 * @return array(string => string)
	 */
	private function firstError() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_first($this['errors']);
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function messageI() {return $this['message'];}

	/** @return string */
	private function reason() {return df_a($this->firstError(), 'reason');}
}