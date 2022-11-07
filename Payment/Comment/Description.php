<?php
namespace Df\Payment\Comment;
use Magento\Config\Model\Config\CommentInterface as IComment;
# 2017-09-05
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Description extends \Df\Config\Comment {
	/**
	 * 2017-09-05
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * «How to implement a dynamically generated comment for a backend configuration field?»
	 * https://mage2.pro/t/4076
	 * @override
	 * @see IComment::getCommentText()
	 * @used-by \Magento\Config\Model\Config\Structure\Element\Field::getComment():
	 *		public function getComment($currentValue = '') {
	 *			$comment = '';
	 *			if (isset($this->_data['comment']) && $this->_data['comment']) {
	 *				if (is_array($this->_data['comment'])) {
	 *					if (isset($this->_data['comment']['model'])) {
	 *						$model = $this->_commentFactory->create($this->_data['comment']['model']);
	 *						$comment = $model->getCommentText($currentValue);
	 *					}
	 *				}
	 *				else {
	 *					$comment = parent::getComment();
	 *				}
	 *			}
	 *			return $comment;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/app/code/Magento/Config/Model/Config/Structure/Element/Field.php#L106-L126
	 * @param string $v
	 */
	function getCommentText($v):string {
		$rules = $this->sibling('description_rules'); /** @var array(string => mixed)|null $rules */
		# 2019-09-27 $lengthS is empty for Dfe_ACH.
		$lengthS = ''; /** @var string $lengthS */
		# 2019-09-28 Variables are disabled for Dfe_ACH.
		$allowVariables = df_bool(dfa($rules, 'allowVariables', true)); /** @var bool $allowVariables */
		if ($maxLength = dfa($rules, 'maxLength')) { /** @var array(string => mixed)|null $maxLength */
			$title = $maxLength['title']; /** @var string $title */
			$url = $maxLength['url']; /** @var string $url */
			$v = dfa($maxLength, 'value'); /** @var int|null $v */
			$variablesS = !$allowVariables ? '' : ' (after the variables substitution)';
			$lengthS = $v
				? "<p class='df-note'>The full description length$variablesS should be not greater than <b><a href='$url' target='_blank' title='$title'>$v characters</a></b> (the description is automatically chopped to $v characters if it is longer).</p>"
				: "<p class='df-note'>The length <a href='$url' target='_blank' title='$title'>is not limited</a>.</p>"
			;
		}
		return df_cc_n(
			"<p class='df-note'>It is shown {$this->locations(dfa($rules, 'locations', []))}.</p>"
			,!$allowVariables ? '' : "<p class='df-note'>You can use <a href='https://mage2.pro/t/1834' target='_blank'>some variables</a> in the description.</p>"
			,$lengthS
		);
	}

	/**
	 * 2017-09-11
	 * @used-by self::locations()
	 * @param string $text
	 * @param array(string => string|bool) $o
	 */
	private function a($text, array $o):string {return df_tag_if($text, dfa($o, 'url'), 'a', [
		'target' => '_blank', 'title' => dfa($o, 'title'), 'href' => dfa($o, 'url')
	]);}

	/**
	 * 2017-09-11
	 * @used-by self::getCommentText()
	 * @param array(string => mixed) $l
	 */
	private function locations(array $l):string {
		$customer = dfa($l, 'customer', []); /** @var array(string => mixed)|null $customer */
		$merchant = dfa($l, 'merchant', []); /** @var array(string => mixed)|null $merchant */
		$payment = dfa($l, 'payment', []); /** @var array(string => mixed)|null $payment */
		$mTitle = dfpm_title(df_config_group($this->groupPath())->getData()['dfExtension']); /** @var string $mTitle */
		$m = !df_bool(dfa($merchant, 'shown')) ? '' :
			"to you {$this->a("in the $mTitle merchant interface", $merchant)} alongside the payment"
		; /** @var string $m */
		/** @var string $c */
		$c = !df_bool(dfa($customer, 'shown')) ? '' : $this->a("in the $mTitle customer account", $customer);
		/** @var string $p */
		$p = !df_bool(dfa($payment, 'shown')) ? '' :$this->a("on the $mTitle payment page", $payment);
		/** @var string $c */
		$cc = !($c || $p) ? '' : 'to customers' . ($c && $p ? ": $c and $p" : ' ' . ($c ?: $p));
		return df_ccc(' and ', $m, $cc);
	}
}