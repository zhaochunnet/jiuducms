<?php
/**
 * 前台列表页 Action
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
import('Home');
class ListAction extends HomeAction{
	public $htmlid;
    public function _before_index(){
    	$this->typeid = $_POST['typeid'] = $this->htmlid ? $this->htmlid : $_GET['id'];
    	$type = M('Arctype')->where('status =1 AND id='.$this->typeid)->field('id as typeid,cid,typename,seotitle,keywords,description,typeimg,content,ispart,tempindex,templist,typedir,listnum')->find();
    	if(!$type){
			$this->error('你访问的内容不存在');
		}
    	if($type['ispart'] == 2){
    		$typeid = $this->typeid;
    		$this->tempfile = $type['templist'];
    	}elseif($type['ispart'] == 1){
    		$typeid = M('Arctype')->where('pid='.$this->typeid)->getField('id',true);
    		$this->tempfile = $type['tempindex'];
    	}elseif($type['ispart'] == 3){
    		redirect($type['typeurl']);
    	}
    	//调用附加表代码暂时删除
    	if($typeid){
    		$where['typeid'] = array('in',$typeid);
    		$where['status'] = 1;
    		$model = M('Archives');
    		$count = $model->where($where)->count('id');
    		if($count){
    			import("HomePage");
    			$p = new HomePage ( $count,$type['listnum'],$this->typeid);
    			$p->setConfig('theme',C('JIUDU_PAGE_STYLE'));
    			$field = M('Conmodel')->field('addtable,listfields')->find($type['cid']);
    			if($field['listfields']){
    				$field['listfields'] = explode(',', $field['listfields']);
    				foreach ($field['listfields'] as $k=>$v){
    					$fields[] = C('DB_PREFIX').$field['addtable'].'.'.$v.' AS `'.$v.'`';
    				}
    				$fields = ','.join(',', $fields);
    				$model->join(C('DB_PREFIX').$field['addtable'].' ON '.$model->getTableName().'.aid = '.C('DB_PREFIX').$field['addtable'].'.id');
    			}
    			$list = $model->field($model->getTableName().'.id AS `id`,click,title,shorttitle,writer,source,litpic,pubdate,description,keywords,url'.$fields)->where($where)->limit($p->firstRow.','.$p->listRows)->order('sortrank ASC,id DESC')->select();
    			foreach ($list as $k=>$v){
    				$list[$k]['titleurl'] = $v['url'] ? $v['url'] : Url::archives($v['id']);
    			}
    			$this->assign('list',$list);
    			$this->assign('page',$p->show());
    		}
    	}
    	$this->assign('field',$type);
    }
}