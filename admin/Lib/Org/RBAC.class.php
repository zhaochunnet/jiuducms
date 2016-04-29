<?php
/**
 +------------------------------------------------------------------------------
 * 基于角色的数据库方式验证类
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: RBAC.class.php 2947 2012-05-13 15:57:48Z liu21st@gmail.com $
 +------------------------------------------------------------------------------
 */
// 配置文件增加设置
// USER_AUTH_ON 是否需要认证
// USER_AUTH_TYPE 认证类型
// USER_AUTH_KEY 认证识别号
// REQUIRE_AUTH_MODULE  需要认证模块
// NOT_AUTH_MODULE 无需认证模块
// USER_AUTH_GATEWAY 认证网关
// RBAC_DB_DSN  数据库连接DSN
// RBAC_ROLE_TABLE 角色表名称
// RBAC_USER_TABLE 用户表名称
// RBAC_ACCESS_TABLE 权限表名称
// RBAC_NODE_TABLE 节点表名称

class RBAC {
    // 认证方法
    static public function authenticate($map,$model='') {
        if(empty($model)) $model =  C('USER_AUTH_MODEL');
        //使用给定的Map进行认证
        return M($model)->where($map)->find();
    }
    //用于检测用户权限的方法,并保存到Session中，登陆成功以后，注册有权限
    static function saveAccessList($authId = null) {
    	if (null === $authId){
    		$authId = $_SESSION[C('USER_AUTH_KEY')];
    	}
    	// 如果使用普通权限模式，保存当前用户的访问权限列表
    	// 对管理员开发所有权限
    	if (C('USER_AUTH_TYPE') == 1 && $_SESSION[C('USER_AUTH_KEY')]){
    		$_SESSION['_ACCESS_LIST'] = RBAC::getAccessList($authId);
    	}
    	return;
    }
    /**
     +----------------------------------------------------------
     * 取得当前认证号的所有权限列表
     +----------------------------------------------------------
     * @param integer $authId 用户ID
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    static public function getAccessList($authId) {
    	//角色ID
    	$role_id = M(C('USER_AUTH_MODEL'))->where(array("id" => $authId))->getField("role_id");
    	//检查角色
    	$roleinfo = M(C('RBAC_ROLE_TABLE'))->where(array("id"=>$role_id,'status'=>1))->count('id');
    	if($roleinfo == 0){
    		return false;
    	}
    	//全部权限
    	$accessDATA = M(C('RBAC_ACCESS_TABLE'))->where(array("role_id" => $role_id))->select();
    	$accessList = array();
    	foreach ($accessDATA as $acc) {
    		$g = strtoupper($acc['g']);
    		$m = strtoupper($acc['m']);
    		$a = strtoupper($acc['a']);
    		$accessList[$g][$m][$a] = 1;
    	}
    	return $accessList;
    }
    //权限认证的过滤器方法 第一步
    static public function AccessDecision($appName = APP_NAME) {
    	//检查是否需要认证
    	if (RBAC::checkAccess()) {
    		//存在认证识别号，则进行进一步的访问决策
    		$accessGuid = md5($appName . MODULE_NAME . ACTION_NAME);
    		//判断是否超级管理员，是无需进行权限认证
    		if (empty($_SESSION[C('ADMIN_AUTH_KEY')])){
    			//认证类型 1 登录认证 2 实时认证
    			if (C('USER_AUTH_TYPE') == 2) {
    				//加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
    				//通过数据库进行访问检查
    				$accessList = RBAC::getAccessList($_SESSION[C('USER_AUTH_KEY')]);
    			}else{
    				// 如果是管理员或者当前操作已经认证过，无需再次认证
    				if($_SESSION[$accessGuid]){
    					return true;
    				}
    				//登录验证模式，比较登录后保存的权限访问列表
    				$accessList = $_SESSION['_ACCESS_LIST'];
    			}
    			//判断是否为组件化模式，如果是，验证其全模块名
    			if (!isset($accessList[strtoupper($appName)][strtoupper(MODULE_NAME)][strtoupper(ACTION_NAME)])) {
    				if (($appName == "Admin" && in_array(MODULE_NAME, array("Index")) && in_array(ACTION_NAME, array("index"))) || (substr(ACTION_NAME, 0, 7) == 'public_')) {
    					$_SESSION[$accessGuid] = true;
    				}else{
    					$_SESSION[$accessGuid] = false;
    				}
    			} else {
    				$_SESSION[$accessGuid] = true;
    			}
    			return $_SESSION[$accessGuid];
    		}else{
    			//超级管理员无需认证
    			return true;
    		}
    	}else{
    		return true;
    	}
    }
    //检查当前操作是否需要认证 第二步
    static function checkAccess() {
    	//如果项目要求认证，并且当前模块需要认证，则进行权限认证
    	if (C('USER_AUTH_ON')) {
    		$_module = C('NOT_AUTH_MODULE') ? explode(',',strtoupper(C('NOT_AUTH_MODULE'))) : null;
    		//检查当前模块是否需要认证
    		if ((empty($_module) && !in_array(strtoupper(MODULE_NAME), $_module))) {
    			$_action = C('NOT_AUTH_ACTION') ? explode(',', strtoupper(C('NOT_AUTH_ACTION'))) : null;
    			//检查当前操作是否需要认证
    			if ((empty($_action) && !in_array(strtoupper(ACTION_NAME), $_action))) {
    				return true;
    			} else {
    				return false;
    			}
    		} else {
    			return false;
    		}
    	}
    	return false;
    }
    // 登录检查
    static public function checkLogin() {
    	//检查当前操作是否需要认证
    	if (RBAC::checkAccess()) {
    		//检查认证识别号
    		return isset($_SESSION[C('USER_AUTH_KEY')]);
    	}
    	return true;
    }
}