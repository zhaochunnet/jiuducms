<?php
/**
 * 文档评论首页
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class CommentAction extends HomeAction {
	function _before_index(){
		$this->tempfile = 'index';
	}
	public function post(){
		$_POST['ip'] = get_client_ip();
		$_POST['time'] = time();
		$model = D('Comment');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->assign('jumpUrl',url($_POST['aid']));
			$this->success ('评论成功!');
		} else {
			//失败提示
			$this->error ('评论失败!');
		}
	}
	public function reply(){
		$_POST['ip'] = get_client_ip();
		$_POST['time'] = time();
		$model = D('Comment');
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		//保存当前数据对象
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->assign('jumpUrl',url($_POST['aid']));
			$this->success ('评论成功!');
		} else {
			//失败提示
			$this->error ('评论失败!');
		}
	}
}