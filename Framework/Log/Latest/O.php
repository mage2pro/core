<?php
namespace Df\Framework\Log\Latest;
/**
 * 2024-03-04
 * @see \Df\Framework\Log\Latest\Record
 * @see \Df\Framework\Log\Latest\Throwable
 */
interface O {
	/**
	 * 2024-03-04
	 * @see \Df\Framework\Log\Latest\Record::id()
	 * @see \Df\Framework\Log\Latest\Throwable::id()
	 * @used-by \Df\Framework\Log\Latest::register()
	 * @used-by \Df\Framework\Log\Latest::registered()
	 */
	function id(): string;
}