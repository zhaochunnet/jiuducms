<?php
// 文档评论模型
class CommentModel extends CommonModel {
	public $_auto		=	array(
		array('status','1',MODEL::MODEL_INSERT),
	);
}
?>