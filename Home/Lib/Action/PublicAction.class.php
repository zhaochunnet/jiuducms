<?php
/**
 * 网站前台公共方法
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class PublicAction extends Action {
    public function adurl(){
    	$Ad = M('ad');
    	$Ad->where('id = '.$_GET['id'])->setInc('onclick'); 
    	$vo = $Ad->field('url')->find($_GET['id']);
    	if(substr($vo['url'],0,1) == '#'){
    		$vo['url'] = '/';
    	}
    	redirect($vo['url']);
    }
    public function Articleclicks(){
    	$id = $_GET['id'];
    	$model = M('archives');
    	$num = $model->field('click')->find($id);
    	$model->where('id='.$id)->setInc('click');
    	exit('document.write("'.$num['click'].'");');
    }
    public function download(){
    	$urlid = $_GET['urlid'];
    	$type = $_GET['type'];
    	$archive = M('Archives')->field('aid,cid')->find($_GET['aid']);
    	if($archive && md5($archive['aid'].$type.$urlid) == $_GET['key']){
    		$table = M('conmodel')->where(array('id'=>$archive['cid'],'status'=>1))->getField('addtable');
    		$soft = M($table)->where('id='.$archive['aid'])->getField('softurl_'.$type);
    		if($type == 'local'){
    			$soft = SITE_PATH.$soft;
    			if(is_file($soft)){
    				import('Http');
    				Http::download($soft);
    			}else{
    				$this->error('该软件不存在或已损坏');
    			}
    		}elseif($type == 'list1'){
    			$soft = unserialize($soft);
    			redirect($soft[$urlid]['host'].$soft[$urlid]['url']);
    		}elseif($type == 'list2'){
    			$soft = unserialize($soft);
    			redirect($soft[$urlid]['url']);
    		}else{
    			$this->error('该软件不存在');
    		}
    	}else{
    		$this->error('该软件不存在');
    	}
    }
    public function _empty(){
    	redirect('/');
    }
}