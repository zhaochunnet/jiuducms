<?php
/**
 * 专题管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class TopicAction extends AdminAction {
	public function index(){
		$voList = M('Topic')->select();
		$this->assign ( 'list', $voList );
		$this->display();
	}
	//添加专题
	public function _before_add(){
		if(IS_POST && IS_AJAX){
			$_POST['tempindex'] = $_POST['index_temp'];
			$_POST['typedir'] = $_POST['typedir'].'/'.$_POST['dirname'];
		}else{
			$value = M('TopicType')->select();
			$this->assign('pathdir','{cmspath}'.C('JIUDU_TOPPICDIR'));
			$this->assign('value',$value);
		}
    }
	public function _before_update(){
		if(!IS_POST && IS_AJAX){
			$value = M('TopicType')->select();
			$this->assign('pathdir',C('JIUDU_TOPPICDIR'));
			$this->assign('value',$value);
		}
    }
}