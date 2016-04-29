<?php
/**
 * 栏目管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class ArctypeAction extends AdminAction {
	private $cat;
	function _initialize() {
		parent::_initialize();
		import("Category");
		$this->cat = new Category('Arctype', array('id', 'pid', 'typename', 'fullname'));
	}
	function index(){
		$voList = $this->cat->getList('id,pid,typename,ispart,status,sortrank',NULL,0,'id ASC');
		$this->assign('list',$voList);
		$this->display();
	}
	function _filter(&$map){
		$map['typename'] = array('like',"%".$_POST['typename']."%");
		$this->assign('typename',$_POST['typename']);
	}
	//添加栏目(子栏目)
	public function _before_add(){
		if(IS_POST && IS_AJAX){
			$_POST['tempindex'] = $_POST['index_temp'];
			$_POST['templist'] = $_POST['list_temp'];
			$_POST['temparticle'] = $_POST['article_temp'];
			if($_POST['referpath']){
				$_POST['typedir'] = '{cmspath}/'.$_POST['dirname'];
			}else{
				$_POST['typedir'] = $_POST['typedir'].'/'.$_POST['dirname'];
			}
		}else{
			$id = $_GET['id'];
			if($id){
				$Arctype = M('arctype');
				$void=$Arctype->field('pid,cid,typedir')->find($id);
				$vo['typedir'] = $void['typedir'];
				$vo['pid'] = $id;
				$vo['cid'] = $void['cid'];
			}else{
				$vo['typedir'] = '{cmspath}';
				$vo['pid'] = 0;
			}
			$conm = M('conmodel')->field("id,typename")->where("status=1")->select();
			$this->assign('vo',$vo);
			$this->assign('conm',$conm);
		}
    }
    //修改栏目
	public function update(){
		if(IS_POST && IS_AJAX){
			$_POST['tempindex'] = $_POST['index_temp'];
			$_POST['templist'] = $_POST['list_temp'];
			$_POST['temparticle'] = $_POST['article_temp'];
			if($_POST['referpath']){
				$_POST['typedir'] = '{cmspath}/'.$_POST['dirname'];
			}else{
				$_POST['typedir'] = $_POST['typedir'].'/'.$_POST['dirname'];
			}
			$model = D('Arctype');
			if($_POST['upnext'] == 1){
				$sondata = array('cid'=>$_POST['cid'],'tempindex'=>$_POST['tempindex'],'templist'=>$_POST['templist'],'temparticle'=>$_POST['temparticle'],'listrule'=>$_POST['listrule'],'arctrule'=>$_POST['arctrule'],'listnum'=>$_POST['listnum'],'linknum'=>$_POST['linknum']);
				$model->where('pid = '.$_POST['id'])->data($sondata)->save();
			}
			$data = $model->create();
			if (false === $data){
				$this->error($model->getError());
			}
			// 更新数据
			if (false !== $model->where('id = '.$_POST['id'])->data($data)->save()){
				//成功提示
				$this->success ('编辑成功!');
			} else {
				//错误提示
				$this->error ('编辑失败!');
			}
		}else{
			$conmodel = M('conmodel');
			$conm = $conmodel->field("id,typename")->where("status=1")->select();
			$model = M ('arctype');
			$id = $_GET['id'];
			$vo = $model->getById($id);
			if($vo['pid']){
				$pid = $model->field('typedir')->find($vo['pid']);
				$vo['typedir'] = $pid['typedir'];
			}else{
				$vo['typedir'] ='{cmspath}';
			}
			$this->assign('conm',$conm);
			$this->assign ('vo', $vo );
			$this->display ();
		}
    }
    //移动栏目
    public function move(){
    	if(IS_POST && IS_AJAX){
    		$id = $_POST['id'];
    		$pid = $_POST['pid'];
    		$model = M('arctype');
    		$vo=$model->field('dirname')->find($id);
    		if($pid==0){
    			$_POST['typedir'] = '{cmspath}/'.$vo['dirname'];
    		}else{
    			$void = $model->field('typedir')->find($pid);
    			$_POST['typedir'] = $void['typedir'].'/'.$vo['dirname'];
    		}
    		$data = array('pid'=>$pid,'typedir'=>$_POST['typedir']);
    		if($model->where('id ='.$_POST['id'])->data($data)->save()){
    			$this->success ('移动成功!');
    		}else{
    			$this->error ('移动失败!');
    		}
    	}else{
    		$voList = $this->cat->getList('id,pid,typename,ispart','id !='.$_GET['id']);
    		$this->assign('id',$_GET['id']);
    		$this->assign('value',$voList);
    		$this->display();
    	}
    }
    //删除栏目
    public function foreverdelete(){
    	$voList = $this->cat->getList('id,pid',null,$_GET['id']);
    	foreach ($voList as $v){
    		$id[] = $v['id'];
    	}
    	$id[] = $_GET['id'];
    	if(isset($id)){
    		$condition = array('id' =>array('in',$id));
    		if (false !== M('arctype')->where($condition)->delete()) {
    			M('Archives')->where(array('typeid'=>array('in',$id)))->delete();
    			$this->success('删除成功！');
    		} else {
    			$this->error('删除失败！');
    		}
    	}else{
    		$this->error('非法操作');
    	}
    }
}