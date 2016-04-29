<?php
// 版本模型
class EditionModel extends CommonModel {
	public $_validate	=	array(
		array('name','require','名称必须填写'),
		array('udid','require','标示符必须填写'),
		array('name','','名称已经存在',0,'unique',self::MODEL_BOTH),
		array('udid','','标示符已经存在',0,'unique',self::MODEL_BOTH),
	);

	public $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);
}
?>