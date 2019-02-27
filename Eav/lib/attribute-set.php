<?php
use Magento\Catalog\Model\Product as P;
use Magento\Eav\Api\AttributeSetRepositoryInterface as IASR;
use Magento\Eav\Api\Data\AttributeSetInterface as IAS;
use Magento\Eav\Model\AttributeSetRepository as ASR;
use Magento\Eav\Model\Entity\Attribute\Set as _AS;
/**
 * 2019-02-27
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::pOI()
 * @param IAS|_AS|P|int $a
 * @return IAS|_AS
 */
function df_attribute_set($a) {return $a instanceof IAS ? $a : df_attribute_set_r()->get(
	$a instanceof P ? $a->getAttributeSetId() : $a
);}

/**
 * 2019-02-27
 * @used-by df_attribute_set()
 * @return IASR|ASR
 */
function df_attribute_set_r() {return df_o(IASR::class);}