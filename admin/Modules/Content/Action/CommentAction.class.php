<?php
/**
 * 文档评论管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class CommentAction extends AdminAction {
	public function index(){
		$model = M('Comment');
		$count = $model->where ( $map )->count ( 'id' );
		if ($count > 0) {
			import ( "Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			$voList = $model->field($model->getTableName().".id as id,title,name,email,ip,time,content,reply,".$model->getTableName().".status as status,concat(path,'-',".$model->getTableName().".id) as bpath")->join(C('DB_PREFIX').'archives ON '.C('DB_PREFIX').'archives.id ='.$model->getTableName().'.aid')->order("bpath ASC")->limit($p->firstRow . ',' . $p->listRows)->select();
		}
		$this->assign ( 'list', $voList );
		$this->assign ( 'totalCount', $count );
		$this->assign ( 'numPerPage', $p->listRows );
		$this->assign ( 'currentPage', !empty($_REQUEST[C('VAR_PAGE')])?$_REQUEST[C('VAR_PAGE')]:1);
		Cookie::set ( '_currentUrl_', __SELF__ );
		$this->display();
	}
	function reply(){
		$this->display();
	}
	public function replyDo(){
		$model = D('Comment');
		$vo = $model->field('tid,aid,path')->find($_POST['id']);
		$_POST['name'] = $_SESSION['loginUserName'];
		$_POST['email'] = $_SESSION['email'];
		$_POST['time'] = time();
		$_POST['ip'] = get_client_ip();
		$_POST['tid'] = $vo['tid'];
		$_POST['aid'] = $vo['aid'];
		$_POST['reply'] = 1;
		$_POST['status'] = 1;
		$_POST['reid'] = $_POST['id'];
		$_POST['path'] = $vo['tid'].'-'.$_POST['id'];
		unset($_POST['id']);
		if (false === $model->create ()) {
			$this->error ( $model->getError () );
		}
		$list=$model->add ();
		if ($list!==false) { //保存成功
			$this->setlog('新增','ID为'.$list.',成功');
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->setlog('新增','失败，失败原因'.$model->getError());
			$this->error ('新增失败!');
		}
    }
}