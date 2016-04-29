<?php
/**
 * 前台公共方法
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class HomeAction extends Action {
	public $tempfile;
	public $typeid;
	public $pathname;
	function _initialize(){
		if(C('JIUDU_OFFLINE')){
			$this->assign('val',C('JIUDU_OFFLINEMESSAGE'));
			$this->display(C('TMPL_ACTION_OFFLINE'));
			exit;
		}
		import('Switcher');
		$s = new Switcher();
		C('TEMPLETS_CHECK_TYPE',$s->t());
		import('Url');
	}
	public function index(){
		$tempfile = $this->gettempurl($this->tempfile);
		$this->assign('newsnav',$this->newsnav());
		$this->assign('global',$this->getgloballabel());
		$this->display($tempfile,'','','',C('TEMPLETS_CHECK_TYPE').'_');
	}
	protected function getgloballabel(){
		$vo = C();
		foreach ($vo as $k=>$v){
			if(substr($k,0,6) == 'jiudu_'){
				$global[$k] = $v;
			}
		}
		unset($vo);
		$global['jiudu_tempdir'] = C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE').'/'.C('TEMPLETS_CHECK_TYPE');
		return $global;
	}
	//获取模版路径
	private function gettempurl($tempfile){
		$filename = C('TEMPLETS_PATH').'/'.C('TEMPLETS_STYLE').'/'.C('TEMPLETS_CHECK_TYPE').'/'.$tempfile;
		$tempname = SITE_PATH.'/'.$filename;
		if(!is_file($tempname)){
			halt(array('message'=>$tempfile.'模板不存在','file'=>$filename));
		}
		return $tempname;
	}
	//获取当前位置	
	private function newsnav($id='',$newsnav=''){
		$typeid = $id ? $id : $this->typeid;
		if(!$typeid){
			return '';
		}
		$vo =  M('Arctype')->field('id,pid,typename')->find($typeid);
		$newsnav = ' &gt; <a href="'.Url::arctype($vo['id']).'">'.$vo['typename'].'</a>'.$newsnav;
		if($vo['pid']){
			return $this->newsnav($vo['pid'],$newsnav);
		}else{
			return '<a href="'.Url::index().'">'.C('JIUDU_INDEXNAME').'</a>'.$newsnav;
		}
	}
	//内链替换
	protected function key($con){
		if(!C('JIUDU_CONKEY')){
			return $con;
			exit;
		}
		$filename = SITE_PATH.'/'.C('UPLOAD_PATH').'/keywords.xml';
		if(!$_SESSION['archives_keywords'] && is_file($filename)){
			import ('GetXmlData');
			$xml = new GetXmlData();
			$_SESSION['archives_keywords'] = $xml->readDatabase($filename);
		}
		$keywords = $_SESSION['archives_keywords'];
		foreach ($keywords as $k=>$v){
			$con=preg_replace('/'.$v['name'].'/','<a target="_blank" title="'.$v['name'].'" href="'.$v['url'].'">'.$v['name'].'</a>',$con,$v['rate']);
		}
		return $con;
	}
}
?>