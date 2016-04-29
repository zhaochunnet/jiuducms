<?php
/**
 * 文档管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class ArchivesAction extends AdminAction {
	public $cat;
	public function _initialize() {
		parent::_initialize();
		import("Category");
		$this->cat = new Category('Arctype', array('id', 'pid', 'typename', 'fullname'));
	}
	function _before_index(){
		$value = $this->cat->getList('id,pid,typename,ispart','status=1');
		foreach ($value as $v){
			$type[$v['id']] = $v['typename'];
		}
		$this->assign('type',$type);
		$this->assign('value',$value);
	}
	function _filter(&$map){
		$where = $map;
		$map['title'] = array('like',"%".$_REQUEST['title']."%");
		$where['title'] = $_REQUEST['title'];
		$this->assign ('where',$where);
	}
	public function add(){
		if(IS_POST && IS_AJAX){
			$this->model_post();
			if ($_POST['aid']===false) {
				$this->error ('新增失败，请检查模型问题');
			}
			if($_POST['litpic']){
				$_POST['flags'][]='p';
				$_POST['flags'] = array_flip(array_flip($_POST['flags']));
			}
			if(!in_array('j',$_POST['flags'])){
				$_POST['url'] = NULL;
			}
			$_POST['flag'] = implode(',',$_POST['flags']);
			$_POST['source'] = $_POST['dis_source'];
			$_POST['writer'] = $_POST['district_writer'];
			$_POST['pubdate'] = strtotime($_POST['pubdate']);
			$_POST['uid'] = $_SESSION[C('USER_AUTH_KEY')];
			$model = D('Archives');
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$list = $model->add ();
			if ($list!==false) { //保存成功
				$this->makehtml($list,'add');
				$this->success ('新增成功!');
			} else {
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$flag = C('ARCHIVES_FLAG');
			foreach ($flag as $k => $v){
				$flags[] = array('k'=>$k,'v'=>$v);
			}
			$modelinfo = $this->model_get($_GET['typeid']);
			$this->assign('click',mt_rand(50,100));
			$this->assign('date',date('Y-m-d H:i:s'));
			$this->assign('flags',$flags);
			if($_GET['typeid']){
				$this->display('','','','',$modelinfo['id'].'_');
			}else{
				$this->display();
			}
		}
    }
	//编辑文章
	public function update(){
		if(IS_POST && IS_AJAX){
			//保存当前数据对象
			if ($this->model_post() === false) {
				$this->error ('修改失败!');
			}
			if($_POST['litpic']){
				$_POST['flags'][]='p';
				$_POST['flags'] = array_flip(array_flip($_POST['flags']));
			}
			if(!in_array('j',$_POST['flags'])){
				$_POST['url'] = NULL;
			}
			$_POST['flag'] = implode(',',$_POST['flags']);
			$_POST['source'] = $_POST['dis_source'];
			$_POST['writer'] = $_POST['district_writer'];
			$_POST['pubdate'] = strtotime($_POST['pubdate']);
			$model = D ('Archives');
			$data = $model->create ();
			if (false === $data) {
				$this->error ( $model->getError () );
			}
			// 更新数据
			if (false !== $model->where('id = '.$_POST['id'])->data($data)->save()) {
				//成功提示
				$this->makehtml($_POST['id']);
				$this->success ('编辑成功!');
			} else {
				//错误提示
				$this->error ('编辑失败!');
			}
		}else{
			$flag = C(ARCHIVES_FLAG);
			$id = $_GET ['id'];
			$vo = M('Archives')->find($id);
			$flaga = explode(',',$vo['flag']);
			foreach ($flaga as $v){
				$f[$v] = 1;
			}
			foreach ($flag as $k => $v){
				$flags[] = array('k'=>$k,'v'=>$v,'t'=>$f[$k]);
			}
			$modelinfo = $this->model_get($vo);
			$this->assign('flags',$flags);
			$this->assign ('vo',$vo);
			$this->display ('','','','',$modelinfo['id'].'_');
		}
		
    }
    public function _before_foreverdelete(){
		$vo = M('archives')->field('aid,typeid')->find($_GET['id']);
		$getmodelinfo=$this->getmodelinfodo($vo['typeid']);
		$model = D($getmodelinfo['addtable']);
		if($model->delete($vo['aid']) === false){
			$this->error ('删除失败！');
		}
    }
    public function public_modelinfo(){
		$vo = $this->getmodelinfodo($_GET['id']);
		$filename = SITE_PATH.'/'.C('TEMPLETS_PATH').'/plus/conmodel_'.$vo['id'].'.html';
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->display($filename);
    }
    //查询文档附加表信息
    private function getmodelinfodo($id){
		$cid = $id ? M('Arctype')->where('id='.$id)->getField('cid') : 1;
		$vo = M('conmodel')->field('id,udid,addtable,fieldset')->find($cid);
		return $vo;
    }
    //批量操作文档
    public function caozuo(){
    	$Archives = M('Archives');
    	if(!$_POST['type']){
    		$this->error ('没有选择操作类型!');
    		exit;
    	}
    	if(!$_POST['c1']){
    		$this->error ('没有选择文档!');
    		exit;
    	}
    	$id = implode(',',$_POST['c1']);
    	//1  删除 2 移动 3 推荐 4 推荐
    	if($_POST['type'] == 1){
    		if($Archives->delete($id)){
				$this->success ('批量删除成功!');
    		}else{
    			$this->error ('批量删除失败!');
    		}
    	}elseif($_POST['type'] == 2){
    		if(!$_POST['typeid']){
    			$this->error ('选择要移动到的栏目!');
    			exit;
    		}
			$data = array('typeid'=>$_POST['typeid']);
    		if($Archives->where('id in('.$id.')')->data($data)->save()){
				$this->success ('批量文档移动成功!');
    		}else{
    			$this->error ('批量文档移动失败!');
    		}
    	}elseif($_POST['type'] == 3){
    		$this->error ('功能正在制作中!');
    	}elseif($_POST['type'] == 4){
    		if($this->makehtml($_POST['c1'])){
    			$this->success ('更新HTML文档成功!');
    		}else{
    			$this->error ('更新HTML文档失败!');
    		}
    	}else{
    		$this->error ('非法操作!');
    	}
    	exit;
    }
    public function updateflag(){
    	$archives = M ('Archives');
    	if(IS_POST && IS_AJAX){
    		$_POST['flag'] = implode(',',$_POST['flags']);
    		$data = array('flag'=>$_POST['flag']);
    		if($archives->where('id ='.$_POST['id'])->data($data)->save()){
    			$this->success ('编辑属性成功!');
    		}else{
    			$this->error ('编辑属性失败!');
    		
    		}
    	}else{
    		$flag = C(ARCHIVES_FLAG);
    		$id = $_GET['id'];
    		$vo = $archives->field('flag')->find($id);
    		$flaga = explode(',',$vo['flag']);
    		foreach ($flaga as $v){
    			$f[$v] = 1;
    		}
    		foreach ($flag as $k => $v){
    			$flags[] = array('k'=>$k,'v'=>$v,'t'=>$f[$k]);
    		}
    		$this->assign('id',$id);
    		$this->assign('flags',$flags);
    		$this->display();
    	}
    }
    //移动文章
    public function move(){
    	if(IS_POST && IS_AJAX){
    		$data = array('typeid'=>$_POST['typeid']);
    		if(M('Archives')->where('id ='.$_POST['id'])->data($data)->save()){
    			$this->success('移动成功!');
    		}else{
    			$this->error('移动失败!');
    		}
    	}else{
    		$voList = $this->cat->getList('id,pid,typename,ispart','status=1');
    		$this->assign('id',$_GET['id']);
    		$this->assign('value',$voList);
    		$this->display();
    	}
    }
    //模型处理方法
    private function model_post(){
    	$addon = $_POST['addon'];
    	if(isset($_POST['model_checkbox'])){
    		$_POST['model_checkbox'] = array_filter($_POST['model_checkbox']);
    		foreach ($_POST['model_checkbox'] as $v){
    			$addon[$v] = $addon[$v] ? join(',',$addon[$v]) : '';
    		}
    	}
    	if(isset($_POST['model_image'])){
    		$_POST['model_image'] = array_filter($_POST['model_image']);
    		foreach ($_POST['model_image'] as $v){
    			foreach ($addon[$v.'_pic'] as $pk=>$pv){
    				$addon[$v][] = array('pic'=>$pv,'text'=>$addon[$v.'_text'][$pk]);
    			}
    			$addon[$v] = $addon[$v] ? serialize($addon[$v]) : '';
    		}
    	}
    	if(isset($_POST['model_file'])){
    		$file = $_POST['model_file'];
    		foreach ($addon[$file.'_list1_url'] as $pk=>$pv){
    			$addon[$file.'_list1'][] = array('host'=>$addon[$file.'_list1_host'][$pk],'url'=>$pv,'name'=>$addon[$file.'_list1_name'][$pk],'status'=>($addon[$file.'_list1_status'][$pk]+0));
    		}
    		$addon[$file.'_list1'] = $addon[$file.'_list1'] ? serialize($addon[$file.'_list1']) : '';
    		foreach ($addon[$file.'_list2_url'] as $pk=>$pv){
    			if($pv){
    				$addon[$file.'_list2'][] = array('url'=>$pv,'name'=>$addon[$file.'_list2_name'][$pk]);
    			}
    		}
    		$addon[$file.'_list2'] = $addon[$file.'_list2'] ? serialize($addon[$file.'_list2']) : '';
    		if($addon['softurl_local'] && !$addon['softsize']){
    			$softsize = @filesize(SITE_PATH.$addon['softurl_local']);
    			$addon['softsize'] = $softsize ? byte_format($softsize) : '';
    		}
    	}
        $modelinfo = $this->getmodelinfodo($_POST['typeid']);
    	$model = M($modelinfo['addtable']);
    	$data = $model->create($addon);
    	if(false === $data){
    		$this->error($model->getError());
    	}
    	$_POST['cid'] = $modelinfo['id'];
    	if(ACTION_NAME == 'update'){
    		return $model->where('id='.$_POST['aid'])->data($data)->save();
    	}elseif(ACTION_NAME == 'add'){
    		$_POST['aid'] = $model->add($data);
    	}
    }
    private function model_get($vo){
    	if((ACTION_NAME == 'add' && $_GET['typeid']) || ACTION_NAME == 'update'){
    		$modelinfo=$this->getmodelinfodo($vo['typeid']);
    		$filename = SITE_PATH.'/'.C('TEMPLETS_PATH').'/plus/conmodel_'.$modelinfo['id'].'.html';
    	}
    	if(ACTION_NAME == 'update'){
    		$val = M($modelinfo['addtable'])->find($vo['aid']);
    		$this->assign('addon',$val);
    		$filename = SITE_PATH.'/'.C('TEMPLETS_PATH').'/plus/conmodel_'.$modelinfo['id'].'.html';
    		$this->assign('fieldset',$filename);
    	}elseif(ACTION_NAME == 'add'){
    		$where['status'] = 1;
    		if($vo['typeid']){
    			$where['cid'] = $modelinfo['id'];
    			$this->assign('typeid',$vo['typeid']);
    			$this->assign('fieldset',$filename);
    		}
    		$value = $this->cat->getList('id,pid,typename,ispart',$where);
    		$this->assign('value',$value);
    	}
    	return $modelinfo;
    }
    //生成静态
    private function makehtml($id,$method='',$type='archives'){
    	import('Html');
    	C('DIY_TEMPLATE_NAME',true);
		if($method == 'add'){
			if(C('JIUDU_MAKE_INDEX')){
				Html::index();
			}
			if(C('JIUDU_MAKE_ANDCAT')){
				Html::arctype($_POST['typeid']);
			}
			$id = C('JIUDU_MAKE_PRENEXT') ? $this->context($id) : $id;
		}
		$vo = Html::$type($id);
		C('DIY_TEMPLATE_NAME',null);
		return $vo;
    }
    //获取上下文ID
    private function context($id){
    	$info[] = $id;
    	$info[] = M('archives')->where('id<'.$id.' AND typeid='.$_POST['typeid'])->order('id desc')->getField('id');
    	$info[] = M('archives')->where('id>'.$id.' AND typeid='.$_POST['typeid'])->order('id asc')->getField('id');
    	return array_filter($info);
    }
}