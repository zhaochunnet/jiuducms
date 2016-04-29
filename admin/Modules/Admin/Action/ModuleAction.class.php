<?php
/**
 * 模块管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class ModuleAction extends AdminAction {
	//模块列表
	public function index(){
		$dirs = glob(APP_PATH . C("APP_GROUP_PATH") . DIRECTORY_SEPARATOR . '*');
		foreach ($dirs as $k=>$path) {
			if (is_dir($path)) {
				$configfile = $path.DIRECTORY_SEPARATOR.'Install'.DIRECTORY_SEPARATOR.'Config.inc.php';
				$path = basename($path);
				if(is_file($configfile)){
					include $configfile;
					$dirs_arr[$path]['modulename'] = $modulename;
					$dirs_arr[$path]['version'] = $version;
					$dirs_arr[$path]['module'] = $path;
				}
			}
		}
		$voList = M('Module')->select();
		foreach ($voList as $v){
			$vo[$v['module']]['modulename'] = $v['modulename'];
			$vo[$v['module']]['module'] = $v['module'];
			$vo[$v['module']]['version'] = $v['version'];
			$vo[$v['module']]['iscore'] = $v['iscore'];
			$vo[$v['module']]['installdate'] = $v['installdate'];
			$vo[$v['module']]['updatedate'] = $v['updatedate'];
			$vo[$v['module']]['status'] = $v['status'] ? $v['status'] : 0;
		}
		$list =  array_merge($vo,$dirs_arr);
		$this->assign ('list',$list);
		$this->display();
	}
	//安装模块
	public function install(){
		import("Module");
		$Module = new Module();
		$Module->check($_GET['module']);
		var_dump($Module->config);
		exit;
	}
	//安装模块
	public function uninstall(){
		
	}
	//模块详细
	public function info(){
		$module = $_GET['module'];
		$model = M('Module');
		$vo = $model->where("module='".$module."'")->find();
		if(!$vo){
			$modulefile = APP_PATH . C("APP_GROUP_PATH").'/'.$module.'/Install/Config.inc.php';
			if(is_file($modulefile)){
				include $modulefile;
				$vo['modulename'] = $modulename;
				$vo['version'] = $version;
				$vo['module'] = $module;
				$vo['description'] = $description;
				$vo['author'] = $author;
				$vo['authorsite'] = $authorsite;
				$vo['authoremail'] = $authoremail;
			}else{
				$this->error('模块包不完整');
			}
		}
		$this->assign('vo',$vo);
		$this->display();
	}
}