<?php

/**
 * Switcher 多版本切换
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class Switcher {
	private $vo;
	public function __construct(){
		$filename = SITE_PATH.'/Uploads/Edition/edition.inc';
		$data = file_get_contents($filename);
		$this->vo = json_decode($data,true);
	}
	public function t(){
		$t = $_GET['t'];
		if(!empty($t) || !empty($_SESSION['switcher_t'])){
			$_SESSION['switcher_t'] = !empty($t) ? $t : $_SESSION['switcher_t'];
			return  strtoupper($_SESSION['switcher_t']);
			exit();
		}
		if(C('JIUDU_EDITION_SWITCH')){
			return $this->check();
		}else{
			$where['type'] = 'Wap';
			$where['isdefault'] = 1;
			$udid = M('Edition')->where($where)->getField('udid');
			return $udid;
		}
	}
	private function check(){
		$volist = $this->vo;
		$pcurl = null;
		if(C('JIUDU_ZNTIAO_SWITCH')){
			$where['phoneurl'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$where['status'] = 1;
			$pcurl = M('Zntiao')->where($where)->getField('pcurl');
		}
		foreach($volist as $v){
			if($v['type'] == 'Home'){
				if(preg_match("/(".strtolower($v['device']).")/i", strtolower($_SERVER[$v['value']]))){
					if($v['iscore'] == 1){
						$urltype = array('Article'=>'archives','List'=>'arctype','Topic'=>'topic','Search'=>'search');
						$type = $urltype[MODULE_NAME];
						$url = C('JIUDU_BASEHOST');
						if($type){
							import('HomeUrl');
							if($type == 'search'){
								$_GET['id'] = $_REQUEST['keyword'];
							}
							$url .= Url::$type($_GET['id']);
						}
						redirect($url);
					}else{
						$url = $pcurl ? $pcurl : $v['url'];
						redirect($url);
					}
				}
			}
		}
		foreach($volist as $v){
			if($v['type'] == 'Wap'){
				if($v['isdefault'] == 1){
					$default = $v['udid'];
				}
				if($v['tid'] == 5){
					$mobileHeaders = explode('|',strtoupper($v['device']));
					foreach($mobileHeaders as $val){
						if(isset( $_SERVER [$val])){
							return $v['udid'];
						}
					}
				}else{
					if(preg_match("/(".strtolower($v['device']).")/i", strtolower($_SERVER[$v['value']]))){
						return $v['udid'];
					}
				}
			}
		}
		return $default;
	}
}

?>
