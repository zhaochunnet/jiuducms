<?php
/**
 * 系统入口文件
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */

//网站当前路径
define('WAP_PATH', dirname(__FILE__));
//项目名称，不可更改
define('APP_NAME', 'Wap');
//项目路径，不可更改
define('APP_PATH',WAP_PATH.'/');

require(WAP_PATH."/../include/run.php");
?>