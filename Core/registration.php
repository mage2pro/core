<?php
use Magento\Framework\Component\ComponentRegistrar as R;
R::register(R::MODULE, 'Df_Core', __DIR__);
\Df\Core\Boot::run();