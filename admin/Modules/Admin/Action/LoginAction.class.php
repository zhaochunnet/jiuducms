<?php
/**
 * 后台系统登录
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class LoginAction extends Action {
	public function index(){
		redirect(U('Login/login'));
	}
	// 用户登录页面
	public function login() {
		if(IS_POST){
			if(empty($_POST['account'])){
				$this->error('帐号错误！');
			}elseif (empty($_POST['password'])){
				$this->error('密码必须！');
			}elseif (empty($_POST['verify'])){
				$this->error('验证码必须！');
			}
			//生成认证条件
			$map            =   array();
			// 支持使用绑定帐号登录
			$map['account']	= $_POST['account'];
			$map["status"]	=	array('gt',0);
			if($_SESSION['verify'] != md5($_POST['verify'])) {
				$this->error('验证码错误！');
			}
			$authInfo = RBAC::authenticate($map);
			//使用用户名、密码和状态的方式进行认证
			if(!$authInfo) {
				$this->error('帐号不存在或已禁用！');
			}else {
				if($authInfo['password'] != md5($_POST['password'].$authInfo['verify'])){
					$this->setloginlog(0);
					$this->error('密码错误！');
				}
				$this->setloginlog(1);
				$_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
				$_SESSION['email']	=	$authInfo['email'];
				$_SESSION['loginUserName']		=	$authInfo['nickname'];
				$_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
				$_SESSION['login_count']	=	$authInfo['login_count'];
				if($authInfo['role_id']==1){
					$_SESSION[C('ADMIN_AUTH_KEY')] = true;
				}
				//保存登录信息
				$data = array('id'=>$authInfo['id'],'last_login_time'=>time(),'login_count'=>array('exp','login_count+1'),'last_login_ip'=>get_client_ip());
				M(C('USER_AUTH_MODEL'))->save($data);
				// 缓存访问权限
				RBAC::saveAccessList();
				$this->success('登录成功！');
			}
		}else{
			if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
				$this->display();
			}else{
				redirect(U('Index/index'));
			}
		}
	}
	// 用户登出
    public function logout(){
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION);
			session_destroy();
            $this->assign("jumpUrl",U('Login/login'));
            $this->success('登出成功！');
        }else {
            $this->error('已经登出！');
        }
    }
    //验证码
	public function verify(){
		$type	 =	 isset($_GET['type'])?$_GET['type']:'gif';
        import("Image");
        Image::buildImageVerify(4,1,$type);
    }
    private function setloginlog($status=1){
    	if(C('JIUDU_LOGINLOG')){
    		$data['username'] = $_POST['account'];
    		$data['logintime'] = time();
    		$data['loginip'] = get_client_ip();
    		$data['status'] = $status;
    		$data['password']  = substr($_POST['password'],0,3).'*****'.substr($_POST['password'],-2,2);
    		return M('loginlog')->add($data);
    	}else{
    		return C('JIUDU_LOGINLOG');
    	}
    }
}
?>