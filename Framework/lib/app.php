<?php
use Magento\Framework\App\DeploymentConfig as DC;
/**
 * 2021-02-24
 * @used-by df_db_credentials()
 * @param string|string[] $k [optional]
 * @return DC|string|string[]|null
 */
function df_deployment_cfg($k = '') {
	$r = df_o(DC::class); /** @var DC $r */
	return df_nes($k) ? $r : dfa($r->get(), $k);
}