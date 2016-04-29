<?php
/**
 * 智能分流
 * @version        JiuduCMS 1.0 2012年8月29日10:41:23 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2012, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class ZntiaoAction extends AdminAction {
	function _filter(&$map){
		$where = $map;
		$map['title'] = array('like',"%".$_REQUEST['title']."%");
		$map['pcurl|phoneurl'] = array('like',"%".$_REQUEST['url']."%");
		$where['title'] = $_REQUEST['title'];
		$where['url'] = $_REQUEST['url'];
		$this->assign ('where',$where);
	}
}