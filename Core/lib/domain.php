<?php
/**
 * @param \Df\Core\O $object
 * @return void
 */
function df_destructable_singleton(\Df\Core\O $object) {
	\Df\Core\GlobalSingletonDestructor::s()->register($object);
}