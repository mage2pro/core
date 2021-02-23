<?php
use Magento\Framework\App\DeploymentConfig as DC;
/**
 * 2021-02-24
 * @used-by df_db_credentials()
 * @param string|string[]|null $k [optional]
 * @return DC|string|string[]|null
 */
function df_deployment_cfg($k = null) {
	$r = df_o(DC::class); /** @var DC $r */
	return is_null($k) ? $r : dfa($r->get(), $k);
}