<?php
/**
 * 2015-08-16
 * https://mage2.ru/t/95
 * https://mage2.pro/t/60
 * @param string $eventName
 * @param array(string => mixed) $data
 * @return void
 */
function df_dispatch($eventName, array $data = []) {
	/** @var \Magento\Framework\Event\ManagerInterface|\Magento\Framework\Event\Manager $manager */
	$manager = df_o(\Magento\Framework\Event\ManagerInterface::class);
	$manager->dispatch($eventName, $data);
}

/** @return \Df\Core\Helper\Output */
function df_output() {return \Df\Core\Helper\Output::s();}

