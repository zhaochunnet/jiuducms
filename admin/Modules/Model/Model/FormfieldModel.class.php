<?php
// 用户模型
class FormfieldModel extends Model {
	protected $_validate	=	array(
		array('fieldname','','字段名称已存在！',0,'unique',1),
	);
	protected $_auto		=	array(
		array('fieldname','strtolower',self::MODEL_BOTH,'function'),
	);
}
?>