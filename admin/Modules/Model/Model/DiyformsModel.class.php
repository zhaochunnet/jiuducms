<?php
// 自定义表单模型
class DiyformsModel extends CommonModel {
	protected $_validate	=	array(
		array('addtable','','附加表已经存在！',0,'unique',1),
	);
	protected $_auto		=	array(
		array('status','1',MODEL::MODEL_INSERT),
		array('udid','strtolower',self::MODEL_BOTH,'function'),
		array('table','strtolower',self::MODEL_BOTH,'function'),
	);
}
?>