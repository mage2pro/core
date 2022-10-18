<?php
namespace Df\Core;
/**
 * 2017-07-13
 * @see \CanadaSatellite\Bambora\Response (canadasatellite.ca, https://github.com/canadasatellite-ca/bambora/issues/1)
 * @see \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element
 * @see \Df\Config\A
 * @see \Df\Config\O
 * @see \Df\Core\Format\Html\Tag
 * @see \Df\Core\Text\Regex
 * @see \Df\Core\Visitor
 * @see \Df\GoogleFont\Font
 * @see \Df\GoogleFont\Font\Variant
 * @see \Df\GoogleFont\Font\Variant\Preview\Params
 * @see \Df\GoogleFont\Fonts
 * @see \Df\GoogleFont\Fonts\Png
 * @see \Df\Qa\Trace\Frame
 * @see \Df\Sso\Customer
 * @see \Df\Typography\Css
 * @see \Df\Typography\Size
 * @see \Df\Xml\G
 * @see \Dfe\CheckoutCom\Handler
 * @see \Dfe\TwoCheckout\Address
 * @see \Dfe\TwoCheckout\Handler
 * @see \Dfe\TwoCheckout\LineItem
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
	 * @used-by \Df\Backend\Block\Widget\Form\Renderer\Fieldset\Element::render()
	 * @used-by \Df\Config\A::i()
	 * @used-by \Df\Core\Text\Regex::i()
	 * @used-by \Df\Framework\Log\Record::__construct()
	 * @used-by \Df\GoogleFont\Font\Variant::i()
	 * @used-by \Df\GoogleFont\Font\Variant\Preview\Params::fromRequest()
	 * @used-by \Df\GoogleFont\Fonts\Sprite::i()
	 * @used-by \Df\Qa\Failure\Error::i()
	 * @used-by \Df\Qa\Failure\Exception::i()
	 * @used-by \Df\Xml\G::p()
	 * @used-by \Dfe\TwoCheckout\Exception::__construct()
	 * @used-by \Dfe\TwoCheckout\LineItem::buildLI()
	 * @used-by \Inkifi\Mediaclip\Event::s()
	 * @used-by \Inkifi\Pwinty\API\B\Order\AddImages::p()
	 * @used-by \Inkifi\Pwinty\AvailableForDownload::images()
	 * @used-by \Inkifi\Pwinty\Event::shipments()
	 * @used-by \Inkifi\Pwinty\T\CaseT\V30\Order\AddImages::t01()
	 * @param array(string => mixed) $a [optional]
	 */
	final function __construct(array $a = []) {$this->_a = $a;}

	/**
	 * 2017-07-13
	 * @used-by \CanadaSatellite\Bambora\Action::check() (https://github.com/canadasatellite-ca/bambora)
	 * @used-by \Df\API\Operation::a()
	 * @used-by \Df\API\Operation::req()
	 * @used-by \Df\Config\Backend\ArrayT::processI()
	 * @used-by \Df\Config\O::v()
	 * @used-by \Df\Core\Format\Html\Tag::attributes()
	 * @used-by \Df\Framework\Log\Record::d()
	 * @used-by \Dfe\TwoCheckout\Exception::message()
	 * @used-by \Dfe\TwoCheckout\Exception::messageC()
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
	 */
	function offsetExists($k): bool {return !is_null(dfa_deep($this->_a, $k));}

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
	 */
	function offsetGet($k): mixed {return dfa_deep($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @used-by df_prop()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v): void {dfa_deep_set($this->_a, $k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k): void {dfa_deep_unset($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by a()
	 * @var array(string => mixed)
	 */
	private $_a;
}