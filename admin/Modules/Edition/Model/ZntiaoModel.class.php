<?php
// 版本模型
class ZntiaoModel extends CommonModel {
	public $_validate	=	array(
		array('title','require','标题必须填写'),
		array('pcurl','require','PC端URL必须填写'),
		array('phoneurl','require','手机端URL必须填写'),
		array('title','','标题已经存在',0,'unique',self::MODEL_BOTH),
		array('pcurl','','PC端URL已经存在',0,'unique',self::MODEL_BOTH),
		array('phoneurl','','手机端URL已经存在',0,'unique',self::MODEL_BOTH)
	);

	public $_auto		=	array(
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);
}
?>