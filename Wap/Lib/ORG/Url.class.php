<?php
/**
 * Wap版 URL 控制类
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class Url {
	//获取首页URL
	public static function index(){
		$url = C('JIUDU_INDEXURL');
		return $url;
	}
	//栏目页URL
	public static function arctype($id,$page=''){
		$viod = M('Arctype')->field('typedir')->find($id);
		if($viod['ispart']==3){
			return $viod['typedir'];
		}else{
			if($page>1){
				$url = 'index.php?m=List&id='.$id.'&p='.$page;
			}else{
				$url = 'index.php?m=List&id='.$id;
			}
		}
		return $url;
	}
	//文章页URL
	public static function archives($id){
		$viod = M('Archives')->field('url')->where('id='.$id)->find();
		if($viod['url']){
			return $viod['url'];
		}else{
			$url = 'index.php?m=Article&id='.$id;
		}
		return $url;
	}
	//专题URL
	public static function topic($id){
		$viod = M('Topic')->field('zturl')->find($id);
		if($viod['zturl']){
			$url = $viod['zturl'];
		}else{
			$url = 'index.php?m=Topic&id='.$id;
		}
		return $url;
	}
	//搜索页URL
	public static function search($id){
		$url = 'index.php?m=Search&keyword='.$id;
		return $url;
	}
	//广告url
	public static function adurl($id){
		$url = 'index.php?m=Public&a=adurl&id='.$id;
		return $url;
	}
}