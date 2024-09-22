<?php
/**
 * 2018-04-24 I have added @uses trim() today.
 * @used-by df_module_enum()
 * @used-by df_parse_colon()
 * @used-by df_tab()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\Core\Text\Regex::getSubjectSplitted()
 * @used-by \Dfe\AllPay\Charge::descriptionOnKiosk()
 * @used-by \Dfe\Moip\P\Charge::pInstructionLines()
 * @used-by \Dfe\TBCBank\W\Reader::reqFilter()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @used-by \Inkifi\Core\Plugin\Catalog\Block\Product\View::afterSetLayout()
 * @return string[]
 */
function df_explode_n(string $s):array {return explode("\n", df_normalize(df_trim($s)));}