<?php
/**
 * 中转跳转管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class CatalogAction extends AdminAction {
	public function index(){
		header('Location:index.php?g=Admin&m=Archives&a=add');
	}
}