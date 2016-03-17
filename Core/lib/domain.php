<?php
/**
 * @param \Df\Core\Destructable $object
 * @return void
 */
function df_destructable_singleton(\Df\Core\Destructable $object) {
	\Df\Core\GlobalSingletonDestructor::s()->register($object);
}