<?php
/** @param \Df\Core\O $o */
function df_destructable_sg(\Df\Core\O $o) {\Df\Core\GlobalSingletonDestructor::s()->register($o);}