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
	function getItemById($idValue) {
		$this->load();
		return dfa($this->_items, $idValue);
	}

	/**
	 * 2016-05-19
	 * @param string $iso2
	 * @return bool
	 */
	function isIso2CodePresent($iso2) {return !!$this->getItemById($iso2);}

	/**
	 * 2016-05-20
	 * @return array(string => string)
	 */
	function mapFrom2To3() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_flip($this->mapFrom3To2());
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-20
	 * @return array(string => string)
	 */
	function mapFrom3To2() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			$result = [];
			foreach ($this as $c) {
				/** @var Country $c */
				$result[$c->getIso3Code()] = $c->getIso2Code();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * 2016-05-19
	 * @used-by rm_countries_ctn()
	 * Возвращает массив,
	 * в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
	 * а значениями — названия стран для заданной локали (или системной локали по умолчанию).
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
	 *	array(
	 *		'AU' => 'Австралия'
	 *		,'AT' => 'Австрия'
	 *	)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	function mapFromCodeToName($l = null) {
		$l = df_locale($l);
		if (!isset($this->{__METHOD__}[$l])) {
			$result = []; /** @var array(string => string) $result */
			$needTranslate = 'en_US' !== $l; /** @var bool $needTranslate */
			$zLocale = new \Zend_Locale($l); /** @var \Zend_Locale $zLocale */
			foreach ($this as $c) {
				/** @var Country $c */ 
				$iso2 = $c->getId(); /** @var string $iso2 */
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
	 *	array(
	 *		'AU' => 'АВСТРАЛИЯ'
	 *		,'AT' => 'АВСТРИЯ'
	 *	)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	function mapFromCodeToNameUc($l = null) {return dfc($this, function($l) {return df_strtoupper(
		$this->mapFromCodeToName($l)
	);}, [df_locale($l)]);}

	/**
	 * 2016-05-19
	 * @used-by rm_countries_ntc()
	 * Возвращает массив,
	 * в котором ключами являются
	 * названия стран для заданной локали (или системной локали по умолчанию)
	 * а значениями — 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например:
	 *	array(
	 *		'Австралия' => 'AU'
	 *		,'Австрия' => 'AT'
	 *	)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	function mapFromNameToCode($l = null) {
		$l = df_locale($l);
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
	 *	array(
	 *		'АВСТРАЛИЯ' => 'AU'
	 *		,'АВСТРИЯ' => 'AT'
	 *	)
	 * @param string|null $l [optional]
	 * @return array(string => string)
	 */
	function mapFromNameToCodeUc($l = null) {return dfc($this, function($l) {return array_flip(
		$this->mapFromCodeToNameUc($l)
	);}, [df_locale($l)]);}

	/**
	 * 2016-05-19
	 * @override
	 * @see \Magento\Directory\Model\ResourceModel\Country\Collection::_construct
	 */
	protected function _construct() {
		parent::_construct();
		$this->setModel(Country::class);
	}

	/**
	 * 2016-05-19
	 * 2016-05-20
	 * Создавать коллекцию надо обязательно через Object Manager,
	 * потому что родительский конструктор использует Dependency Injection.
	 * @used-by df_countries()
	 * @used-by \Df\Directory\Model\Country::cs()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}