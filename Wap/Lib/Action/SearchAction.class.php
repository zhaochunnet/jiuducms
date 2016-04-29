<?php
/**
 * 前台搜索 Action
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class SearchAction extends HomeAction {
	function _before_index(){
		$this->tempfile = 'search.html';
    	$Searchkey = M('Searchkey');
    	$data['keyword'] = $_REQUEST['keyword'];
    	$searchkey_id = $Searchkey->where($data)->getField('id');
    	$data['lasttime'] = time();
    	$model = M('Archives');
    	if($_REQUEST['searchtype']){
    		$where['title'] = array('like','%'.$_REQUEST['keyword'].'%');
    		$where[$model->getTableName().'.keywords'] = array('like','%'.$_REQUEST['keyword'].'%');
    		$where[$model->getTableName().'.description'] = array('like','%'.$_REQUEST['keyword'].'%');
    		$where['_logic'] = 'or';
    		$map['_complex'] = $where;
    	}else{
    		$map['title'] = array('like','%'.$_REQUEST['keyword'].'%');
    	}
    	if($_REQUEST['typeid']){
    		$map['typeid']  = array('eq',$_REQUEST['typeid']);
    	}
    	$count = $model->where($map)->count('id');
    	$data['total'] = $count;
    	if($searchkey_id){
    		$data['count'] = array('exp','count+1');
    		$Searchkey->where('id='.$searchkey_id)->save($data);
    	}else{
    		$Searchkey->add($data);
    	}
    	if($count){
	    	import("Page");
	    	$p = new Page($count,C('JIUDU_SEARCH'));
	    	$p->setConfig('theme',C('JIUDU_PAGE_STYLE'));
	    	$list = $model->field($model->getTableName().'.id as id,click,title,typeid,typename,shorttitle,writer,source,litpic,pubdate,'.$model->getTableName().'.description as description,'.$model->getTableName().'.keywords as keywords,url')->join(C('DB_PREFIX').'arctype ON '.C('DB_PREFIX').'arctype.id = typeid')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
	    	foreach ($list as $k=>$v){
				$list[$k]['titleurl'] = $v['url'] ? $v['url'] : Url::archives($v['id']);
				$list[$k]['typeurl'] = Url::arctype($v['typeid']);
				$list[$k]['title'] = preg_replace('/'.$_REQUEST['keyword'].'/','<font color="#FF0000">'.$_REQUEST['keyword'].'</font>',$v['title'],1);
				$list[$k]['description'] = preg_replace('/'.$_REQUEST['keyword'].'/','<font color="#FF0000">'.$_REQUEST['keyword'].'</font>',$v['description'],1);
			}
	    	$this->assign('list',$list);
	    	$this->assign('page',$p->show());
		}
		$this->assign('field',$_REQUEST);
	}
}