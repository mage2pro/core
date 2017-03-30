<?php
/**
 * @param \Df\Core\O $object
 */
function df_destructable_singleton(\Df\Core\O $object) {
	\Df\Core\GlobalSingletonDestructor::s()->register($object);
}