<?php
/**
 * 内容模型管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class ConmodelAction extends AdminAction {
	function index(){
		$voList = M('Conmodel')->order('id ASC')->select();
		$this->assign ( 'list', $voList );
		$this->display();
	}	
	public function add(){
		if(IS_POST && IS_AJAX){
			$model = D('Conmodel');
			if (false === $model->create()){
				$this->error ( $model->getError () );
			}
			$_POST['addtable'] = strtolower($_POST['addtable']);
			$sql = 'CREATE TABLE '.C('DB_PREFIX').$_POST['addtable'].' (`id` int(11) NOT NULL auto_increment PRIMARY KEY) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci';
			if($model->execute($sql) === false){
				$this->error ('添加系统模型失败!');
			}
			//保存当前数据对象
			if ($model->add()!==false) { //保存成功
				$this->success ('新增成功!');
			}else{
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$conmodel = M()->query('SHOW TABLE STATUS LIKE  "'.C('DB_PREFIX').'Conmodel"');
			$autoid = $conmodel[0]['Auto_increment'];
			$this->assign('udid','pd'.$autoid);
			$this->assign('addtable','addon'.$autoid);
			$this->display();
		}
	}
	public function update(){
		if(IS_POST && IS_AJAX){
			$model = D('Conmodel');
			$data = $model->create ();
			if (false === $data) {
				$this->error ( $model->getError () );
			}
			if($_POST['oldaddtable'] && $_POST['oldaddtable'] != $_POST['addtable']){
				$sql = 'RENAME TABLE `'.C('DB_PREFIX').$_POST['oldaddtable'].'` TO `'.C('DB_PREFIX').$_POST['addtable'].'`';
				if(M()->execute($sql) === false){
					$this->error ($sql);
				}
			}
			if($_POST['fieldset']){
				$_POST['fieldset'] = htmlspecialchars_decode($_POST['fieldset']);
				$filename = SITE_PATH.'/'.C('TEMPLETS_PATH').'/plus/conmodel_'.$_POST['id'].'.html';
				if(write($filename, $_POST['fieldset']) === false){
					$this->error ('模型字段配置失败!'.$filename.'不能打开');
				}
			}
			$list=$model->where('id = '.$_POST['id'])->data($data)->save();
			if (false !== $list) {
				//成功提示
				$this->success ('模型修改成功!');
			} else {
				//错误提示
				$this->error ('模型修改失败!');
			}
		}else{
			$vo = M('Conmodel')->find($_GET['id']);
			$this->assign ('vo',$vo);
			$this->display ();
		}
    }
    public function _before_foreverdelete(){
    	$vo = M('Conmodel')->field('id,addtable')->find($_GET['id']);
    	M('Field')->where('mid='.$vo['id'])->delete();
    	M()->execute('DROP TABLE `'.C('DB_PREFIX').$vo['addtable'].'`');
    }
}