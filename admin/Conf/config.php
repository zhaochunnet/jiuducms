<?php
$commonconfig = require SITE_PATH.'/Conf/common.inc.php';
$configinc = require SITE_PATH.'/Conf/config.inc.php';
$config = array(
	//系统配置
	'SESSION_AUTO_START'=>true,
	'APP_AUTOLOAD_PATH'=>'@.TagLib',
	/* 项目设定 */
	'URL_MODEL'=>0,
    'APP_STATUS'            => 'debug',  // 应用调试模式状态 调试模式开启后有效 默认为debug 可扩展 并自动加载对应的配置文件
    'APP_FILE_CASE'         => true,   // 是否检查文件的大小写 对Windows平台有效
    'APP_AUTOLOAD_PATH'     => '',// 自动加载机制的自动搜索路径,注意搜索顺序
    'APP_TAGS_ON'           => true, // 系统标签扩展开关
    'APP_GROUP_LIST'        => 'Admin,Ad,Content,Filemanager,Link,Model,Templets',      // 项目分组设定,多个组之间用逗号分隔,例如'Home,Admin'
    'DEFAULT_GROUP'			=> 'Admin',
    'APP_GROUP_MODE'        =>  1,  // 分组模式 0 普通分组 1 独立分组
    'APP_GROUP_PATH'        =>  'Modules', // 分组目录 独立分组模式下面有效
    'ACTION_SUFFIX'         =>  '', // 操作方法后缀
	'TAGLIB_BUILD_IN'=>'cx',
	'TAGLIB_PRE_LOAD'=>'jiudu',
	//模板设置
	"TMPL_STRIP_SPACE" => false, //是否去除模板文件里面的html空格与换行
	'TMPL_L_DELIM'=>'<{',     
	'TMPL_R_DELIM'=>'}>',
	//RBAC用户权限配置
	'USER_AUTH_ON'=>true,
	'USER_AUTH_TYPE'=>2,		// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'=>'authId',	// 用户认证SESSION标记
	'ADMIN_AUTH_KEY'=>'administrator',
	'USER_AUTH_GATEWAY'=>'?m=Login&a=login',	// 默认认证网关
	'NOT_AUTH_MODULE'=>'',		// 默认无需认证模块
	'NOT_AUTH_ACTION'=>'',		// 默认无需认证操作
	'GUEST_AUTH_ON'=>false,    // 是否开启游客授权访问
	'GUEST_AUTH_ID'=>0,     // 游客的用户ID
	'USER_AUTH_MODEL'=>'User',	// 默认验证数据表模型
	'RBAC_ROLE_TABLE'=> 'role', //角色表名称
	'RBAC_ACCESS_TABLE'=> 'access', //权限表名称
	'RBAC_NODE_TABLE'=> 'node', //节点表名称
	//错误信息
	'SHOW_ERROR_MSG'        => true,    // 显示错误信息
	/* Cookie设置 */
	'COOKIE_EXPIRE'         => 3600,    // Coodie有效期
	'COOKIE_DOMAIN'         => '',      // Cookie有效域名
	'COOKIE_PATH'           => '/',     // Cookie路径
	'COOKIE_PREFIX'         => 'jiuducms_',      // Cookie前缀 避免冲突
	'TEMPLETS_CHECK_TYPE'=>'Web'
);
return array_merge($config,$configinc,$commonconfig);
?>