<?php

/**
 * @param \Df\Core\Destructable $object
 * @return void
 */
function df_destructable_singleton(\Df\Core\Destructable $object) {
	\Df\Core\GlobalSingletonDestructor::s()->register($object);
}

/**
 * @param Exception|string $e
 * @return string
 */
function df_ets($e) {
	return
		is_string($e)
		? $e
		: ($e instanceof \Df\Core\Exception ? $e->getMessageRm() : $e->getMessage())
	;
}




