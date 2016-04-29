<?php
// 用户模型
class KeywordsModel extends CommonModel {
	public $_validate	=	array(
		array('name','require','关键字必须'),
		array('url','require','昵称必须'),
		array('name','','关键字已经存在',0,'unique',1),
	);
	public $_auto		=	array(
		array('rate','yanzheng',self::MODEL_UPDATE,'callback'),
	);
	protected function yanzheng() {
		if($_POST['rate'] > 10){
			$_POST['rate'] = 10;
		}elseif($_POST['rate'] < 1){
			$_POST['rate'] = 1;
		}
		return $_POST['rate'];
	}
}
?>