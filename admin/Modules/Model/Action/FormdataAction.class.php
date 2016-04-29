<?php
/**
 * 自定义表单内容管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class FormdataAction extends AdminAction {
	private $info;
	public function _initialize() {
		parent::_initialize();
		if(!$_GET['fid']){
			$this->error('缺少参数');
		}
		$where['status'] = 1;
		$where['id'] = $_GET['fid'];
		$this->info = M('Diyforms')->field('table,field')->where($where)->find();
		if(!$this->info){
			$this->error('该表单不存在');
		}
	}
	public function index(){
		$fieldlist = M('formfield')->where('fid='.$_GET['fid'])->order('id ASC')->select();
		$field = explode(',',$this->info['field']);
		$comment['id'] = 'ID';
		foreach ($fieldlist as $v){
			if(in_array($v['fieldname'],$field)){
				$comment[$v['fieldname']] = $v['itemname'];
			}
		}
		//取得满足条件的记录数
		$model = M($this->info['table']);
		//排序字段 默认为主键名
		$order = ! empty ($_REQUEST['_order']) ? $_REQUEST['_order'] : 'id';
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] == 'asc' ? 'asc' : 'desc';
		} else {
			$sort = 'desc';
		}
		$totalCount = $model->count('id');
		if ($totalCount > 0) {
			$numPerPage = $_REQUEST['numPerPage'] ? $_REQUEST['numPerPage'] : 20;
			$pageNum = $_REQUEST['pageNum'] ? $_REQUEST['pageNum'] : 1;
			$firstRow = ($pageNum-1)*$numPerPage;
			$voList = $model->field($field)->order( "`" . $order . "` " . $sort)->limit($firstRow.','.$numPerPage)->select();
			$page = array('numPerPage'=>$numPerPage,'pageNum'=>$pageNum,'totalCount'=>$totalCount);
			$this->assign ('list',$voList);
			$this->assign ("page",$page);
		}
		$this->assign('field',$comment);
		$this->display();
	}
    public function foreverdelete(){
		if (false !== M($this->info['table'])->where('id='.$_GET['id'])->delete()){
			$this->success('删除成功！');
		} else {
			$this->error('删除失败！');
		}
    }
    public function forbid() {
    	if (M($this->info['table'])->where('id='.$_GET['id'])->setField('status',0) !== false) {
    		$this->success('取消审核成功');
    	} else {
    		$this->error('取消审核失败！');
    	}
    }
    public function resume() {
    	if (M($this->info['table'])->where('id='.$_GET['id'])->setField('status',1) !== false){
    		$this->success('审核成功！');
    	} else {
    		$this->error('审核失败！');
    	}
    }
	public function preview(){
		$fieldlist = M('formfield')->where('fid='.$_GET['fid'])->select();
		$comment['id'] = 'ID';
		foreach ($fieldlist as $v){
			$comment[$v['fieldname']] = $v['itemname'];
		}
		$vo = M($this->info['table'])->find($_GET['id']);
		$this->assign('field',$comment);
		$this->assign('vo',$vo);
		$this->display();
	}
}