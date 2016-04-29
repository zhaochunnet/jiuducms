<?php
/**
 * 文件资源管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class FilemanagerAction extends AdminAction {
	function _initialize() {
		parent::_initialize();
		if (preg_match('/\.\./', $_GET['path'])) {
			$this->error('不允许访问上级目录');
		}
		import("Dir");
		$this->Dir = new Dir();
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
		$file = $this->Dir->listFile(SITE_PATH.'/'.$path);
		Load('extend');
		$dirnum=0;
		$filenum=0;
		foreach ($file as $k => $v){
			$file[$k]['fileurl'] = $path.$file[$k]['filename'];
			$file[$k]['filename'] = iconv("gb2312", "UTF-8",$v['filename']);
			$file[$k]['size'] = $v['size'] ? byte_format($v['size']) : '';
			if($file[$k]['isDir']){
				$dirnum++;
			}elseif($file[$k]['isFile']){
				$filenum++;
			}
		}
		$this->assign('dirnum',$dirnum);
		$this->assign('filenum',$filenum);
		$this->assign('uparr',$uparr);
		$this->assign('list',$file);
		$this->display();
	}
	public function adddir(){
		if(IS_POST && IS_AJAX){
			$path = $_POST['path'] ? $_POST['path'].'/' : '';
			$pathname = SITE_PATH.'/'.$path;
			if(!is_dir($pathname)){
				$this->error('非法操作');
			}
			if(mkdir($pathname.$_POST['dirname'],'0755')){
				$this->setlog('新增','目录为'.$_POST['dirname'].',成功');
				$this->success('新增目录成功');
			}else{
				$this->setlog('新增','目录为'.$_POST['dirname'].',失败');
				$this->error('新增目录失败');
			}
		}else{
			$this->display();
		}
	}
	public function addfile(){
		if(IS_POST && IS_AJAX){
			$path = $_POST['path'] ? $_POST['path'].'/' : '';
			$pathname = SITE_PATH.'/'.$path;
			if(!is_dir($pathname)){
				$this->error('非法操作');
			}
			$filename = $pathname.$_POST['filename'];
			if(write($filename, $_POST['content'])){
				$this->success ('新增文件成功!');
			}else{
				$this->error ('新增文件失败!');
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
		$pathname = SITE_PATH.'/'.$_GET['path'];
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
	//文件[夹]改名
	public function rname(){
		if(IS_POST && IS_AJAX){
			$path = $_POST['path'] ? $_POST['path'].'/' : '';
			$pathname = SITE_PATH.'/'.$path;
			if(!is_dir($pathname)){
				$this->error('非法操作');
			}
			if(is_dir($pathname.$_POST['oldname'])){
				$oldname = $pathname.$_POST['oldname'].'/';
				$filename = $pathname.$_POST['filename'].'/';
			}elseif(is_file($pathname.$_POST['oldname'])){
				$oldname = $pathname.$_POST['oldname'];
				$filename = $pathname.$_POST['filename'].'.'.strtolower(substr(strrchr(basename($_POST['oldname']), '.'), 1));
			}else{
				$this->error ('重命名失败!');
			}
			if(rename($oldname,$filename)){
				$this->success ('重命名成功!');
			}else{
				$this->error ('重命名失败!');
			}
		}else{
			$path = $_GET['path'] ? $_GET['path'].'/' : '';
			$filename = SITE_PATH.'/'.$path.$_GET['name'];
			if(is_file($filename)){
				$ext = '.'.strtolower(substr(strrchr(basename($filename), '.'), 1));
			}
			$this->assign('ext',$ext);
			$this->display();
		}
	}
	public function preview(){
		$filename = SITE_PATH.'/'.$_GET['path'];
		redirect('/'.$_GET['path']);
	}
	public function copy(){
		if(IS_POST && IS_AJAX){
			$overwrite = $_POST['overwrite'] ? true : false;
			$name = SITE_PATH.'/'.$_POST['name'];
			$pathname = SITE_PATH.'/'.$_POST['pathname'];
			if(is_file($name)){
				$pathname = $pathname.'/'.basename($name);
				if($this->Dir->copyFile($name,$pathname,$overwrite)){
					$this->success ('复制文件成功!');
				}else{
					$this->error ('复制文件失败!');
				}
			}elseif(is_dir($name)){
				$pathname = $pathname.'/'.array_pop(explode('/',$_POST['name'])).'/';
				$this->Dir->copyDir($name,$pathname,$overwrite);
				$this->success ('复制文件夹完成，如没有复制，请检查文件是否有权限!');
			}
		}else{
			$name = $_GET['name'];
			$path = $_GET['path'] ? $_GET['path'].'/': '';
			$uparr = explode('/',$path);
			foreach ($uparr as $k => $v) {
				if (!$v){unset($uparr[$k]);}
			}
			array_pop($uparr);
			$uparr = implode('/',$uparr);
			$uparr = $uparr ? $uparr.'/' : '';
			$files = array('filename'=>'网站根目录','fileurl'=>'');
			$file = $this->Dir->listFile(SITE_PATH.'/'.$path,1);
			foreach ($file as $k => $v){
				if($path.$file[$k]['filename'] == $name){
					unset($file[$k]);
					continue;
				}
				$file[$k]['fileurl'] = $path.$file[$k]['filename'];
				$file[$k]['filename'] = iconv("gb2312", "UTF-8",$v['filename']);
			}
			$this->assign('uparr',$uparr);
			$this->assign('name',$name);
			$this->assign('list',$file);
			$this->assign('lists',$files);
			$this->assign('path',$_GET['path']);
			$this->display();
		}
	}
	public function move(){
		if(IS_POST && IS_AJAX){
			$overwrite = $_POST['overwrite'] ? true : false;
			$name = SITE_PATH.'/'.$_POST['name'];
			$pathname = SITE_PATH.'/'.$_POST['pathname'];
			if(is_file($name)){
				$pathname = $pathname.'/'.basename($name);
				if($this->Dir->moveFile($name,$pathname,$overwrite)){
					$this->success ('移动文件成功!');
				}else{
					$this->error ('移动文件失败!');
				}
			}elseif(is_dir($name)){
				$pathname = $pathname.'/'.array_pop(explode('/',$_POST['name'])).'/';
				$this->Dir->moveDir($name,$pathname,$overwrite);
				$this->success ('移动文件夹完成，如没有移动，请检查文件是否有权限!');
			}
		}else{
			$name = $_GET['name'];
			$path = $_GET['path'] ? $_GET['path'].'/': '';
			$uparr = explode('/',$path);
			foreach ($uparr as $k => $v) {
				if (!$v){unset($uparr[$k]);}
			}
			array_pop($uparr);
			$uparr = implode('/',$uparr);
			$uparr = $uparr ? $uparr.'/' : '';
			$files = array('filename'=>'网站根目录','fileurl'=>'');
			$file = $this->Dir->listFile(SITE_PATH.'/'.$path,1);
			foreach ($file as $k => $v){
				if($path.$file[$k]['filename'] == $name){
					unset($file[$k]);
					continue;
				}
				$file[$k]['fileurl'] = $path.$file[$k]['filename'];
				$file[$k]['filename'] = iconv("gb2312", "UTF-8",$v['filename']);
			}
			$this->assign('uparr',$uparr);
			$this->assign('name',$name);
			$this->assign('list',$file);
			$this->assign('lists',$files);
			$this->assign('path',$_GET['path']);
			$this->display();
		}
	}
	public function foreverdelete(){
		if(IS_AJAX){	
			if(!$_GET['path']){
				$this->error('非法操作');
				exit;
			}
			$path = SITE_PATH.'/'.$_GET['path'];
			if(!is_dir($path) && !is_file($path)){
				$this->error('非法操作');
			}
			del_dir($path);
			$this->success('删除完成，如没有删除，请检查文件是否有权限');
		}
	}
}