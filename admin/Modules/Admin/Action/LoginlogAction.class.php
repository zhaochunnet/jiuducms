<?php
/**
 * 登录日志管理
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class LoginlogAction extends AdminAction {
	function _before_index(){
		$_REQUEST['listRows'] = 20;
	}
	function deleteall(){
		$model = M('Loginlog');
		$sql = 'DELETE FROM '.$model->getTableName();
		if (false !== $model->execute($sql)) {
			$this->success ('删除全部成功！');
		} else {
			$this->error ('删除全部失败！');
		}
	}
	function emptydata(){
		$model = M('Loginlog');
		$sql = 'TRUNCATE TABLE '.$model->getTableName();
		if (false !== $model->execute($sql)) {
			$this->success ('ID归零成功！');
		} else {
			$this->error ('ID归零失败！');
		}
	}
}