<?php
$commonconfig = require SITE_PATH.'/Conf/common.inc.php';
$configinc = require SITE_PATH.'/Conf/config.inc.php';
$config = array(
	//系统配置
	'APP_AUTOLOAD_PATH'=>'@.TagLib',
	/* 项目设定 */
	'URL_MODEL'=>0,
    'APP_STATUS'            => 'debug',  // 应用调试模式状态 调试模式开启后有效 默认为debug 可扩展 并自动加载对应的配置文件
    'APP_FILE_CASE'         => true,   // 是否检查文件的大小写 对Windows平台有效
    'APP_AUTOLOAD_PATH'     => '',// 自动加载机制的自动搜索路径,注意搜索顺序
    'APP_TAGS_ON'           => true, // 系统标签扩展开关
	'TAGLIB_BUILD_IN'=>'cx',
	'TAGLIB_PRE_LOAD'=>'jiudu',
	//模板设置
	"TMPL_STRIP_SPACE" => false, //是否去除模板文件里面的html空格与换行
	'TMPL_L_DELIM'=>'<{',     
	'TMPL_R_DELIM'=>'}>',
	//错误信息
	'SHOW_ERROR_MSG'        => true,    // 显示错误信息
	/* Cookie设置 */
	'COOKIE_EXPIRE'         => 3600,    // Coodie有效期
	'COOKIE_DOMAIN'         => '',      // Cookie有效域名
	'COOKIE_PATH'           => '/',     // Cookie路径
	'COOKIE_PREFIX'         => 'jiuducms_',      // Cookie前缀 避免冲突
);
return array_merge($config,$configinc,$commonconfig);
?>