<?php
/**
 +------------------------------------------------------------------------------
 * DbBak 数据库备份恢复类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: DbBak.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class DbBak{
	private $model;
	private $dbName;
	private $fields = array();
	private $backupDir ='';
	private $pagenum = 100;
	public function __construct($backupDir=''){
		$this->backupDir = $backupDir;
		$this->model = new Model();
		//$this->getFields();
	}
	//备份数据库
	public function backupDb($dbName,$roll,$num){
		$table = $_SESSION['table'];
		$this->dbName = $table[$dbName];
		$filename = SITE_PATH.'/'.C('JIUDU_DABA_BAK').'/'.$this->backupDir;
		if(!is_dir($filename)){
			mk_dir($filename);
		}
		$sqlfile = $filename.'/'.$this->dbName.'_'.$roll.'.sql';
		if($num == 0){
			$sql = 'SHOW CREATE TABLE `'.$this->dbName.'`';
			$create = $this->model->query($sql);
			$CreateTable = 'DROP TABLE IF EXISTS `'.$this->dbName.'`;'."\n";
			$CreateTable .=$create[0]['Create Table'].';'."\n";
			write($sqlfile,$CreateTable);
		}
		$sql = 'SELECT * FROM '.$this->dbName.' LIMIT '.$num.','.$this->pagenum;
		$data = $this->model->query($sql);
		if($data){
			foreach ($data as $k=>$val){
				foreach ($val as $ks=>$vs){
					$v[$ks] = addslashes($vs);
				}
				$value .= 'INSERT INTO `'.$this->dbName.'` VALUES (\''.join("','",$v).'\');'."\n";
			}
			write($sqlfile,$value,'a');
			unset($value);
		}else{
			return true;
		}
		$num +=$this->pagenum;
		clearstatcache();
		$size = $this->getFilesize($sqlfile);
		if(($size)>2097152){
			$roll++;
			$vo = array('tablekey'=>$dbName,'roll'=>$roll,'num'=>$num);
			return $vo;
		}else{
			return $this->backupDb($dbName, $roll, $num);
		}
	}
	//获取数据库字段
	private function getFields(){
		unset($this->fields);
		$field = $this->model->query('DESC '.$this->dbName);
		foreach ($field as $v){
			$this->fields[] = $v['Field'];
		}
	}
	//获取文件大小
	private function getFilesize($sqlfile){
		return filesize($sqlfile);
	}
	//恢复数据库
	public function restoreDb($dbName){
		$dir = C('JIUDU_DABA_BAK').'/'.$_SESSION['datadir'];
		$val = freadfile($dir.'/'.$dbName);
		$val = explode(";\n",trim($val));
		$model = new Model();
		foreach ($val as $v){
			//var_dump($v);
			$list = $model->execute($v);
			if($list === false){
				$sqlfile = 'error.sql';
				write($sqlfile,$v,'a');
			}
		}
		return true;
	}
}