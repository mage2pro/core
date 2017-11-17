<?php
namespace Df\Framework\W\Result;
// 2016-08-24
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Json extends Text {
	/**
	 * 2016-08-24
	 * 2016-03-18
	 * «The @see \Magento\Framework\App\Response\Http::representJson()
	 * does not specifies a JSON response's charset and removes a previously specified charset,
	 * so not-latin characters are rendered incorrectly by all the modern browsers»
	 * https://mage2.pro/t/976
	 * @override
	 * @see \Df\Framework\W\Result\Text::contentType()
	 * @used-by \Df\Framework\W\Result\Text::render()
	 * @return mixed
	 */
	final protected function contentType() {return 'application/json';}

	/**
	 * 2016-08-24
	 * @override
	 * @see \Df\Framework\W\Result\Text::prepare()
	 * @used-by \Df\Framework\W\Result\Text::i()
	 * @param string|mixed[] $body
	 * @return string
	 */
	final protected function prepare($body) {return is_array($body) ? df_json_encode($body) : $body;}
}