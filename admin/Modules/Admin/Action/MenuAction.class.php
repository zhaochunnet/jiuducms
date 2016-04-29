<?php
/**
 * 菜单管理模块
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class MenuAction extends AdminAction {
	private $cat;
	function _initialize() {
		parent::_initialize();
		import("Category");
		$this->cat = new Category('Menu', array('id', 'pid', 'name', 'fullname'));
	}
	function index(){
		$voList = $this->cat->getList(true,NULL,0,'id ASC');
		$this->assign('list',$voList);
		$this->display();
	}
	public function _before_add(){
		if(IS_POST && IS_AJAX){
			unset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]);
		}else{
			$voList = $this->cat->getList(true,NULL,0,'id ASC');
			$this->assign('list',$voList);
		}
	}
	public function _before_update(){
		if(IS_POST && IS_AJAX){
			unset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]);
		}else{
			$voList = $this->cat->getList(true,NULL,0,'id ASC');
			$this->assign('list',$voList);
		}
	}
}
?>