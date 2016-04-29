<?php
// 用户模型
class ArctypeModel extends CommonModel {
	protected $_validate	=	array(
		array('typename','checkTypename','栏目名称已经存在',0,'callback'),
		array('dirname','checkDirname','文件保存目录已经存在',0,'callback'),
	);
	
	public function checkTypename() {
		$map['typename'] =	$_POST['typename'];
		$map['pid']		=	isset($_POST['pid']) ? $_POST['pid']:0;
		if(!empty($_POST['id'])) {
			$map['id']	=	array('neq',$_POST['id']);
		}
		$result	=	$this->where($map)->count('id');
		if($result) {
			return false;
		}else{
			return true;
		}
	}
	public function checkDirname() {
		$map['dirname'] =	$_POST['dirname'];
		$map['pid']		=	isset($_POST['pid']) ? $_POST['pid']:0;
		if(!empty($_POST['id'])) {
			$map['id']	=	array('neq',$_POST['id']);
		}
		$result	=	$this->where($map)->count('id');
		if($result) {
			return false;
		}else{
			return true;
		}
	}
}
?>