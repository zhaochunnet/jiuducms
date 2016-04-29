<?php
/**
 * 后台用户模块
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class UserAction extends AdminAction {
	function _filter(&$map){
		$map['account'] = array('like',"%".$_POST['account']."%");
	}
	function _before_index(){
		$Role = M('Role');
		$value = $Role->select();
		foreach ($value as $v){
			$type[$v['id']] = $v['name'];
		}
		$this->assign('role',$type);
	}
	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
            $this->error( '用户名必须是字母，且5位以上！');
        }
		$User = M("User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }
    // 插入数据
	public function _before_add(){
		if(IS_POST && IS_AJAX){
			import('String');
			$_POST['verify'] = String::randString(6,5);
		}else{
			$Role = M('role');
			$value = $Role->select();
			$this->assign('role',$value);
		}
    }
    public function _before_foreverdelete(){
    	$id=$_GET['id'];
    	if($id==1){
    		$this->error('超级管理员不能删除');
    	}
    	$Node = M('role_user');
    	if (isset ( $id )) {
			if (!$Node->where ('user_id ='.$id)->delete ()) {
				$this->error ('删除失败！');
			}
		} else {
			$this->error ( '非法操作' );
		}
    }
	//修改用户信息
	public function _before_update(){
		if(!IS_POST && !IS_AJAX){
			$Role = M('role');
			$value = $Role->select();
			$this->assign('role',$value);
		}
    }
    //重置密码
    public function password(){
    	if(IS_POST && IS_AJAX){
	    	$id  =  $_POST['id'];
	        $password = $_POST['password'];
	        if(''== trim($password)) {
	        	$this->error('密码不能为空！');
	        }
	        $User = M('User');
	        $verify = String::randString(6,5);
	        $User->verify = $verify;
			$User->password	=	md5($password.$verify);
			$User->id			=	$id;
			$result	=	$User->save();
	        if(false !== $result) {
	            $this->success("密码修改为$password");
	        }else {
	        	$this->error('重置密码失败！');
	        }
    	}else{
    		$this->display();
    	}
    }
    public function tongji(){
    	$user = M('User')->field('id,nickname')->select();
    	$model = M('Archives');
    	foreach ($user as $k=>$v){
    		$info[$k]['id'] = $v['id'];
    		$info[$k]['nickname'] = $v['nickname'];
    		$info[$k]['numall'] = $model->where('uid='.$v['id'])->count('id');
    		$info[$k]['clickall'] = $model->where('uid='.$v['id'])->sum('click')+0;
    		$time = time()-(2592000*3);
    		$info[$k]['num_j'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->count('id');
    		$info[$k]['click_j'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->sum('click')+0;
    		$time = time()-2592000;
    		$info[$k]['num_m'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->count('id');
    		$info[$k]['click_m'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->sum('click')+0;
    		$time = time()-(86400*7);
    		$info[$k]['num_w'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->count('id');
    		$info[$k]['click_w'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->sum('click')+0;
    		$time = time()-(86400*3);
    		$info[$k]['num_s'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->count('id');
    		$info[$k]['click_s'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->sum('click')+0;
    		$time = time()-86400;
    		$info[$k]['num_d'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->count('id');
    		$info[$k]['click_d'] = $model->where('uid='.$v['id'].' AND pubdate >'.$time)->sum('click')+0;
    	}
    	$this->assign('list',$info);
    	$this->display();
    }
}
?>