<?php
/**
 * 后台首页
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class IndexAction extends AdminAction {
	// 后台首页
	public function index(){
		//栏目总数
		$count['arctype'] = M('Arctype')->count('id');
		//最新文档，文档总数
		$Archives = M('Archives');
		$count['archives'] = $Archives->count('id');
		$archiveslist = $Archives->limit(6)->order('id desc')->select();
		//用户数
		$count['user'] = M('User')->count('id');
		//评论数
		$count['comment'] = M('Comment')->count('id');
		//官方动态
		import('Curl');
		$curl = new Curl();
		$dongtai = $curl->get(C('JIUDU_SERVICE_URL_NEWS'));
		$dongtai = json_decode($dongtai,true);
		$count['version'] = $dongtai['data']['version'];
		$this->menu();
		$this->assign ( 'count', $count);
		$this->assign ( 'archiveslist', $archiveslist );
		$this->assign ( 'dongtai', $dongtai['data']['news']);
		C( 'SHOW_RUN_TIME',false); // 运行时间显示
		C( 'SHOW_PAGE_TRACE',false);
		$this->display ();
	}
	// 菜单页面
	public function menu() {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $voList = array();
           	if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]) && C('JIUDU_MENU_CACHE')) {
                //如果已经缓存，直接读取缓存
               	$voList = $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];
           	}else{
                //读取数据库模块列表生成菜单项
				$model = M('Menu');
				$dingji = $model->where('pid=0 and status=1')->select();
				foreach ($dingji as $k=>$v){
					if($v['type']!=2){
						$voList[$k] = $dingji[$k];
						$val = $model->where('status=1 and pid='.$v['id'])->select();
						if($val){
							$voList[$k]['con'] = $val;
						}
						foreach ($voList[$k]['con'] as $k1=>$v1){
							if($v['type']!=2){
								$val = $model->where('status=1 and pid='.$v1['id'])->select();
								if($val){
									$voList[$k]['con'][$k1]['con'] = $val;
								}
							}
						}
					}else{
						$voList[$k] = $dingji[$k];
					}
				}
				if(C('JIUDU_MENU_CACHE')){
					//缓存菜单访问
					$_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	= $voList;
				}
            }
            $this->assign('menu',$voList);
		}
	}
}
?>