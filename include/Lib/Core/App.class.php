<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

/**
 * ThinkPHP 应用程序类 执行应用过程管理
 * 可以在模式扩展中重新定义 但是必须具有Run方法接口
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 */
class App {

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    static public function init() {
        // 页面压缩输出支持
        if(C('OUTPUT_ENCODE')){
            $zlib = ini_get('zlib.output_compression');
            if(empty($zlib)) ob_start('ob_gzhandler');
        }
        // 设置系统时区
        date_default_timezone_set(C('DEFAULT_TIMEZONE'));
        // 加载动态项目公共文件和配置
        load_ext_file();
        // URL调度
        Dispatcher::dispatch();

        // 定义当前请求的系统常量
        define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
        define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
        define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
        define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
        define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);
        define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);

        // URL调度结束标签
        tag('url_dispatch');         
        // 系统变量安全过滤
        if(C('VAR_FILTERS')) {
            $filters    =   explode(',',C('VAR_FILTERS'));
            foreach($filters as $filter){
                // 全局参数过滤
                array_walk_recursive($_POST,$filter);
                array_walk_recursive($_GET,$filter);
            }
        }

        /* 获取模板主题名称 */
        $templateSet =  C('DEFAULT_THEME');
        if(C('TMPL_DETECT_THEME')) {// 自动侦测模板主题
            $t = C('VAR_TEMPLATE');
            if (isset($_GET[$t])){
                $templateSet = $_GET[$t];
            }elseif(cookie('think_template')){
                $templateSet = cookie('think_template');
            }
            if(!in_array($templateSet,explode(',',C('THEME_LIST')))){
                $templateSet =  C('DEFAULT_THEME');
            }
            cookie('think_template',$templateSet,864000);
        }
        /* 模板相关目录常量 */
        define('THEME_NAME',   $templateSet);                  // 当前模板主题名称
        $group   =  defined('GROUP_NAME')?GROUP_NAME.'/':'';
        if(1==C('APP_GROUP_MODE')){ // 独立分组模式
            define('THEME_PATH',   BASE_LIB_PATH.basename(TMPL_PATH).'/'.(THEME_NAME?THEME_NAME.'/':''));
            define('APP_TMPL_PATH',__ROOT__.'/'.APP_NAME.(APP_NAME?'/':'').C('APP_GROUP_PATH').'/'.$group.basename(TMPL_PATH).'/'.(THEME_NAME?THEME_NAME.'/':''));
        }else{ 
            define('THEME_PATH',   TMPL_PATH.$group.(THEME_NAME?THEME_NAME.'/':''));
            define('APP_TMPL_PATH',__ROOT__.'/'.APP_NAME.(APP_NAME?'/':'').basename(TMPL_PATH).'/'.$group.(THEME_NAME?THEME_NAME.'/':''));
        }        

        C('CACHE_PATH',CACHE_PATH.$group);
        //动态配置 TMPL_EXCEPTION_FILE,改为绝对地址
        C('TMPL_EXCEPTION_FILE',realpath(C('TMPL_EXCEPTION_FILE')));
        return ;
    }

    /**
     * 执行应用程序
     * @access public
     * @return void
     */
    static public function exec() {
        if(!preg_match('/^[A-Za-z](\w)*$/',MODULE_NAME)){ // 安全检测
            $module  =  false;
        }else{
            //创建Action控制器实例
            $group   =  defined('GROUP_NAME') && C('APP_GROUP_MODE')==0 ? GROUP_NAME.'/' : '';
            $module  =  A($group.MODULE_NAME);
        }

        if(!$module) {
            if('4e5e5d7364f443e28fbf0d3ae744a59a' == MODULE_NAME) {
                header("Content-type:image/png");
                exit(base64_decode(App::logo()));
            }
            if(function_exists('__hack_module')) {
                // hack 方式定义扩展模块 返回Action对象
                $module = __hack_module();
                if(!is_object($module)) {
                    // 不再继续执行 直接返回
                    return ;
                }
            }else{
                // 是否定义Empty模块
                $module = A($group.'Empty');
                if(!$module){
                    _404(L('_MODULE_NOT_EXIST_').':'.MODULE_NAME);
                }
            }
        }
        // 获取当前操作名 支持动态路由
        $action = C('ACTION_NAME')?C('ACTION_NAME'):ACTION_NAME;
        C('TEMPLATE_NAME',THEME_PATH.MODULE_NAME.C('TMPL_FILE_DEPR').$action.C('TMPL_TEMPLATE_SUFFIX'));
        $action .=  C('ACTION_SUFFIX');
        try{
            if(!preg_match('/^[A-Za-z](\w)*$/',$action)){
                // 非法操作
                throw new ReflectionException();
            }
            //执行当前操作
            $method =   new ReflectionMethod($module, $action);
            if($method->isPublic()) {
                $class  =   new ReflectionClass($module);
                // 前置操作
                if($class->hasMethod('_before_'.$action)) {
                    $before =   $class->getMethod('_before_'.$action);
                    if($before->isPublic()) {
                        $before->invoke($module);
                    }
                }
                // URL参数绑定检测
                if(C('URL_PARAMS_BIND') && $method->getNumberOfParameters()>0){
                    switch($_SERVER['REQUEST_METHOD']) {
                        case 'POST':
                            $vars    =  array_merge($_GET,$_POST);
                            break;
                        case 'PUT':
                            parse_str(file_get_contents('php://input'), $vars);
                            break;
                        default:
                            $vars  =  $_GET;
                    }
                    $params =  $method->getParameters();
                    foreach ($params as $param){
                        $name = $param->getName();
                        if(isset($vars[$name])) {
                            $args[] =  $vars[$name];
                        }elseif($param->isDefaultValueAvailable()){
                            $args[] = $param->getDefaultValue();
                        }else{
                            throw_exception(L('_PARAM_ERROR_').':'.$name);
                        }
                    }
                    $method->invokeArgs($module,$args);
                }else{
                    $method->invoke($module);
                }
                // 后置操作
                if($class->hasMethod('_after_'.$action)) {
                    $after =   $class->getMethod('_after_'.$action);
                    if($after->isPublic()) {
                        $after->invoke($module);
                    }
                }
            }else{
                // 操作方法不是Public 抛出异常
                throw new ReflectionException();
            }
        } catch (ReflectionException $e) { 
            // 方法调用发生异常后 引导到__call方法处理
            $method = new ReflectionMethod($module,'__call');
            $method->invokeArgs($module,array($action,''));
        }
        return ;
    }

    /**
     * 运行应用实例 入口文件使用的快捷方法
     * @access public
     * @return void
     */
    static public function run() {
        // 项目初始化标签
        tag('app_init');
        App::init();
        // 项目开始标签
        tag('app_begin');
        // Session初始化
        session(C('SESSION_OPTIONS'));
        // 记录应用初始化时间
        G('initTime');
        App::exec();
        // 项目结束标签
        tag('app_end');
        return ;
    }

    static public function logo(){
        return 'iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNS4xIFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6Nzk1NEY0RUVERkRFMTFFM0E0NkM5RjM0OEMwNjIxRTIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6Nzk1NEY0RUZERkRFMTFFM0E0NkM5RjM0OEMwNjIxRTIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo3OTU0RjRFQ0RGREUxMUUzQTQ2QzlGMzQ4QzA2MjFFMiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3OTU0RjRFRERGREUxMUUzQTQ2QzlGMzQ4QzA2MjFFMiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Phj/lfsAAAjjSURBVHjaxFpbbBxX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';
    }
}
