<?php
/**
 * 版本控制
 * @version        JiuduCMS 1.0 2012年8月29日10:41:23 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2012, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class EditionAction extends AdminAction {
	public function index(){
		$pccode = htmlspecialchars('<{$tiaocode}>');
		$this->assign('pccode',$pccode);
		$voList = M('Edition')->select();
		$this->assign ('list', $voList);
		$this->display();
	}
	public function add(){
		if(IS_AJAX && IS_POST){
			$dir = SITE_PATH.'/'.C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE').'/'.$_POST['udid'].'/';
			if (!mkdir($dir)) {
				$this->error ('添加失败，模版目录没有写入权限!');
			}
			$model = D ('Edition');
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$list=$model->add ();
			if ($list!==false) { //保存成功
				$this->freadedition();
				$this->success ('新增成功!');
			} else {
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$this->display ();
		}
	}
	public function foreverdelete(){
		if(IS_AJAX){
			if (isset ($_GET['id'])) {
				$model = M('Edition');
				$vo = M('Edition')->where('id='.$_GET['id'])->field('udid,isdefault')->find();
				if($vo['isdefault'] == 1){
					$this->error ('默认版本不可删除');
				}
				$dir = SITE_PATH.'/'.C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE').'/'.$vo['udid'].'/';
				del_dir($dir);
				if (false !== $model->where ('id='.$_GET['id'])->delete ()) {
					$this->freadedition();
					$this->success ('删除成功！');
				}else{
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	public function update(){
		if(IS_AJAX && IS_POST){
			$oldname = SITE_PATH.'/'.C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE').'/'.$_POST['oldname'].'/';
			$newname = SITE_PATH.'/'.C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE').'/'.$_POST['udid'].'/';
			if(isset($_POST['udid']) && isset($_POST['oldname'])){
				if (!rename($oldname, $newname)) {
					$this->error ('修改失败，模版目录没有写入权限!');
				}
			}
			$model = D('Edition');
			$data = $model->create ();
			if (false === $data) {
				$this->error ( $model->getError () );
			}
			// 更新数据
			if (false !== $model->where('id = '.$_POST['id'])->data($data)->save()) {
				//成功提示
				$this->freadedition();
				$this->success ('编辑成功!');
			} else {
				//错误提示
				$this->error ('编辑失败!');
			}
		}else{
			$vo = M('Edition')->getById($_GET['id']);
			$this->assign ('vo',$vo );
			$this->display ();
		}
	}
	public function setdefault(){
		$model = M('Edition');		
		$where['type'] = $model->where('id ='.$_GET['id'])->getField('type');
		$model->startTrans ();
		$where['id'] = $_GET['id'];
		$result = $model->where($where)->setField(array('isdefault'=>1,'status'=>1));
		$where['id'] = array('neq',$_GET['id']);
		$result = $model->where($where)->setField('isdefault',0);
		$model->commit ();
		if ($result!==false) {
			$this->freadedition();
			$this->success ('设置成功');
		} else {
			$this->error ('设置失败');
		}
	}
	public function forbid(){
		if(IS_AJAX){
			$condition = array ('id'=>array('in',$_GET['id']));
			if (D('Edition')->forbid($condition) !== false) {
				$this->freadedition();
				$this->success('状态禁用成功');
			} else {
				$this->error('状态禁用失败！');
			}
		}
	}
	public function resume(){
		if(IS_AJAX){
			$condition = array ('id' => array ('in', $_GET['id'] ) );
			if(false !== D('Edition')->resume ($condition)){
				$this->freadedition();
				$this->success('状态启用成功！');
			}else{
				$this->error('状态启用失败！');
			}
		}
	}
	//更新版本配置文件
	public function renewedition(){
    	if($this->freadedition()){
    		$this->success ('更新成功!');
    	}else{
    		$this->error ('更新失败!');
    	}
    }
	//更新版本配置文件
	public function freadedition(){
    	$model = M('EditionRule');
    	$where[$model->getTableName().'.status'] = 1;
    	$where[C('DB_PREFIX').'edition.status'] = 1;
    	$join[] = C('DB_PREFIX').'edition ON '.$model->getTableName().'.eid='.C('DB_PREFIX').'edition.id';
    	$join[] = C('DB_PREFIX').'edition_type ON '.$model->getTableName().'.tid='.C('DB_PREFIX').'edition_type.id';
		$voList = $model->field($model->getTableName().'.id as id,tid,device,'.$model->getTableName().'.status as status,'.$model->getTableName().'.sortrank as sortrank,'.C('DB_PREFIX').'edition.udid as udid,type,isdefault,iscore,url,'.C('DB_PREFIX').'edition_type.value as value')->where($where)->join($join)->select();
		$data = json_encode($voList);
		$filename = SITE_PATH.'/'.C('UPLOAD_PATH').'/Edition/edition.inc';
		if (write($filename, $data) === FALSE) {
			return false;
		}else{
			return true;
		}
    }
}