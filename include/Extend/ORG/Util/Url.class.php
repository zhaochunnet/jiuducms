<?php
/**
 +------------------------------------------------------------------------------
 * URL生成类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Net
 * @author    struggle <struggle@9ducx.com>
 * @version   $Id: Url.class.php 2012-11-7 14:59:02Z struggle $
 +------------------------------------------------------------------------------
 */
class Url {
	//获取首页URL
	public static function index(){
		$url = C('JIUDU_INDEXURL');
		return $url;
	}
	//栏目页URL
	public static function arctype($id,$page=1){
		if(!$id){
			return C('JIUDU_CMSPATH');
		}
		$viod = M('Arctype')->field('ispart,typedir,referpath,typeurl,isdefault,listrule,defaultname')->find($id);
		if($viod['ispart']==3){
			return $viod['typeurl'];
		}elseif($viod['isdefault'] == 1){
			$typedir = self::parsingrule2($viod['typedir']);
			if($page>1){
				$url = self::parsingrule2($viod['listrule'],$typedir,$id,$page).C('HTML_FILE_SUFFIX');
			}else{
				$url = $typedir.'/';
			}
		}else{
			if($page>1){
				$url = C('JIUDU_CMSPATH').'/index.php?m=List&id='.$id.'&p='.$page;
			}else{
				$url = C('JIUDU_CMSPATH').'/index.php?m=List&id='.$id;
			}
		}
		return $url;
	}
	//文章页URL
	public static function archives($id){
		$viod = M('Archives')->field('url,pubdate,typedir,isdefault,arctrule')->join(C('DB_PREFIX').'arctype ON '.C('DB_PREFIX').'arctype.id='.C('DB_PREFIX').'archives.typeid')->where(C('DB_PREFIX').'archives.id='.$id)->find();
		if($viod['url']){
			return $viod['url'];
		}elseif($viod['isdefault']==1){
			$typedir = self::parsingrule($viod['typedir']);
			$url = self::parsingrule($viod['arctrule'],$typedir,$id,$viod['pubdate']).C('HTML_FILE_SUFFIX');
		}elseif($viod['isdefault']==0){
			$url = C('JIUDU_CMSPATH').'/index.php?m=Article&id='.$id;
		}
		return $url;
	}
	//专题URL
	public static function topic($id){
		$viod = M('Topic')->field('typedir,isdefault,zturl')->find($id);
		if($viod['zturl']){
			return $viod['zturl'];
		}elseif($viod['isdefault']==1){
			$typedir = self::parsingrule2($viod['typedir']);
			$url = $typedir.'/';
		}elseif($viod['isdefault']==0){
			$url = C('JIUDU_CMSPATH').'/index.php?m=Topic&id='.$id;
		}
		return $url;
	}
	//搜索页URL
	public static function search($keyword){
		$url = C('JIUDU_CMSPATH').'/index.php?m=Search&keyword='.$keyword;
		return $url;
	}
	//评论URL
	public static function comment($id){
		$viod = M('Comment')->field('id,aid')->find($id);
		$url = self::archives($viod['aid']).'#comment-'.$id;
		return $url;
	}
	//广告url
	public static function adurl($id){
		$url = C('JIUDU_CMSPATH').'/index.php?m=Public&a=adurl&id='.$id;
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