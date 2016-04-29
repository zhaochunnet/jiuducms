<?php
// 用户模型
class SysconfigModel extends Model {
	protected $_validate	=	array(
		array('varname','','变量名已经存在！',0,'unique',1), 
	);
	protected $_auto		=	array(
		array('varname','strtoupper',1,'function'),
		);
}
?>