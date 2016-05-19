<?php
namespace Df\Directory\Model\ResourceModel\Country;
use Df\Directory\Model\Country;
class Collection extends \Magento\Directory\Model\ResourceModel\Country\Collection {
	/**
	 * 2016-05-19
	 * Родительский метод зачем-то делает цикл про элементам коллекции.
	 * А мы, по сути, берём реализацию из @see \Magento\Framework\Data\Collection::getItemById()
	 * @override
	 * @see \Magento\Directory\Model\ResourceModel\Country\Collection::getItemById()
	 * @param string $idValue
	 * @return Country|null
	 */
	public function getItemById($idValue) {
		$this->load();
		return dfa($this->_items, $idValue);
	}

	/**
	 * 2016-05-19
	 * @param string $iso2
	 * @return bool
	 */
	public function isIso2CodePresent($iso2) {return !!$this->getItemById($iso2);}

	/**
	 * 2016-05-19
	 * @used-by rm_countries_ctn()
	 * Возвращает массив,
	 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
	 * а значениями — названия стран для заданной локали (или системной локали по умолчанию).
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'AU' => 'Австралия'
	 		,'AT' => 'Австрия'
		)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	public function mapFromCodeToName($l = null) {
		$l = $l ?: df_locale();
		if (!isset($this->{__METHOD__}[$l])) {
			/** @var array(string => string) $result */
			$result = [];
			/** @var bool $needTranslate */
			$needTranslate = 'en_US' !== $l;
			/** @var \Zend_Locale $zLocale */
			$zLocale = new \Zend_Locale($l);
			foreach ($this as $c) {
				/** @var Country $c */
				/** @var string $iso2 */
				$iso2 = $c->getId();
				$result[$iso2] = !$needTranslate ? $c->getName() : (
					\Zend_Locale::getTranslation($iso2, 'country', $zLocale) ?: $c->getName()
				);
			}
			$this->{__METHOD__}[$l] = $result;
		}
		return $this->{__METHOD__}[$l];
	}

	/**
	 * 2016-05-19
	 * @used-by rm_countries_ctn_uc()
	 * Возвращает массив,
	 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
	 * а значениями — названия стран в верхнем регистре для заданной локали
	 * (или системной локали по умолчанию).
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'AU' => 'АВСТРАЛИЯ'
	 		,'AT' => 'АВСТРИЯ'
		)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	public function mapFromCodeToNameUc($l = null) {
		$l = $l ?: df_locale();
		if (!isset($this->{__METHOD__}[$l])) {
			$this->{__METHOD__}[$l] = df_strtoupper($this->mapFromCodeToName($l));
		}
		return $this->{__METHOD__}[$l];
	}

	/**
	 * 2016-05-19
	 * @used-by rm_countries_ntc()
	 * Возвращает массив,
	 * в котором ключами являются
	 * названия стран для заданной локали (или системной локали по умолчанию)
	 * а значениями — 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'Австралия' => 'AU'
	 		,'Австрия' => 'AT'
		)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	public function mapFromNameToCode($l = null) {
		$l = $l ?: df_locale();
		if (!isset($this->{__METHOD__}[$l])) {
			/** @var \Magento\Framework\Stdlib\ArrayUtils $au */
			$au = df_o(\Magento\Framework\Stdlib\ArrayUtils::class);
			$this->{__METHOD__}[$l] =
				$au->ksortMultibyte(array_flip($this->mapFromCodeToName($l)), $l)
			;
		}
		return $this->{__METHOD__}[$l];
	}

	/**
	 * 2016-05-19
	 * @used-by rm_countries_ntc_uc()
	 * Возвращает массив,
	 * в котором ключами являются
	 * названия стран в верхнем регистре для заданной локали (или системной локали по умолчанию)
	 * а значениями — 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
		array(
			'АВСТРАЛИЯ' => 'AU'
	 		,'АВСТРИЯ' => 'AT'
		)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	public function mapFromNameToCodeUc($l = null) {
		$l = $l ?: df_locale();
		if (!isset($this->{__METHOD__}[$l])) {
			$this->{__METHOD__}[$l] = array_flip($this->mapFromCodeToNameUc($l));
		}
		return $this->{__METHOD__}[$l];
	}

	/**
	 * 2016-05-19
	 * @override
	 * @see \Magento\Directory\Model\ResourceModel\Country\Collection::_construct
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->setModel(Country::class);
	}

	/**
	 * 2016-05-19
	 * @used-by df_countries()
	 * @used-by \Df\Directory\Model\Country::cs()
	 * @return $this
	 */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}