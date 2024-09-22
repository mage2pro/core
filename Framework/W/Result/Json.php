<?php
namespace Df\Framework\W\Result;
/**
 * 2016-08-24                                                                               
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by \Df\Core\Controller\Index\Index::execute()
 * @used-by \Dfe\GoogleFont\Controller\Index\Index::execute()
 * @used-by \Dfe\CheckoutCom\Controller\Index\Index::execute()
 * @used-by \Dfe\Square\Controller\Index\Index::execute()
 * @used-by \Dfe\TwoCheckout\Controller\Index\Index::execute()
 * @used-by \Doormall\Shipping\Controller\Index\Index::execute()
 * @used-by \Mangoit\MediaclipHub\Controller\Index\RenewMediaclipToken::execute()
 * @used-by \Wolf\Filter\Controller\Garage\Clean::execute()
 * @used-by \Wolf\Filter\Controller\Garage\Index::execute()
 * @used-by \Wolf\Filter\Controller\Garage\Remove::execute()
 * @used-by \Wolf\Filter\Controller\Index\Change::execute()
 */
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
	 */
	final protected function contentType():string {return 'application/json';}

	/**
	 * 2016-08-24
	 * @override
	 * @see \Df\Framework\W\Result\Text::prepare()
	 * @used-by \Df\Framework\W\Result\Text::i()
	 * @param string|object|mixed[] $s
	 */
	final protected function prepare($s):string {return df_is_stringable($s) ? df_string($s) : df_json_encode($s);}
}