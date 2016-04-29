<?php
/**
 * 前台内容页  Action
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class ArticleAction extends HomeAction {
	public $typeid;
	public $id;
	private $keywords;
	private $linknum;
	private $article;
	function _initialize(){
		parent::_initialize();
		$this->article = M('archives');
	}
    public function _before_index(){
    	$this->id = $_POST['articleid'] = $_GET['id'];
		$vo = $this->article->where('id='.$this->id)->find();
		if(!$vo){
			$this->error('你访问的内容不存在');
		}
		$this->keywords = $vo['keywords'];
		$this->typeid = $_POST['typeid'] = $vo['typeid'];
    	$type = M('Arctype')->field('typename,seotitle,typeimg,temparticle,linknum,cid')->find($this->typeid);
		if(!$type){
			$this->error('你访问的内容所属栏目不存在');
		}
		$this->tempfile = $type['temparticle'];
		$this->linknum = $type['linknum'];
		$type['typeurl'] = Url::arctype($this->typeid);
		$addtable = M('Conmodel')->where('id='.$type['cid'])->getField('addtable');
		$addcon = M($addtable)->field('id',true)->find($vo['aid']);
		$addfield = M('Field')->field('fieldname,dtype')->where(array('mid'=>$type['cid'],'type'=>1))->select();
    	foreach ($addfield as $v){
			if($v['dtype'] == 8){
				$addcon[$v['fieldname']] = unserialize($addcon[$v['fieldname']]);
			}elseif($v['dtype'] == 4){
				$addcon[$v['fieldname']] = $this->key($addcon[$v['fieldname']]);
			}elseif($v['dtype'] == 14){
				if(C('JIUDU_DOWN_SHOWTYPE') == 1){
					$addcon[$v['fieldname']] = U('Download/index').'&id='.$this->id.'&key='.md5($vo['aid']);
					unset($addcon[$v['fieldname'].'_local'],$addcon[$v['fieldname'].'_list1'],$addcon[$v['fieldname'].'_list2']);
				}elseif(C('JIUDU_DOWN_GOTOJUMP') == 1){
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
		$this->assign('linkinfo',$this->linkinfo());
		$this->assign('info',$this->context());
		$this->assign('field',$field);
    }
    private function context(){
    	$pre = $this->article->field('id,title')->where('id<'.$this->id.' AND typeid='.$this->typeid)->order('id desc')->find();
    	$next = $this->article->field('id,title')->where('id>'.$this->id.' AND typeid='.$this->typeid)->order('id asc')->find();
    	$info['pre'] = $pre ? '<a href="'.Url::archives($pre['id']).'">'.$pre['title'].'</a>' : '已经是第一篇了';
    	$info['next'] = $next ? '<a href="'.Url::archives($next['id']).'">'.$next['title'].'</a>' : '已经是最后一篇了';
    	return $info;
    }
    private function linkinfo(){
    	if($this->linknum == 0){
    		return '';
    	}
    	$keywords = explode(',',$this->keywords);
    	foreach ($keywords as $v){
    		$where['keywords'][] = array('like','%'.$v.'%');
    	}
    	$where['keywords'][] = 'or';
    	$where['status'] = array('eq',1);
    	$where['id'] = array('neq',$this->id);
    	$info = $this->article->field('id,title')->order('id DESC')->where($where)->limit($this->linknum)->select();
    	if(!$info){
    		return '暂时没有相关文章';
    	}
    	foreach ($info as $v){
    		$val .= '<li><a href="'.Url::archives($v['id']).'">'.esub($v['title'],C('JIUDU_LINKINFO_ROW')).'</a></li>'."\n";
    	}
    	return $val;
    }
}