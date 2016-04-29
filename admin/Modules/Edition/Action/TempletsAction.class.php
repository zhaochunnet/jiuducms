<?php
/**
 * 模板管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class TempletsAction extends AdminAction {
	private $temppath;
	function _initialize() {
		parent::_initialize();
		$this->temppath = SITE_PATH.'/'.C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE');
	}
	public function index(){
		$path = $_GET['path'] ? $_GET['path'].'/': '';
		$uparr = explode('/',$path);
		foreach ($uparr as $k => $v) {
			if (!$v){unset($uparr[$k]);}
		}
		array_pop($uparr);
		$uparr = implode('/',$uparr);
		$uparr = $uparr ? $uparr.'/' : '';
		import("Dir");
		$dir = new Dir();
		if($path!='' && $uparr==''){
			$voList = M('Templets')->field('id,tempname,tempinfo,default')->select();
			foreach ($voList as $v){
				$voLists[$v['tempname']] = $v;
			}
		}elseif($path == ''){
			$voList = M('Edition')->field('id,name,udid')->select();
			foreach ($voList as $v){
				$voLists[$v['udid']] = $v['name'];
			}
		}
		$file = $dir->listFile($this->temppath.'/'.$path);
		foreach ($file as $k => $v){
			$file[$k]['fileurl'] = $path.$v['filename'];
			if($path!='' && $uparr==''){
				$file[$k]['name'] = $voLists[$v['filename']]['tempinfo'];
				$file[$k]['default'] = $voLists[$v['filename']]['default'];
			}elseif($path == ''){
				$file[$k]['name'] = $voLists[$v['filename']];
			}
		}
		$this->assign('uparr',$uparr);
		$this->assign('list',$file);
		$this->display();
	}
	public function add(){
		if(IS_POST && IS_AJAX){
			$path = $_POST['path'] ? $_POST['path'].'/' : '';
			$pathname = $this->temppath.'/'.$path;
			if(!is_dir($pathname)){
				$this->error('非法操作');
			}
			$filename = $pathname.$_POST['filename'];
			if(write($filename, $_POST['content'])){
				$this->success ('新增模板成功!');
			}else{
				$this->error ('新增模板失败!');
			}	
		}else{
			$this->display();
		}	
    }
	public function update(){
		if(!$_GET['path']){
			$this->error('非法操作');
			exit;
		}
		$pathname = $this->temppath.'/'.$_GET['path'];
		if(!is_file($pathname)){
			$this->error('非法操作');
		}
		if(IS_POST && IS_AJAX){
			$content = htmlspecialchars_decode($_POST['content']);
			if(write($pathname,$content)){
				$this->success ('编辑文件成功!');
			}else{
				$this->error ('编辑文件失败!');
			}
		}else{
			$vo['content'] = htmlspecialchars(freadfile($pathname));
			$vo['name'] = $_GET['name'];
			$this->assign('vo',$vo);
			$this->display();
		}
	}
    public function foreverdelete(){
    	if(IS_AJAX){
    		$path = $this->temppath.'/'.$_GET['path'];
    		if(!is_file($path)){
    			$this->error('非法操作');
    		}
	    	if(unlink($path)){
				$this->success ('模版删除成功!');
			}else{
				$this->error ('模版删除失败!');
			}
    	}
    }
    public function public_list(){
    	$temppath = $this->temppath.'/'.C('TEMPLETS_CHECK_TYPE');
    	$path = $_GET['path'] ? $_GET['path'].'/': '';
		$uparr = explode('/',$path);
		foreach ($uparr as $k => $v) {
			if (!$v){unset($uparr[$k]);}
		}
		array_pop($uparr);
		$uparr = implode('/',$uparr);
		$uparr = $uparr ? $uparr.'/' : '';
		import("Dir");
		$dir = new Dir();
		$file = $dir->listFile($temppath.'/'.$path);
		foreach ($file as $k => $v){
			$file[$k]['fileurl'] = $path.$file[$k]['filename'];
		}
		$this->assign('uparr',$uparr);
		$this->assign('list',$file);
		$this->display();
    }
}