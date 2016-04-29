<?php 
/**
 * 模块安装，菜单/权限配置
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
defined('APP_NAME') or exit('Access Denied');
//添加一个菜单到后台“模块->模块列表”ID等于常量 MENUID
M("Menu")->add(array(
    //父ID
    "pid" => MENUID,
    //模块名称，也是项目名称
    "name" => "网站地图",
    //文件名称，比如SitemapAction.class.php就填写Sitemap
    "model" => "Sitemap",
    //方法名称
    "action" => "index",
    //模块标识
    "rel" => "Sitemap",
    //打开方式
    "target" =>'navTab',
    //类型
    "type" => 2,
    //状态
    "status" =>1
));

//添加其他需要加入权限认证的方法，后台进行权限认证时不通过。
//提示：比如一些删除，修改这类方法需要配合参数使用，该类不适合直接显示出来，可以把status设置为0
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Links","model"=>"Links","action"=>"add","data"=>"","type"=>1,"status"=>1,"name"=>"添加友情链接","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Links","model"=>"Links","action"=>"edit","data"=>"","type"=>1,"status"=>0,"name"=>"编辑","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Links","model"=>"Links","action"=>"delete","data"=>"","type"=>1,"status"=>0,"name"=>"删除","remark"=>"","listorder"=>0));
M("Menu")->add(array("parentid"=>$parentid,"app"=>"Links","model"=>"Links","action"=>"terms","data"=>"","type"=>1,"status"=>1,"name"=>"分类管理","remark"=>"","listorder"=>0));
?>