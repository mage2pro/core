<?php
use Monolog\Logger as L;
use Psr\Log\LoggerInterface as IL;
/**
 * 2024-09-02
 * @used-by \Df\Qa\Failure\Error::check()
 * @return IL|L
 */
function df_logger():IL {return df_o(IL::class);}