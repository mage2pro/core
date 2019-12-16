<?php
use Df\Core\GlobalSingletonDestructor as D;
use Df\Core\O;

/**
 * @used-by \Df\Core\O::_construct()
 * @param O $o
 */
function df_destructable_sg(O $o) {D::s()->register($o);}