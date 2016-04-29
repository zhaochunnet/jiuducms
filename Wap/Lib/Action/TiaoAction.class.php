<?php
/**
 * 前台公共方法
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class TiaoAction extends Action {
	private $vo;
	public function __construct(){
		if(C('JIUDU_EDITION_SWITCH')){
			$filename = SITE_PATH.'/Uploads/Edition/edition.inc';
			$data = file_get_contents($filename);
			$this->vo = json_decode($data,true);
		}else{
			exit;
		}
	}
	public function index(){
		$volist = $this->vo;
		foreach($volist as $k=>$v){
			if($v['type'] =='Home' && $v['tid'] !=5){
				if(!preg_match("/(".strtolower($v['device']).")/i", strtolower($_SERVER[$v['value']]))){
					$phoneurl = C('JIUDU_WAP_BASEHOST') ? C('JIUDU_WAP_BASEHOST') : C('JIUDU_BASEHOST').'/Wap';
					if(C('JIUDU_ZNTIAO_SWITCH')){
						$where['pcurl'] = $_SERVER['HTTP_REFERER'];
						$where['status'] = 1;
						$pcurl = M('Zntiao')->where($where)->getField('phoneurl');
						$url = $pcurl ? $pcurl : $phoneurl;
						echo 'window.location.href = "'.$url.'"'."\n";
					}else{
						if($_GET['type'] && $_GET['id']){
							$phoneurl .= '/?m='.ucfirst($_GET['type']).'&id='.$_GET['id'];
						}
						echo 'window.location.href = "'.$phoneurl.'"'."\n";
					}
				}
			}	
		}
	}
}
?>