<?php
/**
 * 友情链接管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class FriendlinkAction extends AdminAction {
	public function index(){
		$Friendlink = M('Friendlink');
		$voList = $Friendlink->select();
		$adtype=array('文字链接','图片链接');
		$this->assign('adtype',$adtype);
		$this->assign ('list', $voList );
		$this->display();
	}
	public function _before_add(){
		if(!IS_POST){
			$value = M('Linktype')->select();
			$this->assign('date',date('Y-m-d'));
			$this->assign('value',$value);
		}
    }
	public function _before_update(){
		if(!IS_POST){
			$value = M('Linktype')->select();
			$this->assign('date',date('Y-m-d'));
			$this->assign('value',$value);
		}
    }
}