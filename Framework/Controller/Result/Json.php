<?php
namespace Df\Framework\Controller\Result;
/**
 * 2016-08-24
 * Нельзя делать этот класс final:
 * Class Df\Framework\Controller\Result\Json\Interceptor
 * may not inherit from final class (Df\Framework\Controller\Result\Json)
 */
class Json extends Text {
	/**
	 * 2016-08-24
	 * @override
	 * @see \Df\Framework\Controller\Result\Text::contentType()
	 * @used-by \Df\Framework\Controller\Result\Text::render()
	 *
	 * 2016-03-18
	 * «The @see \Magento\Framework\App\Response\Http::representJson()
	 * does not specifies a JSON response's charset and removes a previously specified charset,
	 * so not-latin characters are rendered incorrectly by all the modern browsers»
	 * https://mage2.pro/t/976
	 *
	 * @return mixed
	 */
	protected function contentType() {return 'application/json';}

	/**
	 * 2016-08-24
	 * @override
	 * @see \Df\Framework\Controller\Result\Text::prepare()
	 * @used-by \Df\Framework\Controller\Result\Text::i()
	 * @param string|mixed[] $body
	 * @return string
	 */
	protected function prepare($body) {return is_array($body) ? df_json_encode($body) : $body;}
}