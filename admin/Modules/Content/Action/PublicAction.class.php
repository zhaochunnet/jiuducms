<?php
/**
 * 公共操作
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class PublicAction extends Action {
	// 检查用户是否登录
	function _initialize() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			if(IS_AJAX){
        		$data['status'] = 301;
        		$this->ajaxReturn($data);
        	}else{
        		redirect( PHP_FILE.C('USER_AUTH_GATEWAY'));
        	}
		}
	}
	//选择作者
	public function writer(){
		$info = explode(',',C('ARCHIVES_WRITER'));
		$this->assign('info',$info);
		$this->display();
    }
	//选择来源
	public function source(){
		$info = explode(',',C('ARCHIVES_SOURCE'));
		$this->assign('info',$info);
		$this->display();
    }
    //获取常用汉字拼音
    public function pinyin(){
    	import("Pinyin");
    	$py = new Pinyin();
    	$data = strtolower($py->output($_GET['name']));
    	if($_GET['type']){
    		return $data;
    	}else{
    		echo $data;
    	}
    }
}
?>