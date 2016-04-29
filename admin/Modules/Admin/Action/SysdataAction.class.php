<?php
/**
 * 数据库管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class SysdataAction extends AdminAction {
	function index(){
		$model = new Model();
		$voList = $model->query('SHOW TABLE STATUS LIKE "'.C('DB_PREFIX').'%"');
		foreach ($voList as $k=>$v){
			$voList[$k]['Size'] = byte_format($v['Index_length']+$v['Data_length']);
			$voList[$k]['Data_free'] = byte_format($v['Data_free']);
			$size += $voList[$k]['Size'];
			$Rows += $v['Rows'];
			$Datafree += $v['Data_free'];
		}
		$row = count($voList);
		$this->assign ( 'size',byte_format($size));
		$this->assign ( 'length',$Rows );
		$this->assign ( 'datafree',byte_format($Datafree));
		$this->assign ( 'row', $row );
		$this->assign ( 'list', $voList );
		$this->display();
	}
	//备份数据库
	public function backup(){
		$model = new Model();
		$sql = 'SHOW TABLES';
		$voList = $model->query($sql);
		$key = array_keys($voList[0]);
		foreach ($voList as $k=>$v){
			$list[$k]['Tables'] = $v[$key[0]];
		}
		$this->assign ('list',$list);
		$this->display();
	}
	//备份数据库方法
	public function backupdo(){
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			redirect ( PHP_FILE . C ('USER_AUTH_GATEWAY' ));
		}
		if($_POST['table']){
			$_SESSION['table'] = $_POST['table'];
		}
		$tablekey = $_GET['tablekey'] ? $_GET['tablekey'] : 0;
		$roll = $_GET['roll'] ? $_GET['roll'] : 1;
		$num = $_GET['num'] ? $_GET['num'] : 0;
		$table = $_SESSION['table'];
		if(isset($table[$tablekey])){
			import ('ORG.Net.DbBak');
			$dir = C('DB_NAME').'_'.date('Ymd');
			$DbBak = new DbBak($dir);
			$vo = $DbBak->backupDb($tablekey,$roll,$num);
			if(is_array($vo)){
				$url = U('Sysdata/backupdo',$vo);
				$this->success ('正在跳转下一卷备份',$url);
			}elseif($vo){
				$tablekey++;
				$vars['tablekey']=$tablekey;
				$url = U('Sysdata/backupdo',$vars);
				$this->success ('正在跳转下一数据表备份',$url);
			}
		}else{
			$this->assign ("closeWin",true);
			$this->success ('备份完成',U('Index/index'));
		}
	}
	//恢复数据库
	public function restore(){
		$dir = C('JIUDU_DABA_BAK');
		$d = opendir($dir);
		while ($file = readdir($d)){
			if ($file == '.' || $file == '..') continue;
			if(is_dir($dir.'/'.$file)){
				$files[] = $file;
			}
		}
		$this->assign ("list",$files);
		$this->display();
	}
	//恢复数据库方法
	public function restoredo(){
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			redirect ( PHP_FILE . C ('USER_AUTH_GATEWAY' ));
		}
		$tablekey = $_GET['tablekey'] ? $_GET['tablekey'] : 0;
		if($_POST['datadir']){
			$dir = C('JIUDU_DABA_BAK').'/'.$_POST['datadir'];
			$d = opendir($dir);
			while ($file = readdir($d)){
				if ($file == '.' || $file == '..') continue;
				if(is_file($dir.'/'.$file)){
					$files[] = $file;
				}
			}
			$_SESSION['files'] = $files;
			$_SESSION['datadir'] = $_POST['datadir'];
		}
		$files = $_SESSION['files'];
		import ('ORG.Net.DbBak');
		$DbBak = new DbBak($dir);
		$vo = $DbBak->restoreDb($files[$tablekey]);
		if($vo){
			$tablekey++;
			if($files[$tablekey]){
				$vars['tablekey']=$tablekey;
				$url = U('Sysdata/restoredo',$vars);
				$this->success ('正在跳转下一卷进行恢复',$url);
			}else{
				$url = U('Login/logout');
				$this->success ('恢复数据库完成，请重新登录',$url);
			}
		}
	}
	//修复表
	public function repair(){
		$name = $_REQUEST['name'];
		$model = new Model();
		$sql = 'REPAIR TABLE '.$name;
		$list = $model->execute($sql);
		if($list !== false){
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ('修复成功');
		}else{
			$this->error ('修复失败');
		}
	}
	//优化表
	public function optimize(){
		$name = $_REQUEST['name'];
		$model = new Model();
		$sql = 'OPTIMIZE  TABLE '.$name;
		$list = $model->execute($sql);
		if($list !== false){
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ('优化成功');
		}else{
			$this->error ('优化失败');
		}
	}
	//删除表
	public function foreverdelete(){
		$name = $_REQUEST['name'];
		$model = new Model();
		$sql = 'DROP TABLE `'.$name.'`';
		$list = $model->execute($sql);
		if($list !== false){
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ('删除成功');
		}else{
			$this->success ('删除失败');
		}
	}
	//清空表
	public function truncate(){
		$name = $_REQUEST['name'];
		$model = new Model();
		$sql = 'TRUNCATE  TABLE '.$name;
		$list = $model->execute($sql);
		if($list !== false){
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ('清空成功');
		}else{
			$this->error ('清空失败');
		}
	}
	//表结构
	public function structure(){
		$name = $_REQUEST['name'];
		$model = new Model();
		$sql = 'SHOW CREATE TABLE `'.$name.'`';
		$voList = $model->query($sql);
		$this->assign ('vo', $voList[0]);
		$this->display();
	}
}