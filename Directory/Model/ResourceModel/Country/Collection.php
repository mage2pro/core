<?php
namespace Df\Directory\Model\ResourceModel\Country;
use Df\Directory\Model\Country as C;
use Zend_Locale as zL;
# 2016-05-19
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Collection extends \Magento\Directory\Model\ResourceModel\Country\Collection {
	/**
	 * 2016-05-19
	 * Родительский метод зачем-то делает цикл про элементам коллекции.
	 * А мы, по сути, берём реализацию из @see \Magento\Framework\Data\Collection::getItemById()
	 * @override
	 * @see \Magento\Directory\Model\ResourceModel\Country\Collection::getItemById()
	 * @param string $id
	 * @return C|null
	 */
	function getItemById($id) {$this->load(); return dfa($this->_items, $id);}

	/**
	 * 2016-05-20
	 * @used-by df_country_2_to_3()
	 * @return array(string => string)
	 */
	final function mapFrom2To3():array {return dfc($this, function() {return array_flip($this->mapFrom3To2());});}

	/**
	 * 2016-05-20
	 * @used-by df_country_3_to_2()
	 * @used-by self::mapFrom2To3()
	 * @return array(string => string)
	 */
	final function mapFrom3To2():array {return dfc($this, function() {return df_map_r($this, function(C $c) {return [
		$c->getIso3Code(), $c->getIso2Code()
	];});});}

	/**
	 * 2016-05-19
	 * @used-by df_countries_ctn()
	 * Возвращает массив, в котором ключами являются 2-буквенные коды стран по стандарту ISO 3166-1 alpha-2,
	 * а значениями — названия стран для заданной локали (или системной локали по умолчанию).
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * Например: ['AU' => 'Австралия', 'AT' => 'Австрия']
	 * @return array(string => string)
	 */
	final function mapFromCodeToName(string $l = ''):array {return dfc($this, function($l) {
		$needTranslate = 'en_US' !== $l; /** @var bool $needTranslate */
		$zL = new zL($l); /** @var zL $zL */
		return df_sort(df_map_r($this, function(C $c) use($needTranslate, $zL) {return [
			$iso2 = $c->getId() /** @var string $iso2 */
			,!$needTranslate ? $c->getName() : (zL::getTranslation($iso2, 'country', $zL) ?: $c->getName())];
		}), null, false, $l);
	}, [df_locale($l)]);}

	/**
	 * 2016-05-19
	 * @override
	 * @see \Magento\Directory\Model\ResourceModel\Country\Collection::_construct
	 */
	final protected function _construct():void {
		parent::_construct();
		$this->setModel(C::class);
	}

	/**
	 * 2016-05-19
	 * 2016-05-20
	 * Создавать коллекцию надо обязательно через Object Manager,
	 * потому что родительский конструктор использует Dependency Injection.
	 * @used-by df_countries()
	 * @used-by \Df\Directory\Model\Country::cs()
	 */
	final static function s():self {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}