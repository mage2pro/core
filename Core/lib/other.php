<?php
/**
 * @param float|int $value
 * @return int
 */
function df_ceil($value) {return (int)ceil($value);}

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

/**
 * @param mixed $value
 * @return bool
 */
function df_empty_string($value) {return '' === $value;}

/**
 * @param mixed $value
 * @return mixed
 */
function df_empty_to_null($value) {return $value ? $value : null;}

/**
 * @param float|int $value
 * @return int
 */
function df_floor($value) {return (int)floor($value);}

/** @return \Df\Core\Helper\Output */
function df_output() {return \Df\Core\Helper\Output::s();}

/**
 * @param float|int $value
 * @return int
 */
function df_round($value) {return (int)round($value);}