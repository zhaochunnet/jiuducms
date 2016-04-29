<?php
/**
 * 系统公共方法
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class AdminAction extends CommonAction {
	public function _initialize() {
		parent::_initialize();
        import('Cookie');	
        import('RBAC');
        //检查是否登录
        if(RBAC::checkLogin() == false){
        	if (IS_AJAX){
        		$data['status'] = '301';
        		$this->ajaxReturn($data);
        	} else {
        		//跳转到认证网关
        		redirect (PHP_FILE.C('USER_AUTH_GATEWAY' ));
        	}
        }
		if (!RBAC::AccessDecision (GROUP_NAME)){
			// 没有权限 抛出错误
			if(C( 'RBAC_ERROR_PAGE')){
				// 定义权限错误页面
				redirect(C('RBAC_ERROR_PAGE'));
			}else{
				$this->error (L('_VALID_ACCESS_'));
			}
		}
	}
	public function index(){
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = M($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
        $name = $this->getActionName();
		$model = D ( $name );
		$map = array ();
		foreach ( $model->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '' && $_REQUEST [$val] != 0) {
				$map [$val] = $_REQUEST [$val];
			}
		}
		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _list($model, $map, $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (!empty ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] == 'asc' ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$totalCount = $model->where($map)->count('id');
		if ($totalCount > 0) {
			//每页显示的条数默认20条
			$numPerPage = $_REQUEST ['numPerPage'] ? $_REQUEST ['numPerPage'] : 20;
			//当前页
			$pageNum = $_REQUEST ['pageNum'] ? $_REQUEST ['pageNum'] : 1;
			//分页条件组合
			$firstRow = ($pageNum-1)*$numPerPage;
			//分页查询数据
			$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($firstRow.','.$numPerPage)->select();
			//分页显示
			$page = array('numPerPage'=>$numPerPage,'pageNum'=>$pageNum,'totalCount'=>$totalCount);
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		return;
	}
	//添加操作
	public function add() {
		if(IS_POST){
			$name=$this->getActionName();
			$model = D ($name);
			if (false === $model->create ()) {
				$this->error ( $model->getError () );
			}
			//保存当前数据对象
			$list=$model->add ();
			if ($list!==false) { //保存成功
				$this->success ('新增成功!');
			} else {
				//失败提示
				$this->error ('新增失败!');
			}
		}else{
			$this->display ();
		}
	}
	//修改操作
	public function update() {
		$name=$this->getActionName();
		if(IS_POST && IS_AJAX){	
			$model = D ( $name );
			$data = $model->create ();
			if (false === $data) {
				$this->error ( $model->getError () );
			}
			// 更新数据
			$list=$model->where('id = '.$_POST['id'])->data($data)->save();
			if (false !== $list) {
				//成功提示
				$this->success ('编辑成功!');
			} else {
				//错误提示
				$this->error ('编辑失败!');
			}
		}else{
			$model = M ( $name );
			$id = $_REQUEST [$model->getPk ()];
			$vo = $model->getById ( $id );
			$this->assign ( 'vo', $vo );
			$this->display ();
		}
	}
	/**
     +----------------------------------------------------------
	 * 默认删除操作
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$pk = $model->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $model->where ( $condition )->delete ()) {
					$this->success ('删除成功！');
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
		$this->forward ();
	}
	/**
     +----------------------------------------------------------
	 * 默认禁用操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	public function forbid() {
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$model->forbid ( $condition );
		if ($list!==false) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态禁用成功' );
		} else {
			$this->error  (  '状态禁用失败！' );
		}
	}
	
	/**
     +----------------------------------------------------------
	 * 默认启用操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	public function resume() {
		//恢复指定记录
		$name=$this->getActionName();
		$model = D ($name);
		$pk = $model->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $model->resume ( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态启用成功！' );
		} else {
			$this->error ( '状态启用失败！' );
		}
	}


	public function saveSort() {
		$seqNoList = $_POST ['seqNoList'];
		if (! empty ( $seqNoList )) {
			//更新数据对象
		$name=$this->getActionName();
		$model = D ($name);
			$col = explode ( ',', $seqNoList );
			//启动事务
			$model->startTrans ();
			foreach ( $col as $val ) {
				$val = explode ( ':', $val );
				$model->id = $val [0];
				$model->sort = $val [1];
				$result = $model->save ();
				if (! $result) {
					break;
				}
			}
			//提交事务
			$model->commit ();
			if ($result!==false) {
				//采用普通方式跳转刷新页面
				$this->success ( '更新成功' );
			} else {
				$this->error ( $model->getError () );
			}
		}
	}
    //保存操作日志
    public function setlog($actionname,$message){
    	return true;
    }
}
?>