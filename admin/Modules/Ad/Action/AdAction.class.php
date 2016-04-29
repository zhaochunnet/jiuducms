<?php
/**
 * 站内广告管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class AdAction extends AdminAction {
	public function index(){
		$voList = M('Ad')->field(C('DB_PREFIX').'ad.id AS id,title,type,endtime,onclick,remark,name')->join(C('DB_PREFIX').'ad_type ON typeid= '.C('DB_PREFIX').'ad_type.id')->select();
		$type = array('图片广告','文字广告','代码广告');
		$this->assign('type',$type);
		$this->assign('list', $voList );
		$this->display();
	}
	public function add(){
		if(IS_POST && IS_AJAX){
			$_POST['starttime'] = strtotime($_POST['starttime']);
			$_POST['endtime'] = strtotime($_POST['endtime']);
			$model = D('Ad');
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$list = $model->add ();
			if ($list !== false) { //保存成功
				$this->makedo($list);
				$this->success('新增成功!');
			} else {
				//失败提示
				$this->error('新增失败!');
			}
		}else{
			$value = M('AdType')->select();
			$this->assign('date',date('Y-m-d'));
			$this->assign('value',$value);
			$this->display();
		}
    }
    public function update() {
    	if(IS_POST && IS_AJAX){
    		$_POST['starttime'] = strtotime($_POST['starttime']);
    		$_POST['endtime'] = strtotime($_POST['endtime']);
    		$model = D ('Ad');
    		$data = $model->create ();
    		if(false === $data) {
    			$this->error ( $model->getError () );
    		}
    		// 更新数据
    		$where['id'] = $_POST['id'];
    		if(false !== $model->where($where)->data($data)->save()) {
    			//成功提示
    			$this->makedo($_POST['id']);
    			$this->success ('编辑成功!');
    		}else{
    			//错误提示
    			$this->error ('编辑失败!');
    		}
    	}else{
    		$this->assign('value',M('AdType')->select());
    		$vo = M('Ad')->find($_GET['id']);
    		$this->assign('vo',$vo);
    		$this->display();
    	}
    }
    //广告预览
    public function preview(){
    	$vo = M('ad')->find($_GET['id']);
    	if($vo['endtime'] == 0 || $vo['endtime'] > time()){
	    	if($vo['type'] == 0){
	    		$width = $vo['pic_width'] ?' width="'.$vo['pic_width'].'"':null;
	    		$height = $vo['pic_height'] ?' height="'.$vo['pic_height'].'"':null;
	    		echo '<a href="'.$vo['url'].'" target="_blank"><img src="'.$vo['picurl'].'"'.$width.$height.' alt="'.$vo['alt'].'" title="'.$vo['alt'].'" /></a>';
	    	}elseif($vo['type'] == 1){
	    		echo '<a href="'.$vo['url'].'" target="_blank">'.$vo['text'].'</a>';
	    	}elseif($vo['type'] == 2){
	    		echo $vo['htmlcode'];
	    	}else{
	    		echo '广告类型有误';
	    	}
    	}else{
    		echo $vo['reptext'];
    	}
    }
    //广告生成
    public function make(){
    	$id=$_GET['id'];
		if($this->makedo($id)){
			$this->success ('生成成功');
		}else{
			$this->error('生成失败');
		}
    }
    //广告生成方法
    private function makedo($id){
    	import('Url');
    	$vo = M('ad')->getById($id);
    	$url = Url::adurl($id);
    	if($vo['type'] == 0){
    		$width = $vo['pic_width'] ?' width="'.$vo['pic_width'].'"':null;
    		$height = $vo['pic_height'] ?' height="'.$vo['pic_height'].'"':null;
    		$html= '<a href="'.$url.'" target="'.$vo['target'].'"><img src="'.$vo['picurl'].'"'.$width.$height.' alt="'.$vo['alt'].'" title="'.$vo['alt'].'" /></a>';
    	}elseif($vo['type'] == 1){
    		$html= '<a href="'.$url.'" target="'.$vo['target'].'">'.$vo['text'].'</a>';
    	}elseif($vo['type'] == 2){
    		$html= $vo['htmlcode'];
    	}else{
    		$html= '广告类型有误';
    	}
    	$html = addslashes($html);
    	if($vo['endtime']){
    		$val = 'var myDate = new Date();time = parseInt(myDate.getTime()/1000);starttime = '.$vo['starttime'].';endtime = '.$vo['endtime'].';if(starttime>time){document.write("广告还没有开始");}else if(endtime<time){document.write("'.addslashes($vo['reptext']).'");}else{document.write("'.$html.'");};';
    	}else{
    		$val = 'document.write("'.$html.'");';
    	}
    	$filename = SITE_PATH.'/'.C('UPLOAD_PATH').'/advert/jiuduad'.$id.'.js';
    	if(!is_dir(dirname($filename))){
    		mk_dir(dirname($filename));
    	}
    	return write($filename,$val);
    }
}