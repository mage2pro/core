<?php
namespace Df\Framework\Data\Form\Element\Select;
use Df\Framework\Data\Form\Element\Select;
class Integer extends Select {
	/**
	 * 2016-01-29
	 * @override
	 * @return array(array(string => string))
	 */
	public function getValues() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(mixed => mixed) $result */
			$result = [];
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}