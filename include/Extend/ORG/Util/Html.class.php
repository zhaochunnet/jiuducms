<?php
/**
 +------------------------------------------------------------------------------
 * 网站静态生成类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: Html.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class Html{
	public function __construct(){
		import('Path');
		import('Home');
	}
	//生成首页
	public static function index(){
		import('Index');
		$Index = new IndexAction();
		$Index->pathname=SITE_PATH.C('JIUDU_CMSPATH').'/index'.C('HTML_FILE_SUFFIX');
		$Index->_before_index();
		return $Index->index();
	}
	//生成栏目页
	public static function arctype($id){
		import('List');
		$List = new ListAction();
		$List->htmlid = $id;
		$Arctype = M('Arctype');
		$type = $Arctype->field('id,listnum,ispart')->where('isdefault=1')->find($List->htmlid);
		//如果栏目不是列表页直接生成单页跳出
		if($type['ispart']!=2){
			$List->pathname=SITE_PATH.Path::arctype($List->htmlid);
			$List->_before_index();
			return $List->index();
		}
		$page = M('archives')->where('typeid='.$List->htmlid)->count('id');
		$pagenum = ceil($page/$type['listnum']);
		if($pagenum>1){
			//如果分页大于2页，则循环生成分页
			for ($i=1;$i<=$pagenum;$i++){
				$_REQUEST['p'] = $i;
				$List->pathname = SITE_PATH.Path::arctype($List->htmlid,$i);
				$List->_before_index();
				$List->index();
			}
			return true;
		}
		$List->pathname=SITE_PATH.Path::arctype($List->htmlid);
		$List->_before_index();
		return $List->index();
	}
	//生成内容页
	public static function archives($id,$num=''){
		import('Article');
		$Article = new ArticleAction();
		if(is_array($id)){
			//如果是多个ID的数组，则循环生成
			foreach ($id as $k=>$v){
				$Article->htmlid = $v;
				$Article->pathname=SITE_PATH.Path::archives($Article->htmlid);
				if($Article->pathname){//路径为空不生成
					$Article->_before_index();
					$Article->index();
				}
				$ks = $k;
			}
			if($num !== ''){
				return $num+1;
			}else{
				return true;
			}
		}else{
			//如果是单个ID，则生成后退出
			$Article->htmlid = $id;
			$Article->pathname=SITE_PATH.Path::archives($Article->htmlid);
			if($Article->pathnam){
				$Article->_before_index();
				return $Article->index();
			}
		}
	}
	//生成专题页
	public function topic($id){
		import('Topic');
		$Topic = new TopicAction();
		$Topic->htmlid = $id;
		$Topic->pathname=SITE_PATH.Path::topic($Topic->htmlid);
		$Topic->_before_index();
		return $Topic->index();
	}
	//生成广告文件
	public function ad($id){
		import('Url');
    	$vo = $M('ad')->getById($id);
    	$url = Url::adurl($id);
		if($vo['adtype'] == 0){
			$width = $vo['pic_width'] ?' width="'.$vo['pic_width'].'"':null;
			$height = $vo['pic_height'] ?' height="'.$vo['pic_height'].'"':null;
	    	$html= '<a href="'.$url.'" target="'.$vo['target'].'"><img src="'.$vo['picurl'].'"'.$width.$height.' alt="'.$vo['alt'].'" title="'.$vo['alt'].'" /></a>';
	    }elseif($vo['adtype'] == 1){
	    	$html= '<a href="'.$url.'" target="'.$vo['target'].'">'.$vo['text'].'</a>';
	    }elseif($vo['adtype'] == 2){
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