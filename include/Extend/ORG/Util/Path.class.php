<?php
/**
 +------------------------------------------------------------------------------
 * Path 获取静态文档目录类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: Path.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class Path {
	//获取首页路径
	public static function index(){
		$url = C('JIUDU_INDEXURL');
		return $url;
	}
	//栏目页路径
	public static function arctype($id,$page=''){
		$model = M('arctype');
		$viod = $model->field('ispart,typedir,referpath,siteurl,isdefault,listrule,defaultname')->find($id);
		if($viod['ispart']==3){
			return null;
		}elseif($viod['isdefault']==1){
			$typedir = self::parsingrule2($viod['typedir']);
			if($page>1){
				$url = self::parsingrule2($viod['listrule'],$typedir,$id,$page).C('HTML_FILE_SUFFIX');
			}else{
				$url = $typedir.'/'.$viod['defaultname'].C('HTML_FILE_SUFFIX');
			}
		}else{
			$url = null;
		}
		return $url;
	}
	//文章页路径
	public static function archives($id){
		$model = M('archives');
		$viod = $model->field('url,pubdate,typedir,referpath,siteurl,isdefault,arctrule')->join(C('DB_PREFIX').'arctype ON '.C('DB_PREFIX').'arctype.id='.C('DB_PREFIX').'archives.typeid')->where(C('DB_PREFIX').'archives.id='.$id)->find();
		
		if($viod['isdefault']==1){
			$typedir = self::parsingrule($viod['typedir']);
			$url = self::parsingrule($viod['arctrule'],$typedir,$id,$viod['pubdate']).C('HTML_FILE_SUFFIX');
		}else{
			$url = null;
		}
		return $url;
	}
	//专题页路径
	public static function topic($id){
		$model = $viod = M('topic');
		$viod = $model->field('typedir,isdefault,zturl')->find($id);
		$typedir = self::parsingrule2($viod['typedir']);
		$url = $typedir.'/index'.C('HTML_FILE_SUFFIX');
		return $url;
	}
	//解析文章URL规则
	public static function parsingrule($temphtml,$typedir='',$id='',$pubdate=0){
		$search = array ('/{cmspath}/i','/{typedir}/i','/{aid}/i','/{y}/i','/{m}/i','/{d}/i','/{time}/i');
		$replace = array (C('JIUDU_CMSPATH'),$typedir,$id,date('Y',$pubdate),date('m',$pubdate),date('d',$pubdate),$pubdate);
		return preg_replace($search,$replace,$temphtml);
	}
	//解析栏目URL规则
	public static function parsingrule2($temphtml,$typedir='',$id='',$page=''){
		$search = array ('/{cmspath}/i','/{typedir}/i','/{tid}/i','/{p}/i');
		$replace = array (C('JIUDU_CMSPATH'),$typedir,$id,$page);
		return preg_replace($search,$replace,$temphtml);
	}
}