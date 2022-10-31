<?php
use Magento\Catalog\Model\Product as P;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IASR;
use Magento\Eav\Api\Data\AttributeSetInterface as IAS;
use Magento\Eav\Model\AttributeSetRepository as ASR;
use Magento\Eav\Model\Entity\Attribute\Set as _AS;
/**
 * 2019-02-27
 * @used-by df_att_set_name()
 * @param IAS|_AS|P|int $a
 * @return IAS|_AS
 */
function df_att_set($a) {return $a instanceof IAS ? $a : df_att_set_r()->get(
	$a instanceof P ? $a->getAttributeSetId() : $a
);}

/**
 * 2019-09-04
 * @used-by ikf_is_mediaclip_product()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
 * @param IAS|_AS|P|int $a
 */
function df_att_set_name($a):string {return df_att_set($a)->getAttributeSetName();}

/**
 * 2019-02-27
 * @used-by df_att_set()
 * @return IASR|ASR
 */
function df_att_set_r() {return df_o(IASR::class);}