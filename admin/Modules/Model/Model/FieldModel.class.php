<?php
// 用户模型
class FieldModel extends Model {
	protected $_validate	=	array(
		array('fieldname','checkfield','字段名称已存在',0,'callback'),
	);
	protected $_auto		=	array(
		array('fieldname','strtolower',self::MODEL_BOTH,'function'),
	);
	public function checkfield() {
		$map['fieldname'] = strtolower(strtolower);
		$fields = M('Archives')->getDbFields();
		if(in_array($map['fieldname'],$fields)){
			return false;
		}
		$map['mid']			= 	$_POST['mid'];
		if(!empty($_POST['id'])) {
			$map['id']	=	array('neq',$_POST['id']);
		}
		$result	= $this->where($map)->count('id');
		if($result) {
			return false;
		}else{
			return true;
		}
	}
}
?>