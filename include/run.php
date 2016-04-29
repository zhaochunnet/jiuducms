<?php
/**
 * 系统入口文件
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
define("APP_DEBUG", false);
//网站后台目录
define('INCLUDE_PATH', dirname(__FILE__));
//网站根目录
define('SITE_PATH', dirname(INCLUDE_PATH));
//JiuduCMS版本信息
define('JIUDU_VERSION','1.9.22');
define('JIUDU_VERSION_CODE',10140922);
define('JIUDU_RELEASE',1411347600);
//缓存目录
define('MODE_NAME',APP_NAME);
define('RUNTIME_PATH',SITE_PATH.'/#Runtime/');
//判断程序是否安装
if(!file_exists(SITE_PATH.'/Conf/common.inc.php')){
	header('location:../install/');
	exit;
}
require(INCLUDE_PATH."/jiuducms.php");
?>