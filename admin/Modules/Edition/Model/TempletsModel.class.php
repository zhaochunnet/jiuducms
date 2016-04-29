<?php
// 用户模型
class TempletsModel extends CommonModel {
		public $_validate = array(
		array('tempname','require','模版名称必须'),
	);
}
?>