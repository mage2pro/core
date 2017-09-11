<?php
namespace Df\Payment\Comment;
use Magento\Config\Model\Config\CommentInterface as IComment;
// 2017-09-05
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Description implements IComment {
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
	 * @return string
	 */
	function getCommentText($v) {
		/** @var string $groupPath */
		$groupPath = df_cc_path(df_head(df_explode_path(df_config_field()->getPath())));
		$m = df_config_group($groupPath)->getData()['dfExtension'];  /** @var string $m */
		$rules = df_cfg("$groupPath/description_rules"); /** @var array(string => mixed)|null $rules */
		$maxLength = dfa($rules, 'maxLength'); /** @var array(string => mixed)|null $maxLength */
		$mTitle = dfpm_title($m); /** @var string $mTitle */
		$title = $maxLength['title']; /** @var string $title */
		$url = $maxLength['url']; /** @var string $url */
		$v = dfa($maxLength, 'value'); /** @var int|null $v */
		/** @var string $to */
		$to = "to you in the $mTitle merchant interface alongside the payment";
		if (dfa($rules, 'shownToTheCustomers')) {
			$to = "to the customers on the $mTitle payment page, and $to";
		}
		return
			"<p class='df-note'>It will be displayed $to.</p>
<p class='df-note'>You can use <a href='https://mage2.pro/t/1834' target='_blank'>some variables</a> in the description.</p>" . ($v
			? "<p class='df-note'>The full description length (after the variables substitution) should be not greater than <b><a href='$url' target='_blank' title='$title'>$v characters</a></b> (the description will be automatically chopped to $v characters if it is longer).</p>"
			: "<p class='df-note'>The length <a href='$url' target='_blank' title='$title'>is not limited</a>.</p>"
		);
	}
}