<?php
use Magento\Framework\DB\Select;

/**
 * 2015-04-14
 * 2019-01-12 It is never used.
 * @param string $t
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return array(array(string => string))
 */
function df_fetch_all($t, $cCompare = null, $values = null) {
	$s = df_db_from($t); /** @var Select $s */
	if (!is_null($values)) {
		$s->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	return df_conn()->fetchAssoc($s);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int()
 * @used-by \Df\Customer\AddAttribute\Customer::text()
 * @used-by \Inkifi\Mediaclip\API\Entity\Order\Item::mProduct()
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col($t, $cSelect, $cCompare = null, $values = null, $distinct = false) {
	$s = df_db_from($t, $cSelect); /** @var Select $s */
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$s->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	$s->distinct($distinct);
	return df_conn()->fetchCol($s, $cSelect);
}

/**
 * 2015-04-13
 * @used-by df_fetch_col_int_unique()
 * @used-by \Mangoit\MediaclipHub\Model\ResourceModel\Modules::idByCode()
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int($t, $cSelect, $cCompare = null, $values = null, $distinct = false) {return
	/** намеренно не используем @see df_int() ради ускорения */
	df_int_simple(df_fetch_col($t, $cSelect, $cCompare, $values, $distinct))
;}

/**
 * 2015-04-13
 * 2019-01-12 It is never used.
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int_unique($t, $cSelect, $cCompare = null, $values = null) {return df_fetch_col_int(
	$t, $cSelect, $cCompare, $values, $distinct = true
);}

/**
 * 2016-01-26
 * «How to get the maximum value of a database table's column programmatically»: https://mage2.pro/t/557
 * @used-by df_customer_att_next()
 * @used-by \Dfe\SalesSequence\Config\Next\Backend::updateNextNumber()
 * @param string $t
 * @param string $cSelect
 * @param string|null $cCompare [optional]
 * @param int|string|int[]|string[]|null $values [optional]
 * @return int|float
 */
function df_fetch_col_max($t, $cSelect, $cCompare = null, $values = null) {
	$s = df_db_from($t, "MAX($cSelect)"); /** @var Select $s */
	if (!is_null($values)) {
		if (!$cCompare) {
			$cCompare = $cSelect;
		}
		$s->where($cCompare . ' ' . df_sql_predicate_simple($values), $values);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return df_conn()->fetchOne($s, $cSelect) ?: 0;
}

/**
 * 2015-11-03
 * @used-by df_fetch_one_int()
 * @used-by \Dfe\CheckoutCom\Handler\Charge::paymentByTxnId()
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @used-by \Inkifi\Consolidation\Processor::mcid()
 * @param string $t
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return string|null|array(string => mixed)
 */
function df_fetch_one($t, $cSelect, $cCompare) {
	$s = df_db_from($t, $cSelect); /** @var Select $s */
	foreach ($cCompare as $column => $v) {/** @var string $column */ /** @var string $v */
		$s->where('? = ' . $column, $v);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return '*' !== $cSelect ? df_ftn(df_conn()->fetchOne($s)) : df_eta(df_conn()->fetchRow(
		$s, [], \Zend_Db::FETCH_ASSOC
	));
}

/**
 * 2015-11-03
 * @used-by \Dfe\Markdown\DbRecord::__construct()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @param string $t
 * @param string $cSelect
 * @param array(string => string) $cCompare
 * @return int|null
 */
function df_fetch_one_int($t, $cSelect, $cCompare) {return
	!($r = df_fetch_one($t, $cSelect, $cCompare)) ? null : df_int($r)
;}