<?php
namespace Df\Config;
use Magento\Config\Model\Config\CommentInterface as IComment;
# 2017-10-15
/** @see \Df\Payment\Comment\Description */
abstract class Comment implements IComment {
	/**
	 * 2017-10-15
	 * @used-by \Df\Payment\Comment\Description::getCommentText()
	 * @return string|null
	 */
	final protected function sibling(string $k) {return df_cfg("{$this->groupPath()}/$k", df_scope());}

	/**
	 * 2017-09-11
	 * @used-by self::sibling()
	 * @used-by \Df\Payment\Comment\Description::locations()
	 */
	final protected function groupPath():string {return dfc($this, function() {return df_cc_path(df_head(df_explode_path(
		df_config_field_path()
	)));});}
}