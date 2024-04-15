<?php
use ReflectionClass as RC;

/**
 * 2016-05-06
 * By analogy with https://github.com/magento/magento2/blob/135f967/lib/internal/Magento/Framework/ObjectManager/TMap.php#L97-L99
 * 2016-05-23
 * Намеренно не объединяем строки в единное выражение, чтобы собака @ не подавляла сбои первой строки.
 * Такие сбои могут произойти при синтаксических ошибках в проверяемом классе
 * (похоже, getInstanceType как-то загружает код класса).
 * @used-by df_con_hier_suf()
 * @used-by \Df\Payment\Block\Info::checkoutSuccess()
 */
function df_class_exists(string $c):bool {$c = df_ctr($c); return @class_exists($c);}

/**
 * 2016-01-01
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * https://3v4l.org/k6Hd5
 * @used-by \Df\Config\Plugin\Model\Config\SourceFactory::aroundCreate()
 * @param string|object $c
 */
function df_class_my($c):bool {return in_array(df_class_f($c), ['Df', 'Dfe', 'Dfr']);}

/**
 * 2017-01-11 http://stackoverflow.com/a/666701
 * @used-by df_con_hier_suf()
 * @used-by \Df\Core\R\ConT::generic()
 * @used-by \Df\Payment\W\F::i()
 */
function df_is_abstract(string $c):bool {df_param_sne($c, 0); return (new RC(df_ctr($c)))->isAbstract();}