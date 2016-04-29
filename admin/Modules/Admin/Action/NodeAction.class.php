<?php
/**
 * 节点管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class NodeAction extends AdminAction {
	private $cat;
	function _initialize() {
		parent::_initialize();
		import("Category");
		$this->cat = new Category('Node', array('id', 'pid', 'title', 'fullname'));
	}
	function index(){
		$voList = $this->cat->getList();
		$this->assign ( 'list', $voList );
		$this->display();
	}
	public function _before_add(){
		if(IS_POST && IS_AJAX){
			if($_POST['pid'] == 0){
				$_POST['level'] = '1';
			}else{
				$vo = M('Node')->field('level')->find($_POST['pid']);
				$_POST['level'] = $vo['level']+1;
			}
		}else{
			$voList = $this->cat->getList(true,'level<3');
			$this->assign ('list',$voList );
		}
	}
	public function _before_update(){
		if(IS_POST && IS_AJAX){
			if($_POST['pid'] == 0){
				$_POST['level'] = '1';
			}else{
				$vo = M('Node')->field('level')->find($_POST['pid']);
				$_POST['level'] = $vo['level']+1;
			}
		}else{
			$Node = M('Node');
			$voList = $this->cat->getList(true,'level<3');
			$this->assign ( 'list', $voList );
		}
    }
    public function foreverdelete(){
    	$Node = M('Node');
    	$id=$_GET['id'];
    	if (isset ( $id )) {
			if(false !== M('Node')->where('id='.$id.' or pid='.$id)->delete()){
				$this->success ('删除成功！');
			}else{
				$this->error ('删除失败！');
			}
		}else{
			$this->error('非法操作');
		}
    }
}
?>