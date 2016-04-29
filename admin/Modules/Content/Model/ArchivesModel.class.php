<?php
// 用户模型
class ArchivesModel extends CommonModel {
	public $_validate	=	array(
		array('typeid','require','栏目必须选择！'),
		array('title','require','文章标题必须填写！'),
	);
	public $_auto		=	array(
		array('status','1',MODEL::MODEL_INSERT),
	);
}
?>