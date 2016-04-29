<?php
// 用户模型
class UserModel extends CommonModel {
	public $_validate	=	array(
		array('account','/^[a-z]\w{3,}$/i','帐号格式错误'),
		array('password','require','密码必须'),
		array('nickname','require','昵称必须'),
		array('repassword','require','确认密码必须'),
		array('repassword','password','确认密码不一致',0,'confirm'),
		array('account','','帐号已经存在',0,'unique',self::MODEL_INSERT),
	);

	public $_auto		=	array(
		array('password','pwdHash',self::MODEL_INSERT,'callback'),
		array('create_time','time',self::MODEL_INSERT,'function'),
		array('update_time','time',self::MODEL_UPDATE,'function'),
		);

	protected function pwdHash() {
		if(isset($_POST['password'])) {
			return md5($_POST['password'].$_POST['verify']);
		}else{
			return false;
		}
	}
}
?>