<?php
namespace Df\Core;
/**
 * 2017-07-13
 * @see df_block()
 * @see \Df\Config\A
 * @see \Df\Config\O
 * @see \Df\Core\A
 * @see \Df\Core\Visitor
 * @see \Df\Qa\Message
 * @see \Df\Qa\Trace\Frame
 * @see \Inkifi\Mediaclip\API\Entity\Order\Item
 * @see \Inkifi\Mediaclip\API\Entity\Order\Item\File
 * @see \Inkifi\Mediaclip\Event
 * @see \Inkifi\Pwinty\API\Entity\Image
 * @see \Inkifi\Pwinty\API\Entity\Order
 * @see \Inkifi\Pwinty\API\Entity\Order\ValidationResult
 * @see \Inkifi\Pwinty\API\Entity\Shipment
 * @see \Inkifi\Pwinty\Event
 */
class O implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @used-by ikf_api_oi()
	 * @used-by \Df\API\Facade::p()
	 * @used-by \Df\Config\A::i()
	 * @used-by \Df\Qa\Message\Failure\Error::i()
	 * @used-by \Df\Qa\Message\Failure\Exception::i()
	 * @used-by \Inkifi\Mediaclip\Event::s()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImages::p()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @used-by \Inkifi\Pwinty\Event::shipments()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImages::t01()
	 * @param array(string => mixed) $a [optional]
	 */
	function __construct(array $a = []) {$this->_a = $a;}

	/**
	 * 2017-07-13
	 * @used-by \Df\API\Operation::a()
	 * @used-by \Df\API\Operation::req()
	 * @used-by \Df\Config\O::v()
	 * @used-by \Df\Qa\Message\Failure::postface()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImages::p()
	 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t01()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImage::t02()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Create::t01()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Create::t02()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Get::t01()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\Validate::t01()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = null, $d = null) {return dfa($this->_a, $k, $d);}

	/**
	 * 2017-07-13
	 * @used-by \Df\API\Operation::j()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::_p()
	 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @used-by \Inkifi\Pwinty\Controller\Index\Index::execute()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::execute()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pAvailableForDownload()
	 * @return string
	 */
	function j() {return df_json_encode($this->_a);}

	/**
	 * 2017-07-13
	 * «This method is executed when using isset() or empty() on objects implementing ArrayAccess.
	 * When using empty() ArrayAccess::offsetGet() will be called and checked if empty
	 * only if ArrayAccess::offsetExists() returns TRUE».
	 * http://php.net/manual/arrayaccess.offsetexists.php
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @used-by df_prop()
	 * @param string $k
	 * @return bool
	 */
	function offsetExists($k) {return !is_null(dfa_deep($this->_a, $k));}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @used-by df_prop()
	 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::productId()
	 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::projectId()
	 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item\File::id()
	 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item\File::url()
	 * @used-by \Inkifi\Mediaclip\API\Entity\Project::id()
	 * @used-by \Inkifi\Mediaclip\Event::oidE()
	 * @used-by \Inkifi\Mediaclip\Event::productId()
	 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
	 * @used-by \Inkifi\Pwinty\API\Entity\Order::id()
	 * @used-by \Mangoit\MediaclipHub\Controller\Index\OrderStatusUpdateEndpoint::pAvailableForDownload()
	 * @used-by \Stock2Shop\OrderExport\Observer\OrderSaveAfter::execute()
	 * @param string $k
	 * @return array(string => mixed)|mixed|null
	 */
	function offsetGet($k) {return dfa_deep($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @used-by df_prop()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v) {dfa_deep_set($this->_a, $k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k) {dfa_deep_unset($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by a()
	 * @var array(string => mixed)
	 */
	private $_a;
}