<?php
/**
 * 系统公共方法
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class CommonAction extends Action {
    public function _initialize() {
    	import('Input');
    	//初始化站点
    	$this->initSite();
        //数据处理
        $this->__input();
    }
	/**
     * 对 get post 等进行处理
     */
    final private function __input() {
        $_POST = $this->app__post($_POST);
        $_GET = $this->app__post($_GET);
    }

    /**
     *  数据处理
     *  如果 magic_quotes_gpc 为开启状态，可以反转义字符串
     */
    final private function app__post($post) {
        if (!is_array($post)) {
            return $post;
        }
        foreach ($post as $k => $v) {
            if (is_array($v)) {
                $post[$k] = $this->app__post($v);
            } else {
                $post[$k] = Input::getVar($v);
            }
        }
        return $post;
    }
    /**
     * 初始化站点配置信息
     */
    final protected function initSite() {

    }
    /**
     *  生成静态页面
     * @access protected
     * @htmlfile 生成的静态文件名称
     * @htmlpath 生成的静态文件路径
     * @param string $templateFile 指定要调用的模板文件
     * 默认为空 由系统自动定位模板文件
     * @return string
     */
    public function buildHtml($htmlfile='',$templateFile=''){
    	$content = $this->fetch($templateFile);
    	if(!is_dir(dirname($htmlfile))){
    		// 如果静态目录不存在 则创建
    		mk_dir(dirname($htmlfile),0755);
    	}
    	if(false === write($htmlfile,$content)){
    		return false;
    	}else{
    		return true;
    	}
    }
}
?>