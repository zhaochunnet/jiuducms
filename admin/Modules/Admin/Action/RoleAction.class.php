<?php
/**
 * 用户分组模块
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class RoleAction extends AdminAction {
	//查看权限
	public function privilege(){
		if(IS_POST && IS_AJAX){
			$where['role_id'] = $_POST['role_id'];
			$jd = $_POST['jd'];
			if(M('access')->where($where)->delete() === false){
				$this->error ('修改权限失败!');
			}
			foreach ($jd as $v1){
				foreach ($v1 as $v){
					$val = explode(',', $v);
					$data[] = array('role_id'=>$_POST['role_id'],'g'=>$val[0],'m'=>$val[1],'a'=>$val[2],'node_id'=>$val[3]);
				}
			}
			if(M('access')->addAll($data)){
				$this->success ('修改权限成功!');
			}else{
				$this->error ('修改权限失败!');
			}
		}else{
			$List = M('access')->field("node_id")->where('role_id='.$_GET['id'])->select();
			foreach ($List as $k=>$v){
				$acc[$v['node_id']] = true;
			}
			$model = M('Node');
			$where['status'] = 1;
			$where['pid'] = 0;
			$List = $model->field("id,title,name")->where($where)->order('sort ASC,id ASC')->select();
			foreach ($List as $k=>$v){
				$where['pid'] = $v['id'];
				$jiedian = $model->field("id,name,title")->where($where)->order('sort ASC,id ASC')->select();
				foreach ($jiedian as $k1 => $v1){
					$where['pid'] = $v1['id'];
					$jiedian[$k1]['a'] = $model->field("id,name,title")->where($where)->order('sort ASC,id ASC')->select();
					foreach ($jiedian[$k1]['a'] as $k2=>$v2){
						$jiedian[$k1]['a'][$k2]['type'] = isset($acc[$v2['id']]) ? 1 :0;
						$jiedian[$k1]['type'] = $jiedian[$k1]['a'][0]['type'] ? 1 : 0;
					}
				}
				$List[$k]['m'] = $jiedian;
			}
			$this->assign('list',$List);
			$this->assign('role_id',$_GET['id']);
			$this->display();
		}
	}
    //修改用户组
	public function _before_update(){
		if(!isset($_REQUEST['id'])){
    		$this->error('非法操作');
    	}
    	if($_REQUEST['id'] == 1){
    		$this->error('管理员组不能修改');
    	}
    }
    //删除用户组
    public function _before_foreverdelete(){
    	if(!isset($_GET['id'])){
    		$this->error('非法操作');
    	}
    	if($_GET['id'] == 1){
    		$this->error('管理员组不能删除');
    	}
    }
}
?>