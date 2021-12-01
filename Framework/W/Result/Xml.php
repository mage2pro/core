<?php
namespace Df\Framework\W\Result;
/**
 * 2021-12-02
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class Xml extends Text {
	/**
	 * 2021-12-02
	 * @override
	 * @see \Df\Framework\W\Result\Text::contentType()
	 * @used-by \Df\Framework\W\Result\Text::render()
	 * @return string
	 */
	final protected function contentType() {return 'application/xml';}

	/**
	 * 2021-12-02
	 * @override
	 * @see \Df\Framework\W\Result\Text::prepare()
	 * @used-by \Df\Framework\W\Result\Text::i()
	 * @param string|object|mixed[] $s
	 * @return string
	 */
	final protected function prepare($s) {return $s;}
}