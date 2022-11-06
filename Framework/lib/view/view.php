<?php
use Magento\Framework\Config\View as ConfigView;
use Magento\Framework\View\Config as ViewConfig;
use Magento\Framework\View\ConfigInterface as ViewConfigI;
/**
 * 2016-04-23
 * By analogy with @see \Magento\Catalog\Helper\Image::getConfigView()
 * https://github.com/magento/magento2/blob/958164/app/code/Magento/Catalog/Helper/Image.php#L781-L792
 * @used-by df_product_image_url()
 */
function df_view_config():ConfigView {
	/**
	 * 2021-11-25
	 * 1) It fixes the «Area code is not set» error in @see \Magento\Framework\App\State::getAreaCode()
	 * when the area code is really not set (e.g., for console commands like `bin/magento tfc:google-shopping:1`).
	 * 2.1) @see \Magento\Framework\View\Config::getViewConfig():
	 * 		$this->assetRepo->updateDesignParams($params);
	 * https://github.com/magento/magento2/blob/2.4.3-p1/lib/internal/Magento/Framework/View/Config.php#L59
	 * 2.2) @see \Magento\Framework\View\Asset\Repository::updateDesignParams():
	 *		if (empty($params['area'])) {
	 *			$params['area'] = $this->getDefaultParameter('area');
	 *		}
	 * https://github.com/magento/magento2/blob/2.4.3-p1/lib/internal/Magento/Framework/View/Asset/Repository.php#L141-L143
	 * 2.3) @see \Magento\Framework\View\Asset\Repository::getDefaultParameter():
	 * 		$this->defaults = $this->design->getDesignParams();
	 * https://github.com/magento/magento2/blob/2.4.3-p1/lib/internal/Magento/Framework/View/Asset/Repository.php#L204-L204
	 * 2.4) @see \Magento\Theme\Model\View\Design::getDesignParams():
	 *		$params = [
	 *			'area' => $this->getArea(),
	 *			'themeModel' => $this->getDesignTheme(),
	 *			'locale'     => $this->getLocale(),
	 *		];
	 * https://github.com/magento/magento2/blob/2.4.3-p1/app/code/Magento/Theme/Model/View/Design.php#L279-L283
	 * 2.5) @see \Magento\Theme\Model\View\Design::getArea():
	 * 		return $this->_appState->getAreaCode();
	 * https://github.com/magento/magento2/blob/2.4.3-p1/app/code/Magento/Theme/Model/View/Design.php#L132
	 * 2.6) @see \Magento\Framework\App\State::getAreaCode():
	 *		if (!isset($this->_areaCode)) {
	 *			throw new \Magento\Framework\Exception\LocalizedException(
	 *				new \Magento\Framework\Phrase('Area code is not set')
	 *			);
	 *		}
	 * https://github.com/magento/magento2/blob/2.4.3-p1/lib/internal/Magento/Framework/App/State.php#L152-L156
	 */
	df_area_code_set_d();
	$o = df_o(ViewConfigI::class); /** @var ViewConfig|ViewConfigI $o */
	return $o->getViewConfig();
}