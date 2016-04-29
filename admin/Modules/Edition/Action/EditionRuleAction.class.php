<?php
/**
 * 版本控制
 * @version        JiuduCMS 1.0 2012年8月29日10:41:23 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2012, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class EditionRuleAction extends AdminAction {
	public function index(){
		$model = M('EditionRule');
		$join[] = C('DB_PREFIX').'edition ON '.$model->getTableName().'.eid='.C('DB_PREFIX').'edition.id';
		$join[] = C('DB_PREFIX').'edition_type ON '.$model->getTableName().'.tid='.C('DB_PREFIX').'edition_type.id';
		$voList = $model->field($model->getTableName().'.id as id,eid,tid,'.$model->getTableName().'.name as name,device,'.$model->getTableName().'.status as status,'.$model->getTableName().'.sortrank as sortrank,'.C('DB_PREFIX').'edition.name as ename,'.C('DB_PREFIX').'edition_type.name as tname')->join($join)->select();
		$this->assign ('list', $voList);
		$this->display();
	}
	public function add(){
		if(IS_AJAX && IS_POST){
			$model = D ('EditionRule');
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$list=$model->add ();
			if ($list!==false) { //保存成功
				A('Edition')->freadedition();
				$this->success ('新增成功!');
			} else {
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$edition = M('Edition')->select();
			$type= M('EditionType')->where('pid = 1')->select();
			$this->assign ('edition', $edition);
			$this->assign ('type', $type);
		}
	}
	public function update(){
		if(IS_AJAX && IS_POST){
			$model = D('EditionRule');
			$data = $model->create ();
			if (false === $data) {
				$this->error ( $model->getError () );
			}
			// 更新数据
			if (false !== $model->where('id = '.$_POST['id'])->data($data)->save()) {
				//成功提示
				A('Edition')->freadedition();
				$this->success ('编辑成功!');
			} else {
				//错误提示
				$this->error ('编辑失败!');
			}	
		}else{
			$edition = M('Edition')->select();
			$type= M('EditionType')->where('pid = 1')->select();
			$vo = M('EditionRule')->getById($_GET['id']);
			$this->assign ('edition', $edition);
			$this->assign ('type', $type);
			$this->assign ('vo',$vo );
			$this->display ();
		}
	}
	public function foreverdelete(){
		if(IS_AJAX){
			if (isset ($_GET['id'])){
				if (false !== M('EditionRule')->where('id='.$_GET['id'])->delete()) {
					A('Edition')->freadedition();
					$this->success ('删除成功！');
				}else{
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	public function forbid() {
		if(IS_AJAX){
			$condition = array ('id'=>array('in',$_GET['id']));
			if (D('EditionRule')->forbid($condition) !== false) {
				A('Edition')->freadedition();
				$this->success('状态禁用成功');
			} else {
				$this->error('状态禁用失败！');
			}
		}
	}
	public function resume() {
		if(IS_AJAX){
			$condition = array ('id' => array ('in', $_GET['id'] ) );
			if (false !== D('EditionRule')->resume ( $condition )) {
				A('Edition')->freadedition();
				$this->success('状态启用成功！');
			} else {
				$this->error('状态启用失败！');
			}
		}
	}
	public function sortrank(){
		if(IS_AJAX && IS_POST){
			$sortrank = $_POST['sortrank'];
			$model = M('EditionRule');
			foreach ($sortrank as $k=>$v){
				$model->where('id='.$k)->setField('sortrank',$v);
			}
			A('Edition')->freadedition();
			$this->success ('更新成功!');
		}
	}
}