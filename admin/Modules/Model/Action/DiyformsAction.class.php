<?php
/**
 * 自定义表单管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class DiyformsAction extends AdminAction {
	public function index(){
		$voList = D('Diyforms')->order('id ASC')->select();
		$this->assign ('list',$voList );
		$this->display();
	}
	public function add(){
		if(IS_POST && IS_AJAX){
			$model = D('Diyforms');
			$_POST['tempfile'] = $_POST['index_temp'];
			if (false === $model->create()){
				$this->error($model->getError());
			}
			$sql = 'CREATE TABLE '.C('DB_PREFIX').strtolower($_POST['table']).' (`id` int(11) NOT NULL auto_increment PRIMARY KEY,`create_time` int(11) NOT NULL,`status` tinyint(1) NOT NULL DEFAULT \'0\') ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci';
			if($model->execute($sql) === false){
				$this->error('添加表单失败!');
			}
			if ($model->add()!==false) {
				$this->success('新增成功!');
			}else{
				$this->error('新增失败!');
			}
		}else{
			$conmodel = M()->query('SHOW TABLE STATUS LIKE  "'.C('DB_PREFIX').'Diyforms"');
			$autoid = $conmodel[0]['Auto_increment'];
			$this->assign('udid','pd'.$autoid);
			$this->assign('table','addforms'.$autoid);
			$this->display();
		}
	}  
    public function update(){
    	if(IS_POST && IS_AJAX){
    		$model = D('Diyforms');
    		$data = $model->create ();
    		if (false === $data) {
    			$this->error ($model->getError());
    		}
    		if($_POST['oldtable'] && $_POST['oldtable'] != $_POST['table']){
    			$sql = 'RENAME TABLE `'.C('DB_PREFIX').$_POST['oldtable'].'` TO `'.C('DB_PREFIX').$_POST['table'].'`';
    			if(M()->execute($sql) === false){
    				$this->error ($sql);
    			}
    		}
    		if($_POST['fieldset']){
    			$_POST['fieldset'] = htmlspecialchars_decode($_POST['fieldset']);
    			$filename = SITE_PATH.'/'.C('TEMPLETS_PATH').'/plus/diyform_'.$_POST['id'].'.html';
    			if(write($filename, $_POST['fieldset']) === false){
    				$this->error ('表单字段配置失败!'.$filename.'不能打开');
    			}
    		}
    		$list=$model->where('id = '.$_POST['id'])->data($data)->save();
    		if (false !== $list) {
    			//成功提示
    			$this->success ('表单修改成功!');
    		} else {
    			//错误提示
    			$this->error ('表单修改失败!');
    		}
    	}else{
    		$vo = M('Diyforms')->find($_GET['id']);
    		$this->assign ('vo',$vo);
    		$this->display ();
    	}
    }
    public function _before_foreverdelete(){
    	$vo = M('Diyforms')->field('id,table')->find($_GET['id']);
    	M('Formfield')->where('fid='.$vo['id'])->delete();
    	M()->execute('DROP TABLE `'.C('DB_PREFIX').$vo['table'].'`');
    }
}