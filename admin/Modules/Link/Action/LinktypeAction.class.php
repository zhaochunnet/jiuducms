<?php
/**
 * 友情链接分类管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class LinktypeAction extends AdminAction {
	function index(){
		$voList = M('Linktype')->select();
		$this->assign ( 'list', $voList );
		$this->display();
	}
}