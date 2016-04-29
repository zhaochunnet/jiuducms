<?php
return $config = array(
	//参数设置
	'SITENAME'							=> '九度网站内容管理系统 (JiuduCMS)',
	'SITE_VERSION_URL'					=> 'http://www.jiuducms.com',
	'SITEURL'							=> 'http://www.jiuducms.com',
	'JIUDU_DOCUMENT_URL'				=> 'http://document.jiuducms.com/',
	'JIUDU_SERVICE_URL'					=> 'http://api.jiuducms.com/',
	'JIUDU_SERVICE_URL_NEWS' 			=> 'http://api.jiuducms.com/index.php?c=Accept&a=news&v='.JIUDU_VERSION_CODE,
	'JIUDU_SERVICE_URL_VERSION' 		=> 'http://api.jiuducms.com/index.php?c=Accept&a=get_version&v='.JIUDU_VERSION_CODE,
	'JIUDU_SERVICE_URL_FEEDBACK' 		=> 'http://api.jiuducms.com/index.php?c=Accept&a=feedback&v='.JIUDU_VERSION_CODE,
	'JIUDU_SERVICE_URL_GET_AUTHORIZE'	=> 'http://api.jiuducms.com/index.php?c=Accept&a=get_authorize&v='.JIUDU_VERSION_CODE,
	'JIUDU_SERVICE_URL_SET_AUTHORIZE' 	=> 'http://api.jiuducms.com/index.php?c=Accept&a=set_authorize&v='.JIUDU_VERSION_CODE,
	'BBSOURL'							=> 'http://www.jiuducms.com/bbs',
	//系统配置
	'URL_MODEL'=>0,
	'AUTH_PWD_ENCODER'=>'md5',	// 用户认证密码加密方式
	'VAR_PAGE'=>'pageNum',
);
?>