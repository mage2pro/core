<?php
namespace Df\Core;
// 2015-08-13
final class State {

	/**
	 * @used-by \Df\Core\Observer\ControllerActionPredispatch::execute()
	 * @param \Magento\Framework\App\Action\Action $controller
	 */
	function controllerSet(\Magento\Framework\App\Action\Action $controller) {
		$this->_controller = $controller;
	}

	/**
	 * 2015-09-02
	 * Значение по умолчанию null можно не указывать.
	 * @var \Magento\Framework\App\Action\Action|null
	 */
	private $_controller;

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}