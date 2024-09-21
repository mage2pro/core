<?php
use ReflectionNamedType as NT; # 2024-09-21 https://www.php.net/manual/en/class.reflectionnamedtype.php
use ReflectionParameter as P;

/**
 * 2024-09-21
 * https://www.php.net/manual/en/reflectionparameter.gettype.php
 * https://www.php.net/manual/en/class.reflectionnamedtype.php
 * https://www.php.net/manual/en/reflectionnamedtype.getname.php#128874
 */
function dfr_param_type(P $p):string {return ($t = $p->getType()) instanceof NT ? $t->getName() : '';}