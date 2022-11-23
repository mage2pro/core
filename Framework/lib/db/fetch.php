<?php
use Magento\Framework\DB\Select as S;

/**
 * 2019-11-15
 * @param string|string[] $cols [optional]
 * @param string|null|array(string => mixed) $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @return array(array(string => string))
 */
function df_fetch(string $t, $cols = '*', $compareK = null, $compareV = null):array {
	$s = df_db_from($t, $cols); /** @var S $s */
	if (is_array($compareK)) {
		foreach ($compareK as $c => $v) {/** @var string $c */ /** @var string $v */
			$s->where('? = ' . $c, $v);
		}
	}
	elseif (!is_null($compareV)) {
		$s->where($compareK . ' ' . df_sql_predicate_simple($compareV), $compareV);
	}
	return df_conn()->fetchAll($s);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int()
 * @used-by \Df\Customer\AddAttribute\Customer::p()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @param string|null|array(string => mixed) $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @return int[]|string[]
 */
function df_fetch_col(string $t, string $col, $compareK = null, $compareV = null, bool $distinct = false):array {
	$s = df_db_from($t, $col); /** @var S $s */
	if (is_array($compareK)) {
		foreach ($compareK as $c => $v) {/** @var string $c */ /** @var string $v */
			$s->where('? = ' . $c, $v);
		}
	}
	elseif (!is_null($compareV)) {
		$s->where(($compareK ?: $col) . ' ' . df_sql_predicate_simple($compareV), $compareV);
	}
	$s->distinct($distinct);
	return df_conn()->fetchCol($s, $col);
}

/**
 * 2015-04-13
 * @used-by df_att_code2id()
 * @used-by df_fetch_col_int_unique()
 * @used-by \Mangoit\MediaclipHub\Model\ResourceModel\Modules::idByCode()
 * @param string|null|array(string => mixed) $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int(string $t, string $cSelect, $compareK = null, $compareV = null, bool $distinct = false):array {return
	/** I do not use @see df_int() to make the function faster */
	df_int_simple(df_fetch_col($t, $cSelect, $compareK, $compareV, $distinct))
;}

/**
 * 2015-04-13
 * 2019-01-12 @deprecated It is never used.
 * @param string|null|array(string => mixed) $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int_unique(string $t, string $cSelect, $compareK = null, $compareV = null):array {return df_fetch_col_int(
	$t, $cSelect, $compareK, $compareV, true
);}

/**
 * 2016-01-26 «How to get the maximum value of a database table's column programmatically»: https://mage2.pro/t/557
 * @used-by df_customer_att_pos_next()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::updateNextNumber()
 * @param string $t
 * @param string $col
 * @param string|null|array(string => mixed) $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @return int|float
 */
function df_fetch_col_max($t, $col, $compareK = null, $compareV = null) {
	$s = df_db_from($t, "MAX($col)"); /** @var S $s */
	if (is_array($compareK)) {
		foreach ($compareK as $c => $v) {/** @var string $c */ /** @var string $v */
			$s->where('? = ' . $c, $v);
		}
	}
	elseif (!is_null($compareV)) {
		$s->where(($compareK ?: $col) . ' ' . df_sql_predicate_simple($compareV), $compareV);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return df_conn()->fetchOne($s, $col) ?: 0;
}

/**
 * 2015-11-03
 * @used-by df_fetch_one_int()
 * @used-by \Alignet\Paymecheckout\Model\Client\Classic\Order\DataGetter::userCodePayme() (innomuebles.com, https://github.com/innomuebles/m2/issues/17)
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @used-by \Inkifi\Consolidation\Processor::mcid()
 * @param string $t
 * @param string|string[] $cols
 * @param array(string => string) $compare
 * @return string|null|array(string => mixed)
 */
function df_fetch_one($t, $cols, $compare) {
	$s = df_db_from($t, $cols); /** @var S $s */
	foreach ($compare as $c => $v) {/** @var string $c */ /** @var string $v */
		$s->where('? = ' . $c, $v);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return '*' !== $cols ? df_ftn(df_conn()->fetchOne($s)) : df_eta(df_conn()->fetchRow($s, [], \Zend_Db::FETCH_ASSOC));
}

/**
 * 2015-11-03
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @param string $t
 * @param string $cSelect
 * @param array(string => string) $compare
 * @return int|null
 */
function df_fetch_one_int($t, $cSelect, $compare) {return !($r = df_fetch_one($t, $cSelect, $compare)) ? null : df_int($r);}