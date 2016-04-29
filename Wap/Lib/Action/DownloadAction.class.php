<?php
/**
 * 独立下载列表页
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class DownloadAction extends HomeAction {
    public function _before_index(){
    	$this->id = $_GET['id'];
		$vo = M('Archives')->where('id='.$this->id)->find();
		if(!$vo || md5($vo['aid']) !== $_GET['key']){
			$this->error('你访问的软件不存在');
		}
		$this->keywords = $vo['keywords'];
		$this->typeid = $_POST['typeid'] = $vo['typeid'];
		$type = M('Arctype')->field('typename,seotitle,typeimg,temparticle,linknum,cid')->find($this->typeid);
		if(!$type){
			$this->error('你访问的内容所属栏目不存在');
		}
		$type['typeurl'] = Url::arctype($this->typeid);
		$this->tempfile = 'download_list.html';
		$addtable = M('Conmodel')->where('id='.$type['cid'])->getField('addtable');
		$addcon = M($addtable)->find($vo['aid']);
		$addfield = M('Field')->field('fieldname,dtype')->where(array('mid'=>$type['cid'],'type'=>1))->select();
		foreach ($addfield as $v){
			if($v['dtype'] == 14){
				if(C('JIUDU_DOWN_SHOWTYPE') == 1){
					$urlid = mt_rand(0,10);
					$addcon[$v['fieldname'].'_local'] = $addcon[$v['fieldname'].'_local'] ? U('Public/download').'&aid='.$this->id.'&key='.md5($vo['aid'].'local'.$urlid).'&urlid='.$urlid.'&type=local' : null;
					$addcon[$v['fieldname'].'_list1'] = $addcon[$v['fieldname'].'_list1'] ? unserialize($addcon[$v['fieldname'].'_list1']) : null;
					foreach($addcon[$v['fieldname'].'_list1'] as $k1=>$v1){
						if($v1['status'] == 1){
							$addcon[$v['fieldname'].'_list1'][$k1]['url'] = U('Public/download').'&aid='.$this->id.'&key='.md5($vo['aid'].'list1'.$k1).'&urlid='.$k1.'&type=list1';
							unset($addcon[$v['fieldname'].'_list1'][$k1]['status'],$addcon[$v['fieldname'].'_list1'][$k1]['host']);
						}else{
							unset($addcon[$v['fieldname'].'_list1'][$k1]);
						}
					}
					$addcon[$v['fieldname'].'_list2'] = $addcon[$v['fieldname'].'_list2'] ? unserialize($addcon[$v['fieldname'].'_list2']) : null;
					foreach($addcon[$v['fieldname'].'_list2'] as $k1=>$v1){
						$addcon[$v['fieldname'].'_list2'][$k1]['url'] = U('Public/download').'&aid='.$this->id.'&key='.md5($vo['aid'].'list2'.$k1).'&urlid='.$k1.'&type=list2';
					}
				}else{
					$addcon[$v['fieldname'].'_list1'] = $addcon[$v['fieldname'].'_list1'] ? unserialize($addcon[$v['fieldname'].'_list1']) : null;
					foreach($addcon[$v['fieldname'].'_list1'] as $k1=>$v1){
						if($v1['status'] == 1){
							$addcon[$v['fieldname'].'_list1'][$k1]['url'] = $v1['host'].$v1['url'];
							unset($addcon[$v['fieldname'].'_list1'][$k1]['status'],$addcon[$v['fieldname'].'_list1'][$k1]['host']);
						}else{
							unset($addcon[$v['fieldname'].'_list1'][$k1]);
						}
					}
					$addcon[$v['fieldname'].'_list2'] = $addcon[$v['fieldname'].'_list2'] ? unserialize($addcon[$v['fieldname'].'_list2']) : null;
				}
				
			}
		}
		$field = array_merge($vo,$addcon,$type);
		$this->assign('field',$field);
    }
}