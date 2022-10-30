<?php
/**
 * 2015-03-13
 * Отсекает последний элемент массива и возвращает «голову» (массив оставшихся элементов).
 * Похожая системная функция @see array_pop() возвращает отсечённый последний элемент.
 * Противоположная системная функция @see df_tail() отсекает первый элемент массива.
 * @used-by \Df\Config\Comment::groupPath()
 * @used-by \Df\Config\Source::sibling()
 * @used-by \Mineralair\Core\Controller\Modal\Index::execute()
 * @param mixed[] $a
 * @return mixed[]|string[]
 */
function df_head(array $a):array {return array_slice($a, 0, -1);}

/**
 * 2021-11-30
 * @used-by df_bt()
 * @used-by df_product_images_additional()
 * @param array $a
 * @param int $offset
 * @param int|null $length [optional]
 */
function df_slice(array $a, $offset, $length = null):array {return array_slice(
	/**
	 * 2021-10-05
	 * @uses array_slice() returns an empty array if `$limit` is `0`, and returns all elements if `$limit` is `null`,
	 * so I convert `0` and other empty values to `null`;
	 */
	$a, $offset, df_etn($length)
);}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp).
 * Обратите внимание, что если исходный массив содержит меньше 2 элементов, то функция вернёт пустой массив.
 * @see df_first()
 * @see df_last()
 * @used-by \Doormall\Shipping\Partner\Entity::locations()
 * @param mixed[] $a
 * @return mixed[]|string[]
 */
function df_tail(array $a):array {return array_slice($a, 1);}