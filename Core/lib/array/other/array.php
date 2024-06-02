<?php
/**
 * @used-by df_con_hier_suf_ta()
 * @used-by df_explode_xpath()
 * @used-by df_fe_init()
 * @used-by df_find()
 * @used-by df_map()
 * @used-by \Df\API\Facade::p()
 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetComment()
 * @used-by \TFC\Core\B\Home\Slider::p() (tradefurniturecompany.co.uk, https://github.com/tradefurniturecompany/core/issues/43)
 * @param mixed|mixed[] $v
 */
function df_array($v):array {return is_array($v) ? $v : [$v];}