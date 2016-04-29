<?php
/**
 * 前台专题 Action
 * @version        JiuduCMS 1.9.22 2014年09月22日09:00:00 struggle $
 * @package        JiuduCMS.Administrator
 * @copyright      Copyright (c) 2011 - 2014, 9ducx, Inc.
 * @license        http://www.jiuducms.com/help/
 * @link           http://www.jiuducms.com/
 */
class TopicAction extends HomeAction {   
	public $htmlid;
	private $id;
    public function _before_index(){
    	$this->id = $this->htmlid ? $this->htmlid : $_GET['id'];
    	$where['id'] = $this->id;
    	$where['status'] = 1;
		$vo = M('topic')->field('name,ztpic,keywords,description,click,tempindex')->where($where)->find();
    	if(!$vo){
			$this->error('你访问的专题不存在');
		}
		M('topic')->where($where)->setInc('click');
		$this->tempfile = $vo['tempindex'];
		$this->assign('field',$vo);
    }
}