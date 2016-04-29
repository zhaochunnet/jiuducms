<?php
/**
 * 生成静态页面
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class MakehtmlAction extends AdminAction {
	public function _initialize() {
		parent::_initialize();
		import('Url');
		import('Html');
		del_dir(SITE_PATH.'/Runtime/Cache/Admin/');
	}
	public function index(){
		import("Category");
		$cat = new Category('Arctype', array('id', 'pid', 'typename', 'fullname'));
		$voList = $cat->getList('id,pid,typename,ispart','ispart!=3');
		$this->assign ( 'Arctypelist', $voList );
		$Topic = M('topic');
		$voList = $Topic->field('id,name')->where('isdefault=1')->select();
		$this->assign ( 'Topiclist', $voList );
		$this->display();
	}
	//生成首页
	public function home(){
		$vo = Html::index();
		if($vo){
			$this->success ('首页生成成功');
		}else{
			$this->error('首页生成失败');
		}
	}
	//生成栏目页
	public function arctype(){
		if(IS_POST){
			$_SESSION['html_type_id'] = $_POST['typeid'];
		}
		$id = $_GET['id']+0;
		if(!$_SESSION['html_type_id']){
			$where = array('isdefault'=>1,'status'=>1,'ispart'=>array('neq',3));
			$void = M('Arctype')->field('id')->where($where)->order('id ASC')->select();
			foreach ($void as $v){
				$types[] = $v['id'];
			}
			$_SESSION['html_type_id'] = $types;
		}
		$typeid = $_SESSION['html_type_id'];
		if(isset($typeid[$id])){
			$vo = Html::arctype($typeid[$id]);
			if($vo){
				$url = U('Makehtml/arctype').'&id='.($id+1);
				$this->success ('栏目'.$typeid[$id].'生成成功，正在跳到下一栏目生成',$url);
			}else{
				$this->error('栏目'.$typeid[$id].'生成失败');
			}
		}else{
			unset($_SESSION['html_type_id']);
			$this->assign ("closeWin",true);
			$this->success ('栏目生成完成');
		}
	}
	//生成内容页
	public function archives(){
		$num = $_GET['num']+0;
		$row = $_REQUEST['row'] ? $_REQUEST['row'] : 30;
		$offset = $num*$row;
		$url = U('Makehtml/archives');
		if($_REQUEST['strid'] && $_REQUEST['endid']){
			$where[C('DB_PREFIX').'archives.id'] = array(array('gt',$_REQUEST['strid']),array('lt',$_REQUEST['endid']));
			$url.='&strid='.$_REQUEST['strid'].'&endid='.$_REQUEST['endid'];
		}elseif($_REQUEST['strid']){
			$where[C('DB_PREFIX').'archives.id'] = array('gt',$_REQUEST['strid']);
			$url.='&strid='.$_REQUEST['strid'];
		}elseif($_REQUEST['endid']){
			$where[C('DB_PREFIX').'archives.id'] = array('lt',$_REQUEST['endid']);
			$url.='&endid='.$_REQUEST['endid'];
		}
		if($_REQUEST['typeid']){
			$typeid = is_array($_REQUEST['typeid']) ? implode(',',$_REQUEST['typeid']) :$_REQUEST['typeid'];
			$where['typeid'] = array('in',$typeid);
			$url.='&typeid='.$typeid;
		}
		$where[C('DB_PREFIX').'archives.status'] = array('eq',1);
		$where['ispart'] = array('neq',3);
		$where['isdefault'] = array('eq',1);
		$where['url'] = array('exp','IS NULL');
		$void = M('archives')->field(C('DB_PREFIX').'archives.id as id')->join(C('DB_PREFIX').'arctype ON '.C('DB_PREFIX').'arctype.id=typeid')->where($where)->limit($offset,$row)->select();
		if($void){
			foreach ($void as $v){
				$arcid[] = $v['id'];
			}
			$vo = Html::archives($arcid,$num);
			if($vo){
				$url .= '&num='.$vo;
				$this->success ('本组文档生成完成，正在跳到下一组文档生成',$url);
			}
		}else{
			$this->assign ("closeWin",true);
			$this->success ('文章生成完成');
		}
	}
	//生成专题页
	public function topic(){
		if(IS_POST){
			$_SESSION['html_topic_id'] = $_POST['typeid'];
		}
		$id = $_GET['id']+0;
		if(!$_SESSION['html_topic_id']){
			$topic = M('topic');
			$void = $topic->field('id')->where('isdefault=1')->select();
			foreach ($void as $v){
				$types[] = $v['id'];
			}
			$_SESSION['html_topic_id'] = $types;
		}
		$typeid = $_SESSION['html_topic_id'];
		if(isset($typeid[$id])){
			$Html = new Html();
			$vo = $Html->topic($typeid[$id]);
			if($vo){
				$url = 'index.php?g=Admin&m=Makehtml&a=topic&id='.($id+1);
				$this->success ('专题'.$typeid[$id].'生成成功，正在跳到下一专题生成',$url);
			}else{
				$this->error('专题'.$typeid[$id].'生成失败');
			}
		}else{
			unset($_SESSION['html_topic_id']);
			$this->assign ("closeWin",true);
			$this->success ('专题生成完成');
		}
	}
	//生成广告JS文件
	public function ad(){
		$Html = new Html();
		$Ad = M('ad');
    	$vo = $Ad->field('id')->select();
    	foreach ($vo as $v){
    		$Html->ad($v['id']);
    	}
    	$this->success ('广告JS生成完成');
	}
	//生成全部
	public function all(){
	}
}